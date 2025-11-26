<div class="dashboard">
    <h1>Panel de Control</h1>
    
    <div class="bienvenida">
        <p>Bienvenido, <strong><?php echo esc(usuario_nombre()); ?></strong></p>
    </div>
    
    <div class="acciones">
        <a href="/PROYECTO_BLOG/public/admin/entrada/crear" class="btn btn-primary">+ Nueva Entrada</a>
        <?php if (es_admin()): ?>
            <a href="/PROYECTO_BLOG/public/admin/usuarios" class="btn btn-secondary">Gestionar Usuarios</a>
        <?php endif; ?>
    </div>
    
    <h2>Tus Entradas</h2>
    
    <?php if (!empty($entradas)): ?>
        <table class="tabla-entradas">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entradas as $entrada): ?>
                    <tr>
                        <td><?php echo esc($entrada->getTitulo()); ?></td>
                        <td><?php echo fecha($entrada->getCreadoEn()); ?></td>
                        <td>
                            <a href="/PROYECTO_BLOG/public/admin/entrada/<?php echo $entrada->getId(); ?>/editar" class="btn-pequeño">Editar</a>
                            <form method="POST" action="/PROYECTO_BLOG/public/admin/entrada/<?php echo $entrada->getId(); ?>/eliminar" class="form-inline" onsubmit="return confirm('¿Eliminar esta entrada?');">
                                <input type="hidden" name="csrf" value="<?php echo token_csrf(); ?>">
                                <button type="submit" class="btn-pequeño btn-eliminar">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No tienes entradas aún. <a href="/PROYECTO_BLOG/public/admin/entrada/crear">Crea una</a></p>
    <?php endif; ?>
</div>
