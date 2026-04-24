<?php
session_start();
require_once "../config/database.php";

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
$items = [];
?>

<h2>Tu Carrito de Compra</h2>

<div class="salon-card" style="margin-top: 20px;">
    <?php if (empty($carrito)): ?>
        <p>Tu carrito está vacío.</p>
    <?php else: ?>
        <?php foreach ($carrito as $id => $cantidad): ?>
            <?php
            $stmt = $conn->prepare("SELECT * FROM productos WHERE id = :id");
            $stmt->execute([":id" => $id]);
            $p = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($p):
                $subtotal = $p['precio'] * $cantidad;
                $total += $subtotal;
            ?>
                <div class='cart-item' style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                    <div>
                        <strong><?= htmlspecialchars($p['nombre']) ?></strong> 
                        (x<?= (int)$cantidad ?>)
                    </div>
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <span><?= number_format($subtotal, 2) ?> €</span>
                        <a href="../ajax/eliminar_del_carrito.php?accion=decrement&id=<?= $id ?>" class="btn-eliminar" style="background:#e74c3c; color:white; padding:4px 8px; border-radius:4px; text-decoration:none; font-size:0.8rem;" onclick="return confirm('¿Quitar una unidad de este producto?')">Eliminar</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <div class="cart-total" style="margin-top: 20px; text-align: right; font-size: 1.2rem;">
            <strong>Total: <?= number_format($total, 2) ?> €</strong>
        </div>

        <form action="../ajax/confirmar_pedido.php" method="POST" style="text-align: right; margin-top: 20px;">
            <button type="submit" class="salon-btn salon-btn-accent">Confirmar Compra</button>
        </form>
    <?php endif; ?>
</div>