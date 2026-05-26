<?php
    // Iniciar sesión y conectar a la base de datos
    session_start();
    require_once "../config/database.php";

    // Verificar que el usuario tenga rol de administrador o superadministrador
    if (!in_array($_SESSION['rol'], ['admin', 'superadmin'])) { 
        exit("No autorizado"); 
    }

    // Indicar que estamos en el panel de administración (modifica el estilo desheader)
    $esDashboard = true; 
    include "../includes/header.php";

    // ========== CONSULTAS PRINCIPALES ==========

    // Obtener todos los productos ordenados por ID descendente
    $productos = $conn->query("SELECT id, nombre, descripcion, precio, stock, imagen, destacado FROM productos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todos los servicios ordenados por ID descendente
    $servicios = $conn->query("SELECT id, nombre, descripcion, precio, duracion, imagen FROM servicios ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Citas en estado 'reservado' (activas)
    $citas = $conn->query("
        SELECT c.id, u.nombre as cliente, s.nombre as servicio, s.precio, c.fecha, c.hora, c.estado
        FROM citas c
        JOIN usuarios u ON c.usuario_id = u.id
        JOIN servicios s ON c.servicio_id = s.id
        WHERE c.estado = 'reservado'
        ORDER BY c.fecha DESC, c.hora DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Pedidos en estado 'pedido' (pendientes) con sus productos agrupados
    $pedidos = $conn->query("
        SELECT p.id, u.nombre as cliente, p.total, p.fecha,
            GROUP_CONCAT(pr.nombre ORDER BY pr.id SEPARATOR ', ') as productos,
            GROUP_CONCAT(dp.cantidad ORDER BY pr.id SEPARATOR ', ') as cantidades
        FROM pedidos p
        JOIN usuarios u ON p.usuario_id = u.id
        LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
        LEFT JOIN productos pr ON dp.producto_id = pr.id
        WHERE u.activo = 1 and p.estado = 'pedido'
        GROUP BY p.id
        ORDER BY p.fecha DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Gastos registrados, incluyendo quién los registró
    $gastos = $conn->query("SELECT g.id, g.descripcion, g.categoria, g.cantidad, g.fecha, u.nombre as registrado_por
                            FROM gastos g
                            LEFT JOIN usuarios u ON g.usuario_id = u.id
                            ORDER BY g.fecha DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Clientes activos (solo superadmin puede ver la contraseña hasheada)
    $campos = "id, nombre, email, fecha_creacion";
    if ($_SESSION['rol'] == 'superadmin') {
        $campos .= ", password";
    }
    $usuarios_clientes = $conn->query("SELECT $campos FROM usuarios WHERE rol = 'cliente' AND activo = 1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    // ========== ESTADÍSTICAS ==========

    // Total de pedidos pendientes
    $totalPedidos = $conn->query("SELECT COUNT(*) FROM pedidos WHERE pedidos.estado = 'pedido'")->fetchColumn() ?: 0;

    // Ingresos por pedidos entregados + citas completadas
    $totalPedidosSum = $conn->query("SELECT SUM(total) FROM pedidos WHERE pedidos.estado = 'entregado'")->fetchColumn() ?: 0;
    $totalCitasCompletadas = $conn->query("
        SELECT SUM(s.precio) 
        FROM citas c 
        JOIN servicios s ON c.servicio_id = s.id 
        WHERE c.estado = 'completado'
    ")->fetchColumn() ?: 0;
    $totalIngresos = $totalPedidosSum + $totalCitasCompletadas;

    // Total de gastos
    $totalGastos = $conn->query("SELECT SUM(cantidad) FROM gastos")->fetchColumn() ?: 0;
?>

        <!-- Hoja de estilos específica del dashboard -->
        <link rel="stylesheet" href="../assets/css/dashboard.css">

        <div class="dashboard-wrapper">
            <!-- BARRA LATERAL (menú de navegación) -->
            <aside class="sidebar">
                <div style="padding: 0 25px 20px;"><h2 style="color:#efefef"> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></h2></div>
                <nav>
                    <!-- Enlaces para filtrar las tablas -->
                    <a href="#" class="sidebar-link filter-link" data-tabla="todos">Todos</a>
                    <a href="#" class="sidebar-link filter-link" data-tabla="productos">Productos</a>
                    <a href="#" class="sidebar-link filter-link" data-tabla="servicios">Servicios</a>
                    <a href="#" class="sidebar-link filter-link" data-tabla="citas">Citas</a>
                    <a href="#" class="sidebar-link filter-link" data-tabla="pedidos">Pedidos</a>
                    <a href="#" class="sidebar-link filter-link" data-tabla="gastos">Gastos</a>
                    <a href="#" class="sidebar-link filter-link" data-tabla="usuarios">Clientes</a>
                    <hr style="border-color:#333">
                    
                    <!-- Enlaces a páginas adicionales de gestión (solo superadmin ve la de administradores) -->
                    <?php if ($_SESSION['rol'] == 'superadmin'): ?>
                        <a href="gestionar_admins.php" class="sidebar-link"> Gestionar Admins</a>
                    <?php endif; ?>
                    <a href="gestionar_citas.php" class="sidebar-link"> Gestionar Citas</a>
                    <a href="gestionar_pedidos.php" class="sidebar-link"> Gestionar Pedidos</a>
                </nav>
            </aside>

            <!-- ÁREA DE CONTENIDO PRINCIPAL -->
            <div class="content-area">
                <!-- Cabecera del panel -->
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
                    <h2 style="margin:0">Panel de Control</h2>
                    <span style="color:#888"><?= date('d M, Y') ?></span>
                </div>

                <!-- Tarjetas de estadísticas -->
                <div class="salon-grid">
                    <div class="salon-card stat-card">
                        <small>PEDIDOS PENDIENTES</small>
                        <p><?= $totalPedidos ?></p>
                    </div>
                    <div class="salon-card stat-card">
                        <small>INGRESOS TOTALES</small>
                        <p><?= number_format($totalIngresos, 2) ?> €</p>
                    </div>
                    <div class="salon-card stat-card">
                        <small>GASTOS TOTALES</small>
                        <p><?= number_format($totalGastos, 2) ?> €</p>
                    </div>
                </div>

                <!-- TABLA DE PRODUCTOS -->
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
                                <!-- Mostrar imagen si existe -->
                                <td><?= $p['imagen'] ? '<img src="../uploads/'.$p['imagen'].'" class="imagen-tabla">' : '-' ?></td>
                                <td>
                                    <!-- Botón Editar (abre overlay con datos precargados) -->
                                    <button class="btn-editar" data-id="<?= $p['id'] ?>" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '') ?>" data-precio="<?= $p['precio'] ?>" data-stock="<?= $p['stock'] ?>" data-destacado="<?= $p['destacado'] ?>" data-imagen="<?= htmlspecialchars($p['imagen'] ?? '') ?>">Editar</button>
                                    <!-- Botón Eliminar -->
                                    <a href="../ajax/eliminar.php?action=producto&id=<?= $p['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE SERVICIOS (análoga a productos) -->
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
                            <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Duración (min)</th><th>Imagen</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($servicios as $s): ?>
                            <tr>
                                <td><?= $s['id'] ?></td>
                                <td><?= htmlspecialchars($s['nombre']) ?></td>
                                <td><?= htmlspecialchars(substr($s['descripcion'] ?? '', 0, 80)) ?></td>
                                <td><?= number_format($s['precio'], 2) ?> €</td>
                                <td><?= $s['duracion'] ?></td>
                                <td><?= $s['imagen'] ? '<img src="../uploads/'.$s['imagen'].'" class="imagen-tabla">' : '-' ?></td>
                                <td>
                                    <button class="btn-editar-servicio" data-id="<?= $s['id'] ?>" data-nombre="<?= htmlspecialchars($s['nombre']) ?>" data-descripcion="<?= htmlspecialchars($s['descripcion'] ?? '') ?>" data-precio="<?= $s['precio'] ?>" data-duracion="<?= $s['duracion'] ?>" data-imagen="<?= htmlspecialchars($s['imagen'] ?? '') ?>">Editar</button>
                                    <a href="../ajax/eliminar.php?action=servicio&id=<?= $s['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar servicio?')">Eliminar</a>
                                </tr>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE CITAS (solo las reservadas) -->
                <div id="tabla-citas" class="salon-card seccion-tabla">
                    <div class="admin-header">
                        <h4>Citas</h4>
                        <div class="filtro-wrapper">
                            <input type="text" class="filtro-tabla" data-tabla="citas" placeholder=" Buscar...">
                        </div>
                        <div style="width: 130px;"></div> <!-- para mantener el equilibrio visual -->
                    </div>
                    <table class="salon-table">
                        <thead>
                            <tr><th>ID</th><th>Cliente</th><th>Servicio</th><th>Precio</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Acciones</th></tr>
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
                                <td>
                                    <button class="btn-editar-cita" data-id="<?= $c['id'] ?>" data-fecha="<?= $c['fecha'] ?>" data-hora="<?= $c['hora'] ?>">Editar</button>
                                    <a href="../ajax/archivar.php?action=cita&id=<?= $c['id'] ?>" class="btn-completado" onclick="return confirm('¿Marcar cita como completada?')">Completar</a>
                                    <a href="../ajax/eliminar.php?action=cita&id=<?= $c['id'] ?>" class="btn-eliminar" onclick="return confirm('Cancelar cita?')">Cancelar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE PEDIDOS (pendientes) -->
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
                                <td>
                                    <a href="../ajax/archivar.php?action=pedido&id=<?= $ped['id'] ?>" class="btn-completado" onclick="return confirm('Completar este pedido?')">Completada</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE GASTOS -->
                <div id="tabla-gastos" class="salon-card seccion-tabla">
                    <div class="admin-header">
                        <h4>Gastos</h4>
                        <div class="filtro-wrapper">
                            <input type="text" class="filtro-tabla" data-tabla="gastos" placeholder=" Buscar...">
                        </div>
                        <button id="btnGasto" class="btn-crear">+ Nuevo gasto</button>
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
                                    <a href="../ajax/eliminar.php?action=gasto&id=<?= $g['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar gasto?')">Eliminar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE CLIENTES -->
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
                            <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Fecha registro</th><th>Acciones</th></td>
                        </thead>
                        <tbody>
                            <?php foreach($usuarios_clientes as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nombre']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= date('d/m/Y', strtotime($u['fecha_creacion'])) ?></td>
                                <td>
                                    <button class="btn-editar-usuario" data-id="<?= $u['id'] ?>" data-nombre="<?= htmlspecialchars($u['nombre']) ?>" data-email="<?= htmlspecialchars($u['email']) ?>">Editar</button>
                                    <a href="../ajax/eliminar.php?action=usuario&id=<?= $u['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Desactivar este cliente?')">Desactivar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div> <!-- fin content-area -->
        </div> <!-- fin dashboard-wrapper -->


        <!-- ========== OVERLAYS (VENTANAS MODALES) ========== -->

        <!-- OVERLAY CREAR PRODUCTO -->
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

        <!-- OVERLAY CREAR SERVICIO -->
        <div id="overlayServicio" class="form-overlay">
            <div class="form-container">
                <h3>Nuevo servicio</h3>
                <form action="../ajax/crear_servicio.php" method="POST" enctype="multipart/form-data">
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

        <!-- OVERLAY CREAR GASTO -->
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
                <form action="../ajax/actualizar.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="producto">
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
                        <label>Cambiar imagen</label>
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

        <!-- Overlay EDITAR SERVICIO -->
        <div id="overlayEditarServicio" class="form-overlay">
            <div class="form-container">
                <h3>Editar servicio</h3>
                <form action="../ajax/actualizar.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="servicio">
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
                    <div class="form-group">
                        <label>Imagen actual</label><br>
                        <img id="edit_ser_img_actual" src="" style="max-width: 100px; max-height: 100px; margin-bottom: 10px; display: none;">
                        <span id="edit_ser_sin_imagen" style="display: none;">Sin imagen</span>
                    </div>
                    <div class="form-group">
                        <label>Cambiar imagen (opcional)</label>
                        <input type="file" name="imagen" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small>Si no selecciona una nueva, se mantiene la actual.</small>
                        <br>
                        <small>Solo se permite imágenes de 2 MB de tamaño.</small>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="salon-btn salon-btn-light cerrar-editar">Cancelar</button>
                        <button type="submit" class="salon-btn salon-btn-accent">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Overlay EDITAR GASTO -->
        <div id="overlayEditarGasto" class="form-overlay">
            <div class="form-container">
                <h3>Editar gasto</h3>
                <form action="../ajax/actualizar.php" method="POST">
                    <input type="hidden" name="action" value="gasto">
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

        <!-- Overlay EDITAR CITA -->
        <div id="overlayEditarCita" class="form-overlay">
            <div class="form-container">
                <h3>Editar cita</h3>
                <form action="../ajax/actualizar.php" method="POST">
                    <input type="hidden" name="action" value="cita">
                    <input type="hidden" name="id" id="edit_cita_id">
                    <div class="form-group">
                        <label>Cliente</label>
                        <input type="text" id="edit_cita_cliente" disabled style="background-color: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Servicio</label>
                        <input type="text" id="edit_cita_servicio" disabled style="background-color: #f5f5f5;">
                    </div>
                    <div class="form-group">
                        <label>Fecha *</label>
                        <input type="date" name="fecha" id="edit_cita_fecha" required>
                    </div>
                    <div class="form-group">
                        <label>Hora *</label>
                        <input type="time" name="hora" id="edit_cita_hora" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="salon-btn salon-btn-light cerrar-editar-cita">Cancelar</button>
                        <button type="submit" class="salon-btn salon-btn-accent">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Overlay EDITAR CLIENTE -->
        <div id="overlayEditarUsuario" class="form-overlay">
            <div class="form-container">
                <h3>Editar cliente</h3>
                <form action="../ajax/actualizar.php" method="POST">
                    <input type="hidden" name="action" value="usuario">
                    <input type="hidden" name="id" id="edit_usuario_id">
                    <div class="form-group">
                        <label>Nombre completo *</label>
                        <input type="text" name="nombre" id="edit_usuario_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="edit_usuario_email" required>
                    </div>
                    <div class="form-group">
                        <label>Nueva contraseña (opcional)</label>
                        <input type="password" name="password" id="edit_usuario_password" placeholder="Dejar en blanco para no cambiar">
                        <small>Debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número</small>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="salon-btn salon-btn-light cerrar-editar-usuario">Cancelar</button>
                        <button type="submit" class="salon-btn salon-btn-accent">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cargar el JavaScript específico del dashboard -->
        <script src="../assets/js/dashboard.js"></script>

        <!-- Script de emergencia para overlays (funciona incluso si dashboard.js falla) -->
        <script>
            // Capturar y silenciar errores de import (para que no bloqueen)
            window.addEventListener('error', function(e) {
                if (e.message && (e.message.includes('import statement') || e.message.includes('export statement'))) {
                    console.warn('Error de extensión bloqueado:', e.message);
                    e.preventDefault();
                    return false;
                }
            }, true);

            // Forzar la apertura de overlays cuando se hace clic en los botones
            document.addEventListener('DOMContentLoaded', function() {
                const btnProducto = document.getElementById('btnProducto');
                const btnServicio = document.getElementById('btnServicio');
                const btnGasto = document.getElementById('btnGasto');
                const overlayProducto = document.getElementById('overlayProducto');
                const overlayServicio = document.getElementById('overlayServicio');
                const overlayGasto = document.getElementById('overlayGasto');

                function abrir(overlay) {
                    if (overlay) overlay.classList.add('visible');
                }
                function cerrar(overlay) {
                    if (overlay) overlay.classList.remove('visible');
                }

                if (btnProducto && overlayProducto) {
                    btnProducto.addEventListener('click', () => abrir(overlayProducto));
                    // Cerrar con el botón "Cancelar" o haciendo clic fuera
                    const cerrarBtn = overlayProducto.querySelector('.cerrar');
                    if (cerrarBtn) cerrarBtn.addEventListener('click', () => cerrar(overlayProducto));
                    overlayProducto.addEventListener('click', (e) => {
                        if (e.target === overlayProducto) cerrar(overlayProducto);
                    });
                }
                if (btnServicio && overlayServicio) {
                    btnServicio.addEventListener('click', () => abrir(overlayServicio));
                    const cerrarBtn = overlayServicio.querySelector('.cerrar');
                    if (cerrarBtn) cerrarBtn.addEventListener('click', () => cerrar(overlayServicio));
                    overlayServicio.addEventListener('click', (e) => {
                        if (e.target === overlayServicio) cerrar(overlayServicio);
                    });
                }
                if (btnGasto && overlayGasto) {
                    btnGasto.addEventListener('click', () => abrir(overlayGasto));
                    const cerrarBtn = overlayGasto.querySelector('.cerrar');
                    if (cerrarBtn) cerrarBtn.addEventListener('click', () => cerrar(overlayGasto));
                    overlayGasto.addEventListener('click', (e) => {
                        if (e.target === overlayGasto) cerrar(overlayGasto);
                    });
                }
            });
        </script>

    </body>
</html>
