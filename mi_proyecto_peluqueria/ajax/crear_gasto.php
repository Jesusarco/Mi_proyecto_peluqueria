<?php
    // Iniciar sesión para verificar permisos y obtener ID del usuario
    session_start();
    require_once "../config/database.php";

    // Solo administradores o superadministradores pueden registrar gastos
    if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
        header("Location: ../admin/dashboard.php?error=No autorizado");
        exit();
    }

    // Procesar el formulario cuando se envía por POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recoger y sanitizar los datos del formulario
        $descripcion = trim(htmlspecialchars($_POST['descripcion']));
        $categoria = trim(htmlspecialchars($_POST['categoria'] ?? ''));
        $cantidad = (float)$_POST['cantidad'];
        $usuario_id = $_SESSION['usuario_id']; // ID del administrador que registra el gasto

        // Validaciones
        $error = null;
        if (empty($descripcion)) {
            $error = "La descripción es obligatoria";
        } elseif ($cantidad <= 0) {
            $error = "La cantidad debe ser mayor que cero";
        }

        // Si hay error, redirigir con mensaje
        if ($error) {
            header("Location: ../admin/dashboard.php?error=" . urlencode($error));
            exit();
        }

        // Insertar el gasto en la base de datos
        $stmt = $conn->prepare("INSERT INTO gastos (descripcion, categoria, cantidad, usuario_id) VALUES (:d, :c, :m, :u)");
        $stmt->execute([
            ":d" => $descripcion,
            ":c" => $categoria,
            ":m" => $cantidad,
            ":u" => $usuario_id
        ]);

        // Redirigir al panel con mensaje de éxito
        header("Location: ../admin/dashboard.php?success=Gasto registrado");
        exit();
    }
?>