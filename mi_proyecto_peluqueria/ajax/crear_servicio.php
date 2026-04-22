<?php
session_start();
require_once "../config/database.php";

if ($_SESSION['rol'] != 'admin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim(htmlspecialchars($_POST['nombre']));
    $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
    $precio = (float)$_POST['precio'];
    $duracion = (int)$_POST['duracion'];

    if ($nombre && $precio > 0 && $duracion > 0) {
        $stmt = $conn->prepare("INSERT INTO servicios (nombre, descripcion, precio, duracion) VALUES (:n, :d, :p, :dur)");
        $stmt->execute([":n" => $nombre, ":d" => $descripcion, ":p" => $precio, ":dur" => $duracion]);
    }
}
header("Location: ../admin/dashboard.php");
exit();
?>