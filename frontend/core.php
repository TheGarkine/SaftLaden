<?php
    $page = $_GET['page'] ?? 'cover';

    interface Storable {
        function serialize();
        function deserialize($obj);

        function store();
        function load();
    }
    

    function getForeColor($backColorHex) {
        list($r, $g, $b) = sscanf($backColorHex, "#%02x%02x%02x");;
    
        // http://www.w3.org/TR/AERT#color-contrast
        $o = round(
            (
                ($r * 299) +
                ($g * 587) +
                ($b * 114)
            ) / 1000
        );
    
        return (($o > 125) ? '#000000' : '#FFFFFF');
    }


    $hostname = 'localhost';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$hostname;dbname=saftladen", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS `juices` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `name` VARCHAR(20),
            `color` VARCHAR(20),
            PRIMARY KEY (`id`)
        );
    ');
    
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS `glasses` (
            `rfid` BIGINT UNSIGNED NOT NULL,
            `volume` INT NOT NULL,
            PRIMARY KEY (`rfid`)
        );
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS `mixtures` (
            `id` BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`id`) REFERENCES `glasses`(`rfid`) ON DELETE CASCADE
        )
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS `mixture_juices` (
            `id` INT,
            `mixture_id` BIGINT UNSIGNED NOT NULL,
            `juice_id` INT NOT NULL,
            `ratio` FLOAT,
            PRIMARY KEY (`id`, `mixture_id`),
            FOREIGN KEY (`mixture_id`) REFERENCES `mixtures`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`juice_id`) REFERENCES `juices`(`id`) ON DELETE CASCADE
        )
    ');