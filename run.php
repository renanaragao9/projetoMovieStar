#!/usr/bin/env php
<?php

    // Roda as migrations e inicia o servidor local usando o MySQL já instalado na máquina.
    //
    // Uso:
    //   php run.php          Migrations + app em http://localhost:8000
    //
    // Configurações via variáveis de ambiente ou .env:
    //   PORT, DB_HOST, DB_PORT, DB_USER, DB_PASS

    $appPort = getenv("PORT") ?: "8000";

    chdir(__DIR__);

    function info(string $msg): void
    {
        echo "[run] " . $msg . PHP_EOL;
    }

    function error(string $msg): void
    {
        fwrite(STDERR, "[run] ERRO: " . $msg . PHP_EOL);
    }

    function requireDatabase(): void
    {
        if (!defined("DB_HOST")) {
            define("DB_CONNECTION_SKIP", true);
            require_once __DIR__ . "/database/db.php";
        }

        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";

        try {
            $conn = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);
        } catch (PDOException $e) {
            error("Não foi possível conectar ao MySQL ({$dsn}): " . $e->getMessage());
            error("Verifique se o MySQL está rodando e se as credenciais no .env estão corretas.");
            exit(1);
        }

        info("MySQL conectado em " . DB_HOST . ":" . DB_PORT . " (banco '" . DB_NAME . "').");
    }

    function runMigrations(): void
    {
        info("Executando migrations...");

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

    $command = $argv[1] ?? "start";

    if ($command !== "start") {
        echo "Uso: php run.php" . PHP_EOL;
        exit(1);
    }

    requireDatabase();
    runMigrations();
    startApp($appPort);
?>
