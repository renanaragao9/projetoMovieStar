<?php

    session_start();

    // URL base do projeto (raiz), independente de onde o script foi executado
    $scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
    $host = !empty($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : $_SERVER["SERVER_NAME"];

    $docRoot = rtrim(str_replace("\\", "/", realpath($_SERVER["DOCUMENT_ROOT"])), "/");
    $projectRoot = str_replace("\\", "/", __DIR__);

    $basePath = "";

    // Calcula o caminho do projeto relativo ao document root (ex.: /projetoMovieStar)
    if (strpos($projectRoot, $docRoot) === 0) {
        $basePath = substr($projectRoot, strlen($docRoot));
    }

    $BASE_URL = $scheme . "://" . $host . rtrim($basePath, "/") . "/";

?>
