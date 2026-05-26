<?php
    // Iniciar sesión para poder almacenar temporalmente el nombre en caso de error
    session_start();
    require_once "../config/database.php";

    $error = '';

    // Procesar el formulario cuando se envía por POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recoger y sanitizar los datos del formulario
        $nombre = trim(htmlspecialchars($_POST['nombre'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // ========== VALIDACIONES ==========
        if (empty($nombre) || empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios";
        } 
        // Validar formato del email
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email no válido";
        } else {
            // Validar que el dominio del email sea uno de los permitidos
            $partes = explode('@', $email);
            $dominio = strtolower($partes[1] ?? '');
            $dominios_permitidos = ['gmail.com', 'hotmail.com', 'peluqueria.com'];
            if (!in_array($dominio, $dominios_permitidos)) {
                $error = "El dominio del email debe ser: gmail.com, hotmail.com o peluqueria.com";
            } 
            // Validar longitud mínima de la contraseña
            elseif (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres";
            } 
            // Validar que las contraseñas coincidan
            elseif ($password !== $confirm_password) {
                $error = "Las contraseñas no coinciden";
            } else {
                // Verificar si el email ya está registrado en la base de datos
                $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmt->execute([":email" => $email]);
                if ($stmt->fetch()) {
                    $error = "El email ya está registrado";
                } else {
                    // Encriptar la contraseña
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    // Insertar el nuevo usuario con rol 'cliente'
                    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:n, :e, :p, 'cliente')");
                    $stmt->execute([":n" => $nombre, ":e" => $email, ":p" => $hashedPassword]);
                    
                    // Guardar cookie con el email del nuevo usuario por 30 días
                    setcookie('ultimo_email', $email, time() + 86400 * 30, "/");
                    
                    // Redirigir al login con mensaje de éxito
                    header("Location: ../auth/login.php?registro=exitoso");
                    exit();
                }
            }
        }
        
        // Si hubo algún error, guardar el nombre en sesión para mantenerlo en el formulario
        if ($error) {
            $_SESSION['registro_nombre'] = $nombre;
            // Redirigir de vuelta al formulario con el mensaje de error
            header("Location: register_form.php?error=" . urlencode($error));
            exit();
        }
    }
?>