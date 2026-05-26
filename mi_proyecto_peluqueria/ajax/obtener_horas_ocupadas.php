<?php
    /**
     * Devuelve las horas ocupadas en una fecha determinada.
     * GET: fecha (YYYY-MM-DD)
     * Respuesta: JSON array de strings hora (HH:MM)
     */

    // Establecer que la respuesta será JSON
    header('Content-Type: application/json');

    // Incluir la conexión a la base de datos
    require_once "../config/database.php";

    // Obtener la fecha desde la URL (GET)
    $fecha = $_GET['fecha'] ?? '';

    // Validar que la fecha tenga el formato correcto YYYY-MM-DD
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['error' => 'Formato de fecha no válido']);
        exit;
    }

    // Consultar todas las horas únicas que ya están reservadas en esa fecha
    $stmt = $conn->prepare("SELECT DISTINCT hora FROM citas WHERE fecha = :fecha AND estado = 'reservado'");
    $stmt->execute([':fecha' => $fecha]);

    // Obtener un array simple con las horas (sin índices asociativos)
    $ocupadas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Devolver el array en formato JSON
    echo json_encode($ocupadas);
?>