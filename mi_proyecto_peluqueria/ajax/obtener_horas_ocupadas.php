<?php
/**
 * Devuelve las horas ocupadas en una fecha determinada.
 * GET: fecha (YYYY-MM-DD)
 * Respuesta: JSON array de strings hora (HH:MM)
 */
header('Content-Type: application/json');
require_once "../config/database.php";

$fecha = $_GET['fecha'] ?? '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    echo json_encode(['error' => 'Formato de fecha no válido']);
    exit;
}

$stmt = $conn->prepare("SELECT DISTINCT hora FROM citas WHERE fecha = :fecha AND estado = 'reservado'");
$stmt->execute([':fecha' => $fecha]);
$ocupadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($ocupadas);