-- ========================================
-- SQL: Crear 3 Barberos de Prueba (Modified to avoid duplicates)
-- ========================================

-- Set barbershop_id
SET @barbershop_id = 1;

-- Check and create Usuario 1: Raúl García López (if doesn't exist)
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
SELECT 'Raúl', 'García López', 'raul.garcia@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-1234', 0, 1, '', @barbershop_id
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'raul.garcia@barber.com');

-- Get the usuario_id for Raúl
SET @usuario_id_1 = (SELECT id FROM usuarios WHERE email = 'raul.garcia@barber.com' LIMIT 1);

-- Create Barbero 1 if doesn't exist
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
SELECT @usuario_id_1, @barbershop_id, 'Cortes Premium', 1
WHERE NOT EXISTS (SELECT 1 FROM barberos WHERE usuario_id = @usuario_id_1);

-- Check and create Usuario 2: Juan Martínez Rodríguez  
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
SELECT 'Juan', 'Martínez Rodríguez', 'juan.martinez@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-5678', 0, 1, '', @barbershop_id
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'juan.martinez@barber.com');

SET @usuario_id_2 = (SELECT id FROM usuarios WHERE email = 'juan.martinez@barber.com' LIMIT 1);

INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
SELECT @usuario_id_2, @barbershop_id, 'Barbas y Diseños', 1
WHERE NOT EXISTS (SELECT 1 FROM barberos WHERE usuario_id = @usuario_id_2);

-- Check and create Usuario 3: Pedro López Sánchez
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
SELECT 'Pedro', 'López Sánchez', 'pedro.lopez@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-9999', 0, 1, '', @barbershop_id
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'pedro.lopez@barber.com');

SET @usuario_id_3 = (SELECT id FROM usuarios WHERE email = 'pedro.lopez@barber.com' LIMIT 1);

INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
SELECT @usuario_id_3, @barbershop_id, 'Cortes Clásicos', 1
WHERE NOT EXISTS (SELECT 1 FROM barberos WHERE usuario_id = @usuario_id_3);

-- Verify that the barberos were created correctly
SELECT 'Barberos creados exitosamente:' as Resultado;
SELECT b.id, CONCAT(u.nombre, ' ', u.apellido) as nombre, b.especialidad, b.activo, b.barbershop_id
FROM barberos b
JOIN usuarios u ON u.id = b.usuario_id
WHERE b.barbershop_id = @barbershop_id
ORDER BY b.id;
