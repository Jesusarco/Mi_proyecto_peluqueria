<?php
session_start();
require_once "../config/database.php";

// Solo superadmin puede gestionar admins
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'superadmin') {
    header("Location: dashboard.php?error=No tienes permisos para gestionar administradores");
    exit();
}

$esDashboard = true; 
include "../includes/header.php";

// Consultar todos los administradores (incluyendo superadmins)
$campos = "id, nombre, email, rol, fecha_creacion";
if ($_SESSION['rol'] == 'superadmin') {
    $campos .= ", password";
}
$admins = $conn->query("SELECT $campos FROM usuarios WHERE rol IN ('admin', 'superadmin') ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    .salon-table { 
        width: 100%; 
        border-collapse: 
        collapse; 
    }
    
    .salon-table th, .salon-table td { 
        padding: 12px 10px; 
        border-bottom: 1px solid #eee; 
        text-align: left; 
    }
    
    .salon-table th { 
        background: #f8f8f8; 
    }

    .btn-eliminar, .btn-convertir, .btn-editar-admin { 
        background: #e74c3c; 
        color: white;
        padding: 5px 12px; 
        border-radius: 4px; 
        text-decoration: none; 
        display: inline-block; 
        font-size: 0.8rem; 
        margin-right: 5px; 
    }

    .btn-convertir { 
        background: var(--accent-color); 
    }

    .btn-editar-admin {
        background: #3498db;
    }

    .btn-crear { 
        background: var(--accent-color); 
        color: white; 
        padding: 8px 16px; 
        border-radius: 4px; 
        text-decoration: none; 
        display: inline-block; 
    }

    .admin-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 20px; 
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
        padding: 8px 16px; 
        border-radius: 6px; 
        border: none; 
        cursor: pointer; 
    }

    .salon-btn-light { 
        background: #ccc; 
    }

    .salon-btn-accent { 
        background: var(--accent-color); 
        color: white; 
    }
    
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
            <button id="btnCrearAdmin" class="btn-crear">+ Nuevo administrador</button>
        </div>

        <div class="salon-card">
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <table class="salon-table">
                <div class="admin-header-table">
                    <div class="filtro-wrapper">
                        <input type="text" class="filtro-tabla" data-tabla="citas" placeholder=" Buscar...">
                    </div>
                </div>
                <thead>
                    <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Fecha registro</th><th>Acciones</th>
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
                            <?php if ($admin['id'] != $_SESSION['usuario_id'] && $_SESSION['rol'] == 'superadmin'): ?>
                                <button class="btn-editar-admin" data-id="<?= $admin['id'] ?>" data-nombre="<?= htmlspecialchars($admin['nombre']) ?>" data-email="<?= htmlspecialchars($admin['email']) ?>">Editar</button>
                                <?php if ($admin['rol'] == 'admin'): ?>
                                    <a href="../ajax/cambiar_rol_admin.php?id=<?= $admin['id'] ?>&accion=ascender" class="btn-convertir" onclick="return confirm('¿Convertir este administrador en Superadmin?')">Ascender</a>
                                <?php elseif ($admin['rol'] == 'superadmin'): ?>
                                    <a href="../ajax/cambiar_rol_admin.php?id=<?= $admin['id'] ?>&accion=descender" class="btn-convertir" onclick="return confirm('¿Revocar Superadmin a este usuario? Pasará a ser administrador normal.')">Descender</a>
                                <?php endif; ?>
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

<!-- Overlay para crear administrador -->
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

<!-- Overlay EDITAR ADMIN -->
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
    const overlay = document.getElementById('overlayAdmin');
    const btn = document.getElementById('btnCrearAdmin');
    const cerrarBtns = document.querySelectorAll('.cerrar');

    btn.addEventListener('click', () => overlay.classList.add('visible'));
    cerrarBtns.forEach(btn => btn.addEventListener('click', () => overlay.classList.remove('visible')));
    window.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('visible'); });

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

    // Función para mostrar/ocultar contraseña
    function togglePassword(el) {
        const full = el.getAttribute('data-full');
        if (el.innerText === '••••••') {
            el.innerText = full;
        } else {
            el.innerText = '••••••';
        }
    }

    // Lógica para editar administrador
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
    document.querySelectorAll('.cerrar-editar-admin').forEach(btn => {
        btn.addEventListener('click', () => {
            overlayEditAdmin.classList.remove('visible');
        });
    });
    window.addEventListener('click', (e) => {
        if (e.target === overlayEditAdmin) overlayEditAdmin.classList.remove('visible');
    });

</script>
</body>
</html>