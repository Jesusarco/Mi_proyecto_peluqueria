<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit();
}

include "../includes/header.php";
?>

<h1 style="color: var(--primary-color); border-bottom: 2px solid var(--accent-color); padding-bottom: 10px;">Bienvenido a la peluquería</h1>

<div class="salon-grid">
    <section class="salon-card">
        <h3>Sobre nosotros</h3>
        <p>Somos una peluquería profesional dedicada a resaltar tu mejor estilo con los mejores especialistas del sector...</p>
    </section>

    <section class="salon-card">
        <h3>Servicios</h3>
        <ul style="line-height: 1.8;">
            <li>Corte de caballero y señora</li>
            <li>Tinte y mechas</li>
            <li>Peinado y moldeado</li>
        </ul>
    </section>
</div>

<section class="salon-card" style="margin-top: 25px;">
    <h3>Contacto</h3>
    <p>Teléfono: <strong>123 456 789</strong></p>

    <form style="max-width: 500px;">
        <input type="text" class="salon-input" placeholder="Tu Nombre">
        <textarea class="salon-input" placeholder="¿En qué podemos ayudarte?" rows="3"></textarea>
        <button class="salon-btn salon-btn-primary">Enviar Mensaje</button>
    </form>
</section>

<?php include "../includes/footer.php"; ?>