<?php
    // Iniciar sesión para comprobar permisos y evitar auto-cambio
    session_start();
    require_once "../config/database.php";

    // Solo el superadministrador puede cambiar roles de otros administradores
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
        header("Location: ../admin/dashboard.php?error=No autorizado");
        exit();
    }

    // Obtener el ID del usuario a modificar y la acción (ascender/descender)
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $accion = isset($_GET['accion']) ? $_GET['accion'] : '';

    // No permitir que el superadmin se cambie a sí mismo
    if ($id > 0 && $id != $_SESSION['usuario_id']) {
        if ($accion == 'ascender') {
            // Convertir un administrador normal en superadmin
            $stmt = $conn->prepare("UPDATE usuarios SET rol = 'superadmin' WHERE id = :id AND rol = 'admin'");
            $stmt->execute([":id" => $id]);
        } elseif ($accion == 'descender') {
            // Revocar el rango de superadmin (pasa a administrador normal)
            $stmt = $conn->prepare("UPDATE usuarios SET rol = 'admin' WHERE id = :id AND rol = 'superadmin'");
            $stmt->execute([":id" => $id]);
        }
    }

    // Redirigir de vuelta a la gestión de administradores
    header("Location: ../admin/gestionar_admins.php");
    exit();
?>