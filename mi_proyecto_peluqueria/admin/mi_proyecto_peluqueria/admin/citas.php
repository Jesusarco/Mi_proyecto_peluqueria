<?php
session_start();
require_once "../config/database.php";

$sql = "SELECT c.*, u.nombre, s.nombre AS servicio
        FROM citas c
        JOIN usuarios u ON c.usuario_id = u.id
        JOIN servicios s ON c.servicio_id = s.id";

$citas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

foreach ($citas as $c) {
    echo "{$c['nombre']} - {$c['servicio']} ({$c['fecha']} {$c['hora']})<br>";
}
?>