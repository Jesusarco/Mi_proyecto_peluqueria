<?php
    // Iniciar sesión para obtener datos del usuario y verificar permisos
    session_start();
    require_once "../config/database.php";

    // Solo permitir acceso si el usuario tiene una sesión activa (no se restringe solo a superadmin, pero el control de roles se podría añadir)
    if (!isset($_SESSION['rol'])) {
        header("Location: dashboard.php?error=No tienes permisos para gestionar citas");
        exit();
    }

    // Indicar que estamos en el panel de administración (para que el header muestre el layout adecuado)
    $esDashboard = true; 
    include "../includes/header.php";

    // Consultar las citas que ya están completadas (archivadas)
    // Se obtienen datos de la cita, cliente y servicio
    $citas = $conn->query("
        SELECT c.id, u.nombre as cliente, s.nombre as servicio, s.precio, c.fecha, c.hora, c.estado
        FROM citas c
        JOIN usuarios u ON c.usuario_id = u.id
        JOIN servicios s ON c.servicio_id = s.id
        WHERE c.estado = 'completado'
        ORDER BY c.fecha DESC, c.hora DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
?>

        <!-- Estilos CSS específicos para esta página -->
        <style>
            .salon-table { 
                width: 100%; 
                border-collapse: collapse; 
            }

            .salon-table th, .salon-table td { 
                padding: 12px 10px; 
                border-bottom: 1px solid #eee; 
                text-align: left; 
            }

            .salon-table th { 
                background: #f8f8f8; 
            }

            /* Estilos comunes para botones de acciones */
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

            .admin-header { 
                display: flex; 
                justify-content: 
                space-between; 
                align-items: center; 
                margin-bottom: 20px; 
            }

            .filtro-wrapper { 
                flex: 1; display: flex; 
                justify-content: center; 
            }

            .filtro-tabla:focus { 
                border-color: var(--accent-color); 
                box-shadow: 0 0 0 2px rgba(212,175,55,0.2); 
            }

            .filtro-tabla {
                width: 260px; max-width: 100%; padding: 8px 15px; border: 1px solid #ddd;
                border-radius: 30px; font-size: 0.85rem; outline: none; transition: all 0.2s;
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
            <!-- Barra lateral con navegación -->
            <aside class="sidebar">
                <div style="padding: 0 25px 20px;"><h2 style="color:#efefef"> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></h2></div>
                <nav>
                    <a href="dashboard.php" class="sidebar-link">Dashboard</a>
                    <a href="gestionar_citas.php" class="sidebar-link active">Gestionar Citas</a>
                    <hr style="border-color:#333">
                </nav>
            </aside>

            <!-- Área de contenido principal -->
            <div class="content-area">
                <div class="admin-header">
                    <h2>Gestión de Citas</h2>
                </div>

                <div class="salon-card">
                    <!-- Mostrar mensajes de error o éxito pasados por URL -->
                    <?php if (isset($_GET['error'])): ?>
                        <div style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                            <?= htmlspecialchars($_GET['error']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['success'])): ?>
                        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                            <?= htmlspecialchars($_GET['success']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Tabla de citas completadas con filtro en tiempo real -->
                    <table class="salon-table">
                        <div class="admin-header-table">
                            <div class="filtro-wrapper">
                                <input type="text" class="filtro-tabla" data-tabla="citas" placeholder=" Buscar...">
                            </div>
                        </div>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
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

        <!-- Script para filtrar la tabla en tiempo real mientras se escribe en el campo de búsqueda -->
        <script>
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
        </script>

    </body>
</html>