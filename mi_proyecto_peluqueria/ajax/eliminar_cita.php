<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM citas WHERE id = :id");
    $stmt->execute([":id" => $id]);
}
header("Location: ../admin/dashboard.php");
exit();
?>