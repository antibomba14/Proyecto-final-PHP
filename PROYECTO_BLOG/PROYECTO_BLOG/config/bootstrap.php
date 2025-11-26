<?php

// Inicializa la aplicación

// Autoloader manual PSR-4
spl_autoload_register(function ($clase) {
    $prefijo = 'Blog\\';
    if (strpos($clase, $prefijo) === 0) {
        $relativo = substr($clase, strlen($prefijo));
        $archivo = __DIR__ . '/../src/' . str_replace('\\', '/', $relativo) . '.php';
        if (file_exists($archivo)) {
            require $archivo;
        }
    }
});

// Cargar helpers
require __DIR__ . '/../src/Helpers/helpers.php';

$config = require __DIR__ . '/config.php';

// Helper para acceder a configuración
if (!function_exists('config')) {
    function config(?string $key = null) {
        static $cfg = null;
        if ($cfg === null) {
            $cfg = require __DIR__ . '/config.php';
        }
        
        if ($key === null) {
            return $cfg;
        }
        
        $keys = explode('.', $key);
        $value = $cfg;
        
        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }
        
        return $value;
    }
}

use Blog\Models\Database;
use Blog\Models\Sesion;

$dbConfig = config('database');
Database::configure(
    $dbConfig['host'],
    $dbConfig['dbname'],
    $dbConfig['user'],
    $dbConfig['password'],
    $dbConfig['port']
);

Sesion::iniciar();

if (config('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', config('paths.logs') . '/errors.log');
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', config('paths.root'));
    define('PUBLIC_PATH', config('paths.public'));
    define('VIEWS_PATH', config('paths.views'));
    define('UPLOADS_PATH', config('paths.uploads'));
    define('APP_URL', config('app.url'));
}
