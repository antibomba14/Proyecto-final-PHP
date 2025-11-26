<?php

require_once __DIR__ . '/../config/bootstrap.php';

use Blog\Models\Database;
use Blog\Models\Sesion;
use Blog\Models\Usuario;
use Blog\Models\Entrada;
use Blog\Utils\Util;

function render_view(string $file, array $vars = []): void {
    extract($vars);
    ob_start();
    include VIEWS_PATH . "/$file.php";
    $contenido = ob_get_clean();
    include VIEWS_PATH . '/layouts/base.php';
}

function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function procesarImagen(): ?string {
    if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['imagen'];
        $permitidos = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $permitidos)) {
            $nombre = uniqid('img_') . '.' . $ext;
            $ruta = __DIR__ . '/uploads/' . $nombre;

            if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
                return '/PROYECTO_BLOG/public/uploads/' . $nombre;
            }
        }
    }

    $imagenesDefault = [
        '/PROYECTO_BLOG/public/uploads/demo/bosque.jpg',
        '/PROYECTO_BLOG/public/uploads/demo/cafe.jpg',
        '/PROYECTO_BLOG/public/uploads/demo/ciudad.jpg',
        '/PROYECTO_BLOG/public/uploads/demo/noche.jpg',
        '/PROYECTO_BLOG/public/uploads/demo/playa.jpg',
        '/PROYECTO_BLOG/public/uploads/demo/teclado.jpg'
    ];

    return $imagenesDefault[array_rand($imagenesDefault)];
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$base = str_replace('/PROYECTO_BLOG/public', '', $uri);
if ($base !== '/' && str_ends_with($base, '/')) {
    $base = rtrim($base, '/');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Inicio
if ($base === '/' || $base === '') {
    $entradas = Entrada::todas();
    render_view('pages/inicio', ['entradas' => $entradas]);
    exit;
}

// Ver entrada
if (preg_match('#^/entrada/([a-z0-9-]+)$#i', $base, $m)) {
    $entrada = Entrada::porSlug($m[1]);
    if (!$entrada) {
        http_response_code(404);
        render_view('errors/404', ['mensaje' => 'Entrada no encontrada']);
        exit;
    }
    $autor = Usuario::porId($entrada->getAutorId() ?? 0);
    render_view('pages/entrada_ver', [
        'entrada' => $entrada->toArray(),
        'autor' => $autor?->toArray()
    ]);
    exit;
}

if (preg_match('#^/entrada/(\d+)$#', $base, $m)) {
    $entrada = Entrada::porId((int)$m[1]);
    if (!$entrada) {
        http_response_code(404);
        render_view('errors/404', ['mensaje' => 'Entrada no encontrada']);
        exit;
    }
    redirect('/entrada/' . $entrada->getSlug());
}

// Login
if ($base === '/acceder' && $method === 'GET') {
    if (Sesion::estaAutenticado()) {
        redirect('/admin');
    }
    render_view('pages/acceder');
    exit;
}

if ($base === '/acceder' && $method === 'POST') {
    Sesion::validarCSRF($_POST['csrf'] ?? null);
    
    $nombre = trim($_POST['nombre'] ?? '');
    $clave = $_POST['clave'] ?? '';
    
    $usuario = Usuario::verificarCredenciales($nombre, $clave);
    
    if ($usuario) {
        Sesion::autenticar($usuario->getId(), $usuario->getNombre());
        redirect('/PROYECTO_BLOG/public/admin');
    }
    
    render_view('pages/acceder', ['error' => 'Usuario o contraseña incorrectos']);
    exit;
}

// Logout
if ($base === '/salir' && $method === 'POST') {
    Sesion::validarCSRF($_POST['csrf'] ?? null);
    Sesion::cerrar();
    redirect('/PROYECTO_BLOG/public/');
}

// Registro
if ($base === '/registro' && $method === 'GET') {
    render_view('pages/registro');
    exit;
}

if ($base === '/registro' && $method === 'POST') {
    try {
        Sesion::validarCSRF($_POST['csrf'] ?? null);
        
        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $clave = $_POST['clave'] ?? '';
        $clave_conf = $_POST['clave_conf'] ?? '';
        
        $errores = [];
        
        if (empty($nombre) || strlen($nombre) < 3) {
            $errores[] = 'El nombre debe tener al menos 3 caracteres';
        }
        
        if (!Util::esEmailValido($email)) {
            $errores[] = 'El email no es válido';
        }
        
        if (empty($clave) || strlen($clave) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($clave !== $clave_conf) {
            $errores[] = 'Las contraseñas no coinciden';
        }
        
        if (Usuario::porNombre($nombre)) {
            $errores[] = 'El usuario ya existe';
        }
        
        if (!empty($errores)) {
            render_view('pages/registro', ['errores' => $errores]);
            exit;
        }
        
        $usuario = Usuario::crear($nombre, $clave, 'lector', $email);
        
        if ($usuario) {
            render_view('pages/acceder', ['mensaje' => 'Usuario registrado exitosamente. Ahora puedes iniciar sesión.']);
            exit;
        }
        
        render_view('pages/registro', ['errores' => ['Error al crear el usuario']]);
    } catch (Exception $e) {
        render_view('pages/registro', ['errores' => ['Error: ' . $e->getMessage()]]);
    }
    exit;
}

// Panel admin
if (preg_match('#^/admin#', $base)) {
    Sesion::requiereAutenticacion();
}

if ($base === '/admin' || $base === '/admin/') {
    $usuario_id = Sesion::usuarioId();
    $entradas = Entrada::porAutor($usuario_id);
    render_view('pages/admin_dashboard', ['entradas' => $entradas]);
    exit;
}

// Crear entrada
if ($base === '/admin/entrada/crear' && $method === 'GET') {
    render_view('pages/entrada_form', ['entrada' => null]);
    exit;
}

if ($base === '/admin/entrada/crear' && $method === 'POST') {
    try {
        Sesion::validarCSRF($_POST['csrf'] ?? null);
        
        $usuario_id = Sesion::usuarioId();
        if (!$usuario_id) {
            render_view('pages/entrada_form', [
                'error' => 'Debes estar autenticado para crear una entrada',
                'entrada' => []
            ]);
            exit;
        }
        
        $titulo = trim($_POST['titulo'] ?? '');
        $contenido = trim($_POST['contenido'] ?? '');
        $imagen = procesarImagen();
        
        if (empty($titulo) || empty($contenido)) {
            render_view('pages/entrada_form', [
                'error' => 'Título y contenido son requeridos',
                'entrada' => ['titulo' => $titulo, 'contenido' => $contenido, 'imagen' => $imagen]
            ]);
            exit;
        }
        
        $entrada = Entrada::crear($titulo, $contenido, $usuario_id, $imagen);
        
        if ($entrada) {
            redirect('/PROYECTO_BLOG/public/admin');
        }
        
        render_view('pages/entrada_form', [
            'error' => 'Error al crear la entrada. Intenta de nuevo.',
            'entrada' => ['titulo' => $titulo, 'contenido' => $contenido, 'imagen' => $imagen]
        ]);
    } catch (Exception $e) {
        render_view('pages/entrada_form', [
            'error' => 'Error: ' . $e->getMessage(),
            'entrada' => $_POST
        ]);
    }
    exit;
}

// Editar entrada
if (preg_match('#^/admin/entrada/(\d+)/editar$#', $base, $m) && $method === 'GET') {
    $entrada = Entrada::porId((int)$m[1]);
    if (!$entrada || $entrada->getAutorId() !== Sesion::usuarioId()) {
        http_response_code(403);
        exit('No tienes permiso para editar esta entrada');
    }
    render_view('pages/entrada_form', ['entrada' => $entrada->toArray()]);
    exit;
}

if (preg_match('#^/admin/entrada/(\d+)/editar$#', $base, $m) && $method === 'POST') {
    Sesion::validarCSRF($_POST['csrf'] ?? null);
    
    $entrada = Entrada::porId((int)$m[1]);
    if (!$entrada || $entrada->getAutorId() !== Sesion::usuarioId()) {
        http_response_code(403);
        exit('No tienes permiso para editar esta entrada');
    }
    
    $titulo = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    
    if (empty($titulo) || empty($contenido)) {
        render_view('pages/entrada_form', [
            'error' => 'Título y contenido son requeridos',
            'entrada' => $entrada->toArray()
        ]);
        exit;
    }
    
    $entrada->setTitulo($titulo);
    $entrada->setContenido($contenido);
    
    if ($entrada->guardar()) {
        redirect('/PROYECTO_BLOG/public/admin');
    }
    
    render_view('pages/entrada_form', [
        'error' => 'Error al actualizar la entrada',
        'entrada' => $entrada->toArray()
    ]);
    exit;
}

// Eliminar entrada
if (preg_match('#^/admin/entrada/(\d+)/eliminar$#', $base, $m) && $method === 'POST') {
    Sesion::validarCSRF($_POST['csrf'] ?? null);
    
    $entrada = Entrada::porId((int)$m[1]);
    if (!$entrada || $entrada->getAutorId() !== Sesion::usuarioId()) {
        http_response_code(403);
        exit('No tienes permiso para eliminar esta entrada');
    }
    
    if (Entrada::eliminar((int)$m[1])) {
        redirect('/PROYECTO_BLOG/public/admin');
    }
    
    redirect('/PROYECTO_BLOG/public/admin');
}

// Gestión de usuarios
if (preg_match('#^/admin/usuarios#', $base)) {
    Sesion::requiereAdmin();
}

if ($base === '/admin/usuarios' || $base === '/admin/usuarios/') {
    $usuarios = Usuario::todos();
    render_view('pages/admin_usuarios', ['usuarios' => $usuarios]);
    exit;
}

// Cambiar rol
if (preg_match('#^/admin/usuario/(\d+)/rol$#', $base, $m) && $method === 'POST') {
    Sesion::requiereAdmin();
    Sesion::validarCSRF($_POST['csrf'] ?? null);
    
    $usuario = Usuario::porId((int)$m[1]);
    if (!$usuario) {
        http_response_code(404);
        exit('Usuario no encontrado');
    }
    
    $rol = $_POST['rol'] ?? 'lector';
    if (!in_array($rol, ['admin', 'editor', 'lector'])) {
        http_response_code(400);
        exit('Rol inválido');
    }
    
    $usuario->setRol($rol);
    if ($usuario->guardar()) {
        json_response(['success' => true, 'message' => 'Rol actualizado']);
    }
    
    json_response(['success' => false, 'message' => 'Error al actualizar rol'], 500);
}

// Base de datos
if ($base === '/admin/bbdd') {
    Sesion::requiereAdmin();
    render_view('pages/bbdd');
    exit;
}

http_response_code(404);
render_view('errors/404', ['mensaje' => 'Página no encontrada']);

