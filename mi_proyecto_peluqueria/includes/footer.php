</main>

<!-- ==================== PIE DE PÁGINA (FOOTER) ==================== -->
<footer class="pelu-footer">
    <!-- Línea dorada decorativa superior -->
    <div class="footer-gold-line"></div>

    <div class="footer-container">

        <!-- Grid con las 4 columnas principales -->
        <div class="footer-grid">

            <!-- Columna 1: Marca y descripción -->
            <div>
                <div class="footer-brand">
                    Áurea<em>Studio</em>
                </div>
                <p class="footer-description">
                    Expertos en estilismo y cuidado personal. Tu imagen, nuestra pasión desde 2026.
                </p>
                <div class="footer-separator"></div>
            </div>

            <!-- Columna 2: Navegación (enlaces internos) -->
            <div>
                <h5 class="footer-title">Navegación</h5>
                <ul class="footer-nav-list">
                    <!-- Enlace a Inicio (redirige a inicio.php) -->
                    <li><a href="../user/inicio.php" id="menuInicioFooter">Inicio</a></li>
                    <!-- Scroll suave a la sección 'sobre-nosotros' -->
                    <li><a href="#sobre-nosotros" id="menuSobreNosotrosFooter">El estudio</a></li>
                    <!-- Muestra la sección de servicios (JavaScript) -->
                    <li><a href="#" id="menuMostrarServiciosFooter">Servicios</a></li>
                    <!-- Muestra la sección de productos (JavaScript) -->
                    <li><a href="#" id="menuMostrarProductosFooter">Productos</a></li>
                    <!-- Muestra la sección de cita (JavaScript) -->
                    <li><a href="#" id="menuMostrarCitaFooter">Cita</a></li>
                    <!-- Scroll suave a la sección de contacto -->
                    <li><a href="#atencion-cliente" id="menuContactoFooter">Contacto</a></li>
                </ul>
            </div>

            <!-- Columna 3: Horario de apertura -->
            <div>
                <h5 class="footer-title">Horario</h5>
                <div class="footer-hours">
                    <div class="hours-row">
                        <span class="hours-label">Lunes – Viernes</span>
                        <span class="hours-value">09:00 – 13:30 | 16:00 – 20:30</span>
                    </div>
                    <div class="hours-row">
                        <span class="hours-label">Sábados</span>
                        <span class="hours-value">09:00 – 13:30 | 16:00 – 20:30</span>
                    </div>
                    <div class="hours-sunday">
                        Domingos cerrado
                    </div>
                </div>
            </div>

            <!-- Columna 4: Información de contacto -->
            <div>
                <h5 class="footer-title">Contacto</h5>
                <div class="footer-contact">
                    <div class="contact-item">
                        <i class="bi bi-telephone-fill contact-icon"></i>
                        <span>645 87 87 21</span>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-envelope-fill contact-icon"></i>
                        <span>info@peluqueria.com</span>
                    </div>
                    <div class="contact-item-start">
                        <i class="bi bi-geo-alt-fill contact-icon"></i>
                        <span style="line-height: 1.5;">Calle Mayor, 12<br>28001 Madrid</span>
                    </div>
                </div>
            </div>

        </div> <!-- fin footer-grid -->

        <!-- Línea divisoria y copyright -->
        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; <?= date('Y') ?> ÁureaStudios &nbsp;&middot;&nbsp; Elegancia y Estilo &nbsp;&middot;&nbsp; Todos los derechos reservados
            </p>
            <p class="footer-credits">
                Hecho con cuidado &amp; pasión
            </p>
        </div>

    </div> <!-- fin footer-container -->
</footer>