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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #C9A84C;
            --gold-light: #e8c97a;
            --dark: #111110;
            --dark-2: #1a1a18;
            --dark-3: #242420;
            --cream: #f5f0e8;
            --text-muted: #888880;
        }

        /* ── HEADER PRINCIPAL ── */
        .salon-header {
            background: var(--dark);
            border-bottom: 1px solid rgba(201,168,76,0.2);
            padding: 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .salon-nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 30px;
            height: 68px;
        }

        .salon-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 300;
            letter-spacing: 0.3em;
            color: var(--gold);
            text-decoration: none;
            text-transform: uppercase;
        }

        .salon-brand span {
            color: #fff;
            font-style: italic;
            font-weight: 300;
        }

        .nav-group {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-user-name {
            font-family: 'Jost', sans-serif;
            font-size: 0.82rem;
            font-weight: 300;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        /* Botón salir elegante */
        .btn-salir {
            font-family: 'Jost', sans-serif;
            font-size: 0.75rem;
            font-weight: 400;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--gold);
            background: transparent;
            border: 1px solid rgba(201,168,76,0.4);
            padding: 7px 18px;
            border-radius: 2px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-salir:hover {
            background: var(--gold);
            color: var(--dark);
            border-color: var(--gold);
        }

        /* ── MENÚ INTERNO (solo páginas de usuario, no dashboard) ── */
        .menu-interno-header {
            background: var(--dark-2);
            border-bottom: 1px solid rgba(201,168,76,0.12);
            padding: 0;
            text-align: center;
            overflow: hidden;
        }

        .menu-interno-header nav {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .menu-interno-header a {
            display: inline-flex;
            align-items: center;
            padding: 14px 18px;
            font-family: 'Jost', sans-serif;
            font-size: 0.72rem;
            font-weight: 400;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--text-muted);
            text-decoration: none;
            position: relative;
            transition: color 0.25s ease;
        }

        .menu-interno-header a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 1px;
            background: var(--gold);
            transition: width 0.3s ease;
        }

        .menu-interno-header a:hover {
            color: var(--gold-light);
        }

        .menu-interno-header a:hover::after {
            width: 60%;
        }

        /* Separador dorado entre items */
        .menu-interno-header a + a::before {
            content: '·';
            position: absolute;
            left: -2px;
            color: rgba(201,168,76,0.25);
            font-size: 0.7rem;
        }

        /* Icono carrito con badge */
        .menu-interno-header a#menuCarritoPopup {
            gap: 6px;
        }

        .menu-interno-header a#menuCarritoPopup::before {
            content: '◻';
            font-size: 0.9rem;
            color: var(--gold);
        }

        @media (max-width: 700px) {
            .salon-nav-container { padding: 0 16px; }
            .menu-interno-header a {
                padding: 12px 10px;
                font-size: 0.65rem;
                letter-spacing: 0.1em;
            }
            .salon-brand { font-size: 1.2rem; letter-spacing: 0.2em; }
        }

        /* ── MAIN CONTAINER ── */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .main-container.full-width {
            max-width: 100%;
        }
    </style>
</head>
<body>

<header class="salon-header">
    <div class="salon-nav-container">
        <a href="<?= $inicio ?>" class="salon-brand">Pelu<span>quer&iacute;a</span></a>
        <div class="nav-group">
            <?php if ($esDashboard): ?>
                <span class="nav-user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></span>
                <a href="../auth/logout.php" class="btn-salir">Salir</a>
            <?php else: ?>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <span class="nav-user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                    <a href="../auth/logout.php" class="btn-salir">Salir</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if (!$esDashboard): ?>
<div class="menu-interno-header">
    <nav>
        <a href="#inicio">Inicio</a>
        <a href="#sobre-nosotros">Sobre nosotros</a>
        <a href="#atencion-cliente">Atención al cliente</a>
        <a href="#" id="menuMostrarProductos">Nuestros productos</a>
        <a href="#" id="menuMostrarServicios">Nuestros servicios</a>
        <a href="#" id="menuMostrarCita">Pedir cita</a>
        <a href="#" id="menuCarritoPopup">Carrito</a>
    </nav>
</div>
<?php endif; ?>

<main class="main-container <?= $esDashboard ? 'full-width' : '' ?>">