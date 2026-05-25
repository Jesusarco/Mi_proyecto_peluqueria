<?php

// declare(strict_types=1);
// $host = 'localhost';
// $dbname = 'daw2_jesus';
// $user = 'jesus';
// $password = 'MurgiDAW2_2026!';

$host = "localhost";
$dbname = "peluqueria_db";
$user = "root";
$password = "";
// $password = "root";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>