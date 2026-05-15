<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../admin/dashboard.php?error=ID inválido");
    exit();
}

switch ($action) {
    case 'cita':
        $stmt = $conn->prepare("UPDATE citas SET estado = 'completado' WHERE id = :id");
        $stmt->execute([":id" => $id]);
        break;
    case 'pedido':
        $stmt = $conn->prepare("UPDATE pedidos SET estado = 'entregado' WHERE id = :id");
        $stmt->execute([":id" => $id]);
        break;
    default:
        header("Location: ../admin/dashboard.php?error=Acción no válida");
        exit();
}
header("Location: ../admin/dashboard.php");
exit();
?>