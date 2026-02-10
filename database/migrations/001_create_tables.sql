-- Migración de AppSalón: MySQL → PostgreSQL
-- Ejecutar en Supabase para crear las tablas

-- Tabla: usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    apellido VARCHAR(60) NOT NULL,
    email VARCHAR(30) UNIQUE NOT NULL,
    password VARCHAR(60),
    telefono VARCHAR(10) NOT NULL,
    admin SMALLINT DEFAULT 0,
    confirmado SMALLINT DEFAULT 0,
    token VARCHAR(15) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: servicios
CREATE TABLE IF NOT EXISTS servicios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    precio DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: citas
CREATE TABLE IF NOT EXISTS citas (
    id SERIAL PRIMARY KEY,
    fecha DATE,
    hora TIME,
    usuarioId INTEGER REFERENCES usuarios(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: citasservicios (relación muchos a muchos)
CREATE TABLE IF NOT EXISTS citasservicios (
    id SERIAL PRIMARY KEY,
    citaId INTEGER REFERENCES citas(id) ON DELETE SET NULL,
    servicioId INTEGER REFERENCES servicios(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimización
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_citas_usuarioId ON citas(usuarioId);
CREATE INDEX idx_citas_fecha ON citas(fecha);
CREATE INDEX idx_citasservicios_citaId ON citasservicios(citaId);
CREATE INDEX idx_citasservicios_servicioId ON citasservicios(servicioId);

-- Inserts iniciales de servicios (opcional, copiar desde MySQL)
-- INSERT INTO servicios (nombre, precio) VALUES
-- ('Corte de Cabello Hombre', 80.00),
-- ('Corte de Cabello Mujer', 120.00),
-- ... etc
