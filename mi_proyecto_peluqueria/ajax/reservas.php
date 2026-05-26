<?php
    session_start();                              // Iniciar sesión para acceder al ID del usuario

    require_once "../config/database.php";        // Conexión a la base de datos

    header('Content-Type: application/json');    // Respuesta en formato JSON

    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
        exit();
    }

    // Solo procesar solicitudes POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener y sanitizar datos del formulario
        $usuario_id = (int)$_SESSION['usuario_id'];
        $servicio_id = (int)$_POST['servicio_id'];
        $fecha = trim($_POST['fecha']);
        $hora = trim($_POST['hora']);
        
        // Validación de campos obligatorios
        if ($servicio_id <= 0 || empty($fecha) || empty($hora)) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit();
        }
        
        // Validar formato de fecha (YYYY-MM-DD) y que no sea pasada
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) < strtotime(date('Y-m-d'))) {
            echo json_encode(['success' => false, 'message' => 'Fecha no válida']);
            exit();
        }
        
        try {
            // Comprobar si la hora ya está ocupada en esa fecha
            $sql = "SELECT id FROM citas WHERE fecha = :fecha AND hora = :hora";
            $stmt = $conn->prepare($sql);
            $stmt->execute([":fecha" => $fecha, ":hora" => $hora]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => false, 'message' => 'Horario no disponible']);
                exit();
            }
            
            // Insertar la nueva cita con estado 'reservado'
            $sql = "INSERT INTO citas (usuario_id, servicio_id, fecha, hora, estado) 
                    VALUES (:usuario_id, :servicio_id, :fecha, :hora, 'reservado')";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":usuario_id" => $usuario_id,
                ":servicio_id" => $servicio_id,
                ":fecha" => $fecha,
                ":hora" => $hora
            ]);
            
            // Respuesta exitosa
            echo json_encode(['success' => true, 'message' => 'Cita reservada correctamente']);
            
        } catch (PDOException $e) {
            // Error en la base de datos
            echo json_encode(['success' => false, 'message' => 'Error al procesar la reserva']);
        }
    }
?>