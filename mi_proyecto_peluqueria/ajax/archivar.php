<?php
    // Iniciar sesión para verificar permisos
    session_start();
    require_once "../config/database.php";

    // Solo administradores o superadministradores pueden archivar (cambiar estado)
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
        header("Location: ../admin/dashboard.php?error=No autorizado");
        exit();
    }

    // Obtener acción (cita o pedido) y el ID del elemento
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Validar que el ID sea válido
    if ($id <= 0) {
        header("Location: ../admin/dashboard.php?error=ID inválido");
        exit();
    }

    // Realizar la acción según el tipo
    switch ($action) {
        case 'cita':
            // Cambiar estado de la cita a 'completado'
            $stmt = $conn->prepare("UPDATE citas SET estado = 'completado' WHERE id = :id");
            $stmt->execute([":id" => $id]);
            break;
        case 'pedido':
            // Cambiar estado del pedido a 'entregado'
            $stmt = $conn->prepare("UPDATE pedidos SET estado = 'entregado' WHERE id = :id");
            $stmt->execute([":id" => $id]);
            break;
        default:
            // Acción no reconocida
            header("Location: ../admin/dashboard.php?error=Acción no válida");
            exit();
    }

    // Redirigir de vuelta al dashboard
    header("Location: ../admin/dashboard.php");
    exit();
?>