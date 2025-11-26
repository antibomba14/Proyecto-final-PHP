<div class="form-entrada">
    <h1><?php echo isset($entrada) && $entrada ? 'Editar Entrada' : 'Nueva Entrada'; ?></h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo esc($error); ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="formulario">
        <div class="form-grupo">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo esc($entrada['titulo'] ?? ''); ?>" required>
        </div>
        
        <div class="form-grupo">
            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">
            <?php if (!empty($entrada['imagen'])): ?>
                <p>Imagen actual: <img src="<?php echo esc($entrada['imagen']); ?>" style="max-width: 200px; margin-top: 10px;"></p>
            <?php endif; ?>
        </div>
        
        <div class="form-grupo">
            <label for="contenido">Contenido:</label>
            <textarea id="contenido" name="contenido" rows="10" required><?php echo esc($entrada['contenido'] ?? ''); ?></textarea>
        </div>
        
        <input type="hidden" name="csrf" value="<?php echo token_csrf(); ?>">
        
        <div class="botones">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="/PROYECTO_BLOG/public/admin" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
