<?php

namespace Blog\Utils;

// Funciones auxiliares generales
class Util {
    
    public static function limpiar(string $texto): string {
        return trim($texto);
    }

    public static function escapar(string $texto): string {
        return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function generarSlug(string $titulo): string {
        $slug = mb_strtolower($titulo, 'UTF-8');
        $slug = preg_replace('~[\p{Z}\p{P}]+~u', '-', $slug);
        $slug = preg_replace('~[^\p{L}\p{Nd}-]+~u', '', $slug);
        $slug = trim($slug, '-');
        return !empty($slug) ? $slug : 'sin-titulo';
    }

    public static function esEmailValido(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function truncar(string $texto, int $longitud = 100, string $sufijo = '...'): string {
        if (mb_strlen($texto, 'UTF-8') <= $longitud) {
            return $texto;
        }
        return mb_substr($texto, 0, $longitud, 'UTF-8') . $sufijo;
    }

    public static function formatearFecha(string $fecha, string $formato = 'd/m/Y H:i'): string {
        try {
            $date = new \DateTime($fecha);
            return $date->format($formato);
        } catch (\Exception $e) {
            return $fecha;
        }
    }

    public static function tiempoTranscurrido(string $fecha): string {
        try {
            $date = new \DateTime($fecha);
            $now = new \DateTime();
            $diff = $now->diff($date);

            if ($diff->y > 0) {
                return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
            }
            if ($diff->m > 0) {
                return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
            }
            if ($diff->d > 0) {
                return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
            }
            if ($diff->h > 0) {
                return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
            }
            if ($diff->i > 0) {
                return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
            }
            return 'Hace poco';
        } catch (\Exception $e) {
            return $fecha;
        }
    }
}
