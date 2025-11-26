<?php

namespace Blog\Models;

use PDO;
use PDOException;

// Gestiona las entradas/posts del blog
class Entrada {
    private int $id;
    private string $titulo;
    private string $slug;
    private ?string $imagen;
    private string $contenido;
    private ?int $autor_id;
    private ?string $creado_en;

    public function __construct(int $id = 0, string $titulo = '', string $slug = '', ?string $imagen = null, string $contenido = '', ?int $autor_id = null, ?string $creado_en = null) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->slug = $slug;
        $this->imagen = $imagen;
        $this->contenido = $contenido;
        $this->autor_id = $autor_id;
        $this->creado_en = $creado_en;
    }

    // Getters
    public function getId(): int { return $this->id; }
    public function getTitulo(): string { return $this->titulo; }
    public function getSlug(): string { return $this->slug; }
    public function getImagen(): ?string { return $this->imagen; }
    public function getContenido(): string { return $this->contenido; }
    public function getAutorId(): ?int { return $this->autor_id; }
    public function getCreadoEn(): ?string { return $this->creado_en; }

    // Setters
    public function setTitulo(string $titulo): void { $this->titulo = $titulo; }
    public function setImagen(?string $imagen): void { $this->imagen = $imagen; }
    public function setContenido(string $contenido): void { $this->contenido = $contenido; }
    public function setAutorId(?int $autor_id): void { $this->autor_id = $autor_id; }

    public static function todas(): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM entradas ORDER BY creado_en DESC, id DESC");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $entradas = [];
        foreach ($rows as $row) {
            $entradas[] = new self(
                $row['id'],
                $row['titulo'],
                $row['slug'],
                $row['imagen'] ?? null,
                $row['contenido'],
                $row['autor_id'] ?? null,
                $row['creado_en'] ?? null
            );
        }
        return $entradas;
    }

    public static function porId(int $id): ?Entrada {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM entradas WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            return new self(
                $row['id'],
                $row['titulo'],
                $row['slug'],
                $row['imagen'] ?? null,
                $row['contenido'],
                $row['autor_id'] ?? null,
                $row['creado_en'] ?? null
            );
        }
        return null;
    }

    public static function porSlug(string $slug): ?Entrada {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM entradas WHERE slug = ?");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();

        if ($row) {
            return new self(
                $row['id'],
                $row['titulo'],
                $row['slug'],
                $row['imagen'] ?? null,
                $row['contenido'],
                $row['autor_id'] ?? null,
                $row['creado_en'] ?? null
            );
        }
        return null;
    }

    public static function porAutor(int $autor_id): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM entradas WHERE autor_id = ? ORDER BY creado_en DESC");
        $stmt->execute([$autor_id]);
        $rows = $stmt->fetchAll();

        $entradas = [];
        foreach ($rows as $row) {
            $entradas[] = new self(
                $row['id'],
                $row['titulo'],
                $row['slug'],
                $row['imagen'] ?? null,
                $row['contenido'],
                $row['autor_id'] ?? null,
                $row['creado_en'] ?? null
            );
        }
        return $entradas;
    }

    public static function generarSlug(string $titulo): string {
        $slug = mb_strtolower($titulo, 'UTF-8');
        $slug = preg_replace('~[\p{Z}\p{P}]+~u', '-', $slug);
        $slug = preg_replace('~[^\p{L}\p{Nd}-]+~u', '', $slug);
        $slug = trim($slug, '-');
        return !empty($slug) ? $slug : 'entrada';
    }

    private static function slugUnico(string $base, int $exceptoId = 0): string {
        $db = Database::getInstance();
        $slug = $base;
        $n = 1;

        while (true) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM entradas WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $exceptoId]);
            if ((int)$stmt->fetchColumn() === 0) {
                break;
            }
            $n++;
            $slug = $base . '-' . $n;
        }
        return $slug;
    }

    public static function crear(string $titulo, string $contenido, ?int $autor_id = null, ?string $imagen = null): ?Entrada {
        try {
            $titulo = trim($titulo);
            $contenido = trim($contenido);

            if (empty($titulo) || empty($contenido)) {
                return null;
            }

            $slug = self::slugUnico(self::generarSlug($titulo));
            $db = Database::getInstance();
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO entradas (titulo, slug, imagen, contenido, autor_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $slug, $imagen, $contenido, $autor_id]);
            $id = (int)$db->lastInsertId();

            $db->commit();
            return self::porId($id);
        } catch (PDOException $e) {
            if ($db) {
                $db->rollBack();
            }
            return null;
        }
    }

    public function guardar(): bool {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("UPDATE entradas SET titulo = ?, contenido = ?, autor_id = ? WHERE id = ?");
            $stmt->execute([$this->titulo, $this->contenido, $this->autor_id, $this->id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function eliminar(int $id): bool {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("DELETE FROM entradas WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'contenido' => $this->contenido,
            'autor_id' => $this->autor_id,
            'creado_en' => $this->creado_en,
        ];
    }
}
