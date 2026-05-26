// ====================== FUNCIONES GLOBALES (usadas desde HTML) ======================

/**
 * Redirige al login cuando un visitante no autenticado intenta comprar.
 */
function mostrarMensajeLogin() {
    alert("Debes iniciar sesión o registrarte para comprar productos.");
    window.location.href = "../auth/login.php";
}

/**
 * Normaliza un texto: minúsculas, sin acentos, sin espacios extra.
 * Útil para búsquedas en filtros.
 */
function normalizar(texto) {
    return texto.toLowerCase().trim().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

/**
 * Genera un array de horas (HH:MM) dentro de un rango con un paso en minutos.
 * Se usa para mostrar franjas horarias disponibles (mañana y tarde).
 */
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

// ====================== INICIALIZACIONES AL CARGAR EL DOM ======================
document.addEventListener('DOMContentLoaded', function() {

    // ---------- CARRUSEL HERO (Swiper) ----------
    // Configuración: loop infinito, autoplay cada 5s, efecto fade, paginación y navegación.
    const swiper = new Swiper('.hero-slider', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        effect: 'fade',
        fadeEffect: { crossFade: true }
    });

    // ---------- MOSTRAR/OCULTAR SECCIONES (menú principal) ----------
    // Botones del header que abren las secciones de Productos, Servicios y Cita.
    const btnProductos = document.getElementById('menuMostrarProductos');
    const btnServicios = document.getElementById('menuMostrarServicios');
    const btnCita = document.getElementById('menuMostrarCita');
    const seccionProductos = document.getElementById('productos');
    const seccionServicios = document.getElementById('servicios');
    const seccionCita = document.getElementById('cita');

    // Oculta todas las secciones "ocultables" (productos, servicios, cita).
    function ocultarTodas() {
        if (seccionProductos) seccionProductos.classList.add('seccion-oculta');
        if (seccionServicios) seccionServicios.classList.add('seccion-oculta');
        if (seccionCita) seccionCita.classList.add('seccion-oculta');
    }

    // Asignar eventos a cada botón del menú.
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

    // ---------- FILTROS EN TIEMPO REAL (productos y servicios) ----------
    // Filtro de productos: oculta tarjetas cuyo nombre/descripción no coincidan.
    const filtroProductos = document.getElementById('filtroProductos');
    if (filtroProductos) {
        const tarjetasProductos = document.querySelectorAll('#listaProductos .producto-card');
        filtroProductos.addEventListener('input', function() {
            const busqueda = normalizar(this.value);
            tarjetasProductos.forEach(card => {
                const nombre = normalizar(card.getAttribute('data-nombre') || '');
                const descripcion = normalizar(card.getAttribute('data-descripcion') || '');
                card.style.display = (nombre.includes(busqueda) || descripcion.includes(busqueda)) ? '' : 'none';
            });
        });
    }

    // Filtro de servicios, análogo al de productos.
    const filtroServicios = document.getElementById('filtroServicios');
    if (filtroServicios) {
        const tarjetasServicios = document.querySelectorAll('#listaServicios .servicio-card');
        filtroServicios.addEventListener('input', function() {
            const busqueda = normalizar(this.value);
            tarjetasServicios.forEach(card => {
                const nombre = normalizar(card.getAttribute('data-nombre') || '');
                const descripcion = normalizar(card.getAttribute('data-descripcion') || '');
                card.style.display = (nombre.includes(busqueda) || descripcion.includes(busqueda)) ? '' : 'none';
            });
        });
    }

    // ---------- CARRITO (popup) ----------
    // Abre una ventana emergente con el carrito si el usuario está logueado;
    // de lo contrario, muestra mensaje de login.
    const btnCarrito = document.getElementById('menuCarritoPopup');
    if (btnCarrito) {
        if (typeof usuarioLogueado !== 'undefined' && usuarioLogueado) {
            btnCarrito.addEventListener('click', (e) => {
                e.preventDefault();
                const ancho = 800, alto = 600, margenDerecha = 20, margenSuperior = 80;
                const izquierda = window.screen.width - ancho - margenDerecha;
                const arriba = margenSuperior;
                window.open('../user/carrito.php', 'carritoPopup', `width=${ancho},height=${alto},left=${izquierda},top=${arriba},resizable=yes,scrollbars=yes`);
            });
        } else {
            btnCarrito.addEventListener('click', (e) => {
                e.preventDefault();
                mostrarMensajeLogin();
            });
        }
    }

    // ---------- FORMULARIO DE CONTACTO (simulación) ----------
    // Solo muestra un mensaje de éxito y limpia el formulario. No envía datos reales.
    const formContacto = document.getElementById('formContacto');
    if (formContacto) {
        formContacto.addEventListener('submit', (e) => {
            e.preventDefault();
            const div = document.getElementById('mensajeContacto');
            div.innerHTML = '<span style="color:green;">Mensaje enviado (demo). Pronto nos pondremos en contacto.</span>';
            e.target.reset();
            setTimeout(() => div.innerHTML = '', 4000);
        });
    }

    // ---------- RESERVA DE CITA (AJAX) ----------
    // Envía los datos del formulario a reservas.php y muestra el resultado.
    const formReserva = document.getElementById('formReservaInicio');
    if (formReserva && typeof usuarioLogueado !== 'undefined' && usuarioLogueado) {
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
                    const fechaInput = document.getElementById('fecha_cita');
                    if (fechaInput) fechaInput.value = '';
                    const horaInput = document.getElementById('hora_cita');
                    if (horaInput) horaInput.value = '';
                } else {
                    mensajeDiv.innerHTML = '<span style="color:red;"> ' + data.message + '</span>';
                }
            } catch (err) {
                mensajeDiv.innerHTML = '<span style="color:red;">Error de conexión. Inténtalo de nuevo.</span>';
            }
            setTimeout(() => mensajeDiv.innerHTML = '', 5000);
        });
    }

    // ---------- FLATPICKR (selector de fecha) ----------
    // Configura el calendario: idioma español, fecha mínima hoy, domingos deshabilitados.
    const fechaCita = document.getElementById('fecha_cita');
    if (fechaCita) {
        flatpickr("#fecha_cita", {
            locale: "es",
            dateFormat: "Y-m-d",
            minDate: "today",
            altInput: true,
            altFormat: "j F, Y",
            allowInput: false,
            disable: [function(date) { return date.getDay() === 0; }], // domingo
            onChange: function(selectedDates, dateStr) {
                cargarHoras(dateStr);
            }
        });
    }

    // ---------- CARGAR HORAS DISPONIBLES (según fecha seleccionada) ----------
    // Consulta al servidor las horas ocupadas y genera botones interactivos.
    async function cargarHoras(fecha) {
        const contenedor = document.getElementById('contenedor-horas');
        const inputOculto = document.getElementById('hora_seleccionada');
        inputOculto.value = '';
        if (!fecha) {
            contenedor.innerHTML = '<p style="color:#888; font-style:italic;">Primero selecciona una fecha</p>';
            return;
        }
        contenedor.innerHTML = '<p style="color:#888;">Cargando horas...</p>';
        try {
            const respuesta = await fetch(`../ajax/obtener_horas_ocupadas.php?fecha=${fecha}`);
            const horasOcupadas = await respuesta.json();
            const manana = generarRangoHoras(9, 0, 13, 30, 30);
            const tarde = generarRangoHoras(16, 0, 20, 30, 30);
            const todasLasHoras = [...manana, ...tarde];
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
                        document.querySelectorAll('.slot-btn.seleccionado').forEach(el => el.classList.remove('seleccionado'));
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

    // ---------- BOTÓN SCROLL-UP (flotante para volver arriba) ----------
    // Se crea dinámicamente, aparece al hacer scroll > 300px y permite volver al inicio suavemente.
    const btnScrollTop = document.createElement('button');
    btnScrollTop.id = 'btnScrollTop';
    btnScrollTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
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

    // ---------- NAVEGACIÓN SUAVE PARA ANCLAS (enlaces del menú interno) ----------
    // Para los enlaces que apuntan a un ID (#...), excepto los botones de productos/servicios/cita/carrito.
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

    // ===== ENLACES DEL FOOTER (mismo comportamiento que el menú principal) =====
    const btnSobreNosotrosFooter = document.getElementById('menuSobreNosotrosFooter');
    if (btnSobreNosotrosFooter) {
        btnSobreNosotrosFooter.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector('#sobre-nosotros');
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    }
    const btnServiciosFooter = document.getElementById('menuMostrarServiciosFooter');
    if (btnServiciosFooter) {
        btnServiciosFooter.addEventListener('click', (e) => {
            e.preventDefault();
            ocultarTodas();
            if (seccionServicios) seccionServicios.classList.remove('seccion-oculta');
            seccionServicios.scrollIntoView({ behavior: 'smooth' });
        });
    }
    const btnProductosFooter = document.getElementById('menuMostrarProductosFooter');
    if (btnProductosFooter) {
        btnProductosFooter.addEventListener('click', (e) => {
            e.preventDefault();
            ocultarTodas();
            if (seccionProductos) seccionProductos.classList.remove('seccion-oculta');
            seccionProductos.scrollIntoView({ behavior: 'smooth' });
        });
    }
    const btnCitaFooter = document.getElementById('menuMostrarCitaFooter');
    if (btnCitaFooter) {
        btnCitaFooter.addEventListener('click', (e) => {
            e.preventDefault();
            ocultarTodas();
            if (seccionCita) seccionCita.classList.remove('seccion-oculta');
            seccionCita.scrollIntoView({ behavior: 'smooth' });
        });
    }
    const btnContactoFooter = document.getElementById('menuContactoFooter');
    if (btnContactoFooter) {
        btnContactoFooter.addEventListener('click', (e) => {
            e.preventDefault();
            const target = document.querySelector('#atencion-cliente');
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    }
});