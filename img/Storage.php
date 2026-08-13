<?php

    class Storage {

        const MOVIES_DIR = "movies";
        const USERS_DIR = "users";
        const DEFAULT_MOVIE_COVER = "movie_cover.jpg";

        private $baseDir;

        public function __construct() {
            $this->baseDir = __DIR__;
        }

        public function save(array $file, string $dir, string $name): ?string {
            $allowedTypes = ["image/jpeg", "image/jpg", "image/png"];

            if (!in_array($file["type"], $allowedTypes)) {
                return null;
            }

            $imageFile = $file["type"] === "image/png"
                ? imagecreatefrompng($file["tmp_name"])
                : imagecreatefromjpeg($file["tmp_name"]);

            if (!$imageFile) {
                return null;
            }

            imagejpeg($imageFile, $this->baseDir . "/" . $dir . "/" . $name, 100);

            return $name;
        }

        public static function movieCover(?string $image): string {
            if (empty($image) || !is_file(__DIR__ . "/" . self::MOVIES_DIR . "/" . $image)) {
                return self::DEFAULT_MOVIE_COVER;
            }

            return $image;
        }
    }
?>
