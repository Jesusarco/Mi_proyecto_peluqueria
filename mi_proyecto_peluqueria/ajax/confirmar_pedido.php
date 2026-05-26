<?php
    // Iniciar sesión para acceder al ID del usuario y al carrito
    session_start();
    require_once "../config/database.php";

    // Verificar que el usuario esté autenticado
    if (!isset($_SESSION['usuario_id'])) {
        die("No autorizado");
    }

    $usuario_id = (int)$_SESSION['usuario_id'];
    $carrito = $_SESSION['carrito'] ?? [];

    // Si el carrito está vacío, no se puede procesar el pedido
    if (empty($carrito)) {
        die("Carrito vacío");
    }

    try {
        // Iniciar una transacción para asegurar consistencia
        $conn->beginTransaction();
        
        $total = 0;
        
        // Recorrer el carrito para validar productos y calcular el total
        foreach ($carrito as $id => $cantidad) {
            // Obtener datos del producto
            $stmt = $conn->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = :id");
            $stmt->execute([":id" => $id]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$producto) {
                throw new Exception("Producto no encontrado: ID $id");
            }
            
            // Verificar stock suficiente
            if ($producto['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente para {$producto['nombre']}");
            }
            
            // Acumular el total del pedido
            $total += $producto['precio'] * $cantidad;
        }
        
        // Insertar el pedido principal
        $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, total) VALUES (:u, :t)");
        $stmt->execute([":u" => $usuario_id, ":t" => $total]);
        $pedido_id = $conn->lastInsertId();
        
        // Insertar los detalles del pedido y actualizar el stock de cada producto
        foreach ($carrito as $id => $cantidad) {
            // Detalle del pedido (precio se obtiene de la tabla productos en el momento)
            $stmt = $conn->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) 
                                    VALUES (:p, :id, :c, (SELECT precio FROM productos WHERE id = :id))");
            $stmt->execute([":p" => $pedido_id, ":id" => $id, ":c" => $cantidad]);
            
            // Reducir el stock
            $stmt = $conn->prepare("UPDATE productos SET stock = stock - :c WHERE id = :id");
            $stmt->execute([":c" => $cantidad, ":id" => $id]);
        }
        
        // Vaciar el carrito de la sesión
        unset($_SESSION['carrito']);
        
        // Confirmar la transacción
        $conn->commit();
        
        echo "Pedido realizado con éxito";
        
    } catch (Exception $e) {
        // En caso de error, deshacer la transacción
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error al procesar el pedido";
    }
?>