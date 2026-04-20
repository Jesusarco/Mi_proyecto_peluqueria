<?php
session_start();
require_once "../config/database.php";

$gastos = $conn->query("SELECT * FROM gastos")->fetchAll(PDO::FETCH_ASSOC);

foreach ($gastos as $g) {
    echo "{$g['descripcion']} - {$g['cantidad']}€<br>";
}
?>