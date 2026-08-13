#!/usr/bin/env php
<?php

    // Sobe o banco MySQL (Docker), roda as migrations e inicia o servidor local.
    //
    // Uso:
    //   php run.php          Sobe banco + migrations + app em http://localhost:8000
    //   php run.php stop     Para o container do banco
    //
    // Configurações via variáveis de ambiente:
    //   PORT, DB_HOST_PORT, MYSQL_IMAGE

    $appPort = getenv("PORT") ?: "8000";
    $dbContainer = "moviestar-db";
    $dbVolume = "moviestar-db-data";
    $dbHostPort = getenv("DB_HOST_PORT") ?: "3307";
    $mysqlImage = getenv("MYSQL_IMAGE") ?: "mysql:8";

    chdir(__DIR__);

    function info(string $msg): void
    {
        echo "[run] " . $msg . PHP_EOL;
    }

    function error(string $msg): void
    {
        fwrite(STDERR, "[run] ERRO: " . $msg . PHP_EOL);
    }

    function shell(string $command, array &$output = [], int &$exitCode = 0): bool
    {
        exec($command . " 2>&1", $output, $exitCode);
        return $exitCode === 0;
    }

    function requireDocker(): void
    {
        if (!shell("command -v docker")) {
            error("Docker não encontrado. Instale o Docker para subir o banco.");
            exit(1);
        }

        if (!shell("docker info")) {
            error("Docker não está rodando ou o usuário não tem permissão.");
            exit(1);
        }
    }

    function containerExists(string $container, bool $onlyRunning = false): bool
    {
        $filter = $onlyRunning ? "ps" : "ps -a";
        return shell("docker {$filter} --format '{{.Names}}' | grep -qx {$container}");
    }

    function dbIsReady(string $container): bool
    {
        return shell("docker exec {$container} mysqladmin ping -h 127.0.0.1 --silent");
    }

    function startDb(string $dbContainer, string $dbVolume, string $dbHostPort, string $mysqlImage): void
    {
        info("Subindo banco MySQL (container '{$dbContainer}', porta {$dbHostPort})...");

        if (containerExists($dbContainer)) {
            if (!containerExists($dbContainer, true)) {
                shell("docker start {$dbContainer}");
            }
        } else {
            shell(
                "docker run -d" .
                " --name {$dbContainer}" .
                " -e MYSQL_ALLOW_EMPTY_PASSWORD=yes" .
                " -p {$dbHostPort}:3306" .
                " -v {$dbVolume}:/var/lib/mysql" .
                " {$mysqlImage}"
            );
        }

        info("Aguardando MySQL ficar pronto...");

        for ($i = 0; $i < 60; $i++) {
            if (dbIsReady($dbContainer)) {
                info("MySQL pronto.");
                return;
            }
            sleep(2);
        }

        error("MySQL não ficou pronto a tempo. Verifique com: docker logs {$dbContainer}");
        exit(1);
    }

    function runMigrations(string $dbHostPort): void
    {
        info("Executando migrations...");

        putenv("DB_HOST=127.0.0.1");
        putenv("DB_PORT=" . $dbHostPort);

        passthru("php database/migrate.php up", $exitCode);

        if ($exitCode !== 0) {
            error("Falha ao executar as migrations.");
            exit(1);
        }
    }

    function startApp(string $appPort): void
    {
        info("Subindo aplicação em http://localhost:{$appPort} (Ctrl+C para parar)");

        passthru("php -S localhost:{$appPort}", $exitCode);
    }

    function stopDb(string $dbContainer, string $dbVolume): void
    {
        info("Parando banco MySQL (container '{$dbContainer}')...");

        shell("docker stop {$dbContainer}");

        info("Banco parado. Os dados foram mantidos no volume '{$dbVolume}'.");
    }

    $command = $argv[1] ?? "start";

    switch ($command) {
        case "start":
            requireDocker();
            startDb($dbContainer, $dbVolume, $dbHostPort, $mysqlImage);
            runMigrations($dbHostPort);
            startApp($appPort);
            break;

        case "stop":
            requireDocker();
            stopDb($dbContainer, $dbVolume);
            break;

        default:
            echo "Uso: php run.php {start|stop}" . PHP_EOL;
            exit(1);
    }
?>
