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
    <title>Registro - Peluquería</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center;">

<div class="main-container" style="max-width: 450px; margin: 0 auto;">
    <div class="salon-card" style="padding: 40px;">
        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 30px;">Crear cuenta</h2>
        
        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label class="salon-label">Nombre completo</label>
            <input type="text" name="nombre" class="salon-input" required>

            <label class="salon-label">Email</label>
            <input type="email" name="email" class="salon-input" required>

            <label class="salon-label">Contraseña</label>
            <input type="password" name="password" class="salon-input" required>

            <label class="salon-label">Confirmar contraseña</label>
            <input type="password" name="confirm_password" class="salon-input" required>

            <button type="submit" class="salon-btn salon-btn-accent" style="width: 100%; margin-top: 10px;">Registrarse</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="../index.php" style="color: var(--accent-color); text-decoration: none;">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </div>
</div>

</body>
</html>