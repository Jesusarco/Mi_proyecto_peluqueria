<?php
    // Iniciar sesión y conectar a la base de datos
    session_start();
    require_once "../config/database.php";

    // Solo el superadministrador puede acceder a esta página
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
        header("Location: dashboard.php?error=No tienes permisos para gestionar administradores");
        exit();
    }

    // Variable para que el header sepa que estamos en el dashboard (cambia el estilo)
    $esDashboard = true; 
    include "../includes/header.php";

    // Construir la consulta: seleccionar campos básicos y, si es superadmin, también la contraseña (hash)
    $campos = "id, nombre, email, rol, fecha_creacion";
    if ($_SESSION['rol'] == 'superadmin') {
        $campos .= ", password";
    }
    // Obtener todos los administradores y superadministradores, ordenados por ID descendente
    $admins = $conn->query("SELECT $campos FROM usuarios WHERE rol IN ('admin', 'superadmin') ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

        <!-- Hoja de estilos específica para esta página -->
        <link rel="stylesheet" href="../assets/css/gestionar_admins.css">

        <div class="dashboard-wrapper">
            <!-- Barra lateral -->
            <aside class="sidebar">
                <div style="padding: 0 25px 20px;"><h2 style="color:#efefef"> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></h2></div>
                <nav>
                    <a href="dashboard.php" class="sidebar-link">Dashboard</a>
                    <a href="gestionar_admins.php" class="sidebar-link active">Administradores</a>
                    <hr style="border-color:#333">
                </nav>
            </aside>

            <div class="content-area">
                <div class="admin-header">
                    <h2>Gestión de Administradores</h2>
                    <!-- Botón que abre el overlay para crear un nuevo administrador -->
                    <button id="btnCrearAdmin" class="btn-crear">+ Nuevo administrador</button>
                </div>

                <div class="salon-card">
                    <!-- Mostrar mensajes de éxito o error desde la URL -->
                    <?php if (isset($_GET['success'])): ?>
                        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                            <?= htmlspecialchars($_GET['success']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                            <?= htmlspecialchars($_GET['error']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Tabla de administradores -->
                    <table class="salon-table">
                        <div class="admin-header-table">
                            <div class="filtro-wrapper">
                                <input type="text" class="filtro-tabla" data-tabla="citas" placeholder=" Buscar...">
                            </div>
                        </div>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($admins as $admin): ?>
                                <tr>
                                    <td><?= $admin['id'] ?></td>
                                    <td><?= htmlspecialchars($admin['nombre']) ?></td>
                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                    <td><?= $admin['rol'] == 'superadmin' ? 'Superadmin' : 'Admin' ?></td>
                                    <td><?= date('d/m/Y', strtotime($admin['fecha_creacion'])) ?></td>
                                    <td>
                                        <!-- Solo mostrar botones si el usuario no es el propio y es superadmin -->
                                        <?php if ($admin['id'] != $_SESSION['usuario_id'] && $_SESSION['rol'] == 'superadmin'): ?>
                                            <!-- Botón para editar (abre overlay de edición) -->
                                            <button class="btn-editar-admin" data-id="<?= $admin['id'] ?>" data-nombre="<?= htmlspecialchars($admin['nombre']) ?>" data-email="<?= htmlspecialchars($admin['email']) ?>">Editar</button>
                                            
                                            <!-- Botón para ascender (admin -> superadmin) -->
                                            <?php if ($admin['rol'] == 'admin'): ?>
                                                <a href="../ajax/cambiar_rol_admin.php?id=<?= $admin['id'] ?>&accion=ascender" class="btn-convertir" onclick="return confirm('¿Convertir este administrador en Superadmin?')">Ascender</a>
                                            <!-- Botón para descender (superadmin -> admin) -->
                                            <?php elseif ($admin['rol'] == 'superadmin'): ?>
                                                <a href="../ajax/cambiar_rol_admin.php?id=<?= $admin['id'] ?>&accion=descender" class="btn-convertir" onclick="return confirm('¿Revocar Superadmin a este usuario? Pasará a ser administrador normal.')">Descender</a>
                                            <?php endif; ?>
                                            <!-- Botón para eliminar administrador -->
                                            <a href="../ajax/eliminar.php?action=admin&id=<?= $admin['id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este administrador?')">Eliminar</a>
                                        <?php elseif ($admin['id'] == $_SESSION['usuario_id']): ?>
                                            <span style="color:#888;">(Tú)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Overlay para crear un nuevo administrador (modal) -->
        <div id="overlayAdmin" class="form-overlay">
            <div class="form-container">
                <h3>Nuevo administrador</h3>
                <form action="../ajax/crear_admin.php" method="POST">
                    <div class="form-group">
                        <label>Nombre completo *</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña *</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="salon-btn salon-btn-light cerrar">Cancelar</button>
                        <button type="submit" class="salon-btn salon-btn-accent">Crear administrador</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Overlay para editar administrador -->
        <div id="overlayEditarAdmin" class="form-overlay">
            <div class="form-container">
                <h3>Editar administrador</h3>
                <form action="../ajax/actualizar.php" method="POST">
                    <input type="hidden" name="action" value="admin">
                    <input type="hidden" name="id" id="edit_admin_id">
                    <div class="form-group">
                        <label>Nombre completo *</label>
                        <input type="text" name="nombre" id="edit_admin_nombre" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="edit_admin_email" required>
                    </div>
                    <div class="form-group">
                        <label>Nueva contraseña</label>
                        <input type="password" name="password" id="edit_admin_password" placeholder="Dejar en blanco para no cambiar">
                        <small>Debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número</small>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="salon-btn salon-btn-light cerrar-editar-admin">Cancelar</button>
                        <button type="submit" class="salon-btn salon-btn-accent">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Overlay de creación
            const overlay = document.getElementById('overlayAdmin');
            const btn = document.getElementById('btnCrearAdmin');
            const cerrarBtns = document.querySelectorAll('.cerrar');

            btn.addEventListener('click', () => overlay.classList.add('visible'));
            cerrarBtns.forEach(btn => btn.addEventListener('click', () => overlay.classList.remove('visible')));
            window.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('visible'); });

            // Filtro en tiempo real para la tabla (por el texto de todas las celdas)
            function filtrarTabla(input, tablaId) {
                const filtro = input.value.toLowerCase();
                const tabla = document.getElementById(tablaId);
                if (!tabla) return;
                const filas = tabla.querySelectorAll('tbody tr');
                filas.forEach(fila => {
                    const texto = fila.innerText.toLowerCase();
                    fila.style.display = texto.includes(filtro) ? '' : 'none';
                });
            }

            // Lógica para el overlay de edición: al hacer clic en "Editar", se cargan los datos del administrador
            const editAdminBtns = document.querySelectorAll('.btn-editar-admin');
            const overlayEditAdmin = document.getElementById('overlayEditarAdmin');
            if (editAdminBtns.length) {
                editAdminBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.getElementById('edit_admin_id').value = btn.dataset.id;
                        document.getElementById('edit_admin_nombre').value = btn.dataset.nombre;
                        document.getElementById('edit_admin_email').value = btn.dataset.email;
                        overlayEditAdmin.classList.add('visible');
                    });
                });
            }
            // Cerrar overlay de edición con el botón "Cancelar"
            document.querySelectorAll('.cerrar-editar-admin').forEach(btn => {
                btn.addEventListener('click', () => {
                    overlayEditAdmin.classList.remove('visible');
                });
            });
            // Cerrar overlay de edición al hacer clic fuera del modal
            window.addEventListener('click', (e) => {
                if (e.target === overlayEditAdmin) overlayEditAdmin.classList.remove('visible');
            });
        </script>

    </body>
</html>