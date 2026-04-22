<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $nombre = trim(htmlspecialchars($_POST['nombre']));
    $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
    $precio = (float)$_POST['precio'];
    $duracion = (int)$_POST['duracion'];

    if ($id && $nombre && $precio > 0 && $duracion > 0) {
        $stmt = $conn->prepare("UPDATE servicios SET nombre=:n, descripcion=:d, precio=:p, duracion=:dur WHERE id=:id");
        $stmt->execute([":n" => $nombre, ":d" => $descripcion, ":p" => $precio, ":dur" => $duracion, ":id" => $id]);
    }
}
header("Location: ../admin/dashboard.php");
exit();
?>