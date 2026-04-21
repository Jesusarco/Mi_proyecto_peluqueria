<?php
session_start();
require_once "../config/database.php";
if ($_SESSION['rol'] != 'admin') { exit("No autorizado"); }

$esDashboard = true; 
include "../includes/header.php";

// CONSULTAS COMPLETAS
$productos = $conn->query("SELECT id, nombre, precio, stock FROM productos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$citas = $conn->query("SELECT c.id, u.nombre as cliente, s.nombre as servicio, c.fecha, c.hora 
                        FROM citas c
                        JOIN usuarios u ON c.usuario_id = u.id
                        JOIN servicios s ON c.servicio_id = s.id
                        ORDER BY c.fecha DESC, c.hora DESC")->fetchAll(PDO::FETCH_ASSOC);
$pedidos = $conn->query("SELECT p.id, u.nombre as cliente, p.total, p.fecha 
                         FROM pedidos p
                         JOIN usuarios u ON p.usuario_id = u.id
                         ORDER BY p.fecha DESC")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $conn->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$totalPedidos = $conn->query("SELECT COUNT(*) FROM pedidos")->fetchColumn() ?: 0;
$totalIngresos = $conn->query("SELECT SUM(total) FROM pedidos")->fetchColumn() ?: 0;
?>

<style>
    /* Estilos para tablas con letra GRANDE */
    .salon-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 1.1rem;  /* Tamaño base más grande */
    }
    .salon-table th {
        text-align: left;
        padding: 14px 12px;
        border-bottom: 2px solid #eee;
        color: #555;
        text-transform: uppercase;
        font-size: 0.95rem;  /* Cabecera legible */
        letter-spacing: 0.5px;
    }
    .salon-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #f0f0f0;
        color: var(--primary-color);
        font-size: 1rem;      /* Texto de celdas grande */
    }
    .salon-table tr:hover { background-color: #fcfcfc; }
    
    .btn-eliminar {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-block;
    }
    .btn-eliminar:hover { background: #c0392b; }
    
    /* Formulario nuevo producto */
    .form-nuevo-producto {
        display: none;
        background: #f9f9f9;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #eee;
    }
    .form-nuevo-producto.visible {
        display: block;
    }
    .form-group {
        margin-bottom: 18px;
    }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: bold;
        font-size: 0.95rem;
    }
    .form-group input {
        width: 100%;
        padding: 10px;
        font-size: 0.95rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .salon-btn {
        font-size: 0.95rem;
        padding: 8px 18px;
    }
    /* Clases para mostrar/ocultar secciones */
    .seccion-tabla {
        transition: all 0.2s ease;
    }
    .seccion-tabla.oculto {
        display: none;
    }
    /* Ajuste para que las tarjetas no se vean apretadas */
    .salon-card h4 {
        font-size: 1.3rem;
        margin-bottom: 15px;
    }
    .stat-card p {
        font-size: 2.2rem !important;
    }
</style>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div style="padding: 0 25px 20px;"><h5 style="color:var(--accent-color)">ADMIN</h5></div>
        <nav>
            <a href="#" class="sidebar-link filter-link" data-tabla="todos"> Todos</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="productos"> Productos</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="citas"> Citas</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="pedidos"> Pedidos</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="usuarios"> Usuarios</a>
            <hr style="border-color:#333">
        </nav>
    </aside>

    <div class="content-area">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h2 style="margin:0">Panel de Control</h2>
            <span style="color:#888"><?= date('d M, Y') ?></span>
        </div>

        <!-- Tarjetas de estadísticas -->
        <div class="salon-grid">
            <div class="salon-card stat-card">
                <small>PEDIDOS TOTALES</small>
                <p style="font-size:2.5rem; font-weight:bold; margin:10px 0"><?= $totalPedidos ?></p>
            </div>
            <div class="salon-card stat-card">
                <small>INGRESOS TOTALES</small>
                <p style="font-size:2.5rem; font-weight:bold; color:var(--accent-color); margin:10px 0"><?= number_format($totalIngresos, 2) ?> €</p>
            </div>
        </div>

        <!-- TABLA PRODUCTOS -->
        <div id="tabla-productos" class="salon-card seccion-tabla" style="margin-top: 30px;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
                <h2> Productos</h2>
                <button id="btnMostrarForm" class="salon-btn salon-btn-accent"> Añadir producto</button>
            </div>
            
            <div id="formProducto" class="form-nuevo-producto">
                <h5>Nuevo producto</h5>
                <form action="../ajax/crear_producto.php" method="POST">
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Precio (€):</label>
                        <input type="number" step="0.01" name="precio" required>
                    </div>
                    <div class="form-group">
                        <label>Stock:</label>
                        <input type="number" name="stock" value="0" required>
                    </div>
                    <button type="submit" class="salon-btn salon-btn-primary">Guardar producto</button>
                    <button type="button" id="btnCancelarForm" class="salon-btn salon-btn-light">Cancelar</button>
                </form>
            </div>

            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= number_format($p['precio'], 2) ?> €</td>
                        <td><?= $p['stock'] ?></td>
                        <td><a href="../ajax/eliminar_producto.php?id=<?= $p['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- TABLA CITAS -->
        <div id="tabla-citas" class="salon-card seccion-tabla" style="margin-top: 30px;">
            <h2> Citas</h2>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Cliente</th><th>Servicio</th><th>Fecha</th><th>Hora</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($citas as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['cliente']) ?></td>
                        <td><?= htmlspecialchars($c['servicio']) ?></td>
                        <td><?= $c['fecha'] ?></td>
                        <td><?= $c['hora'] ?></td>
                        <td><a href="../ajax/eliminar_cita.php?id=<?= $c['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar esta cita?')">Eliminar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- TABLA PEDIDOS -->
        <div id="tabla-pedidos" class="salon-card seccion-tabla" style="margin-top: 30px;">
            <h2> Pedidos</h2>
            <table class="salon-table">
                <thead>
                    <tr><th>ID Pedido</th><th>Cliente</th><th>Total</th><th>Fecha</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($pedidos as $ped): ?>
                    <tr>
                        <td>#<?= $ped['id'] ?></td>
                        <td><?= htmlspecialchars($ped['cliente']) ?></td>
                        <td><?= number_format($ped['total'], 2) ?> €</td>
                        <td><?= date('d/m/Y H:i', strtotime($ped['fecha'])) ?></td>
                        <td><a href="../ajax/eliminar_pedido.php?id=<?= $ped['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este pedido?')">Eliminar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- TABLA USUARIOS -->
        <div id="tabla-usuarios" class="salon-card seccion-tabla" style="margin-top: 30px;">
            <h2> Usuarios registrados</h2>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= $u['rol'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Mostrar/ocultar formulario de nuevo producto
    const btnMostrar = document.getElementById('btnMostrarForm');
    const formDiv = document.getElementById('formProducto');
    const btnCancelar = document.getElementById('btnCancelarForm');
    
    if (btnMostrar) {
        btnMostrar.addEventListener('click', () => {
            formDiv.classList.toggle('visible');
        });
    }
    if (btnCancelar) {
        btnCancelar.addEventListener('click', () => {
            formDiv.classList.remove('visible');
        });
    }

    // Filtrar tablas y cambio de clase activa en el menú
    const filterLinks = document.querySelectorAll('.filter-link');
    const secciones = {
        productos: document.getElementById('tabla-productos'),
        citas: document.getElementById('tabla-citas'),
        pedidos: document.getElementById('tabla-pedidos'),
        usuarios: document.getElementById('tabla-usuarios')
    };

    function mostrarTodas() {
        for (let key in secciones) {
            if (secciones[key]) secciones[key].classList.remove('oculto');
        }
    }

    function ocultarTodas() {
        for (let key in secciones) {
            if (secciones[key]) secciones[key].classList.add('oculto');
        }
    }

    function setActiveLink(link) {
        filterLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
    }

    filterLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const tabla = link.getAttribute('data-tabla');
            if (tabla === 'todos') {
                mostrarTodas();
            } else {
                ocultarTodas();
                if (secciones[tabla]) {
                    secciones[tabla].classList.remove('oculto');
                }
            }
            setActiveLink(link);
        });
    });

    // Al cargar, mostrar todas y activar Dashboard si no hay activo
    mostrarTodas();
    if (!document.querySelector('.filter-link.active')) {
        const dashboardLink = document.querySelector('.filter-link[data-tabla="todos"]');
        if (dashboardLink) dashboardLink.classList.add('active');
    }
</script>

<?php include "../includes/footer.php"; ?>