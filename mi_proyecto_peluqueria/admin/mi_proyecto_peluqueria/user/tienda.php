<?php
session_start();
require_once "../config/database.php";
include "../includes/header.php";

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Prepared statement
$stmt = $conn->prepare("SELECT id, nombre, precio, stock FROM productos WHERE stock > 0 ORDER BY nombre");
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Tienda de Productos</h2>

<?php if (empty($productos)): ?>
    <div class="salon-card">
        <p>No hay productos disponibles en este momento.</p>
    </div>
<?php else: ?>
    <div class="salon-grid">
        <?php foreach ($productos as $p): ?>
            <div class="salon-card" style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <h4><?= htmlspecialchars($p['nombre']) ?></h4>
                    <p style="font-size: 1.2rem; color: var(--accent-color); font-weight: bold;">
                        <?= number_format($p['precio'], 2) ?> €
                    </p>
                    <small>Stock: <?= (int)$p['stock'] ?> unidades</small>
                </div>

                <button onclick="addToCart(<?= $p['id'] ?>)" 
                        class="salon-btn salon-btn-primary" 
                        style="margin-top: 15px;">
                    Añadir al carrito
                </button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script src="../assets/js/carrito.js"></script>

<?php include "../includes/footer.php"; ?>