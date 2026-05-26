<?php
    // Iniciar sesión para verificar permisos
    session_start();
    require_once "../config/database.php";

    // Solo administradores o superadministradores pueden crear servicios
    if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
        header("Location: ../admin/dashboard.php?error=No autorizado");
        exit();
    }

    // Procesar el formulario cuando se envía por POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recoger y sanitizar los datos del formulario
        $nombre = trim(htmlspecialchars($_POST['nombre']));
        $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
        $precio = (float)$_POST['precio'];
        $duracion = (int)$_POST['duracion'];

        // Procesar la imagen subida (si existe)
        $imagen_nombre = '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/";
            // Crear la carpeta si no existe, con permisos 777
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            // Generar nombre único para evitar colisiones y mejorar seguridad
            $nombre_archivo = uniqid() . '.' . $extension;
            $ruta_destino = $upload_dir . $nombre_archivo;
            // Mover el archivo subido a la carpeta de destino
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                $imagen_nombre = $nombre_archivo;
            }
        }

        // Validar que los campos obligatorios tengan valores correctos
        if ($nombre && $precio > 0 && $duracion > 0) {
            // Insertar el nuevo servicio en la base de datos
            $stmt = $conn->prepare("INSERT INTO servicios (nombre, descripcion, precio, duracion, imagen) VALUES (:n, :d, :p, :dur, :i)");
            $stmt->execute([
                ":n" => $nombre,
                ":d" => $descripcion,
                ":p" => $precio,
                ":dur" => $duracion,
                ":i" => $imagen_nombre
            ]);
        }
    }

    // Redirigir de vuelta al panel de administración
    header("Location: ../admin/dashboard.php");
    exit();
?>