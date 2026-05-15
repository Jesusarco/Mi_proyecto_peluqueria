<?php
// functions.php
function comprobarAdmin() {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
        header("Location: ../auth/login.php");
        exit();
    }
}

function comprobarCliente() {
    if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'cliente' && $_SESSION['rol'] != 'admin')) {
        header("Location: ../auth/login.php");
        exit();
    }
}

function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}