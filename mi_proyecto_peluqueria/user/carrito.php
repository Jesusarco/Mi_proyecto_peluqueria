<?php
    /**
     * Página de carrito de compras.
     * Se abre en ventana emergente desde inicio.php.
     * Muestra los productos añadidos, cantidades, subtotales y total.
     * Estilo refinado con la paleta de la peluquería.
     */

    // Iniciar sesión para acceder al carrito almacenado en $_SESSION
    session_start();
    require_once "../config/database.php";

    // Obtener el carrito de la sesión (vacío si no existe)
    $carrito = $_SESSION['carrito'] ?? [];
    $total = 0; // Acumulador del total del pedido
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tu Carrito — Áurea Estudio</title>
        
        <!-- Tipografías elegantes (Cormorant Garamond para títulos, Jost para textos) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
        
        <!-- Bootstrap Icons para iconografía consistente -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
        <!-- Estilos específicos para la página del carrito -->
        <link rel="stylesheet" href="../assets/css/carrito.css">
    </head>
    <body>

        <div class="cart-wrapper">
            <!-- Cabecera del carrito -->
            <div class="page-header">
                <h2>Tu Carrito</h2>
                <div class="page-header-line"></div>
            </div>

            <div class="cart-card">
                <?php if (empty($carrito)): ?>
                    <!-- Mostrar mensaje de carrito vacío con icono de Bootstrap -->
                    <div class="empty-cart">
                        <div class="empty-cart-icon"><i class="bi bi-bag-x"></i></div>
                        <p>Tu carrito está vacío</p>
                    </div>
                <?php else: ?>
                    <!-- Recorrer cada producto del carrito -->
                    <?php foreach ($carrito as $id => $cantidad): ?>
                        <?php
                        // Obtener los datos del producto desde la base de datos
                        $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
                        $stmt->execute([":id" => $id]);
                        $p = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($p):
                            $subtotal = $p['precio'] * $cantidad;
                            $total += $subtotal; // Acumular para el total general
                        ?>
                        <div class="cart-item">
                            <div class="item-info">
                                <!-- Mostrar la imagen del producto si existe, si no un icono genérico -->
                                <?php if (!empty($p['imagen'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>" class="item-image">
                                <?php else: ?>
                                    <div class="item-image" style="display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 1.5rem;">🛍️</div>
                                <?php endif; ?>
                                <div class="item-details">
                                    <span class="item-name"><?= htmlspecialchars($p['nombre']) ?></span>
                                    <span class="item-quantity">Cantidad: <?= (int)$cantidad ?></span>
                                </div>
                            </div>
                            <span class="item-price"><?= number_format($subtotal, 2) ?> €</span>
                            <!-- Enlace para eliminar una unidad del producto (decrementar) -->
                            <a href="../ajax/eliminar_del_carrito.php?accion=decrement&id=<?= $id ?>" 
                            class="btn-remove" 
                            onclick="return confirm('¿Quitar una unidad de este producto?')">
                            Eliminar
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Resumen del total -->
                    <div class="cart-summary">
                        <span class="total-label">Total</span>
                        <span class="total-amount"><?= number_format($total, 2) ?> €</span>
                    </div>

                    <!-- Formulario para confirmar el pedido (envía a confirmar_pedido.php) -->
                    <form action="../ajax/confirmar_pedido.php" method="POST">
                        <!-- Mensaje de advertencia antes de confirmar -->
                        <div class="warning-text">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            ATENCIÓN, si pulsas el botón, no te eches atrás en la compra. Advertido estas.
                        </div>
                        <button type="submit" class="btn-confirm"> Confirmar compra </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    </body>
</html>