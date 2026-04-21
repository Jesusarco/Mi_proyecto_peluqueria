<?php
require_once "../config/database.php";

try {
    $sql = "SELECT * FROM usuarios";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>";
    print_r($usuarios);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>