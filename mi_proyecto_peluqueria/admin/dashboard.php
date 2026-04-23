<?php
session_start();
require_once "../config/database.php";
if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) { 
    exit("No autorizado"); 
}

$esDashboard = true; 
include "../includes/header.php";

// Consultas (sin administradores)
$productos = $conn->query("SELECT id, nombre, descripcion, precio, stock, imagen, destacado FROM productos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$servicios = $conn->query("SELECT id, nombre, descripcion, precio, duracion FROM servicios WHERE activo = 1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$citas = $conn->query("
    SELECT c.id, u.nombre as cliente, s.nombre as servicio, c.fecha, c.hora, c.estado, c.notas 
    FROM citas c
    JOIN usuarios u ON c.usuario_id = u.id
    JOIN servicios s ON c.servicio_id = s.id
    WHERE u.activo = 1 AND c.activo = 1
    ORDER BY c.fecha DESC, c.hora DESC
")->fetchAll(PDO::FETCH_ASSOC);
$pedidos = $conn->query("
    SELECT p.id, u.nombre as cliente, p.total, p.fecha,
           GROUP_CONCAT(pr.nombre ORDER BY pr.id SEPARATOR ', ') as productos,
           GROUP_CONCAT(dp.cantidad ORDER BY pr.id SEPARATOR ', ') as cantidades
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
    LEFT JOIN productos pr ON dp.producto_id = pr.id
    WHERE u.activo = 1 AND p.activo = 1
    GROUP BY p.id
    ORDER BY p.fecha DESC
")->fetchAll(PDO::FETCH_ASSOC);
$gastos = $conn->query("SELECT g.id, g.descripcion, g.categoria, g.cantidad, g.fecha, u.nombre as registrado_por
                        FROM gastos g
                        LEFT JOIN usuarios u ON g.usuario_id = u.id
                        ORDER BY g.fecha DESC")->fetchAll(PDO::FETCH_ASSOC);
$usuarios_clientes = $conn->query("SELECT id, nombre, email, fecha_creacion FROM usuarios WHERE rol = 'cliente' AND activo = 1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas
$totalPedidos = $conn->query("SELECT COUNT(*) FROM pedidos")->fetchColumn() ?: 0;
$totalIngresos = $conn->query("SELECT SUM(total) FROM pedidos")->fetchColumn() ?: 0;
?>

<style>
    /* (Los mismos estilos que antes, sin cambios) */
    .salon-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 1rem;
        table-layout: auto;
    }
    .salon-table th, .salon-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #f0f0f0;
        text-align: left;
        vertical-align: top;
    }
    .salon-table th {
        background-color: #f8f8f8;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .btn-eliminar, .btn-crear {
        background: #e74c3c;
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
    .btn-crear {
        background: var(--accent-color);
    }
    .btn-crear:hover { background: #b8941a; }
    .btn-eliminar:hover { background: #c0392b; }

    /* Botones de editar y eliminar unificados */
    .btn-editar, .btn-editar-servicio, .btn-editar-gasto, .btn-eliminar {
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
    .btn-editar, .btn-editar-servicio, .btn-editar-gasto {
        background: #3498db;
    }
    .btn-editar:hover, .btn-editar-servicio:hover, .btn-editar-gasto:hover {
        background: #2980b9;
    }
    
    .form-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }
    .form-overlay.visible {
        display: flex;
    }
    .form-container {
        background: white;
        padding: 25px 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 550px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        box-sizing: border-box;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 8px 10px;
        font-size: 0.9rem;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 20px;
    }
    .salon-btn {
        font-size: 0.9rem;
        padding: 8px 16px;
        border-radius: 6px;
    }
    .seccion-tabla {
        overflow-x: auto;
        margin-top: 30px;
    }
    .seccion-tabla.oculto {
        display: none;
    }
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .stat-card p {
        font-size: 2rem !important;
    }
    .salon-card {
        padding: 20px;
    }
    .imagen-tabla {
        max-width: 60px;
        max-height: 60px;
    }
    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .checkbox-group input {
        width: auto;
    }

    .btn-editar, .btn-editar-servicio, .btn-editar-gasto {
        background: #3498db;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-block;
    }
    .btn-editar:hover { background: #2980b9; }

    /* Cabecera de cada tabla con título, buscador centrado y botón */
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }
    .admin-header .filtro-wrapper {
        flex: 1;
        display: flex;
        justify-content: center;
    }
    .admin-header .filtro-tabla {
        width: 260px;
        max-width: 100%;
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 30px;
        font-size: 0.85rem;
        outline: none;
        transition: all 0.2s;
    }
    .admin-header .filtro-tabla:focus {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 2px rgba(212,175,55,0.2);
    }
    .admin-header .btn-crear {
        margin-left: auto;
    }
    /* Para pantallas pequeñas, que no se rompa */
    @media (max-width: 700px) {
        .admin-header {
            flex-direction: column;
            align-items: stretch;
        }
        .admin-header .filtro-wrapper {
            order: 2;
            margin: 10px 0;
        }
        .admin-header .btn-crear {
            order: 3;
            align-self: flex-end;
        }
    }
</style>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div style="padding: 0 25px 20px;"><h5 style="color:var(--accent-color)">ADMIN</h5></div>
        <nav>
            <a href="#" class="sidebar-link filter-link" data-tabla="todos">Todos</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="productos">Productos</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="servicios">Servicios</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="citas">Citas</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="pedidos">Pedidos</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="gastos">Gastos</a>
            <a href="#" class="sidebar-link filter-link" data-tabla="usuarios">Clientes</a>
            <hr style="border-color:#333">
            <?php if ($_SESSION['rol'] == 'superadmin'): ?>
                <a href="gestionar_admins.php" class="sidebar-link"> Gestionar Admins</a>
            <?php endif; ?>
        </nav>
    </aside>

    <div class="content-area">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h2 style="margin:0">Panel de Control</h2>
            <span style="color:#888"><?= date('d M, Y') ?></span>
        </div>

        <div class="salon-grid">
            <div class="salon-card stat-card">
                <small>PEDIDOS TOTALES</small>
                <p><?= $totalPedidos ?></p>
            </div>
            <div class="salon-card stat-card">
                <small>INGRESOS TOTALES</small>
                <p><?= number_format($totalIngresos, 2) ?> €</p>
            </div>
        </div>

        <!-- PRODUCTOS -->
        <div id="tabla-productos" class="salon-card seccion-tabla">
            <div class="admin-header">
                <h4>Productos</h4>
                <div class="filtro-wrapper">
                    <input type="text" class="filtro-tabla" data-tabla="productos" placeholder=" Buscar...">
                </div>
                <button id="btnProducto" class="btn-crear">+ Nuevo producto</button>
            </div>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Stock</th><th>Destacado</th><th>Imagen</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($productos as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><?= htmlspecialchars(substr($p['descripcion'] ?? '', 0, 80)) ?>...</td>
                        <td><?= number_format($p['precio'], 2) ?> €</td>
                        <td><?= $p['stock'] ?></td>
                        <td><?= $p['destacado'] ? 'Sí' : 'No' ?></td>
                        <td><?= $p['imagen'] ? '<img src="../uploads/'.$p['imagen'].'" class="imagen-tabla">' : '-' ?></td>
                        <td>
                            <button class="btn-editar" data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '') ?>" data-precio="<?= $p['precio'] ?>" data-stock="<?= $p['stock'] ?>" data-destacado="<?= $p['destacado'] ?>" data-imagen="<?= htmlspecialchars($p['imagen'] ?? '') ?>">Editar</button>
                            <a href="../ajax/eliminar_producto.php?id=<?= $p['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- SERVICIOS -->
        <div id="tabla-servicios" class="salon-card seccion-tabla">
            <div class="admin-header">
                <h4>Servicios</h4>
                <div class="filtro-wrapper">
                    <input type="text" class="filtro-tabla" data-tabla="servicios" placeholder=" Buscar...">
                </div>
                <button id="btnServicio" class="btn-crear">+ Nuevo servicio</button>
            </div>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Duración (min)</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($servicios as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['nombre']) ?></td>
                        <td><?= htmlspecialchars(substr($s['descripcion'] ?? '', 0, 80)) ?></td>
                        <td><?= number_format($s['precio'], 2) ?> €</td>
                        <td><?= $s['duracion'] ?></td>
                        <td>
                            <button class="btn-editar-servicio" data-id="<?= $s['id'] ?>" data-nombre="<?= htmlspecialchars($s['nombre']) ?>" data-descripcion="<?= htmlspecialchars($s['descripcion'] ?? '') ?>" data-precio="<?= $s['precio'] ?>" data-duracion="<?= $s['duracion'] ?>">Editar</button>
                            <a href="../ajax/eliminar_servicio.php?id=<?= $s['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar servicio?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- CITAS -->
        <div id="tabla-citas" class="salon-card seccion-tabla">
            <div class="admin-header">
                <h4>Citas</h4>
                <div class="filtro-wrapper">
                    <input type="text" class="filtro-tabla" data-tabla="citas" placeholder=" Buscar...">
                </div>
                <!-- No hay botón, se puede dejar vacío o añadir un div invisible para mantener el centrado -->
                <div style="width: 130px;"></div> <!-- opcional para equilibrar -->
            </div>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Cliente</th><th>Servicio</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Notas</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($citas as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['cliente']) ?></td>
                        <td><?= htmlspecialchars($c['servicio']) ?></td>
                        <td><?= $c['fecha'] ?></td>
                        <td><?= $c['hora'] ?></td>
                        <td><?= $c['estado'] ?></td>
                        <td><?= htmlspecialchars(substr($c['notas'] ?? '', 0, 50)) ?></td>
                        <td><a href="../ajax/eliminar_cita.php?id=<?= $c['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Cancelar cita?')">Cancelar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- PEDIDOS (opcional, se puede comentar si no hay tienda) -->
        <div id="tabla-pedidos" class="salon-card seccion-tabla">
            <div class="admin-header">
                <h4>Pedidos</h4>
                <div class="filtro-wrapper">
                    <input type="text" class="filtro-tabla" data-tabla="pedidos" placeholder=" Buscar...">
                </div>
                <div style="width: 130px;"></div>
            </div>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Cliente</th><th>Productos</th><th>Cantidad</th><th>Total</th><th>Fecha</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($pedidos as $ped): ?>
                    <tr>
                        <td>#<?= $ped['id'] ?></td>
                        <td><?= htmlspecialchars($ped['cliente']) ?></td>
                        <td><?= htmlspecialchars($ped['productos'] ?? 'Sin productos') ?></td>
                        <td><?= htmlspecialchars($ped['cantidades'] ?? '-') ?></td>
                        <td><?= number_format($ped['total'], 2) ?> €</td>
                        <td><?= date('d/m/Y H:i', strtotime($ped['fecha'])) ?></td>
                        <a href="../ajax/eliminar_pedido.php?id=<?= $ped['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Archivar este pedido? Se ocultará del dashboard pero se conservará en la base de datos.')">Archivar</a>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- GASTOS -->
        <div id="tabla-gastos" class="salon-card seccion-tabla">
            <div class="admin-header">
                <h4>Gastos</h4>
                <div class="filtro-wrapper">
                    <input type="text" class="filtro-tabla" data-tabla="gastos" placeholder=" Buscar...">
                </div>
                <button id="btnServicio" class="btn-crear">+ Nuevo gasto</button>
            </div>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Descripción</th><th>Categoría</th><th>Cantidad</th><th>Fecha</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($gastos as $g): ?>
                    <tr>
                        <td><?= $g['id'] ?></td>
                        <td><?= htmlspecialchars($g['descripcion']) ?></td>
                        <td><?= htmlspecialchars($g['categoria'] ?? '-') ?></td>
                        <td><?= number_format($g['cantidad'], 2) ?> €</td>
                        <td><?= date('d/m/Y', strtotime($g['fecha'])) ?></td>
                        <td>
                            <button class="btn-editar-gasto" data-id="<?= $g['id'] ?>" data-descripcion="<?= htmlspecialchars($g['descripcion']) ?>" data-categoria="<?= htmlspecialchars($g['categoria'] ?? '') ?>" data-cantidad="<?= $g['cantidad'] ?>">Editar</button>
                            <a href="../ajax/eliminar_gasto.php?id=<?= $g['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar gasto?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- CLIENTES -->
        <div id="tabla-usuarios" class="salon-card seccion-tabla">
            <div class="admin-header">
                <h4>Clientes</h4>
                <div class="filtro-wrapper">
                    <input type="text" class="filtro-tabla" data-tabla="clientes" placeholder=" Buscar...">
                </div>
                <div style="width: 130px;"></div>
            </div>
            <table class="salon-table">
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Fecha registro</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios_clientes as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= date('d/m/Y', strtotime($u['fecha_creacion'])) ?></td>
                        <td><a href="../ajax/desactivar_usuario.php?id=<?= $u['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Desactivar este cliente?')">Desactivar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- OVERLAY PONER NUEVO PRODUCTO -->
<div id="overlayProducto" class="form-overlay">
    <div class="form-container">
        <h3>Nuevo producto</h3>
        <form action="../ajax/crear_producto.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Precio (euros) *</label>
                <input type="number" step="0.01" name="precio" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" value="0">
            </div>
            <div class="form-group checkbox-group">
                <label>Destacado</label>
                <input type="checkbox" name="destacado" value="1">
            </div>
            <div class="form-group">
                <label>Imagen (subir archivo)</label>
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/jpg,image/gif">
                <small style="color:#888; display:block; margin-top:4px;">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
            </div>
            <div class="form-actions">
                <button type="button" class="salon-btn salon-btn-light cerrar">Cancelar</button>
                <button type="submit" class="salon-btn salon-btn-accent">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- OVERLAY SERVICIO -->
<div id="overlayServicio" class="form-overlay">
    <div class="form-container">
        <h3>Nuevo servicio</h3>
        <form action="../ajax/crear_servicio.php" method="POST">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" required>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Precio (euros) *</label>
                <input type="number" step="0.01" name="precio" required>
            </div>
            <div class="form-group">
                <label>Duración (minutos) *</label>
                <input type="number" name="duracion" required>
            </div>
            <div class="form-actions">
                <button type="button" class="salon-btn salon-btn-light cerrar">Cancelar</button>
                <button type="submit" class="salon-btn salon-btn-accent">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- OVERLAY GASTO -->
<div id="overlayGasto" class="form-overlay">
    <div class="form-container">
        <h3>Nuevo gasto</h3>
        <form action="../ajax/crear_gasto.php" method="POST">
            <div class="form-group">
                <label>Descripción *</label>
                <input type="text" name="descripcion" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria">
                    <option value="">Seleccionar</option>
                    <option value="Alquiler">Alquiler</option>
                    <option value="Material">Material</option>
                    <option value="Sueldos">Sueldos</option>
                    <option value="Publicidad">Publicidad</option>
                    <option value="Servicios">Servicios (luz, agua, etc.)</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad (euros) *</label>
                <input type="number" step="0.01" name="cantidad" required>
            </div>
            <div class="form-actions">
                <button type="button" class="salon-btn salon-btn-light cerrar">Cancelar</button>
                <button type="submit" class="salon-btn salon-btn-accent">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Overlay EDITAR PRODUCTO -->
<div id="overlayEditarProducto" class="form-overlay">
    <div class="form-container">
        <h3>Editar producto</h3>
        <form action="../ajax/actualizar_producto.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="edit_prod_id">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" id="edit_prod_nombre" required>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" id="edit_prod_descripcion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Precio (euros) *</label>
                <input type="number" step="0.01" name="precio" id="edit_prod_precio" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" id="edit_prod_stock">
            </div>
            <div class="form-group checkbox-group">
                <label>Destacado</label>
                <input type="checkbox" name="destacado" id="edit_prod_destacado" value="1">
            </div>
            <div class="form-group">
                <label>Imagen actual</label><br>
                <img id="edit_prod_img_actual" src="" style="max-width: 100px; max-height: 100px; margin-bottom: 10px; display: none;">
                <span id="edit_prod_sin_imagen" style="display: none;">Sin imagen</span>
            </div>
            <div class="form-group">
                <label>Cambiar imagen (opcional)</label>
                <input type="file" name="imagen" accept="image/jpeg,image/png,image/jpg,image/gif">
                <small>Si no selecciona una nueva, se mantiene la actual.</small>
            </div>
            <div class="form-actions">
                <button type="button" class="salon-btn salon-btn-light cerrar-editar">Cancelar</button>
                <button type="submit" class="salon-btn salon-btn-accent">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="overlayEditarServicio" class="form-overlay">
    <div class="form-container">
        <h3>Editar servicio</h3>
        <form action="../ajax/actualizar_servicio.php" method="POST">
            <input type="hidden" name="id" id="edit_serv_id">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" id="edit_serv_nombre" required>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" id="edit_serv_descripcion" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Precio (euros) *</label>
                <input type="number" step="0.01" name="precio" id="edit_serv_precio" required>
            </div>
            <div class="form-group">
                <label>Duración (minutos) *</label>
                <input type="number" name="duracion" id="edit_serv_duracion" required>
            </div>
            <div class="form-actions">
                <button type="button" class="salon-btn salon-btn-light cerrar-editar">Cancelar</button>
                <button type="submit" class="salon-btn salon-btn-accent">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="overlayEditarGasto" class="form-overlay">
    <div class="form-container">
        <h3>Editar gasto</h3>
        <form action="../ajax/actualizar_gasto.php" method="POST">
            <input type="hidden" name="id" id="edit_gasto_id">
            <div class="form-group">
                <label>Descripción *</label>
                <input type="text" name="descripcion" id="edit_gasto_descripcion" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria" id="edit_gasto_categoria">
                    <option value="">Seleccionar</option>
                    <option value="Alquiler">Alquiler</option>
                    <option value="Material">Material</option>
                    <option value="Sueldos">Sueldos</option>
                    <option value="Publicidad">Publicidad</option>
                    <option value="Servicios">Servicios (luz, agua, etc.)</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            <div class="form-group">
                <label>Cantidad (euros) *</label>
                <input type="number" step="0.01" name="cantidad" id="edit_gasto_cantidad" required>
            </div>
            <div class="form-actions">
                <button type="button" class="salon-btn salon-btn-light cerrar-editar">Cancelar</button>
                <button type="submit" class="salon-btn salon-btn-accent">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    const overlays = {
        producto: document.getElementById('overlayProducto'),
        servicio: document.getElementById('overlayServicio'),
        gasto: document.getElementById('overlayGasto')
    };
    const btns = {
        producto: document.getElementById('btnProducto'),
        servicio: document.getElementById('btnServicio'),
        gasto: document.getElementById('btnGasto')
    };
    const cerrarBtns = document.querySelectorAll('.cerrar');

    function abrirOverlay(nombre) {
        if (overlays[nombre]) overlays[nombre].classList.add('visible');
    }
    function cerrarOverlays() {
        for (let key in overlays) {
            if (overlays[key]) overlays[key].classList.remove('visible');
        }
    }

    if (btns.producto) btns.producto.addEventListener('click', () => abrirOverlay('producto'));
    if (btns.servicio) btns.servicio.addEventListener('click', () => abrirOverlay('servicio'));
    if (btns.gasto) btns.gasto.addEventListener('click', () => abrirOverlay('gasto'));

    cerrarBtns.forEach(btn => btn.addEventListener('click', cerrarOverlays));
    window.addEventListener('click', (e) => {
        for (let key in overlays) {
            if (e.target === overlays[key]) cerrarOverlays();
        }
    });

    // Filtros laterales (sin administradores)
    const filterLinks = document.querySelectorAll('.filter-link');
    const secciones = {
        productos: document.getElementById('tabla-productos'),
        servicios: document.getElementById('tabla-servicios'),
        citas: document.getElementById('tabla-citas'),
        pedidos: document.getElementById('tabla-pedidos'),
        gastos: document.getElementById('tabla-gastos'),
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
                if (secciones[tabla]) secciones[tabla].classList.remove('oculto');
            }
            setActiveLink(link);
        });
    });

    mostrarTodas();
    if (!document.querySelector('.filter-link.active')) {
        const todos = document.querySelector('.filter-link[data-tabla="todos"]');
        if (todos) todos.classList.add('active');
    }


// Editar producto
const editProdBtns = document.querySelectorAll('.btn-editar');
const overlayEditProd = document.getElementById('overlayEditarProducto');
if (editProdBtns.length) {
    editProdBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit_prod_id').value = btn.dataset.id;
            document.getElementById('edit_prod_nombre').value = btn.dataset.nombre;
            document.getElementById('edit_prod_descripcion').value = btn.dataset.descripcion;
            document.getElementById('edit_prod_precio').value = btn.dataset.precio;
            document.getElementById('edit_prod_stock').value = btn.dataset.stock;
            document.getElementById('edit_prod_destacado').checked = (btn.dataset.destacado == '1');

            // Mostrar imagen actual
            const imgActual = document.getElementById('edit_prod_img_actual');
            const sinImg = document.getElementById('edit_prod_sin_imagen');
            if (btn.dataset.imagen && btn.dataset.imagen !== '') {
                imgActual.src = '../uploads/' + btn.dataset.imagen;
                imgActual.style.display = 'block';
                sinImg.style.display = 'none';
            } else {
                imgActual.style.display = 'none';
                sinImg.style.display = 'block';
            }

            overlayEditProd.classList.add('visible');
        });
    });
}

