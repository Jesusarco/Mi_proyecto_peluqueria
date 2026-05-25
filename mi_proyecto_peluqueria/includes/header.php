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
    <title>Áurea Estudio | Peluquería</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <!-- Swiper CSS (solo para frontend, pero lo cargamos siempre sin problema) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-cream: #F9F6F0;
            --text-dark: #2C2825;
            --text-muted: #6B625C;
            --gold: #C6A43F;
            --gold-dark: #A8872E;
            --white: #FFFFFF;
            --border-light: #E6DFD6;
            --footer-bg: #1E1B18;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: var(--bg-cream);
            color: var(--text-dark);
            font-family: 'Jost', sans-serif;
            scroll-behavior: smooth;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===== HEADER FRONTEND (sticky) ===== */
        <?php if (!$esDashboard): ?>
        .main-header {
            position: sticky;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            z-index: 1000;
            border-bottom: 1px solid var(--border-light);
        }
        .main-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }
        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.7rem;
            font-weight: 400;
            letter-spacing: 2px;
            color: var(--text-dark);
            text-decoration: none;
        }
        .logo span { color: var(--gold); }
        .main-nav { display: flex; gap: 32px; }
        .main-nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.2s;
        }
        .main-nav a:hover, .main-nav a.active { color: var(--gold); }
        .header-actions { display: flex; gap: 20px; align-items: center; }
        .cart-icon {
            font-size: 1.3rem;
            text-decoration: none;
            color: var(--text-dark);
            position: relative;
            cursor: pointer;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -12px;
            background: var(--gold);
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 20px;
        }
        .btn-outline {
            padding: 6px 16px;
            border: 1px solid var(--gold);
            border-radius: 30px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gold);
            transition: all 0.2s;
        }
        .btn-outline:hover { background: var(--gold); color: white; }
        <?php else: ?>
        /* ===== HEADER DASHBOARD (sin sticky, oscuro) ===== */
        .salon-header {
            background: #1a1a1a;
            border-bottom: 2px solid var(--gold);
        }
        .salon-nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 68px;
        }
        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.7rem;
            font-weight: 400;
            letter-spacing: 2px;
            color: var(--bg-cream);
            text-decoration: none;
        }
        .logo span { color: var(--gold); }
        
        .nav-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .nav-user-name {
            font-size: 0.82rem;
            font-weight: 300;
            color: var(--bg-cream);
            text-transform: uppercase;
        }
        .btn-salir {
            font-size: 0.75rem;
            font-weight: 400;
            letter-spacing: 0.15em;
            color: var(--gold);
            background: transparent;
            border: 1px solid rgba(201,168,76,0.4);
            padding: 7px 18px;
            border-radius: 2px;
            text-decoration: none;
            transition: 0.25s;
        }
        .btn-salir:hover {
            background: var(--gold);
            color: #1a1a1a;
        }
        <?php endif; ?>

        /* ===== FOOTER COMÚN MEJORADO ===== */
        .salon-footer {
            background: var(--footer-bg);
            color: #aaa;
            padding: 60px 0 30px;
            margin-top: 80px;
            border-top: 1px solid rgba(198,164,63,0.2);
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        .footer-col h4 {
            font-family: 'Cormorant Garamond', serif;
            color: var(--gold);
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        .footer-col p, .footer-col a {
            color: #999;
            text-decoration: none;
            font-size: 0.85rem;
            line-height: 1.8;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.05);
            font-size: 0.75rem;
        }
        @media (max-width: 768px) {
            .main-nav { display: none; } /* Simplificado, luego puedes añadir menú hamburguesa */
        }
    </style>
</head>
<body>

<?php if (!$esDashboard): ?>
<!-- HEADER FRONTEND STICKY (con IDs compatibles con tu JS) -->
<header class="main-header">
    <div class="container">
        <a href="../user/inicio.php" class="logo"><span>Áurea</span>Studio</a>
        <nav class="main-nav">
            <a href="../user/inicio.php" id="menuInicio">Inicio</a>
            <a href="#sobre-nosotros" id="menuSobreNosotros">El estudio</a>
            <a href="#" id="menuMostrarServicios">Servicios</a>
            <a href="#" id="menuMostrarProductos">Productos</a>
            <a href="#" id="menuMostrarCita">Cita</a>
            <a href="#atencion-cliente" id="menuContacto">Contacto</a>
        </nav>
        <div class="header-actions">
        <a href="#" id="menuCarritoPopup" class="cart-icon"><i class="bi bi-bag"></i></a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="../auth/logout.php" class="btn-outline">Salir</a>
            <?php else: ?>
                <a href="../auth/login.php" class="btn-outline">Acceder</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php else: ?>
<!-- HEADER DASHBOARD (sin cambios, igual que antes) -->
<header class="salon-header">
    <div class="salon-nav-container">
        <a href="<?= $inicio ?>" class="logo"><span>Áurea</span>Studio</a>
        <div class="nav-group">
            <span class="nav-user-name"><?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></span>
            <a href="../auth/logout.php" class="btn-salir">Salir</a>
        </div>
    </div>
</header>
<?php endif; ?>

<main class="main-container <?= $esDashboard ? 'full-width' : '' ?>">