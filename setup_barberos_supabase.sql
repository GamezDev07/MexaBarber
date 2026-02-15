-- ========================================
-- SCRIPT PARA SUPABASE (PostgreSQL)
-- ========================================

-- Paso 1: Crear tabla barbershops
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

-- Insertar barbershop por defecto
INSERT INTO barbershops (id, nombre, slug, email, telefono, direccion, activo) 
VALUES (1, 'MexaBarber', 'mexa-barber', 'info@mexabarber.com', '555-0000', 'Calle Principal 123', 1)
ON CONFLICT (id) DO NOTHING;

-- Actualizar la secuencia para que continúe desde 2
SELECT setval('barbershops_id_seq', (SELECT MAX(id) FROM barbershops));

-- Paso 2: Agregar barbershop_id a usuarios si no existe
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'usuarios' AND column_name = 'barbershop_id'
    ) THEN
        ALTER TABLE usuarios ADD COLUMN barbershop_id INTEGER REFERENCES barbershops(id);
    END IF;
END $$;

-- Actualizar usuarios existentes para que tengan barbershop_id = 1
UPDATE usuarios SET barbershop_id = 1 WHERE barbershop_id IS NULL;

-- Paso 3: Crear tabla barberos
CREATE TABLE IF NOT EXISTS barberos (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
    barbershop_id INTEGER REFERENCES barbershops(id) ON DELETE CASCADE,
    especialidad VARCHAR(100),
    activo SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Paso 4: Agregar barbero_id a citas si no existe
DO $$ 
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'citas' AND column_name = 'barbero_id'
    ) THEN
        ALTER TABLE citas ADD COLUMN barbero_id INTEGER REFERENCES barberos(id);
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'citas' AND column_name = 'barbershop_id'
    ) THEN
        ALTER TABLE citas ADD COLUMN barbershop_id INTEGER REFERENCES barbershops(id);
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'citas' AND column_name = 'estado'
    ) THEN
        ALTER TABLE citas ADD COLUMN estado VARCHAR(20) DEFAULT 'pendiente';
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'citas' AND column_name = 'metodo_pago'
    ) THEN
        ALTER TABLE citas ADD COLUMN metodo_pago VARCHAR(20);
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'citas' AND column_name = 'pago_estado'
    ) THEN
        ALTER TABLE citas ADD COLUMN pago_estado VARCHAR(20) DEFAULT 'pendiente';
    END IF;
    
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'citas' AND column_name = 'turno'
    ) THEN
        ALTER TABLE citas ADD COLUMN turno INTEGER;
    END IF;
END $$;

-- Paso 5: Crear tabla notificaciones
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

-- Paso 6: Crear tabla pagos
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

-- Paso 7: Crear índices
CREATE INDEX IF NOT EXISTS idx_barberos_barbershop ON barberos(barbershop_id);
CREATE INDEX IF NOT EXISTS idx_barberos_usuario ON barberos(usuario_id);
CREATE INDEX IF NOT EXISTS idx_citas_barbero ON citas(barbero_id);
CREATE INDEX IF NOT EXISTS idx_citas_barbershop ON citas(barbershop_id);
CREATE INDEX IF NOT EXISTS idx_notificaciones_usuario ON notificaciones(usuario_id);
CREATE INDEX IF NOT EXISTS idx_notificaciones_leida ON notificaciones(leida);
CREATE INDEX IF NOT EXISTS idx_pagos_cita ON pagos(cita_id);
CREATE INDEX IF NOT EXISTS idx_usuarios_barbershop ON usuarios(barbershop_id);

-- Verificar
SELECT 'Tablas creadas exitosamente' AS resultado;
