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
            
            header("Location: ../index.php?registro=exitoso");
            exit();
        }
    }
    
    if ($error) {
        header("Location: register_form.php?error=" . urlencode($error));
        exit();
    }
}
?>