-- ========================================
-- SQL: Crear 3 Barberos de Prueba
-- ========================================
-- Ejecuta este script una sola vez en tu cliente localhost

-- NOTA: Asegúrate de que la tabla 'barbershops' tenga un registro
-- Si no existe, primero crea una:
-- INSERT INTO barbershops (nombre, slug, email, telefono, direccion, activo) 
-- VALUES ('Mi Barbería', 'mi-barberia', 'info@barberia.com', '555-1234', 'Calle Principal 123', 1);

-- Obtener el barbershop_id (por defecto suponemos 1)
SET @barbershop_id = 1;

-- Crear Usuario 1: Raúl García López
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
VALUES ('Raúl', 'García López', 'raul.garcia@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-1234', 0, 1, '', @barbershop_id);
SET @usuario_id_1 = LAST_INSERT_ID();

-- Crear Barbero 1: Raúl
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
VALUES (@usuario_id_1, @barbershop_id, 'Cortes Premium', 1);

-- Crear Usuario 2: Juan Martínez Rodríguez
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
VALUES ('Juan', 'Martínez Rodríguez', 'juan.martinez@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-5678', 0, 1, '', @barbershop_id);
SET @usuario_id_2 = LAST_INSERT_ID();

-- Crear Barbero 2: Juan
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
VALUES (@usuario_id_2, @barbershop_id, 'Barbas y Diseños', 1);

-- Crear Usuario 3: Pedro López Sánchez
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
VALUES ('Pedro', 'López Sánchez', 'pedro.lopez@barber.com', '$2y$10$YWq8uvWJRTM3L1G5j7iH.eKJRn0U5GVMZlDEJ4Qx0Z5X8Qx5x5bZi', '555-9999', 0, 1, '', @barbershop_id);
SET @usuario_id_3 = LAST_INSERT_ID();

-- Crear Barbero 3: Pedro
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
VALUES (@usuario_id_3, @barbershop_id, 'Cortes Clásicos', 1);

-- Verificar que los barberos fueron creados correctamente
SELECT b.id, CONCAT(u.nombre, ' ', u.apellido) as nombre, b.especialidad, b.activo
FROM barberos b
JOIN usuarios u ON u.id = b.usuario_id
WHERE b.barbershop_id = @barbershop_id
ORDER BY b.id DESC
LIMIT 3;
