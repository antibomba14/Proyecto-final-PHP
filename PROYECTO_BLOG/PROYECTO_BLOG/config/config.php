<?php

return [
    'database' => [
        'host'     => getenv('DB_HOST') ?: 'localhost',
        'port'     => (int)(getenv('DB_PORT') ?: 3306),
        'user'     => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'dbname'   => getenv('DB_NAME') ?: 'blog_db',
    ],

    'app' => [
        'name'    => 'Nexo de Ideas',
        'url'     => 'http://localhost/PROYECTO_BLOG/public/',
        'debug'   => (bool)getenv('DEBUG') ?: true,
        'env'     => getenv('APP_ENV') ?: 'development',
    ],

    'session' => [
        'name'     => 'blog_session',
        'lifetime' => 1440,
        'secure'   => false,
        'http_only' => true,
        'same_site' => 'Lax',
    ],

    'security' => [
        'password_algo'   => PASSWORD_DEFAULT,
        'password_cost'   => 10,
        'max_login_attempts' => 5,
        'lockout_time'    => 900,
    ],

    'paths' => [
        'root'     => dirname(__DIR__),
        'public'   => dirname(__DIR__) . '/public',
        'views'    => dirname(__DIR__) . '/views',
        'storage'  => dirname(__DIR__) . '/storage',
        'uploads'  => dirname(__DIR__) . '/storage/uploads',
        'logs'     => dirname(__DIR__) . '/storage/logs',
    ],

    'files' => [
        'max_size'     => 10 * 1024 * 1024,
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
    ],

    'pagination' => [
        'per_page' => 10,
    ],
];
