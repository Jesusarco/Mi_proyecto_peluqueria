<?php
require_once "config/database.php";

$nombre = "Admin";
$email = "admin@admin.com";
$password = password_hash("123456", PASSWORD_DEFAULT);
$rol = "admin";

$sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:n, :e, :p, :r)";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ":n" => $nombre,
    ":e" => $email,
    ":p" => $password,
    ":r" => $rol
]);

echo "Admin creado correctamente";
?>