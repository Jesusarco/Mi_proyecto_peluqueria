<?php
    // Iniciar sesión y conectar a la base de datos
    session_start();
    require_once "../config/database.php";

    // Solo permitir acceso a usuarios autenticados (no se restringe solo a superadmin, pero se puede ajustar)
    if (!isset($_SESSION['rol'])) {
        header("Location: dashboard.php?error=No tienes permisos para gestionar pedidos");
        exit();
    }

    // Variable para indicar que estamos en el dashboard (modifica el header)
    $esDashboard = true; 
    include "../includes/header.php";

    // Consultar pedidos con estado 'entregado' (completados) y agrupar productos
    $pedidos = $conn->query("
        SELECT p.id, u.nombre as cliente, p.total, p.fecha, estado,
            GROUP_CONCAT(pr.nombre ORDER BY pr.id SEPARATOR ', ') as productos,
            GROUP_CONCAT(dp.cantidad ORDER BY pr.id SEPARATOR ', ') as cantidades
        FROM pedidos p
        JOIN usuarios u ON p.usuario_id = u.id
        LEFT JOIN detalle_pedido dp ON p.id = dp.pedido_id
        LEFT JOIN productos pr ON dp.producto_id = pr.id
        WHERE estado = 'entregado'
        GROUP BY p.id
        ORDER BY p.fecha DESC
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

            .btn-archivar { 
                background: #e74c3c; 
                color: white; 
                padding: 5px 12px; 
                border-radius: 4px; 
                text-decoration: none; 
                display: inline-block; 
                font-size: 0.8rem; 
            }

            .admin-header { 
                display: flex; 
                justify-content: space-between; 
                align-items: center; 
                margin-bottom: 20px; 
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
            <!-- Barra lateral -->
            <aside class="sidebar">
                <div style="padding: 0 25px 20px;"><h2 style="color:#efefef"> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Admin') ?></h2></div>
                <nav>
                    <a href="dashboard.php" class="sidebar-link">Dashboard</a>
                    <a href="gestionar_pedidos.php" class="sidebar-link active">Gestionar Pedidos</a>
                    <hr style="border-color:#333">
                </nav>
            </aside>

            <!-- Área de contenido principal -->
            <div class="content-area">
                <div class="admin-header">
                    <h2>Gestión de Pedidos</h2>
                </div>

                <div class="salon-card">
                    <!-- Mostrar mensajes de error o éxito desde la URL -->
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

                    <!-- Tabla de pedidos con filtro en tiempo real -->
                    <table class="salon-table">
                        <div class="admin-header-table">
                            <div class="filtro-wrapper">
                                <input type="text" class="filtro-tabla" data-tabla="citas" placeholder=" Buscar...">
                            </div>
                        </div>
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
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
                                    <td><?= htmlspecialchars($ped['estado']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($pedidos) == 0): ?>
                                <tr><td colspan="7" style="text-align:center;">No hay pedidos activos.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Script de filtrado en tiempo real sobre la tabla -->
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