// Editar servicio
const editServBtns = document.querySelectorAll('.btn-editar-servicio');
const overlayEditServ = document.getElementById('overlayEditarServicio');
if (editServBtns.length) {
    editServBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit_serv_id').value = btn.dataset.id;
            document.getElementById('edit_serv_nombre').value = btn.dataset.nombre;
            document.getElementById('edit_serv_descripcion').value = btn.dataset.descripcion;
            document.getElementById('edit_serv_precio').value = btn.dataset.precio;
            document.getElementById('edit_serv_duracion').value = btn.dataset.duracion;
            overlayEditServ.classList.add('visible');
        });
    });
}

// Editar gasto
const editGastoBtns = document.querySelectorAll('.btn-editar-gasto');
const overlayEditGasto = document.getElementById('overlayEditarGasto');
if (editGastoBtns.length) {
    editGastoBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit_gasto_id').value = btn.dataset.id;
            document.getElementById('edit_gasto_descripcion').value = btn.dataset.descripcion;
            document.getElementById('edit_gasto_categoria').value = btn.dataset.categoria;
            document.getElementById('edit_gasto_cantidad').value = btn.dataset.cantidad;
            overlayEditGasto.classList.add('visible');
        });
    });
}

// Cerrar overlays de edición
const cerrarEditBtns = document.querySelectorAll('.cerrar-editar');
cerrarEditBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        overlayEditProd.classList.remove('visible');
        overlayEditServ.classList.remove('visible');
        overlayEditGasto.classList.remove('visible');
    });
});
window.addEventListener('click', (e) => {
    if (e.target === overlayEditProd) overlayEditProd.classList.remove('visible');
    if (e.target === overlayEditServ) overlayEditServ.classList.remove('visible');
    if (e.target === overlayEditGasto) overlayEditGasto.classList.remove('visible');
});

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

// Asignar evento a cada filtro
const filtros = document.querySelectorAll('.filtro-tabla');
filtros.forEach(filtro => {
    const tablaId = filtro.getAttribute('data-tabla');
    // Mapear data-tabla al ID real de la tabla contenedora
    let idReal = '';
    switch (tablaId) {
        case 'productos': idReal = 'tabla-productos'; break;
        case 'servicios': idReal = 'tabla-servicios'; break;
        case 'citas': idReal = 'tabla-citas'; break;
        case 'pedidos': idReal = 'tabla-pedidos'; break;
        case 'gastos': idReal = 'tabla-gastos'; break;
        case 'clientes': idReal = 'tabla-usuarios'; break;
        default: idReal = 'tabla-' + tablaId;
    }
    filtro.addEventListener('keyup', () => filtrarTabla(filtro, idReal));
});
</script>