<?php
    // Iniciar la sesión para poder destruirla
    session_start();

    // Destruir todas las variables de sesión y eliminar la sesión actual
    session_destroy();

    // Redirigir al usuario a la página de inicio de sesión
    header("Location: ../auth/login.php");

    // Asegurar que el script se detiene después de la redirección
    exit();
?>