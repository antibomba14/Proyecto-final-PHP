<?php

namespace Blog\Models;

use PDO;
use PDOException;

// Gestiona usuarios y autenticación
class Usuario {
    private int $id;
    private string $nombre;
    private string $clave_hash;
    private string $rol;
    private ?string $email;
    private ?string $creado_en;

    public function __construct(int $id = 0, string $nombre = '', string $clave_hash = '', string $rol = 'lector', ?string $email = null, ?string $creado_en = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->clave_hash = $clave_hash;
        $this->rol = $rol;
        $this->email = $email;
        $this->creado_en = $creado_en;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getClaveHash(): string { return $this->clave_hash; }
    public function getRol(): string { return $this->rol; }
    public function getEmail(): ?string { return $this->email; }
    public function getCreadoEn(): ?string { return $this->creado_en; }

    // Setters
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setEmail(?string $email): void { $this->email = $email; }
    public function setRol(string $rol): void { $this->rol = $rol; }

    // Búsquedas
    public static function porNombre(string $nombre): ?Usuario {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        $stmt->execute([$nombre]);
        $row = $stmt->fetch();

        if ($row) {
            $claveHash = $row['clave_hash'] ?? '';
            return new self(
                $row['id'],
                $row['nombre'],
                $claveHash,
                $row['rol'],
                $row['email'] ?? null,
                $row['creado_en'] ?? null
            );
        }
        return null;
    }

    public static function porId(int $id): ?Usuario {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            $claveHash = $row['clave_hash'] ?? '';
            return new self(
                $row['id'],
                $row['nombre'],
                $claveHash,
                $row['rol'],
                $row['email'] ?? null,
                $row['creado_en'] ?? null
            );
        }
        return null;
    }

    public static function verificarCredenciales(string $nombre, string $clave): ?Usuario {
        $usuario = self::porNombre($nombre);
        if (!$usuario) return null;

        if (password_verify($clave, $usuario->getClaveHash())) {
            return $usuario;
        }
        return null;
    }

    public static function rolPorId(int $id): ?string {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT rol FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() ?: null;
    }

    public static function todos(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuarios ORDER BY creado_en DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $usuarios = [];
        foreach ($rows as $row) {
            $usuarios[] = new self(
                $row['id'],
                $row['nombre'],
                $row['clave_hash'],
                $row['rol'],
                $row['email'] ?? null,
                $row['creado_en'] ?? null
            );
        }
        return $usuarios;
    }

    public static function crear(string $nombre, string $clave, string $rol = 'lector', ?string $email = null): ?Usuario {
        try {
            $hash = password_hash($clave, PASSWORD_DEFAULT);
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, clave_hash, rol, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $hash, $rol, $email]);
            $id = (int)$db->lastInsertId();

            return self::porId($id);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function guardar(): bool {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE usuarios SET nombre = ?, rol = ?, email = ? WHERE id = ?");
            $stmt->execute([$this->nombre, $this->rol, $this->email, $this->id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function eliminar(int $id): bool {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'rol' => $this->rol,
            'email' => $this->email,
            'creado_en' => $this->creado_en,
        ];
    }
}
