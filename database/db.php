<?php
    $envPath = __DIR__ . "/../.env";

    if (is_readable($envPath)) {
        $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($envLines as $line) {
            $line = trim($line);

            // Ignora comentários e linhas sem "="
            if ($line === "" || strpos($line, "#") === 0 || strpos($line, "=") === false) {
                continue;
            }

            list($key, $value) = explode("=", $line, 2);

            $key = trim($key);
            $value = trim($value);

            // Remove aspas simples ou duplas ao redor do valor
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = $value[strlen($value) - 1];

                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            // Variáveis de ambiente reais têm prioridade sobre o .env
            if (getenv($key) === false) {
                putenv($key . "=" . $value);
                $_ENV[$key] = $value;
            }
        }
    }

    define("DB_NAME", getenv("DB_NAME") ?: "moviestar");
    define("DB_HOST", getenv("DB_HOST") ?: "localhost");
    define("DB_PORT", getenv("DB_PORT") ?: "3306");
    define("DB_USER", getenv("DB_USER") ?: "root");
    define("DB_PASS", getenv("DB_PASS") ?: "");

    // Permite incluir este arquivo sem abrir conexão (usado pelo runner de migrations)
    if (!defined("DB_CONNECTION_SKIP")) {
        $conn = new PDO("mysql:dbname=". DB_NAME .";host=". DB_HOST .";port=". DB_PORT, DB_USER, DB_PASS);

        // Habilitar erros PDO
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
?>
