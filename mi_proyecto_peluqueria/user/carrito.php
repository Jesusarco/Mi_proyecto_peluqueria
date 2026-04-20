<?php
session_start();
require_once "../config/database.php";
include "../includes/header.php";

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
$items = [];
?>

<h2>Tu Carrito de Compra</h2>

<div class="salon-card" style="margin-top: 20px;">
    <?php if (empty($carrito)): ?>
        <p>Tu carrito está vacío.</p>
        <a href="tienda.php" class="salon-btn salon-btn-primary">Ver productos</a>
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
                <div class='cart-item' style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                    <span>
                        <strong><?= htmlspecialchars($p['nombre']) ?></strong> 
                        (x<?= (int)$cantidad ?>)
                    </span>
                    <span><?= number_format($subtotal, 2) ?> €</span>
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

<?php include "../includes/footer.php"; ?>