<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$inicio = (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') ? "../admin/dashboard.php" : "../user/inicio.php";
$textoInicio = (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin') ? "Dashboard" : "Inicio";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peluquería</title>
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<header class="salon-header">
    <div class="salon-nav-container">
        <?php if (isset($esDashboard) && $esDashboard): ?>
            <div class="nav-group"><span style="color:#efefef">👤 <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></span></div>
            <a href="<?= $inicio ?>" class="salon-brand">PELUQUERÍA</a>
            <div class="nav-group"><a href="../auth/logout.php" class="salon-btn salon-btn-danger">Salir</a></div>
        <?php else: ?>
            <a class="salon-brand" href="<?= $inicio ?>">PELUQUERÍA</a>
            <div class="nav-group">
                <nav class="d-none d-md-flex gap-2">
                    <a href="<?= $inicio ?>" class="salon-btn salon-btn-light"><?= $textoInicio ?></a>
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'cliente'): ?>
                        <a href="../user/reservas.php" class="salon-btn salon-btn-light">Reservas</a>
                        <a href="../user/tienda.php" class="salon-btn salon-btn-light">Tienda</a>
                        <a href="../user/carrito.php" class="salon-btn salon-btn-accent">🛒 Carrito</a>
                    <?php endif; ?>
                </nav>
                <a href="../auth/logout.php" class="salon-btn salon-btn-danger">Salir</a>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="main-container <?= (isset($esDashboard) && $esDashboard) ? 'full-width' : '' ?>">