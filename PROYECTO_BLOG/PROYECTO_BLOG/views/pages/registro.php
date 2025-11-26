<div class="formulario-container">
    <h1>Registro</h1>
    
    <?php if (!empty($errores)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/PROYECTO_BLOG/public/registro" class="formulario">
        <div class="form-grupo">
            <label for="nombre">Usuario:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        
        <div class="form-grupo">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-grupo">
            <label for="clave">Contraseña:</label>
            <input type="password" id="clave" name="clave" required>
        </div>
        
        <div class="form-grupo">
            <label for="clave_conf">Confirmar Contraseña:</label>
            <input type="password" id="clave_conf" name="clave_conf" required>
        </div>
        
        <input type="hidden" name="csrf" value="<?php echo token_csrf(); ?>">
        
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
    
    <p class="enlace-login">
        ¿Ya tienes cuenta? <a href="/PROYECTO_BLOG/public/acceder">Accede aquí</a>
    </p>
</div>
