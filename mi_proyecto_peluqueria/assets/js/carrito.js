// Añade un producto al carrito mediante petición AJAX
function addToCart(id) {
    const btn = event.target;               // Botón que se pulsó
    const originalText = btn.innerText;     // Guardamos el texto original
    btn.innerText = 'Añadiendo...';         // Texto mientras se procesa
    btn.disabled = true;                    // Evita clics múltiples
    
    fetch("../ajax/carrito.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "producto_id=" + id
    })
    .then(res => res.json())                // Convertir respuesta a JSON
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');   // Aviso de éxito
            updateCartCount(data.cartCount);             // Actualiza contador del carrito
        } else {
            showNotification(data.message, 'error');     // Aviso de error
        }
    })
    .catch(error => {
        showNotification('Error al añadir al carrito', 'error');
    })
    .finally(() => {
        btn.innerText = originalText;        // Restaurar texto original
        btn.disabled = false;                // Reactivar botón
    });
}

// Muestra una notificación temporal (verde éxito / rojo error) (Aparece al pulsar el botón de Añadir al carrito)
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 4px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Eliminar notificación tras 3 segundos
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Actualiza el contador visual del carrito (badge sobre el icono)
function updateCartCount(count) {
    // Buscar el enlace del carrito en el header
    let cartLink = document.getElementById('menuCarritoPopup');
    if (!cartLink) return;
    
    // Crear el badge si no existe
    let badge = cartLink.querySelector('.cart-count-badge');
    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'cart-count-badge';
        badge.style.cssText = `
            position: absolute;
            top: -8px;
            right: -12px;
            background: #C6A43F;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 20px;
            font-family: sans-serif;
        `;
        cartLink.style.position = 'relative';
        cartLink.appendChild(badge);
    }
    
    // Mostrar/ocultar según el número de productos
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

// Añadir estilos CSS para las animaciones de entrada y salida
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);