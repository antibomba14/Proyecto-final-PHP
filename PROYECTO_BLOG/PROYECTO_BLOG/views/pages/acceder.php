<div class="formulario-container">
    <h1>Iniciar Sesión</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo esc($error); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-success"><?php echo esc($mensaje); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="/PROYECTO_BLOG/public/acceder" class="formulario">
        <div class="form-grupo">
            <label for="nombre">Usuario:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        
        <div class="form-grupo">
            <label for="clave">Contraseña:</label>
            <input type="password" id="clave" name="clave" required>
        </div>
        
        <input type="hidden" name="csrf" value="<?php echo token_csrf(); ?>">
        
        <button type="submit" class="btn btn-primary">Acceder</button>
    </form>
    
    <p class="enlace-registro">
        ¿No tienes cuenta? <a href="/PROYECTO_BLOG/public/registro">Regístrate aquí</a>
    </p>
</div>
