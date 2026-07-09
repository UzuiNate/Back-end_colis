<?php 

function connectDatabase() {
    $dsn = "mysql:host=localhost;dbname=db_php;charset=utf8mb4";
    $username = "root";
    $password = "nate1303";

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
        return false;
    }
}

