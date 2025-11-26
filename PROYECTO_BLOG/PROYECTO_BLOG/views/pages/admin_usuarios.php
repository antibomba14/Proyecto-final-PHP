<div class="admin-usuarios">
    <h1>Gestionar Usuarios</h1>
    
    <?php if (!empty($usuarios)): ?>
        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo esc($usuario->getNombre()); ?></td>
                        <td><?php echo esc($usuario->getEmail() ?? '-'); ?></td>
                        <td>
                            <form method="POST" action="/PROYECTO_BLOG/public/admin/usuario/<?php echo $usuario->getId(); ?>/rol" class="form-rol">
                                <select name="rol" onchange="this.form.submit()">
                                    <option value="lector" <?php echo $usuario->getRol() === 'lector' ? 'selected' : ''; ?>>Lector</option>
                                    <option value="editor" <?php echo $usuario->getRol() === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                    <option value="admin" <?php echo $usuario->getRol() === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <input type="hidden" name="csrf" value="<?php echo token_csrf(); ?>">
                            </form>
                        </td>
                        <td>
                            <span class="usuario-id">ID: <?php echo $usuario->getId(); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay usuarios registrados.</p>
    <?php endif; ?>
</div>
