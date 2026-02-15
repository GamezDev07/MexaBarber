-- ========================================
-- INSERTAR BARBEROS DE PRUEBA EN SUPABASE
-- ========================================

-- Crear Usuario 1: Raúl García López
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
VALUES ('Raúl', 'García López', 'raul.garcia@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-1234', 0, 1, '', 1)
ON CONFLICT (email) DO NOTHING;

-- Crear Barbero 1
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
SELECT id, 1, 'Cortes Premium', 1
FROM usuarios 
WHERE email = 'raul.garcia@barber.com'
AND NOT EXISTS (
    SELECT 1 FROM barberos 
    WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'raul.garcia@barber.com' LIMIT 1)
);

-- Crear Usuario 2: Juan Martínez Rodríguez
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
VALUES ('Juan', 'Martínez Rodríguez', 'juan.martinez@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-5678', 0, 1, '', 1)
ON CONFLICT (email) DO NOTHING;

-- Crear Barbero 2
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
SELECT id, 1, 'Barbas y Diseños', 1
FROM usuarios 
WHERE email = 'juan.martinez@barber.com'
AND NOT EXISTS (
    SELECT 1 FROM barberos 
    WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'juan.martinez@barber.com' LIMIT 1)
);

-- Crear Usuario 3: Pedro López Sánchez
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
VALUES ('Pedro', 'López Sánchez', 'pedro.lopez@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-9999', 0, 1, '', 1)
ON CONFLICT (email) DO NOTHING;

-- Crear Barbero 3
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
SELECT id, 1, 'Cortes Clásicos', 1
FROM usuarios 
WHERE email = 'pedro.lopez@barber.com'
AND NOT EXISTS (
    SELECT 1 FROM barberos 
    WHERE usuario_id = (SELECT id FROM usuarios WHERE email = 'pedro.lopez@barber.com' LIMIT 1)
);

-- Verificar barberos creados
SELECT 
    b.id, 
    CONCAT(u.nombre, ' ', u.apellido) as nombre, 
    b.especialidad, 
    b.activo,
    b.barbershop_id
FROM barberos b
JOIN usuarios u ON u.id = b.usuario_id
WHERE b.barbershop_id = 1
ORDER BY b.id;
