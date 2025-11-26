<?php

namespace Blog\Models;

// Gestiona sesiones y autenticación de usuarios
class Sesion {
    
    public static function iniciar(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }

    public static function usuarioId(): ?int {
        self::iniciar();
        return isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
    }

    public static function estaAutenticado(): bool {
        return self::usuarioId() !== null;
    }

    public static function usuarioNombre(): ?string {
        self::iniciar();
        return $_SESSION['unombre'] ?? null;
    }

    public static function usuarioRol(): ?string {
        $id = self::usuarioId();
        if ($id === null) {
            return null;
        }
        return Usuario::rolPorId($id);
    }

    public static function esAdmin(): bool {
        return self::usuarioRol() === 'admin';
    }

    public static function autenticar(int $id, string $nombre): void {
        self::iniciar();
        session_regenerate_id(true);
        $_SESSION['uid'] = $id;
        $_SESSION['unombre'] = $nombre;
    }

    public static function cerrar(): void {
        self::iniciar();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function requiereAutenticacion(): void {
        if (!self::estaAutenticado()) {
            header('Location: /acceder');
            exit;
        }
    }

    public static function requiereAdmin(): void {
        if (!self::esAdmin()) {
            header('Location: /admin');
            exit;
        }
    }

    public static function tokenCSRF(): string {
        self::iniciar();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function verificarCSRF(?string $token): bool {
        self::iniciar();
        return !empty($token) && hash_equals($_SESSION['csrf'] ?? '', $token);
    }

    public static function validarCSRF(?string $token): void {
        if (!self::verificarCSRF($token)) {
            http_response_code(400);
            exit('Solicitud no válida (CSRF).');
        }
    }
}
