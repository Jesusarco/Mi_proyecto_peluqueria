</main>

<footer style="
    background: #111110;
    border-top: 1px solid rgba(201,168,76,0.15);
    margin-top: 60px;
    font-family: 'Jost', sans-serif;
">
    <!-- Línea dorada decorativa -->
    <div style="height: 2px; background: linear-gradient(90deg, transparent, #C9A84C 30%, #e8c97a 50%, #C9A84C 70%, transparent); opacity: 0.6;"></div>

    <div style="max-width: 1200px; margin: 0 auto; padding: 55px 30px 35px;">

        <!-- Grid superior -->
        <div style="display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 50px;">

            <!-- Columna marca -->
            <div>
                <div style="
                    font-family: 'Cormorant Garamond', serif;
                    font-size: 1.5rem;
                    font-weight: 300;
                    letter-spacing: 0.3em;
                    color: #C9A84C;
                    text-transform: uppercase;
                    margin-bottom: 18px;
                ">
                    Pelu<em style="color:#fff; font-style:italic;">quer&iacute;a</em>
                </div>
                <p style="
                    color: #666660;
                    font-size: 0.85rem;
                    font-weight: 300;
                    line-height: 1.8;
                    max-width: 220px;
                    margin: 0;
                ">
                    Expertos en estilismo y cuidado personal. Tu imagen, nuestra pasión desde 2026.
                </p>
                <!-- Separador decorativo -->
                <div style="width: 40px; height: 1px; background: #C9A84C; margin-top: 22px; opacity: 0.5;"></div>
            </div>

            <!-- Navegación -->
            <div>
                <h5 style="
                    font-family: 'Jost', sans-serif;
                    font-size: 0.68rem;
                    font-weight: 500;
                    letter-spacing: 0.25em;
                    text-transform: uppercase;
                    color: #C9A84C;
                    margin: 0 0 20px 0;
                ">Navegación</h5>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php
                    $links = [
                        ['../user/inicio.php', 'Inicio'],
                        ['../user/tienda.php', 'Tienda Online'],
                        ['../user/reservas.php', 'Reservar Cita'],
                    ];
                    foreach ($links as [$href, $label]):
                    ?>
                    <li style="margin-bottom: 12px;">
                        <a href="<?= $href ?>" style="
                            color: #666660;
                            text-decoration: none;
                            font-size: 0.82rem;
                            font-weight: 300;
                            letter-spacing: 0.05em;
                            transition: color 0.2s;
                            display: inline-flex;
                            align-items: center;
                            gap: 8px;
                        "
                        onmouseover="this.style.color='#C9A84C'"
                        onmouseout="this.style.color='#666660'"
                        >
                            <span style="color: rgba(201,168,76,0.4); font-size: 0.6rem;">▸</span>
                            <?= $label ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Horario -->
            <div>
                <h5 style="
                    font-family: 'Jost', sans-serif;
                    font-size: 0.68rem;
                    font-weight: 500;
                    letter-spacing: 0.25em;
                    text-transform: uppercase;
                    color: #C9A84C;
                    margin: 0 0 20px 0;
                ">Horario</h5>
                <div style="color: #666660; font-size: 0.82rem; font-weight: 300; line-height: 2;">
                    <div style="display:flex; justify-content:space-between; gap:20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; margin-bottom: 8px;">
                        <span>Lun – Vie</span>
                        <span style="color: #999990;">10:00 – 20:00</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; gap:20px;">
                        <span>Sábados</span>
                        <span style="color: #999990;">09:00 – 14:00</span>
                    </div>
                    <div style="margin-top: 16px; font-size: 0.75rem; color: #555550; font-style: italic; letter-spacing: 0.05em;">
                        Domingos cerrado
                    </div>
                </div>
            </div>

            <!-- Contacto -->
            <div>
                <h5 style="
                    font-family: 'Jost', sans-serif;
                    font-size: 0.68rem;
                    font-weight: 500;
                    letter-spacing: 0.25em;
                    text-transform: uppercase;
                    color: #C9A84C;
                    margin: 0 0 20px 0;
                ">Contacto</h5>
                <div style="font-size: 0.82rem; font-weight: 300; line-height: 2.2;">
                    <div style="color: #666660; display: flex; align-items: center; gap: 10px;">
                        <span style="color: #C9A84C; font-size: 0.9rem;">✆</span>
                        <span>123 456 789</span>
                    </div>
                    <div style="color: #666660; display: flex; align-items: center; gap: 10px;">
                        <span style="color: #C9A84C; font-size: 0.9rem;">✉</span>
                        <span>info@peluqueria.es</span>
                    </div>
                    <div style="color: #666660; display: flex; align-items: flex-start; gap: 10px; margin-top: 4px;">
                        <span style="color: #C9A84C; font-size: 0.9rem;">⌖</span>
                        <span style="line-height: 1.5;">Calle Mayor, 12<br>28001 Madrid</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Línea divisoria -->
        <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <p style="
                color: #444440;
                font-size: 0.72rem;
                font-weight: 300;
                letter-spacing: 0.08em;
                margin: 0;
            ">
                &copy; <?= date('Y') ?> Peluquería Profesional &nbsp;&middot;&nbsp; Elegancia y Estilo &nbsp;&middot;&nbsp; Todos los derechos reservados
            </p>
            <p style="
                color: #333330;
                font-size: 0.68rem;
                font-weight: 300;
                letter-spacing: 0.05em;
                margin: 0;
                font-style: italic;
            ">
                Hecho con cuidado &amp; pasión
            </p>
        </div>

    </div>
</footer>

</body>
</html>