document.getElementById("formReserva").addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerText;
    submitBtn.innerText = 'Procesando...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch("../ajax/reservas.php", {
            method: "POST",
            body: new FormData(this)
        });
        const data = await response.json();
        
        const mensajeDiv = document.getElementById("mensaje");
        mensajeDiv.style.padding = "10px";
        mensajeDiv.style.borderRadius = "4px";
        mensajeDiv.style.marginTop = "15px";
        mensajeDiv.style.textAlign = "center";
        
        if (data.success) {
            mensajeDiv.style.backgroundColor = "#d4edda";
            mensajeDiv.style.color = "#155724";
            mensajeDiv.style.border = "1px solid #c3e6cb";
            mensajeDiv.innerText = data.message;
            this.reset();
        } else {
            mensajeDiv.style.backgroundColor = "#f8d7da";
            mensajeDiv.style.color = "#721c24";
            mensajeDiv.style.border = "1px solid #f5c6cb";
            mensajeDiv.innerText = data.message;
        }
    } catch (error) {
        const mensajeDiv = document.getElementById("mensaje");
        mensajeDiv.style.backgroundColor = "#f8d7da";
        mensajeDiv.style.color = "#721c24";
        mensajeDiv.style.border = "1px solid #f5c6cb";
        mensajeDiv.innerText = 'Error de conexión';
    } finally {
        submitBtn.innerText = originalText;
        submitBtn.disabled = false;
    }
});