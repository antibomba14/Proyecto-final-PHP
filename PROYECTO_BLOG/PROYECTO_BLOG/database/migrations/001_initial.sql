-- ============================================================
-- 1. CREAR BASE DE DATOS
-- ============================================================

CREATE DATABASE IF NOT EXISTS `blog_db` 
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;
USE `blog_db`;
-- ============================================================
-- 2. ELIMINAR TABLAS ANTIGUAS (EN ORDEN CORRECTO)
-- ============================================================

-- Primero eliminar tabla con foreign key
DROP TABLE IF EXISTS `entradas`;

-- Luego eliminar tabla referenciada
DROP TABLE IF EXISTS `usuarios`;

-- ============================================================
-- 3. CREAR TABLA: usuarios
-- ============================================================

CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) UNIQUE NOT NULL,
  `clave_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255),
  `rol` ENUM('admin', 'editor', 'lector') DEFAULT 'lector',
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_nombre` (`nombre`),
  INDEX `idx_rol` (`rol`),
  INDEX `idx_creado_en` (`creado_en`)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. CREAR TABLA: entradas
-- ============================================================

CREATE TABLE `entradas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `contenido` LONGTEXT NOT NULL,
  `autor_id` INT,
  `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  CONSTRAINT `fk_entradas_usuarios` FOREIGN KEY (`autor_id`) 
    REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  
  INDEX `idx_slug` (`slug`),
  INDEX `idx_autor_id` (`autor_id`),
  INDEX `idx_creado_en` (`creado_en`)
) 
ENGINE=InnoDB 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. INSERTAR DATOS INICIALES
-- ============================================================

-- Usuario administrador por defecto
-- Usuario: admin
-- Contraseña: admin123
-- Hash: $2y$10$F9cFJzUgS5DgA8W03LLE7uNncQ7I9ADkYHiXY1JDrV6k3QGhFY6wG

INSERT IGNORE INTO `usuarios` 
  (`nombre`, `clave_hash`, `rol`, `email`, `creado_en`) 
VALUES 
  ('admin', '$2y$10$F9cFJzUgS5DgA8W03LLE7uNncQ7I9ADkYHiXY1JDrV6k3QGhFY6wG', 'admin', 'admin@blog.local', NOW());

-- ============================================================
-- 6. VERIFICAR INSTALACIÓN
-- ============================================================

-- Ver usuarios creados
SELECT 'Usuarios en la BD:' AS 'Información';
SELECT `id`, `nombre`, `rol`, `email`, `creado_en` FROM `usuarios`;

-- Ver estructura de tablas
SELECT 'Tablas creadas:' AS 'Información';
SHOW TABLES;

-- Ver campos de usuarios
SELECT 'Estructura de tabla usuarios:' AS 'Información';
DESCRIBE `usuarios`;

-- Ver campos de entradas
SELECT 'Estructura de tabla entradas:' AS 'Información';
DESCRIBE `entradas`;

UPDATE blog_db.usuarios 
SET clave_hash = '$2y$10$sTXN438WttfbFieLtNweyu4MQcLOhrASWJdNyi8OkYCRXX9nTJhiO' 
WHERE nombre = 'admin';

ALTER TABLE blog_db.entradas ADD COLUMN imagen VARCHAR(255) AFTER slug;