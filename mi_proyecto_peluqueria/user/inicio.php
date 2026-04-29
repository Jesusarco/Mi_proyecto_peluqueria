<?php
session_start();
require_once "../config/database.php";

// Obtener servicios para el formulario de cita y para mostrar la sección
$servicios = $conn->query("SELECT id, nombre, descripcion, precio, duracion FROM servicios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos destacados o los primeros 6
$productos = $conn->query("SELECT id, nombre, descripcion, precio, imagen FROM productos WHERE stock > 0 ORDER BY destacado DESC, id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
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
        .producto-card:hover, .servicio-card:hover { transform: translateY(-5px); box-shadow: 0 6px 12px rgba(0,0,0,0.1); }
        .producto-card img { max-width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
        .precio { font-size: 1.3rem; color: var(--accent-color); font-weight: bold; }
        .btn-carrito {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
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
        .form-cita button {
            background: var(--accent-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
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
            background: rgba(0,0,0,0.7);
            transform: scale(1.1);
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
        <div class="hero">
            <h1>Bienvenido a nuestra peluquería</h1>
            <p>Descubre los mejores servicios de estilismo y cuidado personal. Profesionales a tu servicio.</p>
        </div>
    </section>

    <!-- Sección Sobre nosotros (visible siempre) -->
    <section id="sobre-nosotros" class="section">
        <h2>Sobre nosotros</h2>
        <p>Somos un equipo de profesionales apasionados por la belleza y el cuidado del cabello. Con más de 10 años de experiencia, ofrecemos servicios de alta calidad utilizando productos de primeras marcas. Tu satisfacción es nuestra mayor recompensa.</p>
        <p>Nuestro salón está diseñado para que te sientas cómodo y relajado mientras nuestros estilistas trabajan para realzar tu imagen.</p>
    </section>

    <!-- Sección Nuestros productos (oculta inicialmente) con filtro -->
    <section id="productos" class="section seccion-oculta">
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
                    <button class="btn-carrito" onclick="addToCart(<?= $p['id'] ?>)">Añadir al carrito</button>
                </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($productos) == 0): ?>
            <p>Próximamente nuevos productos.</p>
        <?php endif; ?>
    </section>

    <!-- Sección Nuestros servicios (oculta inicialmente) con filtro -->
    <section id="servicios" class="section seccion-oculta">
        <h2>Nuestros servicios</h2>
        <div class="filtro-busqueda">
            <input type="text" id="filtroServicios" placeholder=" Buscar servicio...">
        </div>
        <div class="salon-grid" id="listaServicios">
            <?php foreach ($servicios as $s): ?>
                <div class="servicio-card"
                     data-nombre="<?= htmlspecialchars($s['nombre']) ?>"
                     data-descripcion="<?= htmlspecialchars($s['descripcion'] ?? '') ?>">
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
    </section>

    <!-- Sección Pedir cita (oculta inicialmente) -->
    <section id="cita" class="section seccion-oculta">
        <h2>Pedir cita</h2>
        <p>Selecciona el servicio y la fecha que prefieras. Te confirmaremos la reserva lo antes posible.</p>
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
                <div class="form-group">
                    <label>Hora</label>
                    <input type="text" id="hora_cita" name="hora" placeholder="Selecciona hora" required>
                </div>
                <button type="submit">Reservar cita</button>
            </form>
            <div id="mensajeCita" style="margin-top:15px; text-align:center;"></div>
        </div>
    </section>

    <!-- Sección Atención al cliente (visible siempre) -->
    <section id="atencion-cliente" class="section">
        <h2>Atención al cliente</h2>
        <div class="contacto-info">
            <div class="contacto-item"><h3> Teléfono</h3><p>123 456 789</p></div>
            <div class="contacto-item"><h3> Email</h3><p>info@peluqueria.com</p></div>
            <div class="contacto-item"><h3> Dirección</h3><p>Calle Principal, 123</p></div>
        </div>
        <form id="formContacto" style="margin-top: 30px;">
            <h3>Envíanos un mensaje</h3>
            <input type="text" placeholder="Tu nombre" required style="width:100%; padding:10px; margin-bottom:10px;">
            <input type="email" placeholder="Tu email" required style="width:100%; padding:10px; margin-bottom:10px;">
            <textarea placeholder="Tu mensaje" rows="4" style="width:100%; padding:10px; margin-bottom:10px;"></textarea>
            <button type="submit" style="background:var(--accent-color); color:white; border:none; padding:10px 20px; border-radius:30px;">Enviar mensaje</button>
        </form>
        <div id="mensajeContacto" style="margin-top:15px; text-align:center;"></div>
    </section>

</div>

<?php include "../includes/footer.php"; ?>
<script src="../assets/js/carrito.js"></script>
<script>
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

    // ---------- CARRITO EMERGENTE ----------
    const btnCarrito = document.getElementById('menuCarritoPopup');
    if (btnCarrito) {
        btnCarrito.addEventListener('click', (e) => {
            e.preventDefault();
            const ancho = 800;
            const alto = 600;
            // Margen desde la derecha y desde la parte superior (debajo del header)
            const margenDerecha = 20;
            const margenSuperior = 80;  // Ajusta según la altura de tu header
            const izquierda = window.screen.width - ancho - margenDerecha;
            const arriba = margenSuperior;
            window.open('../user/carrito.php', 'carritoPopup', `width=${ancho},height=${alto},left=${izquierda},top=${arriba},resizable=yes,scrollbars=yes`);
        });
    }

    // ---------- FORMULARIO DE CONTACTO (DEMO) ----------
    document.getElementById('formContacto')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const div = document.getElementById('mensajeContacto');
        div.innerHTML = '<span style="color:green;">Mensaje enviado (demo). Pronto nos pondremos en contacto.</span>';
        e.target.reset();
        setTimeout(() => div.innerHTML = '', 4000);
    });

    // ---------- RESERVA DE CITA AJAX ----------
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
                    // limpiar campos Flatpickr
                    if (document.getElementById('fecha_cita')) document.getElementById('fecha_cita').value = '';
                    if (document.getElementById('hora_cita')) document.getElementById('hora_cita').value = '';
                } else {
                    mensajeDiv.innerHTML = '<span style="color:red;">❌ ' + data.message + '</span>';
                }
            } catch (err) {
                mensajeDiv.innerHTML = '<span style="color:red;">Error de conexión. Inténtalo de nuevo.</span>';
            }
            setTimeout(() => mensajeDiv.innerHTML = '', 5000);
        });
    }

    // ---------- FLATPICKR para fecha y hora ----------
    if (document.getElementById('fecha_cita')) {
        flatpickr("#fecha_cita", {
            locale: "es",
            dateFormat: "Y-m-d",
            minDate: "today",
            altInput: true,
            altFormat: "j F, Y",
            allowInput: false
        });
    }
    if (document.getElementById('hora_cita')) {
        flatpickr("#hora_cita", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 30,
            minTime: "10:00",
            maxTime: "20:00"
        });
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