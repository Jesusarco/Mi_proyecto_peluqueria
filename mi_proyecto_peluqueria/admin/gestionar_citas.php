<?php
session_start();
require_once "../config/database.php";

// Solo superadmin puede gestionar citas
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: dashboard.php?error=No tienes permisos para gestionar citas");
    exit();
}

$esDashboard = true; 
include "../includes/header.php";

// Consultar citas activas (solo las que no están archivadas)
$citas = $conn->query("
    SELECT c.id, u.nombre as cliente, s.nombre as servicio, s.precio, c.fecha, c.hora, c.estado, c.notas
    FROM citas c
    JOIN usuarios u ON c.usuario_id = u.id
    JOIN servicios s ON c.servicio_id = s.id
    WHERE u.activo = 1 AND c.estado = 'completado'
    ORDER BY c.fecha DESC, c.hora DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .salon-table { width: 100%; border-collapse: collapse; }
    .salon-table th, .salon-table td { padding: 12px 10px; border-bottom: 1px solid #eee; text-align: left; }
    .salon-table th { background: #f8f8f8; }
    .btn-editar, .btn-editar-servicio, .btn-editar-gasto, .btn-eliminar, .btn-completado {
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-block;
        margin-right: 5px;
        font-family: inherit;
        line-height: normal;
    }
    .btn-eliminar {
        background: #e74c3c;
    }
    .btn-eliminar:hover {
        background: #c0392b;
    }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .filtro-wrapper {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .filtro-tabla:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 2px rgba(212,175,55,0.2);
    }

    .filtro-tabla {
        width: 260px;
        max-width: 100%;
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 30px;
        font-size: 0.85rem;
        outline: none;
        transition: all 0.2s;
    }

    .admin-header-table {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
</style>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div style="padding: 0 25px 20px;"><h2 style="color:#efefef">👤 <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></h2></div>
        <nav>
            <a href="dashboard.php" class="sidebar-link">Dashboard</a>
            <a href="gestionar_citas.php" class="sidebar-link active">Gestionar Citas</a>
            <hr style="border-color:#333">
        </nav>
    </aside>

    <div class="content-area">
        <div class="admin-header">
            <h2>Gestión de Citas</h2>
        </div>

        <div class="salon-card">
            <?php if (isset($_GET['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <table class="salon-table">
                <div class ="admin-header-table">
                    <div class="filtro-wrapper">
                        <input type="text" class="filtro-tabla" data-tabla="citas" placeholder=" Buscar...">
                    </div>
                </div>
                <thead>
                    <tr>
                        <th>ID</th><th>Cliente</th><th>Servicio</th><th>Precio</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($citas as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['cliente']) ?></td>
                        <td><?= htmlspecialchars($c['servicio']) ?></td>
                        <td><?= number_format($c['precio'], 2) ?> €</td>
                        <td><?= $c['fecha'] ?></td>
                        <td><?= $c['hora'] ?></td>
                        <td><?= $c['estado'] ?></td>
                        <td><a href="../ajax/eliminar_cita.php?id=<?= $c['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar cita?')">Eliminar</a></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($citas) == 0): ?>
                        <tr><td colspan="7" style="text-align:center;">No hay citas activas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Filtrar tablas en tiempo real
    function filtrarTabla(input, tablaId) {
        const filtro = input.value.toLowerCase();
        const tabla = document.getElementById(tablaId);
        if (!tabla) return;
        const filas = tabla.querySelectorAll('tbody tr');
        filas.forEach(fila => {
            const texto = fila.innerText.toLowerCase();
            if (texto.includes(filtro)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    }
</script>