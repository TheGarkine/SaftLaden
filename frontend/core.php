<?php
    $page = $_GET['page'] ?? 'cover';

    interface Storable {
        function serialize();
        function deserialize($obj);

        function store();
        function load();
    }

    $hostname = 'localhost';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$hostname;dbname=saftladen", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS `glasses` (
            `rfid` INT NOT NULL,
            `volume` INT NOT NULL,
            PRIMARY KEY (`rfid`)
        );
    ');

    $pdo->exec('
        CREATE TABLE IF NOT EXISTS `mixtures` (
            `id` INT NOT NULL,
            `glass` INT NOT NULL,
            `juice_ratio` TEXT,
            PRIMARY KEY (`id`, `glass`),
            FOREIGN KEY (`glass`) REFERENCES `glasses`(`rfid`) ON DELETE CASCADE
        )
    ');
    