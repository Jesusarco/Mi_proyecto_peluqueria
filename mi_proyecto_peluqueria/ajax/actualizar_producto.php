<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $nombre = trim(htmlspecialchars($_POST['nombre']));
    $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $destacado = isset($_POST['destacado']) ? 1 : 0;

    // Obtener imagen actual para posible borrado
    $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    $imagen_actual = $producto['imagen'] ?? '';

    $imagen_nueva = $imagen_actual; // por defecto mantener la actual

    // Procesar nueva imagen si se subió
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = uniqid() . '.' . $extension;
        $ruta_destino = $upload_dir . $nombre_archivo;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $imagen_nueva = $nombre_archivo;
            // Borrar imagen anterior si existe
            if ($imagen_actual && file_exists($upload_dir . $imagen_actual)) {
                unlink($upload_dir . $imagen_actual);
            }
        }
    }

    if ($id && $nombre && $precio > 0) {
        $stmt = $conn->prepare("UPDATE productos SET nombre=:n, descripcion=:d, precio=:p, stock=:s, destacado=:dest, imagen=:i WHERE id=:id");
        $stmt->execute([
            ":n" => $nombre,
            ":d" => $descripcion,
            ":p" => $precio,
            ":s" => $stock,
            ":dest" => $destacado,
            ":i" => $imagen_nueva,
            ":id" => $id
        ]);
    }
}
header("Location: ../admin/dashboard.php");
exit();
?>