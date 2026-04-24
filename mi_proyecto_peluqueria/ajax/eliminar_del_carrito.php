<?php
session_start();

if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = (int)$_GET['id'];
    $accion = $_GET['accion'];
    
    if (isset($_SESSION['carrito'][$id])) {
        if ($accion == 'decrement') {
            // Disminuir en 1
            $_SESSION['carrito'][$id]--;
            if ($_SESSION['carrito'][$id] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        } elseif ($accion == 'remove') {
            // Eliminar completamente
            unset($_SESSION['carrito'][$id]);
        }
    }
}
header("Location: ../user/carrito.php");
exit();
?>