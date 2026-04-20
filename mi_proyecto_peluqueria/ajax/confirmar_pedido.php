<?php
session_start();
require_once "../config/database.php";
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
    exit();
}

try {
    $conn->beginTransaction();
    
    $total = 0;
    $items = [];
    
    // Calcular total y validar productos
    foreach ($carrito as $id => $cantidad) {
        $stmt = $conn->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            throw new Exception("Producto no encontrado: ID $id");
        }
        
        if ($producto['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente para {$producto['nombre']}");
        }
        
        $subtotal = $producto['precio'] * $cantidad;
        $total += $subtotal;
        $items[] = $producto;
    }
    
    // Crear pedido
    $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (:u, :t)");
    $stmt->execute([":u" => $usuario_id, ":t" => $total]);
    $pedido_id = $conn->lastInsertId();
    
    // Insertar detalles y actualizar stock
    foreach ($carrito as $id => $cantidad) {
        $stmt = $conn->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) 
                                VALUES (:p, :id, :c, (SELECT precio FROM productos WHERE id = :id))");
        $stmt->execute([":p" => $pedido_id, ":id" => $id, ":c" => $cantidad]);
        
        // Actualizar stock
        $stmt = $conn->prepare("UPDATE productos SET stock = stock - :c WHERE id = :id");
        $stmt->execute([":c" => $cantidad, ":id" => $id]);
    }
    
    // Vaciar carrito
    unset($_SESSION['carrito']);
    
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Pedido realizado con éxito', 'pedido_id' => $pedido_id]);
    
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error al procesar el pedido']);
}