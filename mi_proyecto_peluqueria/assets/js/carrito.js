function addToCart(id) {
    const btn = event.target;
    const originalText = btn.innerText;
    btn.innerText = 'Añadiendo...';
    btn.disabled = true;
    
    fetch("../ajax/carrito.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "producto_id=" + id
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            updateCartCount(data.cartCount);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error al añadir al carrito', 'error');
    })
    .finally(() => {
        btn.innerText = originalText;
        btn.disabled = false;
    });
}

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
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateCartCount(count) {
    const cartBtn = document.querySelector('.salon-btn-accent');
    if (cartBtn && cartBtn.innerText.includes('🛒')) {
        cartBtn.innerText = `🛒 Carrito (${count})`;
    }
}

// Añadir estilos para las animaciones
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