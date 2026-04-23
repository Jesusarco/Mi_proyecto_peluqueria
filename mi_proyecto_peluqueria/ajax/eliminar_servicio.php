<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // Verificar si el servicio tiene citas asociadas antes de eliminar
    $stmt = $conn->prepare("SELECT COUNT(*) FROM citas WHERE servicio_id = :id");
    $stmt->execute([":id" => $id]);
    $citas_asociadas = $stmt->fetchColumn();
    
    if ($citas_asociadas > 0) {
        // En lugar de eliminar, podrías desactivarlo (si tuvieras campo activo)
        // Por ahora, redirigimos con error
        header("Location: ../admin/dashboard.php?error=No se puede eliminar el servicio porque tiene citas asociadas");
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM servicios WHERE id = :id");
    $stmt->execute([":id" => $id]);
}
header("Location: ../admin/dashboard.php");
exit();
?>