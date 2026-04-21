<?php
session_start();
require_once "../config/database.php";

$sql = "SELECT p.*, u.nombre
        FROM pedidos p
        JOIN usuarios u ON p.usuario_id = u.id";

$ventas = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

foreach ($ventas as $v) {
    echo "Pedido #{$v['id']} - {$v['nombre']} - {$v['total']}€<br>";
}
?>