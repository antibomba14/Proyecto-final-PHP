<div class="entrada-completa">
    <h1><?php echo esc($entrada['titulo'] ?? ''); ?></h1>
    <?php if (!empty($entrada['imagen'])): ?>
        <img src="<?php echo esc($entrada['imagen']); ?>" alt="<?php echo esc($entrada['titulo']); ?>" class="entrada-imagen-completa">
    <?php endif; ?>
    <div class="entrada-meta">
        <span class="fecha"><?php echo fecha($entrada['creado_en'] ?? ''); ?></span>
        <?php if ($autor): ?>
            <span class="autor">Por: <?php echo esc($autor['nombre'] ?? 'Anónimo'); ?></span>
        <?php endif; ?>
    </div>
    <div class="entrada-body">
        <?php echo nl2br(esc($entrada['contenido'] ?? '')); ?>
    </div>
</div>

<div class="volver-listado">
    <a href="/PROYECTO_BLOG/public/">← Volver al listado</a>
</div>
