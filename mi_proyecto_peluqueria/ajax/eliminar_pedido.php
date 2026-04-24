<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // Borrado lógico: marcar como inactivo
    $stmt = $conn->prepare("DELETE FROM pedidos WHERE id = :id");
    $stmt->execute([":id" => $id]);
}
header("Location: ../admin/gestionar_pedidos.php");
exit();
?>