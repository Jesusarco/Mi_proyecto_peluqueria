<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = isset($_GET['accion']) ? $_GET['accion'] : ''; // 'ascender' o 'descender'

if ($id > 0 && $id != $_SESSION['usuario_id']) {
    if ($accion == 'ascender') {
        $stmt = $conn->prepare("UPDATE usuarios SET rol = 'superadmin' WHERE id = :id AND rol = 'admin'");
        $stmt->execute([":id" => $id]);
    } elseif ($accion == 'descender') {
        $stmt = $conn->prepare("UPDATE usuarios SET rol = 'admin' WHERE id = :id AND rol = 'superadmin'");
        $stmt->execute([":id" => $id]);
    }
}
header("Location: ../admin/gestionar_admins.php");
exit();
?>