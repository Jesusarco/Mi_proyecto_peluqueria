<?php
session_start();
require_once "../config/database.php";

$usuario_logueado = isset($_SESSION['usuario_id']);

// Servicios (incluyendo imagen)
$servicios = $conn->query("SELECT id, nombre, descripcion, precio, duracion, imagen FROM servicios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Productos destacados
$productos = $conn->query("SELECT id, nombre, descripcion, precio, imagen FROM productos WHERE stock > 0 ORDER BY destacado DESC, id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

// Citas futuras
$citas_futuras = $conn->query("
    SELECT c.fecha, c.hora, s.nombre AS servicio
    FROM citas c
    JOIN servicios s ON c.servicio_id = s.id
    WHERE c.estado = 'reservado' AND c.fecha >= CURDATE()
    ORDER BY c.fecha, c.hora
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Áurea Estudio | Peluquería y Estilismo</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600&family=Playfair+Display:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="../assets/css/inicio.css">

        <link rel="stylesheet" href="../assets/css/footer.css">
    </head>
    <body>

        <?php include "../includes/header.php"; ?>

        <!-- HERO CARRUSEL -->
        <div class="hero-slider swiper">
            <div class="swiper-wrapper">
                <!-- Cambia las rutas de estas imágenes por las tuyas -->
                <div class="swiper-slide" style="background-image: url('../uploads/slide1.png');">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <h2>Arte & Elegancia</h2>
                            <p>Cortes, color y tratamientos de autor</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" style="background-image: url('../uploads/slide2.png');">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <h2>Tu estilo, nuestra pasión</h2>
                            <p>Expertos en transformación de imagen</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide" style="background-image: url('../uploads/slide3.png');">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <h2>Ambiente único</h2>
                            <p>Disfruta de una experiencia sensorial</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <!-- SOBRE NOSOTROS -->
        <section id="sobre-nosotros" class="section">
            <div class="grid-2cols">
                <div class="about-img">
                    <img src="../uploads/salon-interior.png" alt="Interior del salón">
                </div>
                <div class="about-text">
                    <h3>Áurea Estudio</h3>
                    <p>Un espacio donde la tradición y la vanguardia se encuentran. Nuestro equipo de estilistas está formado en las técnicas más actuales para realzar tu belleza natural.</p>
                    <p>Utilizamos productos de alta gama y ofrecemos un trato personalizado porque cada cabello es único. Más de 15 años creando tendencia.</p>                </div>
            </div>
        </section>

        <!-- SERVICIOS (ocultos hasta clic en menú) -->
        <section id="servicios" class="section seccion-oculta">
            <h2 class="section-title">Nuestros Servicios</h2>
            <div class="filtro-busqueda">
                <input type="text" id="filtroServicios" placeholder=" Buscar servicio...">
            </div>
            <div class="cards-grid" id="listaServicios">
                <?php foreach ($servicios as $s): ?>
                    <div class="servicio-card" data-nombre="<?= htmlspecialchars($s['nombre']) ?>" data-descripcion="<?= htmlspecialchars($s['descripcion'] ?? '') ?>">
                        <div class="card-img">
                            <?php if (!empty($s['imagen'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($s['imagen']) ?>" alt="<?= htmlspecialchars($s['nombre']) ?>">
                            <?php else: ?>
                                <img src="../uploads/default-service.jpg" alt="Servicio">
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3><?= htmlspecialchars($s['nombre']) ?></h3>
                            <!-- En el substr en el 100 se puede cambiar para que muestre más información. No recomnedado ponerlo con un valor grande -->
                            <p><?= htmlspecialchars(substr($s['descripcion'] ?? '', 0, 100)) ?>...</p>
                            <div class="precio"><?= number_format($s['precio'], 2) ?> €</div>
                            <small><i class="bi bi-clock"></i> Duración: <?= $s['duracion'] ?> min</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- PRODUCTOS (ocultos) -->
        <section id="productos" class="section seccion-oculta">
            <h2 class="section-title">Nuestros Productos</h2>
            <div class="filtro-busqueda">
                <input type="text" id="filtroProductos" placeholder=" Buscar producto...">
            </div>
            <div class="cards-grid" id="listaProductos">
                <?php foreach ($productos as $p): ?>
                    <div class="producto-card" data-nombre="<?= htmlspecialchars($p['nombre']) ?>" data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '') ?>">
                        <div class="card-img">
                            <?php if (!empty($p['imagen'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                            <?php else: ?>
                                <img src="../uploads/default-product.jpg" alt="Producto">
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                            <!-- En el substr en el 100 se puede cambiar para que muestre más información. No recomnedado ponerlo con un valor grande -->
                            <p><?= htmlspecialchars(substr($p['descripcion'] ?? '', 0, 100)) ?>...</p>      
                            <div class="precio"><?= number_format($p['precio'], 2) ?> €</div>
                            <?php if ($usuario_logueado): ?>
                                <button class="btn-carrito" onclick="addToCart(<?= $p['id'] ?>)">Añadir al carrito</button>
                            <?php else: ?>
                                <button class="btn-carrito" onclick="mostrarMensajeLogin()">Inicia sesión</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- PEDIR CITA (oculta) -->
        <section id="cita" class="section seccion-oculta">
            <h2 class="section-title">Reserva tu cita</h2>
            <div class="cita-wrapper">
                <?php if ($usuario_logueado): ?>
                    <form id="formReservaInicio" class="form-cita">
                        <div class="form-group">
                            <label>Servicio</label>
                            <select name="servicio_id" required>
                                <option value="">Selecciona un servicio</option>
                                <?php foreach ($servicios as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nombre']) ?> - <?= number_format($s['precio'], 2) ?>€ (<?= $s['duracion'] ?> min)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha</label>
                            <input type="text" id="fecha_cita" name="fecha" placeholder="Elige una fecha" required>
                        </div>
                        <div class="form-group">
                            <label>Hora</label>
                            <div id="contenedor-horas" class="slots-grid">
                                <p style="color:#aaa;">Primero selecciona una fecha</p>
                            </div>
                            <input type="hidden" id="hora_seleccionada" name="hora">
                        </div>
                        <button type="submit" class="btn-reservar">Confirmar reserva</button>
                    </form>
                    <div id="mensajeCita" style="margin-top:20px; text-align:center;"></div>
                    <!-- Citas ya reservadas -->
                    <div style="margin-top: 40px;">
                        <h3 style="font-weight:500;">Próximas citas ocupadas</h3>
                        <?php if (count($citas_futuras) > 0): ?>
                            <ul style="margin-top:15px; list-style:none;">
                                <?php foreach ($citas_futuras as $c): ?>
                                    <li><i class="bi bi-calendar-event"></i> <?= date('d/m/Y', strtotime($c['fecha'])) ?> a las <?= $c['hora'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No hay citas reservadas próximamente.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="login-requerido">
                        <p>Para reservar una cita, <a href="../auth/login.php">inicia sesión</a> o <a href="../auth/register_form.php">regístrate</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- CONTACTO -->
        <section id="atencion-cliente" class="section">
            <h2 class="section-title">Contacto</h2>
            <div class="contacto-grid">
                <div>
                    <p><strong><i class="bi bi-geo-alt-fill"></i> Dirección</strong><br>Calle Mayor, 12 · 28001 Madrid</p>
                    <p><strong><i class="bi bi-telephone-fill"></i> Teléfono</strong><br>+34 123 456 789</p>
                    <p><strong><i class="bi bi-envelope-fill"></i> Email</strong><br>info@aureaestudio.com</p>
                    <p><strong><i class="bi bi-clock"></i> Horario</strong><br>Lun-Vie 10:00–20:00 · Sáb 09:00–14:00</p>
                </div>
                <div class="mapa">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.650519083765!2d-3.7086849227506273!3d40.4165922714398!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd42287e92cbaa27%3A0x632dd8a75b86a3d!2sCalle%20Mayor%2C%2012%2C%20Centro%2C%2028013%20Madrid!5e0!3m2!1ses!2ses!4v1779739520189!5m2!1ses!2ses" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>                </div>
                </div>
            <form id="formContacto" style="margin-top: 3rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                <h3>¿Tienes alguna duda? Escríbenos</h3>
                <input type="text" placeholder="Tu nombre" required style="width:100%; padding:12px; margin:10px 0; border-radius:60px; border:1px solid #E2DCD5;">
                <input type="email" placeholder="Tu email" required style="width:100%; padding:12px; margin:10px 0; border-radius:60px; border:1px solid #E2DCD5;">
                <textarea placeholder="Mensaje" rows="4" style="width:100%; padding:12px; border-radius:20px; border:1px solid #E2DCD5;"></textarea>
                <button type="submit" class="btn-reservar" style="width: auto; padding:12px 32px;">Enviar mensaje</button>
            </form>
            <div id="mensajeContacto" style="text-align:center; margin-top:20px;"></div>
        </section>

        <?php include "../includes/footer.php"; ?>

        <script src="../assets/js/carrito.js"></script>
        <!-- Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

        <script src="../assets/js/inicio.js"></script>
        <script>
            var usuarioLogueado = <?= $usuario_logueado ? 'true' : 'false' ?>;
        </script>
    </body>
</html>