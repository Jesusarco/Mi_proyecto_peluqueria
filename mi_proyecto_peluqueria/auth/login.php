<?php
session_start();
require_once "../config/database.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([":email" => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_regenerate_id(true); // Prevenir fixation
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['nombre'] = $usuario['nombre'];

            //Redirección en fución del rol
            $redirect = ($usuario['rol'] == 'admin' || $usuario['rol'] == 'superadmin') ? '../admin/dashboard.php' : '../user/inicio.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = "Credenciales incorrectas"; // Mensaje genérico por seguridad
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — Peluquería</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold: #C9A84C;
            --gold-light: #e8c97a;
            --dark: #0e0e0c;
            --dark-2: #161614;
            --dark-3: #1e1e1b;
            --cream: #f5f0e8;
            --error: #c0392b;
        }

        body {
            background: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Jost', sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* Fondo con textura sutil */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 800px 600px at 20% 50%, rgba(201,168,76,0.04) 0%, transparent 70%),
                radial-gradient(ellipse 600px 800px at 80% 30%, rgba(201,168,76,0.03) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Líneas decorativas de fondo */
        body::after {
            content: '';
            position: fixed;
            top: 0; left: 50%;
            transform: translateX(-50%);
            width: 1px;
            height: 100vh;
            background: linear-gradient(to bottom, transparent, rgba(201,168,76,0.08) 30%, rgba(201,168,76,0.08) 70%, transparent);
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.6s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Marca arriba */
        .login-brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-brand a {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 300;
            letter-spacing: 0.35em;
            color: var(--gold);
            text-decoration: none;
            text-transform: uppercase;
        }

        .login-brand a em {
            color: #fff;
            font-style: italic;
            font-weight: 300;
        }

        .login-brand-tagline {
            font-size: 0.65rem;
            letter-spacing: 0.4em;
            text-transform: uppercase;
            color: #444440;
            margin-top: 8px;
        }

        /* Tarjeta */
        .login-card {
            background: var(--dark-2);
            border: 1px solid rgba(201,168,76,0.15);
            border-radius: 4px;
            padding: 48px 44px;
            position: relative;
            overflow: hidden;
        }

        /* Esquina decorativa */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            background: linear-gradient(225deg, rgba(201,168,76,0.08) 0%, transparent 60%);
        }

        .login-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            width: 60px; height: 60px;
            background: linear-gradient(45deg, rgba(201,168,76,0.05) 0%, transparent 60%);
        }

        /* Título */
        .login-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            font-weight: 400;
            color: #fff;
            margin-bottom: 6px;
            letter-spacing: 0.05em;
        }

        .login-subtitle {
            font-size: 0.75rem;
            font-weight: 300;
            letter-spacing: 0.15em;
            color: #555550;
            text-transform: uppercase;
            margin-bottom: 36px;
            padding-bottom: 28px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        /* Error */
        .login-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(192,57,43,0.1);
            border: 1px solid rgba(192,57,43,0.3);
            border-left: 3px solid var(--error);
            color: #e07070;
            padding: 12px 16px;
            border-radius: 3px;
            font-size: 0.82rem;
            font-weight: 300;
            margin-bottom: 28px;
            letter-spacing: 0.03em;
        }

        .login-error::before {
            content: '!';
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1px solid #e07070;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 500;
            color: #e07070;
        }

        /* Campo */
        .field {
            margin-bottom: 24px;
            position: relative;
        }

        .field label {
            display: block;
            font-size: 0.67rem;
            font-weight: 500;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: #666660;
            margin-bottom: 10px;
        }

        .field input {
            width: 100%;
            background: var(--dark-3);
            border: 1px solid rgba(255,255,255,0.08);
            border-bottom-color: rgba(201,168,76,0.25);
            border-radius: 3px;
            padding: 13px 16px;
            font-family: 'Jost', sans-serif;
            font-size: 0.9rem;
            font-weight: 300;
            color: #ddddd5;
            letter-spacing: 0.03em;
            outline: none;
            transition: all 0.25s ease;
        }

        .field input:focus {
            border-color: rgba(201,168,76,0.5);
            background: rgba(201,168,76,0.03);
            box-shadow: 0 0 0 3px rgba(201,168,76,0.05);
            color: #fff;
        }

        .field input::placeholder {
            color: #3a3a36;
            font-size: 0.8rem;
        }

        /* Botón */
        .btn-login {
            width: 100%;
            background: transparent;
            border: 1px solid var(--gold);
            color: var(--gold);
            font-family: 'Jost', sans-serif;
            font-size: 0.72rem;
            font-weight: 500;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            padding: 15px 20px;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 8px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: var(--gold);
            transition: left 0.3s ease;
            z-index: 0;
        }

        .btn-login:hover::before {
            left: 0;
        }

        .btn-login:hover {
            color: var(--dark);
        }

        .btn-login span {
            position: relative;
            z-index: 1;
        }

        /* Link registro */
        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 0.78rem;
            font-weight: 300;
            color: #444440;
        }

        .login-footer a {
            color: var(--gold);
            text-decoration: none;
            letter-spacing: 0.05em;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s;
        }

        .login-footer a:hover {
            border-bottom-color: var(--gold);
        }

        /* Separador decorativo */
        .ornament {
            text-align: center;
            color: rgba(201,168,76,0.25);
            font-size: 0.9rem;
            letter-spacing: 0.4em;
            margin: 20px 0 0;
            user-select: none;
        }

        @media (max-width: 480px) {
            .login-card { padding: 36px 28px; }
            .login-brand a { font-size: 1.6rem; }
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-brand">
        <div><a href="../index.php">Pelu<em>quer&iacute;a</em></a></div>
        <div class="login-brand-tagline">Elegancia &amp; Estilo</div>
    </div>

    <div class="login-card">

        <h2 class="login-title">Bienvenido</h2>
        <p class="login-subtitle">Accede a tu cuenta</p>

        <?php if ($error): ?>
            <div class="login-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="tu@email.com" required>
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">
                <span>Ingresar</span>
            </button>
        </form>

        <div class="login-footer">
            ¿Sin cuenta todavía? <a href="register_form.php">Regístrate aquí</a>
        </div>

        <div class="ornament">— ✦ —</div>

    </div>

</div>

</body>
</html>