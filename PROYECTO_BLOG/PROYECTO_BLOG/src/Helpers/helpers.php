<?php

// Funciones globales auxiliares

use Blog\Models\Database;
use Blog\Models\Usuario;
use Blog\Models\Entrada;
use Blog\Models\Sesion;
use Blog\Utils\Util;

function db(): \PDO {
    return Database::getInstance();
}

function esc(string $texto): string {
    return Util::escapar($texto);
}

function truncar(string $texto, int $longitud = 100, string $sufijo = '...'): string {
    return Util::truncar($texto, $longitud, $sufijo);
}

function fecha(string $fecha, string $formato = 'd/m/Y H:i'): string {
    return Util::formatearFecha($fecha, $formato);
}

function tiempo(string $fecha): string {
    return Util::tiempoTranscurrido($fecha);
}

function slug(string $titulo): string {
    return Util::generarSlug($titulo);
}

function esEmail(string $email): bool {
    return Util::esEmailValido($email);
}

function token_csrf(): string {
    return Sesion::tokenCSRF();
}

function usuario_id(): ?int {
    return Sesion::usuarioId();
}

function autenticado(): bool {
    return Sesion::estaAutenticado();
}

function es_admin(): bool {
    return Sesion::esAdmin();
}

function usuario_nombre(): ?string {
    return Sesion::usuarioNombre();
}

function redirigir(string $url): void {
    header("Location: $url");
    exit;
}
