-- ========================================
-- Migración Multi-Barbería para MySQL - PASO 1
-- ========================================

-- Tabla de barberías (multi-tenant)
CREATE TABLE IF NOT EXISTS barbershops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(60) NOT NULL,
    telefono VARCHAR(15),
    direccion VARCHAR(255),
    logo VARCHAR(255),
    activo TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar barbería por defecto
INSERT IGNORE INTO barbershops (id, nombre, slug, email, telefono, direccion, activo) 
VALUES (1, 'Mi Barbería', 'mi-barberia', 'info@barberia.com', '555-0000', 'Calle Principal 123', 1);

-- Crear tabla de barberos/empleados
CREATE TABLE IF NOT EXISTS barberos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    barbershop_id INT DEFAULT 1,
    especialidad VARCHAR(100),
    activo TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY (usuario_id),
    KEY (barbershop_id)
);

-- Crear tabla de notificaciones
CREATE TABLE IF NOT EXISTS notificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    barbershop_id INT DEFAULT 1,
    tipo VARCHAR(50),
    titulo VARCHAR(255),
    mensaje TEXT,
    leida TINYINT DEFAULT 0,
    cita_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY (usuario_id),
    KEY (barbershop_id)
);

-- Crear tabla de pagos
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cita_id INT,
    barbershop_id INT DEFAULT 1,
    monto DECIMAL(10, 2),
    metodo VARCHAR(50),
    estado VARCHAR(20),
    referencia_externa VARCHAR(255),
    datos_pago JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY (cita_id),
    KEY (barbershop_id)
);

-- Crear índices
CREATE INDEX idx_barberos_barbershop ON barberos(barbershop_id);
CREATE INDEX idx_barberos_usuario ON barberos(usuario_id);
CREATE INDEX idx_notificaciones_usuario ON notificaciones(usuario_id);
CREATE INDEX idx_notificaciones_leida ON notificaciones(leida);
CREATE INDEX idx_pagos_cita ON pagos(cita_id);
CREATE INDEX idx_pagos_barbershop ON pagos(barbershop_id);
