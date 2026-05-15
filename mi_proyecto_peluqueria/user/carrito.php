<?php
session_start();
require_once "../config/database.php";

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
$items = [];
?>

<div class="page-header">
    <h2>Tu Carrito</h2>
    <div class="page-header-line"></div>
</div>

<div class="salon-card" style="max-width: 720px;">
    <?php if (empty($carrito)): ?>
        <div style="text-align:center; padding: 40px 0;">
            <div style="font-size:2.5rem; margin-bottom:16px; opacity:0.3;">◻</div>
            <p style="color:var(--text-muted); font-family:'Playfair Display',serif; font-style:italic; font-size:1.05rem; margin:0;">
                Tu carrito está vacío.
            </p>
        </div>
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
                <div class="cart-item">
                    <div>
                        <div style="font-family:'Playfair Display',serif; font-size:1.05rem; color:var(--forest); margin-bottom:4px;">
                            <?= htmlspecialchars($p['nombre']) ?>
                        </div>
                        <div style="
                            display:inline-flex;
                            align-items:center;
                            gap:6px;
                            background:rgba(58,82,64,0.07);
                            border-radius:20px;
                            padding:3px 10px;
                            font-size:0.72rem;
                            letter-spacing:0.08em;
                            color:var(--moss);
                        ">
                            Cantidad: <?= (int)$cantidad ?>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:20px;">
                        <span style="
                            font-family:'Playfair Display',serif;
                            font-size:1.15rem;
                            color:var(--forest);
                            font-weight:400;
                        ">
                            <?= number_format($subtotal, 2) ?> €
                        </span>
                        <a href="../ajax/eliminar_del_carrito.php?accion=decrement&id=<?= $id ?>"
                           class="btn-eliminar"
                           onclick="return confirm('¿Quitar una unidad de este producto?')">
                            Eliminar
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Separator -->
        <div style="border-top: 2px solid var(--cream-2); margin-top:8px; padding-top:24px;">
            <div class="cart-total">
                Total: <?= number_format($total, 2) ?> €
            </div>
        </div>

        <form action="../ajax/confirmar_pedido.php" method="POST" style="text-align:right; margin-top:24px;">
            <button type="submit" class="salon-btn salon-btn-accent">
                Confirmar compra →
            </button>
        </form>
    <?php endif; ?>
</div>