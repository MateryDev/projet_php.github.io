<?php

// données d'authentification hardcodées

define('USERNAME', 'admin');
define('PASSWORD', '1234');
define('DB_HOST', 'localhost');
define('DB_NAME', 'epicerie_en_ligne');
define('DB_USER', 'root');
define('DB_PASS', 'root');

function getPDOConnection()
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}
