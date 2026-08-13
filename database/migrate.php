<?php

    /**
     * Runner de migrations do MovieStar.
     *
     * Uso:
     *   php database/migrate.php status     Lista migrations aplicadas e pendentes
     *   php database/migrate.php up         Executa todas as migrations pendentes
     *   php database/migrate.php down       Reverte a última migration aplicada
     *   php database/migrate.php down --all Reverte todas as migrations
     *   php database/migrate.php fresh      Reverte tudo e aplica do zero
     */

    define("DB_CONNECTION_SKIP", true);
    require_once __DIR__ . "/db.php";

    $command = $argv[1] ?? "status";
    $flag = $argv[2] ?? null;

    function getConnection(): PDO
    {
        $conn = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $conn->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->exec("USE `" . DB_NAME . "`");
        $conn->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        return $conn;
    }

    function loadMigrations(): array
    {
        $files = glob(__DIR__ . "/migrations/*.php");
        sort($files);

        $migrations = [];
        foreach ($files as $file) {
            $migrations[basename($file)] = require $file;
        }

        return $migrations;
    }

    function getApplied(PDO $conn): array
    {
        return $conn->query("SELECT migration FROM migrations ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
    }

    function runStep(PDO $conn, string $migrationName, array $definition, string $direction): void
    {
        $step = $definition[$direction] ?? null;

        if (is_callable($step)) {
            $step($conn);
        } elseif (is_string($step)) {
            $conn->exec($step);
        } else {
            throw new RuntimeException("Migration '{$migrationName}' não possui o passo '{$direction}'.");
        }
    }

    function migrateUp(PDO $conn, string $migrationName, array $definition): void
    {
        runStep($conn, $migrationName, $definition, "up");

        $stmt = $conn->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
        $stmt->execute([":migration" => $migrationName]);

        echo "[UP] {$migrationName}" . PHP_EOL;
    }

    function migrateDown(PDO $conn, string $migrationName, array $definition): void
    {
        runStep($conn, $migrationName, $definition, "down");

        $stmt = $conn->prepare("DELETE FROM migrations WHERE migration = :migration");
        $stmt->execute([":migration" => $migrationName]);

        echo "[DOWN] {$migrationName}" . PHP_EOL;
    }

    function printStatus(array $migrations, array $applied): void
    {
        foreach ($migrations as $name => $definition) {
            $status = in_array($name, $applied) ? "aplicada" : "pendente";
            echo "- {$name} ({$status})" . PHP_EOL;
        }

        if (empty($migrations)) {
            echo "Nenhuma migration encontrada em database/migrations/" . PHP_EOL;
        }
    }

    function printUsage(): void
    {
        echo "Uso: php database/migrate.php {status|up|down|fresh} [--all]" . PHP_EOL;
    }

    $conn = getConnection();
    $migrations = loadMigrations();
    $applied = getApplied($conn);

    switch ($command) {
        case "status":
            printStatus($migrations, $applied);
            break;

        case "up":
            $count = 0;
            foreach ($migrations as $name => $definition) {
                if (!in_array($name, $applied)) {
                    migrateUp($conn, $name, $definition);
                    $count++;
                }
            }
            echo $count === 0
                ? "Tudo em dia, nenhuma migration pendente." . PHP_EOL
                : "{$count} migration(s) executada(s)." . PHP_EOL;
            break;

        case "down":
            $targets = array_reverse($applied);

            if ($flag !== "--all") {
                $targets = array_slice($targets, 0, 1);
            }

            foreach ($targets as $name) {
                if (isset($migrations[$name])) {
                    migrateDown($conn, $name, $migrations[$name]);
                } else {
                    echo "[AVISO] {$name} não encontrada em database/migrations/, removida do registro." . PHP_EOL;
                    $stmt = $conn->prepare("DELETE FROM migrations WHERE migration = :migration");
                    $stmt->execute([":migration" => $name]);
                }
            }
            break;

        case "fresh":
            foreach (array_reverse($applied) as $name) {
                if (isset($migrations[$name])) {
                    migrateDown($conn, $name, $migrations[$name]);
                }
            }
            foreach ($migrations as $name => $definition) {
                migrateUp($conn, $name, $definition);
            }
            echo "Banco recriado do zero." . PHP_EOL;
            break;

        default:
            printUsage();
            break;
    }
?>
