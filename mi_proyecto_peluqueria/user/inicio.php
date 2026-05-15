<?php
    session_start();
    require_once "../config/database.php";

    // Comprobar si el usuario está logueado
    $usuario_logueado = isset($_SESSION['usuario_id']);

    // Obtener servicios para el formulario de cita y para mostrar la sección
    $servicios = $conn->query("SELECT id, nombre, descripcion, precio, duracion, imagen FROM servicios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener productos destacados o los primeros 6
    $productos = $conn->query("SELECT id, nombre, descripcion, precio, imagen FROM productos WHERE stock > 0 ORDER BY destacado DESC, id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

    // Obtener citas reservadas futuras para mostrar disponibilidad
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
    <title>Peluquería Profesional - Inicio</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Flatpickr CSS y JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <style>
        /* Estilos adicionales para la página de inicio */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 40px;
        }
        .hero h1 { font-size: 2.5rem; margin-bottom: 20px; }
        .hero p { font-size: 1.2rem; max-width: 800px; margin: 0 auto; }
        .section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .section h2 {
            color: var(--primary-color);
            border-left: 4px solid var(--accent-color);
            padding-left: 15px;
            margin-top: 0;
            margin-bottom: 25px;
        }
        .seccion-oculta { display: none; }

        /* Tarjetas de productos y servicios */
        .producto-card, .servicio-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.2s;
        }
        .producto-card:hover, .servicio-card:hover { 
            transform: translateY(-5px); box-shadow: 0 6px 12px rgba(0,0,0,0.1); 
        
        }
        .producto-card img { 
            max-width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; 
        }
        
        .precio { 
            font-size: 1.3rem; color: var(--accent-color); font-weight: bold; 
        }
        .btn-carrito {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-carrito-deshabilitado {
            background: #ccc;
            color: #666;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            margin-top: 10px;
            cursor: not-allowed;
        }

        /* Filtros de búsqueda */
        .filtro-busqueda {
            text-align: center;
            margin-bottom: 25px;
        }
        .filtro-busqueda input {
            width: 100%;
            max-width: 400px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            outline: none;
            transition: 0.2s;
        }
        .filtro-busqueda input:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(212,175,55,0.2);
        }

        /* Formulario cita centrado */
        .form-cita input, .form-cita select, .form-cita textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            background: var(--accent-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        .login-requerido {
            text-align: center;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            margin: 20px 0;
        }
        .login-requerido a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: bold;
        }

        /* Contacto */
        .contacto-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .contacto-item {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        /* Botón scroll-up estilo cristal */
        #btnScrollTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            color: white;
            font-size: 26px;
            font-weight: bold;
            cursor: pointer;
            opacity: 0;
            transform: translateY(20px) scale(0.9);
            pointer-events: none;
            transition: all 0.35s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        #btnScrollTop.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        #btnScrollTop:hover {
            background: rgba(175, 134, 0, 0.7);
            transform: scale(1.1);
        }

        /* Slots de hora */
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 8px;
        }

        .slot-btn {
            padding: 8px 0;
            border: 1px solid #ccc;
            background: #fff;
            border-radius: 6px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .slot-btn:hover:not(.ocupado):not(.seleccionado) {
            background: #f0f0f0;
            border-color: var(--accent-color);
        }

        .slot-btn.ocupado {
            background: #fce4e4;
            border-color: #e0a0a0;
            color: #b55;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .slot-btn.seleccionado {
            background: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
            font-weight: bold;
        }

        .tabla-reservas tr:hover {
            background: #ffe0e0 !important;
        }   

        @media (max-width: 768px) {
            .hero h1 { font-size: 1.8rem; }
            #btnScrollTop { width: 45px; height: 45px; font-size: 22px; bottom: 20px; right: 20px; }
        }
    </style>
</head>
<body>

<?php include "../includes/header.php"; ?>

<div class="main-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

    <!-- Sección Inicio (visible siempre) -->
    <section id="inicio" class="section">
        <br><br>
        <div class="hero">
            <h1>Bienvenido a nuestra peluquería</h1>
            <p>Descubre los mejores servicios de estilismo y cuidado personal. Profesionales a tu servicio.</p>
        </div>
        <br><br>
    </section>

    <!-- Sección Sobre nosotros (visible siempre) -->
    <section id="sobre-nosotros" class="section">
        <br><br>
        <h2>Sobre nosotros</h2>
        <p>Somos un equipo de profesionales apasionados por la belleza y el cuidado del cabello. Con más de 10 años de experiencia, ofrecemos servicios de alta calidad utilizando productos de primeras marcas. Tu satisfacción es nuestra mayor recompensa.</p>
        <p>Nuestro salón está diseñado para que te sientas cómodo y relajado mientras nuestros estilistas trabajan para realzar tu imagen.</p>
        <br><br>
    </section>

    <!-- Sección Nuestros productos (oculta inicialmente) con filtro -->
    <section id="productos" class="section seccion-oculta">
        <br><br>
        <h2>Nuestros productos</h2>
        <div class="filtro-busqueda">
            <input type="text" id="filtroProductos" placeholder=" Buscar producto...">
        </div>
        <div class="salon-grid" id="listaProductos">
            <?php foreach ($productos as $p): ?>
                <div class="producto-card" 
                     data-nombre="<?= htmlspecialchars($p['nombre']) ?>" 
                     data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '') ?>">
                    <?php if ($p['imagen']): ?>
                        <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                    <?php else: ?>
                        <div style="height:150px; background:#f0f0f0; display:flex; align-items:center; justify-content:center;">Sin imagen</div>
                    <?php endif; ?>
                        <h3><?= htmlspecialchars($p['nombre']) ?></h3>
                        <p><?= htmlspecialchars(substr($p['descripcion'] ?? '', 0, 100)) ?>...</p>
                        <p class="precio"><?= number_format($p['precio'], 2) ?> €</p>
                    <?php if ($usuario_logueado): ?>
                        <button class="btn-carrito" onclick="addToCart(<?= $p['id'] ?>)">Añadir al carrito</button>
                    <?php else: ?>
                        <button class="btn-carrito-deshabilitado" onclick="mostrarMensajeLogin()">Inicia sesión para comprar</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($productos) == 0): ?>
            <p>Próximamente nuevos productos.</p>
        <?php endif; ?>
        <br><br>
    </section>

    <!-- Sección Nuestros servicios (oculta inicialmente) con filtro -->
    <section id="servicios" class="section seccion-oculta">
        <br><br>
        <h2>Nuestros servicios</h2>
        <div class="filtro-busqueda">
            <input type="text" id="filtroServicios" placeholder=" Buscar servicio...">
        </div>
        <div class="salon-grid" id="listaServicios">
            <?php foreach ($servicios as $s): ?>
                <div class="servicio-card"
                    data-nombre="<?= htmlspecialchars($s['nombre']) ?>"
                    data-descripcion="<?= htmlspecialchars($s['descripcion'] ?? '') ?>">
                    
                    <?php if ($s['imagen']): ?>
                        <img src="../uploads/<?= htmlspecialchars($s['imagen']) ?>" alt="<?= htmlspecialchars($s['nombre']) ?>" style="width:100%; height:150px; object-fit:cover; border-radius:8px;">
                    <?php else: ?>
                        <div style="height:150px; background:#f0f0f0; display:flex; align-items:center; justify-content:center;">Sin imagen</div>
                    <?php endif; ?>
                    
                    <h3><?= htmlspecialchars($s['nombre']) ?></h3>
                    <p><?= htmlspecialchars(substr($s['descripcion'] ?? '', 0, 100)) ?></p>
                    <p class="precio"><?= number_format($s['precio'], 2) ?> €</p>
                    <p><small>Duración: <?= $s['duracion'] ?> minutos</small></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($servicios) == 0): ?>
            <p>Próximamente nuevos servicios.</p>
        <?php endif; ?>
        <br><br>
    </section>

    <!-- Sección Pedir cita (oculta inicialmente) -->
    <section id="cita" class="section seccion-oculta">
        <br><br>
        <h2>Pedir cita</h2>
        <p>Selecciona el servicio y la fecha que prefieras. Te confirmaremos la reserva lo antes posible.</p>
        <?php if ($usuario_logueado): ?>
            <div style="max-width: 600px; margin: 0 auto;">
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
                        <input type="text" id="fecha_cita" name="fecha" placeholder="Selecciona una fecha" required>
                    </div>

                    <!-- Contenedor de slots de hora -->
                    <div class="form-group">
                        <label>Hora</label>
                        <div id="contenedor-horas" class="slots-grid">
                            <p style="color: #888; font-style: italic;">Primero selecciona una fecha</p>
                        </div>
                        <!-- Input oculto para almacenar la hora seleccionada -->
                        <input type="hidden" id="hora_seleccionada" name="hora" value="">
                    </div>
                    <br>
                    <button type="submit">Reservar cita</button>
                </form>
                <div id="mensajeCita" style="margin-top:15px; text-align:center;"></div>
                <!-- Tabla de citas ya reservadas -->
                <div style="margin-top: 30px;">
                    <h3 style="color: var(--primary-color); border-left: 4px solid var(--accent-color); padding-left: 15px;">
                        Citas ya reservadas
                    </h3>
                    <?php if (count($citas_futuras) > 0): ?>
                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 8px;">
                            <table class="tabla-reservas" style="width:100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f4f4f4;">
                                        <th style="padding:10px; text-align:left;">Fecha</th>
                                        <th style="padding:10px; text-align:left;">Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($citas_futuras as $c): ?>
                                    <tr style="background: #fef0f0; border-bottom:1px solid #eee;">
                                        <td style="padding:10px;"><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                                        <td style="padding:10px;"><?= $c['hora'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color:#888;">No hay citas reservadas próximamente. Estas libre para elegir la hora que quieras.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="login-requerido">
                <p>Para reservar una cita, primero debes <a href="../auth/login.php">iniciar sesión</a> o <a href="../auth/register_form.php">registrarte</a>.</p>
            </div>
        <?php endif; ?>
        <br><br>
    </section>

    <!-- Sección Atención al cliente (visible siempre) -->
    <section id="atencion-cliente" class="section">
        <br><br>
        <h2>Atención al cliente</h2>
        <div class="contacto-info">
            <div class="contacto-item"><h3> Teléfono</h3><p>645 87 87 21</p></div>
            <div class="contacto-item"><h3> Email</h3><p>info@peluqueria.com</p></div>
            <div class="contacto-item"><h3> Dirección</h3><p>Calle Principal, 123</p></div>
        </div>
        <form id="formContacto" style="margin-top: 30px;">
            <h3>Envíanos un mensaje</h3>
            <input type="text" placeholder="Tu nombre" required style="width:100%; padding:10px; margin-bottom:10px;">
            <input type="email" placeholder="Tu email" required style="width:100%; padding:10px; margin-bottom:10px;">
            <textarea placeholder="Tu mensaje" rows="4" style="width:100%; padding:10px; margin-bottom:10px;"></textarea>
            <button type="submit">Enviar mensaje</button>
        </form>
        <div id="mensajeContacto" style="margin-top:15px; text-align:center;"></div>
        <br><br>
    </section>

</div>

<?php include "../includes/footer.php"; ?>
<script src="../assets/js/carrito.js"></script>
<script>
    // ---------- función para redirigir al login ----------
    function mostrarMensajeLogin() {
        alert("Debes iniciar sesión o registrarte para comprar productos.");
        window.location.href = "../auth/login.php";
    }

    // ---------- MOSTRAR/OCULTAR SECCIONES (menú) ----------
    const btnProductos = document.getElementById('menuMostrarProductos');
    const btnServicios = document.getElementById('menuMostrarServicios');
    const btnCita = document.getElementById('menuMostrarCita');
    const seccionProductos = document.getElementById('productos');
    const seccionServicios = document.getElementById('servicios');
    const seccionCita = document.getElementById('cita');

    function ocultarTodas() {
        if (seccionProductos) seccionProductos.classList.add('seccion-oculta');
        if (seccionServicios) seccionServicios.classList.add('seccion-oculta');
        if (seccionCita) seccionCita.classList.add('seccion-oculta');
    }

    if (btnProductos) {
        btnProductos.addEventListener('click', (e) => {
            e.preventDefault();
            ocultarTodas();
            seccionProductos.classList.remove('seccion-oculta');
            seccionProductos.scrollIntoView({ behavior: 'smooth' });
        });
    }
    if (btnServicios) {
        btnServicios.addEventListener('click', (e) => {
            e.preventDefault();
            ocultarTodas();
            seccionServicios.classList.remove('seccion-oculta');
            seccionServicios.scrollIntoView({ behavior: 'smooth' });
        });
    }
    if (btnCita) {
        btnCita.addEventListener('click', (e) => {
            e.preventDefault();
            ocultarTodas();
            seccionCita.classList.remove('seccion-oculta');
            seccionCita.scrollIntoView({ behavior: 'smooth' });
        });
    }

    // ---------- FILTROS EN TIEMPO REAL ----------
    function normalizar(texto) {
        return texto.toLowerCase().trim()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    // Filtro productos
    const filtroProductos = document.getElementById('filtroProductos');
    if (filtroProductos) {
        const tarjetasProductos = document.querySelectorAll('#listaProductos .producto-card');
        filtroProductos.addEventListener('input', function() {
            const busqueda = normalizar(this.value);
            tarjetasProductos.forEach(card => {
                const nombre = normalizar(card.getAttribute('data-nombre') || '');
                const descripcion = normalizar(card.getAttribute('data-descripcion') || '');
                const coincide = nombre.includes(busqueda) || descripcion.includes(busqueda);
                card.style.display = coincide ? '' : 'none';
            });
        });
    }

    // Filtro servicios
    const filtroServicios = document.getElementById('filtroServicios');
    if (filtroServicios) {
        const tarjetasServicios = document.querySelectorAll('#listaServicios .servicio-card');
        filtroServicios.addEventListener('input', function() {
            const busqueda = normalizar(this.value);
            tarjetasServicios.forEach(card => {
                const nombre = normalizar(card.getAttribute('data-nombre') || '');
                const descripcion = normalizar(card.getAttribute('data-descripcion') || '');
                const coincide = nombre.includes(busqueda) || descripcion.includes(busqueda);
                card.style.display = coincide ? '' : 'none';
            });
        });
    }

    // ---------- CARRITO EMERGENTE (solo si el usuario está logueado) ----------
    <?php if ($usuario_logueado): ?>
    const btnCarrito = document.getElementById('menuCarritoPopup');
    if (btnCarrito) {
        btnCarrito.addEventListener('click', (e) => {
            e.preventDefault();
            const ancho = 800;
            const alto = 600;
            const margenDerecha = 20;
            const margenSuperior = 80;
            const izquierda = window.screen.width - ancho - margenDerecha;
            const arriba = margenSuperior;
            window.open('../user/carrito.php', 'carritoPopup', `width=${ancho},height=${alto},left=${izquierda},top=${arriba},resizable=yes,scrollbars=yes`);
        });
    }
    <?php else: ?>
    const btnCarrito = document.getElementById('menuCarritoPopup');
    if (btnCarrito) {
        btnCarrito.addEventListener('click', (e) => {
            e.preventDefault();
            mostrarMensajeLogin();
        });
    }
    <?php endif; ?>

    // ---------- FORMULARIO DE CONTACTO (DEMO) ----------
    document.getElementById('formContacto')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const div = document.getElementById('mensajeContacto');
        div.innerHTML = '<span style="color:green;">Mensaje enviado (demo). Pronto nos pondremos en contacto.</span>';
        e.target.reset();
        setTimeout(() => div.innerHTML = '', 4000);
    });

    // ---------- RESERVA DE CITA AJAX ----------
    <?php if ($usuario_logueado): ?>
    const formReserva = document.getElementById('formReservaInicio');
    if (formReserva) {
        formReserva.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(formReserva);
            const mensajeDiv = document.getElementById('mensajeCita');
            mensajeDiv.innerHTML = 'Procesando...';
            try {
                const response = await fetch('../ajax/reservas.php', { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success) {
                    mensajeDiv.innerHTML = '<span style="color:green;"> ' + data.message + '</span>';
                    formReserva.reset();
                    if (document.getElementById('fecha_cita')) document.getElementById('fecha_cita').value = '';
                    if (document.getElementById('hora_cita')) document.getElementById('hora_cita').value = '';
                } else {
                    mensajeDiv.innerHTML = '<span style="color:red;"> ' + data.message + '</span>';
                }
            } catch (err) {
                mensajeDiv.innerHTML = '<span style="color:red;">Error de conexión. Inténtalo de nuevo.</span>';
            }
            setTimeout(() => mensajeDiv.innerHTML = '', 5000);
        });
    }
    <?php endif; ?>

    
    // ---------- FLATPICKR SOLO PARA FECHA ----------
    if (document.getElementById('fecha_cita')) {
        flatpickr("#fecha_cita", {
            locale: "es",
            dateFormat: "Y-m-d",
            minDate: "today",
            altInput: true,
            altFormat: "j F, Y",
            allowInput: false,
            disable: [
                function(date) {
                    // Deshabilitar domingos (0 = domingo)
                    return date.getDay() === 0;
                }
            ],
            onChange: function(selectedDates, dateStr) {
                cargarHoras(dateStr);
            }
        });
    }

    // ---------- CARGAR HORAS DISPONIBLES ----------
    async function cargarHoras(fecha) {
        const contenedor = document.getElementById('contenedor-horas');
        const inputOculto = document.getElementById('hora_seleccionada');
        // Limpiar selección previa
        inputOculto.value = '';

        if (!fecha) {
            contenedor.innerHTML = '<p style="color:#888; font-style:italic;">Primero selecciona una fecha</p>';
            return;
        }

        contenedor.innerHTML = '<p style="color:#888;">Cargando horas...</p>';

        try {
            const respuesta = await fetch(`../ajax/obtener_horas_ocupadas.php?fecha=${fecha}`);
            const horasOcupadas = await respuesta.json();

            // Generar todas las horas permitidas en los rangos (09:00-13:30 y 16:00-20:00)
            const mañana = generarRangoHoras(9, 0, 13, 30, 30);
            const tarde = generarRangoHoras(16, 0, 20, 30, 30);
            const todasLasHoras = [...mañana, ...tarde];

            contenedor.innerHTML = '';

            todasLasHoras.forEach(hora => {
                const btn = document.createElement('div');
                btn.className = 'slot-btn';
                btn.textContent = hora;

                if (horasOcupadas.includes(hora)) {
                    btn.classList.add('ocupado');
                    btn.title = 'Ocupado';
                } else {
                    btn.addEventListener('click', () => {
                        // Quitar selección anterior
                        document.querySelectorAll('.slot-btn.seleccionado').forEach(el => el.classList.remove('seleccionado'));
                        // Marcar este como seleccionado
                        btn.classList.add('seleccionado');
                        inputOculto.value = hora;
                    });
                }

                contenedor.appendChild(btn);
            });

            if (todasLasHoras.length === 0) {
                contenedor.innerHTML = '<p style="color:#888;">No hay horarios disponibles para esta fecha.</p>';
            }

        } catch (error) {
            contenedor.innerHTML = '<p style="color:red;">Error al cargar disponibilidad</p>';
        }
    }

    // Función auxiliar para generar rango de horas (formato HH:MM) con paso en minutos
    function generarRangoHoras(hIni, mIni, hFin, mFin, pasoMin) {
        const horas = [];
        let actual = new Date();
        actual.setHours(hIni, mIni, 0, 0);
        const fin = new Date();
        fin.setHours(hFin, mFin, 0, 0);
        while (actual <= fin) {
            const hh = String(actual.getHours()).padStart(2, '0');
            const mm = String(actual.getMinutes()).padStart(2, '0');
            horas.push(`${hh}:${mm}`);
            actual.setMinutes(actual.getMinutes() + pasoMin);
        }
        return horas;
    }

    // ---------- BOTÓN SCROLL-UP (cristal) ----------
    const btnScrollTop = document.createElement('button');
    btnScrollTop.id = 'btnScrollTop';
    btnScrollTop.innerHTML = '↑';
    document.body.appendChild(btnScrollTop);

    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            btnScrollTop.classList.add('show');
        } else {
            btnScrollTop.classList.remove('show');
        }
    });

    btnScrollTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ---------- NAVEGACIÓN SUAVE PARA ANCLAS (sin interferir con botones especiales) ----------
    document.querySelectorAll('.menu-interno-header a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            if (this.id === 'menuMostrarProductos' || this.id === 'menuMostrarServicios' || this.id === 'menuMostrarCita' || this.id === 'menuCarritoPopup') return;
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const target = document.querySelector(targetId);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    });
</script>
</body>
</html>
