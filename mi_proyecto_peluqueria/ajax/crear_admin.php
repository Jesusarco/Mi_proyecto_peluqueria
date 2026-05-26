<?php
    // Iniciar sesión para comprobar permisos
    session_start();
    require_once "../config/database.php";

    // Solo el superadministrador puede crear nuevos administradores
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
        header("Location: ../admin/dashboard.php?error=No autorizado");
        exit();
    }

    // Procesar el formulario cuando se envía por POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recoger y sanitizar los datos del formulario
        $nombre = trim(htmlspecialchars($_POST['nombre']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        $error = null;
        
        // Validaciones de campos obligatorios
        if (empty($nombre) || empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios";
        } 
        // Validar formato de email
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email no válido";
        } 
        // Validar longitud de contraseña (mínimo 6 caracteres)
        elseif (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres";
        } 
        else {
            // Verificar si el email ya existe en la base de datos
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([":email" => $email]);
            if ($stmt->fetch()) {
                $error = "El email ya está registrado";
            } else {
                // Encriptar la contraseña
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                // Insertar nuevo administrador con rol 'admin' (no superadmin)
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES (:n, :e, :p, 'admin', 1)");
                if ($stmt->execute([":n" => $nombre, ":e" => $email, ":p" => $hashedPassword])) {
                    // Redirigir con mensaje de éxito
                    header("Location: ../admin/gestionar_admins.php?success=Administrador creado");
                    exit();
                } else {
                    $error = "Error al crear el administrador";
                }
            }
        }
        
        // Si hubo algún error, redirigir sin mensaje de éxito
        if ($error) {
            header("Location: ../admin/gestionar_admins.php");
            exit();
        }
    }
?>