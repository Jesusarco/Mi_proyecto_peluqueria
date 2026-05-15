<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim(htmlspecialchars($_POST['nombre']));
    $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $destacado = isset($_POST['destacado']) ? 1 : 0;

    // Procesar imagen
    $imagen_nombre = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid() . '.' . $extension;
        $ruta_destino = $upload_dir . $nombre_archivo;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $imagen_nombre = $nombre_archivo;
        }
    }

    if ($nombre && $precio > 0) {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, destacado, imagen) VALUES (:n, :d, :p, :s, :dest, :i)");
        $stmt->execute([
            ":n" => $nombre,
            ":d" => $descripcion,
            ":p" => $precio,
            ":s" => $stock,
            ":dest" => $destacado,
            ":i" => $imagen_nombre
        ]);
    }
}
header("Location: ../admin/dashboard.php");
exit();
?>