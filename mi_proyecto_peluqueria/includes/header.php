<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
$esAdmin = (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'superadmin']));
$inicio = $esAdmin ? "../admin/dashboard.php" : "../user/inicio.php";
$esDashboard = isset($esDashboard) && $esDashboard;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peluquería</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        /* Estilos para el menú interno (centrado, con fondo y bordes visibles) */
        .menu-interno-header {
            background: #f8f8f8;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            text-align: center;
        }
        .menu-interno-header a {
            display: inline-block;
            margin: 0 15px;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 30px;
            transition: all 0.2s;
            /* Aseguramos que se vea siempre */
            background: transparent;
        }
        .menu-interno-header a:hover {
            background: var(--accent-color);
            color: white;
        }
        @media (max-width: 700px) {
            .menu-interno-header a {
                margin: 0 8px;
                font-size: 0.85rem;
            }
        }

        /* Ajustes para el header principal */
        .salon-nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .salon-brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--accent-color);
            text-decoration: none;
        }
        .nav-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
    </style>
</head>
<body>

<header class="salon-header">
    <div class="salon-nav-container">
        <a href="<?= $inicio ?>" class="salon-brand">PELUQUERÍA</a>
        <div class="nav-group">
            <?php if ($esDashboard): ?>
                <span style="color:#efefef;"> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></span>
                <a href="../auth/logout.php" class="salon-btn salon-btn-danger">Salir</a>
            <?php else: ?>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="../auth/logout.php" class="salon-btn salon-btn-danger">Salir</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!$esDashboard): ?>
<!-- Barra de menú centrada con estilo original -->
<div class="menu-interno-header">
    <a href="#inicio">Inicio</a>
    <a href="#sobre-nosotros">Sobre nosotros</a>
    <a href="#atencion-cliente">Atención al cliente</a>
    <a href="#" id="menuMostrarProductos">Nuestros productos</a>
    <a href="#" id="menuMostrarServicios">Nuestros servicios</a>
    <a href="#" id="menuMostrarCita">Pedir cita</a>
    <a href="#" id="menuCarritoPopup">Carrito</a>
</div>
<?php endif; ?>

<main class="main-container <?= $esDashboard ? 'full-width' : '' ?>">