<?php
    // Iniciar sesión para verificar el rol del usuario
    session_start();
    require_once "../config/database.php";

    // Solo permitir a administradores o superadministradores
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
        header("Location: ../admin/dashboard.php?error=No autorizado");
        exit();
    }

    // Obtener la acción (qué eliminar) y el ID
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Validar que el ID sea válido
    if ($id <= 0) {
        header("Location: ../admin/dashboard.php?error=ID inválido");
        exit();
    }

    // Por defecto redirigir al dashboard
    $redirect = "../admin/dashboard.php";

    // Elegir la acción según el parámetro 'action'
    switch ($action) {
        case 'producto':
            // Eliminar un producto
            $stmt = $conn->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->execute([":id" => $id]);
            break;

        case 'servicio':
            // Verificar si el servicio tiene citas asociadas antes de eliminar
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
            // Eliminar un gasto
            $stmt = $conn->prepare("DELETE FROM gastos WHERE id = :id");
            $stmt->execute([":id" => $id]);
            break;

        case 'cita':
            // Eliminar una cita
            $stmt = $conn->prepare("DELETE FROM citas WHERE id = :id");
            $stmt->execute([":id" => $id]);
            break;

        case 'usuario':
            // No permitir que el usuario se elimine a sí mismo
            if ($id == $_SESSION['usuario_id']) {
                header("Location: ../admin/dashboard.php?error=No puedes eliminarte a ti mismo");
                exit();
            }
            // Eliminar cliente o administrador normal (no superadmin)
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id AND rol IN ('admin', 'cliente')");
            $stmt->execute([":id" => $id]);
            $redirect = "../admin/dashboard.php";
            break;

        case 'admin':
            // Similar a 'usuario', pero redirige a gestión de administradores
            if ($id == $_SESSION['usuario_id']) {
                header("Location: ../admin/dashboard.php?error=No puedes eliminarte a ti mismo");
                exit();
            }
            $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = :id AND rol IN ('admin', 'cliente')");
            $stmt->execute([":id" => $id]);
            $redirect = "../admin/gestionar_admins.php";
            break;

        case 'pedido':
            // Eliminar pedido físicamente
            $stmt = $conn->prepare("DELETE FROM pedidos WHERE id = :id");
            $stmt->execute([":id" => $id]);
            $redirect = "../admin/gestionar_pedidos.php";
            break;

        default:
            // Acción no reconocida
            header("Location: ../admin/dashboard.php?error=Acción no válida");
            exit();
    }

    // Redirigir a la página correspondiente después de eliminar
    header("Location: $redirect");
    exit();
?>