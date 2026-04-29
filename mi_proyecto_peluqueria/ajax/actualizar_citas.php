<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $estado = $_POST['estado'];
    
    try {
        $stmt = $conn->prepare("UPDATE citas SET fecha = ?, hora = ?, estado = ? WHERE id = ?");
        $stmt->execute([$fecha, $hora, $estado, $id]);
        
        header('Location: ../admin/dashboard.php?success=cita_actualizada');
    } catch(PDOException $e) {
        header('Location: ../admin/dashboard.php?error=' . $e->getMessage());
    }
} else {
    header('Location: ../admin/dashboard.php');
}
?>