<?php
session_start();
require_once "../config/database.php";

// Solo superadmin puede crear administradores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim(htmlspecialchars($_POST['nombre']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $error = null;
    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email no válido";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    } else {
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([":email" => $email]);
        if ($stmt->fetch()) {
            $error = "El email ya está registrado";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // Siempre crear como 'admin' (no superadmin)
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, activo) VALUES (:n, :e, :p, 'admin', 1)");
            if ($stmt->execute([":n" => $nombre, ":e" => $email, ":p" => $hashedPassword])) {
                header("Location: ../admin/gestionar_admins.php?success=Administrador creado");
                exit();
            } else {
                $error = "Error al crear el administrador";
            }
        }
    }
    
    if ($error) {
        header("Location: ../admin/gestionar_admins.php?error=" . urlencode($error));
        exit();
    }
}
?>