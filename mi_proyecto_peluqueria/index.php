<?php
/**
 * Punto de entrada de la aplicación.
 * Redirige automáticamente a la página de inicio pública.
 * El inicio de sesión se realiza desde el enlace en la cabecera.
 */
header("Location: user/inicio.php");
exit();
?>