-- ========================================
-- FIX: Actualizar nombres de barberos con caracteres correctos
-- ========================================
-- Este script corrige los nombres que se guardaron incorrectamente

-- Actualizar Usuario: Raúl García López
UPDATE usuarios 
SET nombre = 'Raúl', apellido = 'García López'
WHERE email = 'raul.garcia@barber.com';

-- Actualizar Usuario: Juan Martínez Rodríguez  
UPDATE usuarios
SET nombre = 'Juan', apellido = 'Martínez Rodríguez'
WHERE email = 'juan.martinez@barber.com';

-- Actualizar Usuario: Pedro López Sánchez
UPDATE usuarios
SET nombre = 'Pedro', apellido = 'López Sánchez'
WHERE email = 'pedro.lopez@barber.com';

-- Actualizar otros usuarios con caracteres especiales comunes
UPDATE usuarios
SET nombre = 'Francisco'
WHERE nombre LIKE 'Francisco%' OR nombre LIKE 'Franc%';

-- Verificar los cambios
SELECT u.id, u.nombre, u.apellido, b.especialidad
FROM usuarios u
INNER JOIN barberos b ON b.usuario_id = u.id
WHERE b.activo = 1
ORDER BY u.id;
