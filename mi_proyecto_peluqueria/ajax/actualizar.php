<?php
session_start();
require_once "../config/database.php";

if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) {
    header("Location: ../admin/dashboard.php?error=No autorizado");
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'producto':
        $id = (int)$_POST['id'];
        $nombre = trim(htmlspecialchars($_POST['nombre']));
        $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
        $precio = (float)$_POST['precio'];
        $stock = (int)$_POST['stock'];
        $destacado = isset($_POST['destacado']) ? 1 : 0;
        // Imagen
        $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $imagen_actual = $stmt->fetchColumn() ?: '';
        $imagen_nueva = $imagen_actual;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/";
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $nombre_archivo)) {
                $imagen_nueva = $nombre_archivo;
                if ($imagen_actual && file_exists($upload_dir . $imagen_actual)) unlink($upload_dir . $imagen_actual);
            }
        }
        if ($id && $nombre && $precio > 0) {
            $stmt = $conn->prepare("UPDATE productos SET nombre=:n, descripcion=:d, precio=:p, stock=:s, destacado=:dest, imagen=:i WHERE id=:id");
            $stmt->execute([":n" => $nombre, ":d" => $descripcion, ":p" => $precio, ":s" => $stock, ":dest" => $destacado, ":i" => $imagen_nueva, ":id" => $id]);
        }
        $redirect = "../admin/dashboard.php";
        break;
    case 'servicio':
        $id = (int)$_POST['id'];
        $nombre = trim(htmlspecialchars($_POST['nombre']));
        $descripcion = trim(htmlspecialchars($_POST['descripcion'] ?? ''));
        $precio = (float)$_POST['precio'];
        $duracion = (int)$_POST['duracion'];

        // Procesar imagen antes del UPDATE
        $stmt = $conn->prepare("SELECT imagen FROM servicios WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $imagen_actual = $stmt->fetchColumn() ?: '';
        $imagen_nueva = $imagen_actual;

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/";
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $nombre_archivo)) {
                $imagen_nueva = $nombre_archivo;
                if ($imagen_actual && file_exists($upload_dir . $imagen_actual)) {
                    unlink($upload_dir . $imagen_actual);
                }
            }
        }

        // Ahora el UPDATE con todos los campos, incluida la imagen
        if ($id && $nombre && $precio > 0 && $duracion > 0) {
            $stmt = $conn->prepare("UPDATE servicios SET nombre=:n, descripcion=:d, precio=:p, duracion=:dur, imagen=:i WHERE id=:id");
            $stmt->execute([
                ":n" => $nombre,
                ":d" => $descripcion,
                ":p" => $precio,
                ":dur" => $duracion,
                ":i" => $imagen_nueva,
                ":id" => $id
            ]);
        }
        $redirect = "../admin/dashboard.php";
        break;
    case 'gasto':
        $id = (int)$_POST['id'];
        $descripcion = trim(htmlspecialchars($_POST['descripcion']));
        $categoria = trim(htmlspecialchars($_POST['categoria'] ?? ''));
        $cantidad = (float)$_POST['cantidad'];
        if ($id && $descripcion && $cantidad > 0) {
            $stmt = $conn->prepare("UPDATE gastos SET descripcion=:d, categoria=:c, cantidad=:m WHERE id=:id");
            $stmt->execute([":d" => $descripcion, ":c" => $categoria, ":m" => $cantidad, ":id" => $id]);
        }
        $redirect = "../admin/dashboard.php";
        break;
    case 'cita':
        $id = (int)$_POST['id'];
        $fecha = $_POST['fecha'];
        $hora = $_POST['hora'];
        if ($id && $fecha && $hora) {
            $stmt = $conn->prepare("UPDATE citas SET fecha = :f, hora = :h WHERE id = :id");
            $stmt->execute([":f" => $fecha, ":h" => $hora, ":id" => $id]);
        }
        $redirect = "../admin/dashboard.php";
        break;
        case 'usuario':
        case 'admin':
            // Solo superadmin puede editar usuarios (clientes o admins)
            if ($_SESSION['rol'] != 'superadmin') {
                header("Location: ../admin/dashboard.php?error=No autorizado");
                exit();
            }
                
            $id = (int)$_POST['id'];
            $nombre = trim(htmlspecialchars($_POST['nombre']));
            $email = trim(htmlspecialchars($_POST['email']));
            $password = $_POST['password'] ?? '';
                
            if ($id <= 0 || empty($nombre) || empty($email)) {
                header("Location: ../admin/dashboard.php?error=Datos incompletos");
                exit();
            }
                
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../admin/dashboard.php?error=Email no válido");
                exit();
            }
                
            // Verificar que el email no esté en uso por otro usuario
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
            $stmt->execute([':email' => $email, ':id' => $id]);
            if ($stmt->fetch()) {
                header("Location: ../admin/dashboard.php?error=El email ya está registrado por otro usuario");
                exit();
            }
            
            $updateFields = "nombre = :nombre, email = :email";
            $params = [':nombre' => $nombre, ':email' => $email, ':id' => $id];
                
            if (!empty($password)) {
                if (strlen($password) < 6) {
                    header("Location: ../admin/dashboard.php?error=La contraseña debe tener al menos 6 caracteres");
                    exit();
                }
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $updateFields .= ", password = :password";
                $params[':password'] = $hashed;
            }
                
            $sql = "UPDATE usuarios SET $updateFields WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
                
            // Redirigir según el origen (si viene de gestionar_admins.php o dashboard.php)
            $redirect = "../admin/dashboard.php";
            if ($action == 'admin') {
                $redirect = "../admin/gestionar_admins.php";
            }
            header("Location: $redirect?success=Usuario actualizado");
            exit();
            break;
    default:
        header("Location: ../admin/dashboard.php?error=Acción no válida");
        exit();
}
header("Location: $redirect");
exit();
?>