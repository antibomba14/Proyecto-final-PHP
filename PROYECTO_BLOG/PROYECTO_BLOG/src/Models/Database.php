<?php

namespace Blog\Models;

use PDO;
use PDOException;

// Gestiona la conexión a MySQL (Singleton)
class Database {
    private static ?PDO $instance = null;
    private static string $host = 'localhost';
    private static string $db = 'blog_db';
    private static string $user = 'root';
    private static string $password = '';
    private static int $port = 3306;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::connect();
        }
        return self::$instance;
    }

    private static function connect(): void {
        try {
            $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$db . ";charset=utf8mb4";
            self::$instance = new PDO($dsn, self::$user, self::$password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Error de conexión: " . $e->getMessage());
        }
    }

    public static function configure(string $host = 'localhost', string $db = 'blog_db', string $user = 'root', string $password = '', int $port = 3306): void {
        self::$host = $host;
        self::$db = $db;
        self::$user = $user;
        self::$password = $password;
        self::$port = $port;
        self::$instance = null;
    }

    public static function pdo(): PDO {
        return self::getInstance();
    }

    public static function initSchema(): void {
        $db = self::getInstance();
        $schemaFile = __DIR__ . '/../../database/migrations/001_initial.sql';
        $schema = file_get_contents($schemaFile);
        
        if (!$schema) {
            throw new \RuntimeException("No se pudo leer el archivo SQL: $schemaFile");
        }
        
        $statements = explode(';', $schema);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $db->exec($statement);
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        throw $e;
                    }
                }
            }
        }
    }
}
