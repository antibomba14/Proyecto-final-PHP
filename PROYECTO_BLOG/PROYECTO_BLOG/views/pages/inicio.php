<div class="titulo-pagina">
    <h1>💡 Nexo de Ideas</h1>
    <p class="subtitulo-blog">Donde convergen el pensamiento y la innovación</p>
</div>

<?php if (!empty($entradas)): ?>
    <div style="margin-bottom: 30px;">
        <input 
            type="text" 
            class="search-entrada" 
            placeholder="🔍 Buscar entradas por título o contenido..."
            id="searchInput"
        >
    </div>
<?php endif; ?>

<div class="listado-entradas">
    <?php if (!empty($entradas)): ?>
        <?php foreach ($entradas as $entrada): ?>
            <article class="entrada">
                <?php if ($entrada->getImagen()): ?>
                    <img src="<?php echo esc($entrada->getImagen()); ?>" alt="<?php echo esc($entrada->getTitulo()); ?>" class="entrada-imagen">
                <?php endif; ?>
                <h2><a href="/PROYECTO_BLOG/public/entrada/<?php echo esc($entrada->getSlug()); ?>"><?php echo esc($entrada->getTitulo()); ?></a></h2>
                <div class="entrada-meta">
                    <span class="fecha"><?php echo fecha($entrada->getCreadoEn()); ?></span>
                    <span class="autor">Por: Autor</span>
                </div>
                <div class="entrada-contenido">
                    <p><?php echo truncar($entrada->getContenido(), 150); ?></p>
                </div>
                <a href="/PROYECTO_BLOG/public/entrada/<?php echo esc($entrada->getSlug()); ?>" class="leer-mas">Leer más →</a>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="sin-entradas">No hay entradas disponibles aún.</p>
    <?php endif; ?>
</div>
