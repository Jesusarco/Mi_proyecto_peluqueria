<?php
    // ========== CONFIGURACIÓN DE LA BASE DE DATOS ==========

    // Configuración actual (para el servidor remoto con credenciales del alumno)
    $host = 'localhost';
    $dbname = 'daw2_jesus';
    $user = 'jesus';
    $password = 'MurgiDAW2_2026!';

    // Configuración alternativa para entorno local (XAMPP/WAMP) con usuario root
    // (descomentar las líneas siguientes y comentar las de arriba para usar local)
    // $host = "localhost";
    // $dbname = "peluqueria_db";
    // $user = "root";
    // $password = "";
    // // $password = "root";

    try {
        // Crear una nueva conexión PDO a MySQL
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        
        // Configurar el modo de error de PDO para que lance excepciones (mejor para depuración)
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    } catch (PDOException $e) {
        // Si falla la conexión, mostrar el error y detener la ejecución
        die("Error de conexión: " . $e->getMessage());
    }
?>