<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0 && $id != $_SESSION['usuario_id']) {
    // Solo eliminar admins normales, no superadmins (por seguridad)
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id AND rol = 'admin'");
    $stmt->execute([":id" => $id]);
}
header("Location: ../admin/gestionar_admins.php");
exit();
?>