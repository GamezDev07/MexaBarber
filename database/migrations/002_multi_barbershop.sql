-- Migración: Multi-tenant con barbershop_id + barberos + notificaciones + pagos
-- Ejecutar en Supabase después de 001_create_tables.sql

-- Tabla de barberías (multi-tenant)
CREATE TABLE IF NOT EXISTS barbershops (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(60) NOT NULL,
    telefono VARCHAR(15),
    direccion VARCHAR(255),
    logo VARCHAR(255),
    activo SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar barbershop_id a tablas existentes
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS barbershop_id INTEGER REFERENCES barbershops(id);
ALTER TABLE servicios ADD COLUMN IF NOT EXISTS barbershop_id INTEGER REFERENCES barbershops(id);
ALTER TABLE citas ADD COLUMN IF NOT EXISTS barbershop_id INTEGER REFERENCES barbershops(id);

-- Tabla de barberos/empleados
CREATE TABLE IF NOT EXISTS barberos (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
    barbershop_id INTEGER REFERENCES barbershops(id) ON DELETE CASCADE,
    especialidad VARCHAR(100),
    activo SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Nuevas columnas en citas
ALTER TABLE citas ADD COLUMN IF NOT EXISTS barbero_id INTEGER REFERENCES barberos(id);
ALTER TABLE citas ADD COLUMN IF NOT EXISTS estado VARCHAR(20) DEFAULT 'pendiente';
ALTER TABLE citas ADD COLUMN IF NOT EXISTS metodo_pago VARCHAR(20);
ALTER TABLE citas ADD COLUMN IF NOT EXISTS pago_estado VARCHAR(20) DEFAULT 'pendiente';
ALTER TABLE citas ADD COLUMN IF NOT EXISTS pago_referencia VARCHAR(255);
ALTER TABLE citas ADD COLUMN IF NOT EXISTS turno INTEGER;

-- Tabla de notificaciones in-app
CREATE TABLE IF NOT EXISTS notificaciones (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
    barbershop_id INTEGER REFERENCES barbershops(id) ON DELETE CASCADE,
    tipo VARCHAR(50) NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    mensaje TEXT NOT NULL,
    leida SMALLINT DEFAULT 0,
    cita_id INTEGER REFERENCES citas(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pagos (registro detallado)
CREATE TABLE IF NOT EXISTS pagos (
    id SERIAL PRIMARY KEY,
    cita_id INTEGER REFERENCES citas(id) ON DELETE SET NULL,
    barbershop_id INTEGER REFERENCES barbershops(id) ON DELETE CASCADE,
    monto DECIMAL(8,2) NOT NULL,
    metodo VARCHAR(20) NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    referencia_externa VARCHAR(255),
    datos_pago TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_barberos_barbershop ON barberos(barbershop_id);
CREATE INDEX IF NOT EXISTS idx_barberos_usuario ON barberos(usuario_id);
CREATE INDEX IF NOT EXISTS idx_citas_barbero ON citas(barbero_id);
CREATE INDEX IF NOT EXISTS idx_citas_estado ON citas(estado);
CREATE INDEX IF NOT EXISTS idx_citas_barbershop ON citas(barbershop_id);
CREATE INDEX IF NOT EXISTS idx_notificaciones_usuario ON notificaciones(usuario_id);
CREATE INDEX IF NOT EXISTS idx_notificaciones_leida ON notificaciones(leida);
CREATE INDEX IF NOT EXISTS idx_pagos_cita ON pagos(cita_id);
CREATE INDEX IF NOT EXISTS idx_pagos_barbershop ON pagos(barbershop_id);
CREATE INDEX IF NOT EXISTS idx_usuarios_barbershop ON usuarios(barbershop_id);
CREATE INDEX IF NOT EXISTS idx_servicios_barbershop ON servicios(barbershop_id);
