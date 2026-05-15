<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — Peluquería</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: var(--cream);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 6px;
            height: 100vh;
            background: linear-gradient(to bottom, var(--moss), var(--gold), var(--moss));
            opacity: 0.5;
        }

        .register-wrapper {
            width: 100%;
            max-width: 500px;
            padding: 24px;
            animation: fadeUp 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .register-brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .register-brand a {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 400;
            color: var(--forest);
            text-decoration: none;
        }

        .register-brand a em {
            color: var(--moss);
            font-style: italic;
        }

        .register-brand-tagline {
            font-size: 0.62rem;
            letter-spacing: 0.45em;
            text-transform: uppercase;
            color: var(--parchment);
            margin-top: 8px;
        }

        .register-card {
            background: var(--white);
            border: 1px solid var(--cream-2);
            border-radius: 12px;
            padding: 48px 44px;
            box-shadow: 0 4px 24px rgba(28,43,30,0.06);
            position: relative;
            overflow: hidden;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0; left: 44px; right: 44px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold) 30%, var(--gold-soft) 50%, var(--gold) 70%, transparent);
        }

        .register-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.7rem;
            font-weight: 400;
            color: var(--forest);
            margin-bottom: 4px;
        }

        .register-card .subheading {
            font-size: 0.72rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--parchment);
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--cream-2);
        }

        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 0.67rem;
            font-weight: 500;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .field input {
            width: 100%;
            background: var(--cream);
            border: 1px solid var(--cream-2);
            border-bottom: 2px solid rgba(58,82,64,0.2);
            border-radius: 6px;
            padding: 12px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9rem;
            font-weight: 300;
            color: var(--text-body);
            outline: none;
            transition: all 0.2s ease;
        }

        .field input:focus {
            border-color: var(--moss);
            border-bottom-color: var(--moss);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(58,82,64,0.07);
        }

        .field input::placeholder { color: var(--parchment); font-size: 0.85rem; }

        .btn-register {
            width: 100%;
            background: var(--forest);
            color: var(--cream);
            border: none;
            border-radius: 6px;
            padding: 15px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.25s ease;
        }

        .btn-register:hover {
            background: var(--moss);
            box-shadow: 0 4px 16px rgba(28,43,30,0.2);
        }

        .register-footer-link {
            text-align: center;
            margin-top: 24px;
            font-size: 0.82rem;
            color: var(--text-muted);
        }

        .register-footer-link a {
            color: var(--moss);
            text-decoration: none;
            border-bottom: 1px solid rgba(58,82,64,0.3);
            transition: all 0.2s;
        }

        .register-footer-link a:hover { color: var(--forest); border-bottom-color: var(--forest); }

        .error-box {
            background: rgba(184,84,80,0.07);
            border: 1px solid rgba(184,84,80,0.2);
            border-left: 3px solid var(--error);
            color: #8b3532;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 0.84rem;
            margin-bottom: 24px;
        }

        @media (max-width: 480px) {
            .register-card { padding: 36px 24px; }
        }
    </style>
</head>
<body>

<div class="register-wrapper">

    <div class="register-brand">
        <div><a href="../index.php">Pelu<em>quería</em></a></div>
        <div class="register-brand-tagline">Estudio &amp; Estilo</div>
    </div>

    <div class="register-card">
        <h2>Crear cuenta</h2>
        <p class="subheading">Únete a nosotros</p>

        <?php if ($error): ?>
            <div class="error-box"><?= $error ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="field">
                <label>Nombre completo</label>
                <input type="text" name="nombre" placeholder="Tu nombre" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" placeholder="tu@email.com" required>
            </div>
            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
            </div>
            <div class="field">
                <label>Confirmar contraseña</label>
                <input type="password" name="confirm_password" placeholder="Repite tu contraseña" required>
            </div>
            <button href="../auth/login.php" type="submit" class="btn-register">Crear mi cuenta</button>
        </form>

        <div class="register-footer-link">
            ¿Ya tienes cuenta? <a href="../auth/login.php">Inicia sesión</a>
        </div>
    </div>

</div>
</body>
</html>