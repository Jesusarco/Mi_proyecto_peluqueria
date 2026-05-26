<?php
    // Iniciar sesión para poder almacenar el carrito en $_SESSION
    session_start();
    // Indicar que la respuesta será en formato JSON
    header('Content-Type: application/json');

    // Solo procesar peticiones POST que incluyan 'producto_id'
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
        $producto_id = (int)$_POST['producto_id'];
        
        // Validar que el ID sea un número positivo
        if ($producto_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
            exit();
        }
        
        // Inicializar el carrito en la sesión si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Si el producto ya está en el carrito, incrementar su cantidad
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]++;
        } else {
            // Si no está, añadirlo con cantidad 1
            $_SESSION['carrito'][$producto_id] = 1;
        }
        
        // Calcular el número total de productos (suma de cantidades)
        $total_items = array_sum($_SESSION['carrito']);
        
        // Responder con éxito y devolver el nuevo contador
        echo json_encode([
            'success' => true,
            'message' => 'Producto añadido al carrito',
            'cartCount' => $total_items
        ]);
    } else {
        // Si la petición no es POST o falta 'producto_id', devolver error
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
?>