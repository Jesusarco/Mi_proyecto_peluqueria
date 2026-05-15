<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) { exit("No autorizado"); }

$productos = $conn->query("SELECT * FROM productos")->fetchAll(PDO::FETCH_ASSOC);

foreach ($productos as $p) {
    echo "{$p['nombre']} - {$p['precio']}€
    <a href='../ajax/eliminar_producto.php?id={$p['id']}'>Eliminar</a><br>";
}
?>