<?php
    // Iniciar sesión para acceder al carrito almacenado en $_SESSION
    session_start();

    // Verificar si se han recibido los parámetros necesarios (id del producto y acción)
    if (isset($_GET['id']) && isset($_GET['accion'])) {
        $id = (int)$_GET['id'];          // ID del producto a modificar
        $accion = $_GET['accion'];        // Acción: 'decrement' (quitar una unidad) o 'remove' (eliminar completamente)

        // Comprobar que el producto existe en el carrito
        if (isset($_SESSION['carrito'][$id])) {
            if ($accion == 'decrement') {
                // Reducir la cantidad en 1
                $_SESSION['carrito'][$id]--;
                // Si la cantidad llega a 0 o menos, eliminar el producto del carrito
                if ($_SESSION['carrito'][$id] <= 0) {
                    unset($_SESSION['carrito'][$id]);
                }
            } elseif ($accion == 'remove') {
                // Eliminar el producto por completo
                unset($_SESSION['carrito'][$id]);
            }
        }
    }

    // Redirigir de vuelta a la página del carrito
    header("Location: ../user/carrito.php");
    exit();
?>