<?php

    return [
        "up" => "CREATE TABLE movies (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            image VARCHAR(255),
            trailer VARCHAR(255),
            category VARCHAR(50) NOT NULL,
            length VARCHAR(50),
            users_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_movies_users FOREIGN KEY (users_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "down" => "DROP TABLE IF EXISTS movies",
    ];
?>
