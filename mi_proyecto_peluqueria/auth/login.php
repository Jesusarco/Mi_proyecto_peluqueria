<?php
    // Iniciar sesión para poder almacenar datos del usuario tras el login
    session_start();
    require_once "../config/database.php";

    $error = '';

    // Leer cookie del último email (si existe) para precargar el campo
    $ultimo_email = $_COOKIE['ultimo_email'] ?? '';

    // Procesar el formulario cuando se envía por POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Validar que los campos no estén vacíos
        if (empty($email) || empty($password)) {
            $error = "Por favor complete todos los campos";
        } else {
            // Buscar el usuario en la base de datos por email
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->execute([":email" => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar que el usuario existe y que la contraseña es correcta
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Regenerar ID de sesión para prevenir fijación de sesión
                session_regenerate_id(true);
                // Guardar datos del usuario en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['imagen'] = $usuario['imagen'] ?? null;

                // Guardar cookie con el email del usuario por 30 días
                setcookie('ultimo_email', $email, time() + 86400 * 30, "/");

                // Redirigir según el rol (admin/superadmin al dashboard, cliente al inicio)
                $redirect = ($usuario['rol'] == 'admin' || $usuario['rol'] == 'superadmin') ? '../admin/dashboard.php' : '../user/inicio.php';
                header("Location: $redirect");
                exit();
            } else {
                $error = "Credenciales incorrectas";
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso — Áurea Estudio</title>

        <!-- Descarga de las fuentes que utilizo para los tipos de letras -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- Estilos principales del login -->
        <link rel="stylesheet" href="../assets/css/login.css">

        <style>
            /* Posicionamiento del icono del ojo dentro del campo de contraseña */
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
            .field input {
                padding-right: 40px; /* Espacio para el icono */
            }
            /* Estilo del ornamento con iconos Bootstrap */
            .ornament {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                color: rgba(200,168,75,0.35);
                font-size: 0.8rem;
                margin-top: 22px;
            }
            .ornament i {
                font-size: 0.7rem;
            }

            /* Forzar scroll vertical en pantallas pequeñas para que se vea todo el formulario */
            body {
                overflow-y: auto !important;
                min-height: 100vh;
                align-items: flex-start !important;
            }
            .login-wrapper {
                margin-bottom: 2rem;
            }
            @media (max-width: 600px) {
                .register-card {
                    padding: 30px 25px !important;
                }
            }
        </style>
    </head>
    <body>

        <div class="login-wrapper">
            <!-- Logotipo y eslogan -->
            <div class="login-brand">
                <div><a href="../index.php">Pelu<em>quería</em></a></div>
                <div class="login-brand-tagline">Estudio &amp; Estilo</div>
            </div>

            <!-- Tarjeta del formulario de inicio de sesión -->
            <div class="login-card">
                <h2 class="login-heading">Bienvenido</h2>
                <p class="login-subheading">Accede a tu cuenta</p>

                <!-- Mostrar mensaje de error si existe -->
                <?php if ($error): ?>
                    <div class="login-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" id="loginForm">
                    <div class="field">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tu@email.com" 
                            value="<?= htmlspecialchars($ultimo_email) ?>" required>
                    </div>
                    <div class="field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <!-- Icono para mostrar/ocultar la contraseña -->
                        <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                    </div>
                    <button type="submit" class="btn-login"><span>Ingresar</span></button>
                </form>

                <div class="login-footer-link">
                    ¿Sin cuenta todavía? <a href="register_form.php">Regístrate aquí</a>
                </div>

                <!-- Decoración con estrella -->
                <div class="ornament">— <i class="bi bi-star-fill"></i> —</div>
            </div>
        </div>

        <script>
            // Convertir el email a minúsculas cuando el campo pierde el foco
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    this.value = this.value.toLowerCase();
                });
            }

            const passwordInput = document.getElementById('password');

            // Mostrar/ocultar la contraseña al hacer clic en el icono del ojo
            const toggleIcon = document.getElementById('togglePassword');
            if (passwordInput && toggleIcon) {
                toggleIcon.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    // Cambiar el icono entre ojo abierto y cerrado
                    this.classList.toggle('bi-eye');
                    this.classList.toggle('bi-eye-slash');
                });
            }
        </script>

    </body>
</html>