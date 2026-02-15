-- ========================================
-- COMPLETE DATABASE SETUP SCRIPT (MySQL Compatible)
-- ========================================

-- Step 1: Create barbershops table if not exists
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

-- Insert default barbershop if not exists
INSERT IGNORE INTO barbershops (id, nombre, slug, email, telefono, direccion, activo) 
VALUES (1, 'MexaBarber', 'mexa-barber', 'info@mexabarber.com', '555-0000', 'Calle Principal 123', 1);

-- Step 2: Create barberos table if not exists
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

-- Step 3: Create notificaciones table if not exists
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

-- Step 4: Create pagos table if not exists
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

-- Verify the setup
SELECT 'Barbershops Table:' as Info;
SELECT * FROM barbershops LIMIT 5;

SELECT 'Barberos Count:' as Info;
SELECT COUNT(*) as total_barberos FROM barberos;
