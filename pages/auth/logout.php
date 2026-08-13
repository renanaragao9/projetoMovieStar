<?php
    require_once(__DIR__ . "/../../templates/header.php");

    if($userDao) {
        $userDao->destroyToken();
    }

?>