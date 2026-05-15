<?php
/**
 * Página de carrito de compras.
 * Se abre en ventana emergente desde inicio.php.
 * Muestra los productos añadidos, cantidades, subtotales y total.
 * Estilo refinado con la paleta de la peluquería.
 */
session_start();
require_once "../config/database.php";

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Carrito — Peluquería</title>
    <!-- Tipografías elegantes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #C9A84C;
            --gold-light: #e8c97a;
            --forest: #1c2b1e;
            --moss: #3a5240;
            --cream: #f5f0e8;
            --cream-2: #e8e0d0;
            --white: #faf8f3;
            --text-body: #3a3028;
            --text-muted: #7a7060;
            --error: #b85450;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--cream);
            font-family: 'Jost', sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 20px;
            color: var(--text-body);
        }

        .cart-wrapper {
            width: 100%;
            max-width: 720px;
        }

        /* Cabecera */
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.2rem;
            font-weight: 300;
            color: var(--forest);
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }

        .page-header-line {
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: 0 auto;
            opacity: 0.7;
        }

        /* Tarjeta principal */
        .cart-card {
            background: var(--white);
            border: 1px solid var(--cream-2);
            border-radius: 12px;
            padding: 40px 35px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        }

        /* Carrito vacío */
        .empty-cart {
            text-align: center;
            padding: 40px 0;
        }

        .empty-cart-icon {
            font-size: 3rem;
            color: var(--gold);
            opacity: 0.3;
            margin-bottom: 16px;
        }

        .empty-cart p {
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
            font-size: 1.2rem;
            color: var(--text-muted);
        }

        /* Lista de items */
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
            border-bottom: 1px solid var(--cream-2);
        }

        .cart-item:last-of-type {
            border-bottom: none;
        }

        .item-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
        }

        .item-image {
            width: 70px;
            height: 70px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--cream-2);
            background: #f0f0f0;
        }

        .item-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .item-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--forest);
        }

        .item-quantity {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(58,82,64,0.06);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            color: var(--moss);
            font-weight: 500;
            width: fit-content;
        }

        .item-price {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            color: var(--forest);
            font-weight: 400;
            margin-right: 20px;
            white-space: nowrap;
        }

        .btn-remove {
            background: none;
            border: 1px solid rgba(184,84,80,0.3);
            color: var(--error);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-remove:hover {
            background: rgba(184,84,80,0.05);
            border-color: var(--error);
        }

        /* Resumen y total */
        .cart-summary {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid var(--cream-2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            color: var(--forest);
        }

        .total-amount {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--forest);
            letter-spacing: 0.03em;
        }

        .btn-confirm {
            display: block;
            width: 100%;
            margin-top: 30px;
            padding: 14px 20px;
            background: var(--forest);
            color: var(--white);
            border: none;
            border-radius: 6px;
            font-family: 'Jost', sans-serif;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.25s ease;
        }

        .btn-confirm:hover {
            background: var(--moss);
        }
    </style>
</head>
<body>

<div class="cart-wrapper">
    <div class="page-header">
        <h2>Tu Carrito</h2>
        <div class="page-header-line"></div>
    </div>

    <div class="cart-card">
        <?php if (empty($carrito)): ?>
            <!-- Estado vacío -->
            <div class="empty-cart">
                <div class="empty-cart-icon">◻</div>
                <p>Tu carrito está vacío</p>
            </div>
        <?php else: ?>
            <!-- Listado de productos -->
            <?php foreach ($carrito as $id => $cantidad): ?>
                <?php
                $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
                $stmt->execute([":id" => $id]);
                $p = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($p):
                    $subtotal = $p['precio'] * $cantidad;
                    $total += $subtotal;
                ?>
                <div class="cart-item">
                    <div class="item-info">
                        <!-- Imagen del producto -->
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
                    <a href="../ajax/eliminar_del_carrito.php?accion=decrement&id=<?= $id ?>" 
                       class="btn-remove" 
                       onclick="return confirm('¿Quitar una unidad de este producto?')">
                       Eliminar
                    </a>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- Total y confirmación -->
            <div class="cart-summary">
                <span class="total-label">Total</span>
                <span class="total-amount"><?= number_format($total, 2) ?> €</span>
            </div>

            <form action="../ajax/confirmar_pedido.php" method="POST">
                <button type="submit" class="btn-confirm">Confirmar compra →</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
