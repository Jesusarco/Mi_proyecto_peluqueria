<?php
session_start();
require_once "../config/database.php";

if ($_SESSION['rol'] != 'admin') {
    exit("No autorizado");
}

$productos = $conn->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos as $p) {
    echo "{$p['nombre']} - {$p['precio']}€
    <a href='../ajax/eliminar_producto.php?id={$p['id']}'>Eliminar</a><br>";
}
?>