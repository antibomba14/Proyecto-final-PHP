<header class="header">
    <nav class="navbar">
        <div class="contenedor navbar-contenedor">
            <div class="logo">
                <a href="/PROYECTO_BLOG/public/">💡 Nexo de Ideas</a>
            </div>
            <ul class="nav-menu">
                <li><a href="/PROYECTO_BLOG/public/">Inicio</a></li>
                <?php if (!autenticado()): ?>
                    <li><a href="/PROYECTO_BLOG/public/acceder">Acceder</a></li>
                    <li><a href="/PROYECTO_BLOG/public/registro">Registrarse</a></li>
                <?php else: ?>
                    <li><a href="/PROYECTO_BLOG/public/admin">Admin</a></li>
                    <li>
                        <form method="POST" action="/PROYECTO_BLOG/public/salir" style="display:inline;">
                            <input type="hidden" name="csrf" value="<?php echo token_csrf(); ?>">
                            <button type="submit" class="btn-salir">Salir</button>
                        </form>
                    </li>
                <?php endif; ?>
                <li>
                    <button id="temaBtn" class="btn-tema" title="Cambiar tema">🌙</button>
                </li>
            </ul>
        </div>
    </nav>
</header>
