<?php

$dbName = "category-tree";
$dbUser = "root";

function dbConn()
{
    $pdo = null;
    global $dbName, $dbUser;
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=' . $dbName, $dbUser, '');
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    return $pdo;
}