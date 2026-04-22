<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    try {
        $conn->beginTransaction();
        // Eliminar primero los detalles del pedido
        $stmt = $conn->prepare("DELETE FROM detalle_pedido WHERE pedido_id = :id");
        $stmt->execute([":id" => $id]);
        // Luego eliminar el pedido
        $stmt = $conn->prepare("DELETE FROM pedidos WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $conn->commit();
    } catch (PDOException $e) {
        $conn->rollBack();
    }
}
header("Location: ../admin/dashboard.php");
exit();
?>