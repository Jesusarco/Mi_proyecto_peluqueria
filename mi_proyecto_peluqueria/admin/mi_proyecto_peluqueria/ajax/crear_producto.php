<?php
session_start();
require_once "../config/database.php";

if ($_SESSION['rol'] != 'admin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim(htmlspecialchars($_POST['nombre']));
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    
    if ($nombre && $precio > 0) {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (:n, :p, :s)");
        $stmt->execute([":n" => $nombre, ":p" => $precio, ":s" => $stock]);
    }
}
header("Location: ../admin/dashboard.php");
exit();
?>