<?php
session_start();
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:n, :e, :p)");
    $stmt->execute([
        ":n" => $nombre,
        ":e" => $email,
        ":p" => $password
    ]);

    header("Location: ../index.php");
}
?>