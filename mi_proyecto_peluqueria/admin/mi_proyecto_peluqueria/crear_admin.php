<?php
require_once "config/database.php";

$email = "admin@admin.com";
$new_password = password_hash("123456", PASSWORD_DEFAULT);

$sql = "UPDATE usuarios SET password = :p WHERE email = :e";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ":p" => $new_password,
    ":e" => $email
]);

echo "Contraseña del admin actualizada";
?>