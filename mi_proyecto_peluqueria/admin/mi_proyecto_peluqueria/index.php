<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Peluquería Profesional</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center;">

<div class="main-container" style="max-width: 450px; margin: 0 auto;">
    <div class="salon-card" style="padding: 40px;">
        <h2 style="text-align: center; color: var(--primary-color); margin-bottom: 30px;">Bienvenido</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso'): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                Registro exitoso. Ahora puedes iniciar sesión.
            </div>
        <?php endif; ?>

        <form action="auth/login.php" method="POST">
            <label class="salon-label">Email</label>
            <input type="email" name="email" class="salon-input" placeholder="tu@email.com" required>
            
            <label class="salon-label">Contraseña</label>
            <input type="password" name="password" class="salon-input" placeholder="••••••" required>

            <button class="salon-btn salon-btn-accent" style="width: 100%; margin-top: 10px;">Iniciar Sesión</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="auth/register_form.php" style="color: var(--accent-color); text-decoration: none;">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>
</div>

</body>
</html>