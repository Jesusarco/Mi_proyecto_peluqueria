<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $descripcion = trim(htmlspecialchars($_POST['descripcion']));
    $categoria = trim(htmlspecialchars($_POST['categoria'] ?? ''));
    $cantidad = (float)$_POST['cantidad'];

    if ($id && $descripcion && $cantidad > 0) {
        $stmt = $conn->prepare("UPDATE gastos SET descripcion=:d, categoria=:c, cantidad=:m WHERE id=:id");
        $stmt->execute([":d" => $descripcion, ":c" => $categoria, ":m" => $cantidad, ":id" => $id]);
    }
}
header("Location: ../admin/dashboard.php");
exit();
?>