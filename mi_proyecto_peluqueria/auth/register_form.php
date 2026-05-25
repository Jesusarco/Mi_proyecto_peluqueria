<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Iniciar sesión para recuperar el nombre guardado

$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
$nombre_guardado = $_SESSION['registro_nombre'] ?? '';
// Limpiar la sesión después de leerlo
unset($_SESSION['registro_nombre']);
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
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Usamos el mismo CSS que login.php -->
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        /* Ajustes específicos para el formulario de registro (mismo estilo que login) */
        .register-card {
            background: var(--white);
            border: 1px solid var(--cream-2);
            border-radius: 12px;
            padding: 52px 48px;
            box-shadow: 0 4px 24px rgba(28,43,30,0.06), 0 1px 3px rgba(28,43,30,0.04);
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .register-card::before {
            content: '';
            position: absolute;
            top: 0; left: 48px; right: 48px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold) 30%, var(--gold-soft) 50%, var(--gold) 70%, transparent);
            border-radius: 0 0 2px 2px;
        }
        .register-card::after {
            content: '✦';
            position: absolute;
            bottom: 20px; right: 24px;
            font-size: 1.2rem;
            color: rgba(200,168,75,0.12);
            pointer-events: none;
        }
        /* Posicionamiento del icono del ojo dentro del campo */
        .field {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            bottom: 14px;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.2rem;
            z-index: 2;
            background: transparent;
        }
        /* Ajuste de padding para el input con icono */
        .field input {
            padding-right: 40px;
        }
        .error-campo {
            color: #e74c3c;
            font-size: 0.75rem;
            margin-top: 4px;
            display: block;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-brand">
        <div><a href="../index.php">Pelu<em>quería</em></a></div>
        <div class="login-brand-tagline">Estudio &amp; Estilo</div>
    </div>

    <div class="register-card">
        <h2 class="login-heading">Crear cuenta</h2>
        <p class="login-subheading">Únete a nosotros</p>

        <?php if ($error): ?>
            <div class="login-error"><?= $error ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="registerForm">
            <div class="field">
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" 
                       value="<?= htmlspecialchars($nombre_guardado) ?>" required>
            </div>
            <div class="field">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" placeholder="tu@email.com" required
                       pattern="^[a-zA-Z0-9._%+-]+@(gmail|hotmail|peluqueria)\.com$"
                       title="Solo se permiten dominios: @gmail.com, @hotmail.com">
                <span id="emailError" class="error-campo" style="display:none;"></span>
            </div>
            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required
                       pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                       title="Debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número">
                <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
            </div>
            <div class="field">
                <label for="confirm_password">Confirmar contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite tu contraseña" required>
                <i class="bi bi-eye-slash password-toggle" id="toggleConfirmPassword"></i>
            </div>
            <button type="submit" class="btn-login"><span>Crear mi cuenta</span></button>
        </form>

        <div class="login-footer-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </div>

        <div class="ornament">— <i class="bi bi-star-fill"></i> —</div>
    </div>

</div>

<script>
    // Transformaciones en tiempo real
    const nombreInput = document.getElementById('nombre');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const emailErrorSpan = document.getElementById('emailError');

    // Nombre: capitalizar primera letra de cada palabra (al perder el foco)
    if (nombreInput) {
        nombreInput.addEventListener('blur', function() {
            let value = this.value.trim();
            if (value.length > 0) {
                const palabras = value.split(' ');
                const capitalizadas = palabras.map(palabra => {
                    if (palabra.length === 0) return '';
                    return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
                });
                this.value = capitalizadas.join(' ');
            }
        });
    }

    // Email a minúsculas (al perder el foco) y validación de dominio
    emailInput.addEventListener('blur', function() {
        let valor = this.value.toLowerCase();
        this.value = valor;
        
        const dominiosPermitidos = ['gmail.com', 'hotmail.com', 'peluqueria.com'];
        let esValido = false;
        const partes = valor.split('@');
        if (partes.length === 2) {
            const dominio = partes[1];
            if (dominiosPermitidos.includes(dominio)) {
                esValido = true;
            }
        }
        
        if (!esValido && valor !== '') {
            emailErrorSpan.textContent = 'El dominio debe ser: gmail.com, hotmail.com o peluqueria.com';
            emailErrorSpan.style.display = 'block';
            this.setCustomValidity('Dominio no permitido');
        } else {
            emailErrorSpan.style.display = 'none';
            this.setCustomValidity('');
        }
    });
    
    emailInput.addEventListener('input', function() {
        emailErrorSpan.style.display = 'none';
        this.setCustomValidity('');
    });

    // Función para alternar visibilidad de la contraseña
    function togglePasswordVisibility(inputElement, toggleIcon) {
        toggleIcon.addEventListener('click', function() {
            const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
            inputElement.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }

    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    if (togglePassword) togglePasswordVisibility(passwordInput, togglePassword);
    if (toggleConfirmPassword) togglePasswordVisibility(confirmInput, toggleConfirmPassword);

    // Validación de coincidencia de contraseñas y capitalización final antes de enviar
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        // Capitalizar nombre por si el usuario no salió del campo
        if (nombreInput) {
            let value = nombreInput.value.trim();
            if (value.length > 0) {
                const palabras = value.split(' ');
                const capitalizadas = palabras.map(palabra => {
                    if (palabra.length === 0) return '';
                    return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
                });
                nombreInput.value = capitalizadas.join(' ');
            }
        }
        
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        if (password !== confirm) {
            e.preventDefault();
            alert('Las contraseñas no coinciden');
        }
        
        const email = emailInput.value;
        const partes = email.split('@');
        const dominiosPermitidos = ['gmail.com', 'hotmail.com', 'peluqueria.com'];
        if (partes.length !== 2 || !dominiosPermitidos.includes(partes[1])) {
            e.preventDefault();
            alert('El correo electrónico debe tener un dominio válido: gmail.com, hotmail.com o peluqueria.com');
        }
    });
</script>

</body>
</html>