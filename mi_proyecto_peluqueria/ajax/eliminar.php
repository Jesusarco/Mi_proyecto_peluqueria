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

$redirect = "../admin/dashboard.php";

switch ($action) {
    case 'producto':
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id");
        $stmt->execute([":id" => $id]);
        break;
    case 'servicio':
        // Verificar citas asociadas
        $stmt = $conn->prepare("SELECT COUNT(*) FROM citas WHERE servicio_id = :id");
        $stmt->execute([":id" => $id]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: ../admin/dashboard.php?error=No se puede eliminar el servicio porque tiene citas asociadas");
            exit();
        }
        $stmt = $conn->prepare("DELETE FROM servicios WHERE id = :id");
        $stmt->execute([":id" => $id]);
        break;
    case 'gasto':
        $stmt = $conn->prepare("DELETE FROM gastos WHERE id = :id");
        $stmt->execute([":id" => $id]);
        break;
    case 'cita':
        $stmt = $conn->prepare("DELETE FROM citas WHERE id = :id");
        $stmt->execute([":id" => $id]);
        break;
    case 'usuario':
        if ($id == $_SESSION['usuario_id']) {
            header("Location: ../admin/gestionar_admins.php?error=No puedes eliminarte a ti mismo");
            exit();
        }
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id AND rol IN ('admin', 'cliente')");
        $stmt->execute([":id" => $id]);
        $redirect = "../admin/gestionar_admins.php";
        break;
    case 'pedido':
        // Borrado físico (DELETE)
        $stmt = $conn->prepare("DELETE FROM pedidos WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $redirect = "../admin/gestionar_pedidos.php";
        break;
    default:
        header("Location: ../admin/dashboard.php?error=Acción no válida");
        exit();
}
header("Location: $redirect");
exit();
?>