-- ============================================================
-- Respaldos y Exportación - Proyecto AeroHaven Hotel
-- ============================================================

CREATE DATABASE IF NOT EXISTS `aerohaven` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `aerohaven`;

-- 1. Estructura de la tabla `usuarios`
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `correo` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('Administrador', 'Cliente', 'Recepcionista') NOT NULL DEFAULT 'Cliente',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Estructura de la tabla `habitaciones`
CREATE TABLE IF NOT EXISTS `habitaciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo` VARCHAR(100) NOT NULL,          -- Ej: 'Suite Premium', 'Habitación Estándar'
  `descripcion` TEXT NOT NULL,
  `precio` DECIMAL(10,2) NOT NULL,       -- Soportado por tu lógica ($hab['price'] ?? $hab['precio'])
  `imagen` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Inserción de Datos Base (Para pruebas de funcionalidad en el Paso 4)
INSERT INTO `usuarios` (`nombre`, `correo`, `password`, `rol`) VALUES
-- Contraseña de ejemplo cifrada con BCRYPT: 'admin123'
('Administrador AeroHaven', 'admin@aerohaven.com', '$2y$10$7rK72Yy1w7V8wXq8Z4h2e.vM/Wz6T2WzOQGZ3DNeL2H9JbeXvX51.', 'Administrador');

INSERT INTO `habitaciones` (`tipo`, `descripcion`, `precio`) VALUES
('Suite Premium', 'Disfruta del máximo confort del futuro con vistas espectaculares y domótica avanzada.', 2500.00),
('Habitación Estándar', 'Descanso perfecto con Wi-Fi 7 y climatización Eco-Aero integrada.', 1200.00);