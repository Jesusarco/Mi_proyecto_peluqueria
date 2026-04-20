<?php
session_start();
require_once "../config/database.php";
include "../includes/header.php";

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

// Obtener servicios para el select
$servicios = $conn->query("SELECT id, nombre FROM servicios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Reservar cita</h2>

<div class="salon-form-container" style="margin: 30px 0;">
    <form id="formReserva">
        <label style="font-weight: bold; margin-bottom: 5px; display: block;">Servicio</label>
        <select name="servicio_id" class="salon-input" required>
            <option value="">Seleccione un servicio</option>
            <?php foreach ($servicios as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?></option>
            <?php endforeach; ?>
        </select>

        <label style="font-weight: bold; margin-bottom: 5px; display: block;">Fecha</label>
        <input type="date" name="fecha" class="salon-input" min="<?= date('Y-m-d') ?>" required>

        <label style="font-weight: bold; margin-bottom: 5px; display: block;">Hora</label>
        <select name="hora" class="salon-input" required>
            <option value="10:00">10:00</option>
            <option value="11:00">11:00</option>
            <option value="12:00">12:00</option>
            <option value="16:00">16:00</option>
            <option value="17:00">17:00</option>
            <option value="18:00">18:00</option>
        </select>

        <button type="submit" class="salon-btn salon-btn-primary" style="width: 100%; margin-top: 10px;">Reservar</button>
    </form>
    
    <div id="mensaje" style="margin-top: 15px; text-align: center;"></div>
</div>

<script src="../assets/js/reservas.js"></script>

<?php include "../includes/footer.php"; ?>