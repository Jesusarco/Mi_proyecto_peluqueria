<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $producto_id = (int)$_POST['producto_id'];
    
    if ($producto_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
        exit();
    }
    
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }
    
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        $_SESSION['carrito'][$producto_id] = 1;
    }
    
    $total_items = array_sum($_SESSION['carrito']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Producto añadido al carrito',
        'cartCount' => $total_items
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}