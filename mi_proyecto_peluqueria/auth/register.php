<?php
session_start();
require_once "../config/database.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim(htmlspecialchars($_POST['nombre'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validaciones
    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email no válido";
    } else {
        // Validar dominio permitido
        $partes = explode('@', $email);
        $dominio = strtolower($partes[1] ?? '');
        $dominios_permitidos = ['gmail.com', 'hotmail.com', 'peluqueria.com'];
        if (!in_array($dominio, $dominios_permitidos)) {
            $error = "El dominio del email debe ser: gmail.com, hotmail.com o peluqueria.com";
        } elseif (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres";
        } elseif ($password !== $confirm_password) {
            $error = "Las contraseñas no coinciden";
        } else {
            // Verificar si el email ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->execute([":email" => $email]);
            if ($stmt->fetch()) {
                $error = "El email ya está registrado";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:n, :e, :p, 'cliente')");
                $stmt->execute([":n" => $nombre, ":e" => $email, ":p" => $hashedPassword]);
                
                // Guardar cookie con el email del nuevo usuario (30 días)
                setcookie('ultimo_email', $email, time() + 86400 * 30, "/");
                
                header("Location: ../auth/login.php?registro=exitoso");
                exit();
            }
        }
    }
    
    if ($error) {
        // Guardar el nombre en sesión para mantenerlo en el formulario
        $_SESSION['registro_nombre'] = $nombre;
        header("Location: register_form.php?error=" . urlencode($error));
        exit();
    }
}
?>