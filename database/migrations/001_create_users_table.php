<?php

    return [
        "up" => "CREATE TABLE users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            lastname VARCHAR(100) NOT NULL,
            email VARCHAR(200) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            image VARCHAR(255),
            token VARCHAR(255),
            bio TEXT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "down" => "DROP TABLE IF EXISTS users",
    ];
?>
