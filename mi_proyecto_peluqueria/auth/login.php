<?php
session_start();
require_once "../config/database.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Por favor complete todos los campos";
    } else {
        $sql = "SELECT * FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute([":email" => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_regenerate_id(true); // Prevenir fixation
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['nombre'] = $usuario['nombre'];

            $redirect = ($usuario['rol'] == 'admin') ? '../admin/dashboard.php' : '../user/inicio.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = "Credenciales incorrectas"; // Mensaje genérico por seguridad
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Salón</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="main-container" style="max-width: 400px;">
    <div class="salon-card">
        <h2>Acceso</h2>
        <?php if ($error): ?>
            <div class="alert alert-error" style="background:#f8d7da; color:#721c24; padding:10px; border-radius:4px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <label class="salon-label">Email</label>
            <input type="email" name="email" class="salon-input" required>
            
            <label class="salon-label">Contraseña</label>
            <input type="password" name="password" class="salon-input" required>
            
            <button type="submit" class="salon-btn salon-btn-accent" style="width:100%">Ingresar</button>
        </form>
        <p style="margin-top:15px;"><a href="register_form.php">¿No tienes cuenta? Regístrate</a></p>
    </div>
</div>
</body>
</html>