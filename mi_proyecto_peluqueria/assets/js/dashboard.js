// ====================== INICIALIZACIÓN AL CARGAR EL DOM ======================
document.addEventListener('DOMContentLoaded', function() {

    // ---------- OVERLAYS DE CREACIÓN (producto, servicio, gasto) ----------
    // Referencias a los elementos overlay (ventanas modales) y a sus botones de apertura.
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

    // Muestra el overlay correspondiente añadiendo la clase 'visible'
    function abrirOverlay(nombre) {
        if (overlays[nombre]) overlays[nombre].classList.add('visible');
    }
    // Oculta todos los overlays de creación
    function cerrarOverlays() {
        for (let key in overlays) {
            if (overlays[key]) overlays[key].classList.remove('visible');
        }
    }

    // Asignar eventos a los botones de apertura
    if (btns.producto) btns.producto.addEventListener('click', () => abrirOverlay('producto'));
    if (btns.servicio) btns.servicio.addEventListener('click', () => abrirOverlay('servicio'));
    if (btns.gasto) btns.gasto.addEventListener('click', () => abrirOverlay('gasto'));

    // Cerrar overlays al hacer clic en el botón "Cancelar" o fuera del overlay
    cerrarBtns.forEach(btn => btn.addEventListener('click', cerrarOverlays));
    window.addEventListener('click', (e) => {
        for (let key in overlays) {
            if (e.target === overlays[key]) cerrarOverlays();
        }
    });

    // ---------- FILTROS LATERALES (menú de la izquierda) ----------
    // Enlaces que filtran las tablas del panel de administración.
    const filterLinks = document.querySelectorAll('.filter-link');
    const secciones = {
        productos: document.getElementById('tabla-productos'),
        servicios: document.getElementById('tabla-servicios'),
        citas: document.getElementById('tabla-citas'),
        pedidos: document.getElementById('tabla-pedidos'),
        gastos: document.getElementById('tabla-gastos'),
        usuarios: document.getElementById('tabla-usuarios')
    };

    // Muestra todas las tablas
    function mostrarTodas() {
        for (let key in secciones) {
            if (secciones[key]) secciones[key].classList.remove('oculto');
        }
    }
    // Oculta todas las tablas
    function ocultarTodas() {
        for (let key in secciones) {
            if (secciones[key]) secciones[key].classList.add('oculto');
        }
    }
    // Marca el enlace activo en el menú lateral
    function setActiveLink(link) {
        filterLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
    }

    // Evento para cada enlace de filtro: muestra la tabla correspondiente.
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

    // Inicialmente mostrar todas las tablas y marcar "Todos" como activo.
    mostrarTodas();
    if (!document.querySelector('.filter-link.active')) {
        const todos = document.querySelector('.filter-link[data-tabla="todos"]');
        if (todos) todos.classList.add('active');
    }

    // ---------- EDICIÓN DE PRODUCTO ----------
    // Botones "Editar" en la tabla de productos → abren overlay con datos precargados.
    const editProdBtns = document.querySelectorAll('.btn-editar');
    const overlayEditProd = document.getElementById('overlayEditarProducto');
    if (editProdBtns.length) {
        editProdBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Rellenar campos del formulario con los datos del producto
                document.getElementById('edit_prod_id').value = btn.dataset.id;
                document.getElementById('edit_prod_nombre').value = btn.dataset.nombre;
                document.getElementById('edit_prod_descripcion').value = btn.dataset.descripcion;
                document.getElementById('edit_prod_precio').value = btn.dataset.precio;
                document.getElementById('edit_prod_stock').value = btn.dataset.stock;
                document.getElementById('edit_prod_destacado').checked = (btn.dataset.destacado == '1');
                // Mostrar vista previa de la imagen actual
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

    // ---------- EDICIÓN DE SERVICIO (análogo a producto) ----------
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
                // Previsualizar imagen del servicio
                const imgActual = document.getElementById('edit_ser_img_actual');
                const sinImg = document.getElementById('edit_ser_sin_imagen');
                if (btn.dataset.imagen && btn.dataset.imagen !== '') {
                    imgActual.src = '../uploads/' + btn.dataset.imagen;
                    imgActual.style.display = 'block';
                    sinImg.style.display = 'none';
                } else {
                    imgActual.style.display = 'none';
                    sinImg.style.display = 'block';
                }
                overlayEditServ.classList.add('visible');
            });
        });
    }

    // ---------- EDICIÓN DE GASTO ----------
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

    // ---------- EDICIÓN DE CITA ----------
    // Extrae cliente y servicio de la fila actual de la tabla.
    const editCitaBtns = document.querySelectorAll('.btn-editar-cita');
    const overlayEditCita = document.getElementById('overlayEditarCita');
    if (editCitaBtns.length) {
        editCitaBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const fila = btn.closest('tr');
                const cliente = fila.cells[1].innerText;
                const servicio = fila.cells[2].innerText;
                document.getElementById('edit_cita_id').value = btn.dataset.id;
                document.getElementById('edit_cita_cliente').value = cliente;
                document.getElementById('edit_cita_servicio').value = servicio;
                document.getElementById('edit_cita_fecha').value = btn.dataset.fecha;
                document.getElementById('edit_cita_hora').value = btn.dataset.hora;
                overlayEditCita.classList.add('visible');
            });
        });
    }

    // ---------- CERRAR OVERLAYS DE EDICIÓN (botones "Cancelar" y clic externo) ----------
    const cerrarEditBtns = document.querySelectorAll('.cerrar-editar');
    cerrarEditBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            overlayEditProd.classList.remove('visible');
            overlayEditServ.classList.remove('visible');
            overlayEditGasto.classList.remove('visible');
        });
    });
    const cerrarEditCitaBtns = document.querySelectorAll('.cerrar-editar-cita');
    cerrarEditCitaBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            overlayEditCita.classList.remove('visible');
        });
    });
    window.addEventListener('click', (e) => {
        if (e.target === overlayEditProd) overlayEditProd.classList.remove('visible');
        if (e.target === overlayEditServ) overlayEditServ.classList.remove('visible');
        if (e.target === overlayEditGasto) overlayEditGasto.classList.remove('visible');
        if (e.target === overlayEditCita) overlayEditCita.classList.remove('visible');
    });

    // ---------- FILTROS EN TIEMPO REAL SOBRE LAS TABLAS ----------
    // Oculta filas que no coincidan con el texto buscado.
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

    const filtros = document.querySelectorAll('.filtro-tabla');
    filtros.forEach(filtro => {
        const tablaId = filtro.getAttribute('data-tabla');
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
    
}); // fin DOMContentLoaded

// ====================== EDICIÓN DE CLIENTE (USUARIO) ======================
// Esta parte está fuera del DOMContentLoaded porque los elementos existen desde el inicio.
const editUserBtns = document.querySelectorAll('.btn-editar-usuario');
const overlayEditUser = document.getElementById('overlayEditarUsuario');
if (editUserBtns.length) {
    editUserBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit_usuario_id').value = btn.dataset.id;
            document.getElementById('edit_usuario_nombre').value = btn.dataset.nombre;
            document.getElementById('edit_usuario_email').value = btn.dataset.email;
            overlayEditUser.classList.add('visible');
        });
    });
}

// Cerrar overlay de cliente con el botón "Cancelar"
document.querySelectorAll('.cerrar-editar-usuario').forEach(btn => {
    btn.addEventListener('click', () => {
        overlayEditUser.classList.remove('visible');
    });
});

// Cerrar overlay de cliente al hacer clic fuera de él
window.addEventListener('click', (e) => {
    if (e.target === overlayEditUser) overlayEditUser.classList.remove('visible');
});