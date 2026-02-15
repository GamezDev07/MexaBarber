-- Verificar que los barberos existen en MySQL local
SELECT 
    b.id, 
    CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
    u.email,
    b.especialidad, 
    b.activo,
    b.barbershop_id
FROM barberos b
JOIN usuarios u ON u.id = b.usuario_id
WHERE b.barbershop_id = 1 AND b.activo = 1
ORDER BY b.id;

-- También verificar cuántos barberos hay en total
SELECT COUNT(*) as total_barberos_activos FROM barberos WHERE barbershop_id = 1 AND activo = 1;
