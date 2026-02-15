-- Fix: Asignar barbershop_id=1 a todos los usuarios que no lo tengan
-- Esto es necesario para que el sistema muestre los barberos correctamente

-- Verificar usuarios sin barbershop_id
SELECT id, nombre, apellido, email, barbershop_id 
FROM usuarios 
WHERE barbershop_id IS NULL OR barbershop_id = 0;

-- Actualizar TODOS los usuarios para que tengan barbershop_id = 1
UPDATE usuarios 
SET barbershop_id = 1 
WHERE barbershop_id IS NULL OR barbershop_id = 0;

-- Verificar que se actualizó
SELECT id, nombre, apellido, email, barbershop_id 
FROM usuarios;
