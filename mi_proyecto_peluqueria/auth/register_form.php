<?php
    // Mostrar todos los errores de PHP en desarrollo (se puede desactivar en producción)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Iniciar sesión para poder recuperar el nombre guardado si el registro falló
    session_start();

    // Obtener mensaje de error de la URL (si existe)
    $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

    // Recuperar el nombre que el usuario introdujo antes del error (para mantenerlo en el formulario)
    $nombre_guardado = $_SESSION['registro_nombre'] ?? '';

    // Limpiar la variable de sesión después de usarla para no mantener datos basura
    unset($_SESSION['registro_nombre']);
?>


<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro — Áurea Estudio</title>

        <!-- Preconexión a Google Fonts para mejorar velocidad de carga -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
        
        <!-- Bootstrap Icons para iconografía consistente -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <!-- Reutilizamos los estilos base del login para mantener coherencia visual -->
        <link rel="stylesheet" href="../assets/css/login.css">

        <style>
            /* ===== ESTILOS ESPECÍFICOS PARA EL FORMULARIO DE REGISTRO ===== */

            /* Tarjeta del formulario (mismo estilo que la tarjeta de login) */
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

            /* Línea dorada decorativa superior */
            .register-card::before {
                content: '';
                position: absolute;
                top: 0; left: 48px; right: 48px;
                height: 2px;
                background: linear-gradient(90deg, transparent, var(--gold) 30%, var(--gold-soft) 50%, var(--gold) 70%, transparent);
                border-radius: 0 0 2px 2px;
            }

            /* Motivo decorativo en la esquina inferior derecha */
            .register-card::after {
                content: '✦';
                position: absolute;
                bottom: 20px; right: 24px;
                font-size: 1.2rem;
                color: rgba(200,168,75,0.12);
                pointer-events: none;
            }

            /* Posición relativa para el icono del ojo dentro del input */
            .field {
                position: relative;
            }

            /* Icono para mostrar/ocultar contraseña */
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

            /* Espacio a la derecha para que el icono no tape el texto */
            .field input {
                padding-right: 40px;
            }

            /* Mensajes de error específicos por campo */
            .error-campo {
                color: #e74c3c;
                font-size: 0.75rem;
                margin-top: 4px;
                display: block;
            }

            /* Forzar scroll vertical en pantallas pequeñas para que el botón de registro sea visible */
            body {
                overflow-y: auto !important;
                min-height: 100vh;
                align-items: flex-start !important;
            }
            .login-wrapper {
                margin-bottom: 2rem;
            }

            /* Ajuste de padding en móviles para que el formulario no quede pegado a los bordes */
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

            <div class="register-card">
                <h2 class="login-heading">Crear cuenta</h2>
                <p class="login-subheading">Únete a nosotros</p>

                <!-- Mostrar mensaje de error general si existe -->
                <?php if ($error): ?>
                    <div class="login-error"><?= $error ?></div>
                <?php endif; ?>

                <!-- Formulario de registro que envía los datos a register.php -->
                <form action="register.php" method="POST" id="registerForm">
                    <!-- Campo Nombre (se conserva si hubo error) -->
                    <div class="field">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" 
                            value="<?= htmlspecialchars($nombre_guardado) ?>" required>
                    </div>

                    <!-- Campo Email con validación de dominio mediante patrón HTML5 -->
                    <div class="field">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tu@email.com" required
                            pattern="^[a-zA-Z0-9._%+-]+@(gmail|hotmail|peluqueria)\.com$"
                            title="Solo se permiten dominios: @gmail.com, @hotmail.com">
                        <!-- Span para mensaje de error personalizado (dominio inválido) -->
                        <span id="emailError" class="error-campo" style="display:none;"></span>
                    </div>

                    <!-- Campo Contraseña con requisitos de seguridad -->
                    <div class="field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                            title="Debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número">
                        <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                    </div>

                    <!-- Campo Confirmar contraseña -->
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

                <!-- Decoración con estrella -->
                <div class="ornament">— <i class="bi bi-star-fill"></i> —</div>
            </div>
        </div>

        <script>
            // ========== REFERENCIAS A ELEMENTOS DEL DOM ==========
            const nombreInput = document.getElementById('nombre');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');
            const emailErrorSpan = document.getElementById('emailError');

            // ---------- CAPITALIZAR NOMBRE (primera letra de cada palabra) ----------
            // Se ejecuta cuando el campo pierde el foco (blur)
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

            // ---------- VALIDACIÓN DE DOMINIO DEL EMAIL (en cliente) ----------
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
                
                // Mostrar mensaje de error si el dominio no es válido y el campo no está vacío
                if (!esValido && valor !== '') {
                    emailErrorSpan.textContent = 'El dominio debe ser: gmail.com, hotmail.com o peluqueria.com';
                    emailErrorSpan.style.display = 'block';
                    this.setCustomValidity('Dominio no permitido');
                } else {
                    emailErrorSpan.style.display = 'none';
                    this.setCustomValidity('');
                }
            });
            
            // Ocultar mensaje de error mientras el usuario escribe
            emailInput.addEventListener('input', function() {
                emailErrorSpan.style.display = 'none';
                this.setCustomValidity('');
            });

            // ---------- MOSTRAR/OCULTAR CONTRASEÑA (icono del ojo) ----------
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

            // ---------- VALIDACIONES ANTES DE ENVIAR EL FORMULARIO ----------
            document.getElementById('registerForm').addEventListener('submit', function(e) {
                // Asegurar que el nombre esté capitalizado (por si el usuario no salió del campo)
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
                
                // Verificar que las dos contraseñas coincidan
                const password = passwordInput.value;
                const confirm = confirmInput.value;
                if (password !== confirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                }
                
                // Volver a validar el dominio del email (por si acaso)
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