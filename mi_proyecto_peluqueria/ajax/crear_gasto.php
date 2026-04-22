<?php
session_start();
require_once "../config/database.php";

// Solo administradores pueden crear gastos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = trim(htmlspecialchars($_POST['descripcion']));
    $categoria = trim(htmlspecialchars($_POST['categoria'] ?? ''));
    $cantidad = (float)$_POST['cantidad'];
    $usuario_id = $_SESSION['usuario_id']; // ID del admin logueado

    $error = null;
    if (empty($descripcion)) {
        $error = "La descripción es obligatoria";
    } elseif ($cantidad <= 0) {
        $error = "La cantidad debe ser mayor que cero";
    }

    if ($error) {
        header("Location: ../admin/dashboard.php?error=" . urlencode($error));
        exit();
    }

    // Insertar gasto
    $stmt = $conn->prepare("INSERT INTO gastos (descripcion, categoria, cantidad, usuario_id) VALUES (:d, :c, :m, :u)");
    $stmt->execute([
        ":d" => $descripcion,
        ":c" => $categoria,
        ":m" => $cantidad,
        ":u" => $usuario_id
    ]);

    header("Location: ../admin/dashboard.php?success=Gasto registrado");
    exit();
}
?>