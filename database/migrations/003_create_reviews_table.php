<?php

    return [
        "up" => "CREATE TABLE reviews (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            rating INT NOT NULL,
            review TEXT,
            users_id INT UNSIGNED NOT NULL,
            movies_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_reviews_users FOREIGN KEY (users_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT fk_reviews_movies FOREIGN KEY (movies_id) REFERENCES movies(id) ON DELETE CASCADE,
            CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 10),
            CONSTRAINT uq_reviews_users_movies UNIQUE (users_id, movies_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        "down" => "DROP TABLE IF EXISTS reviews",
    ];
?>
