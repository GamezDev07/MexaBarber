-- Insertar barbería de prueba
INSERT INTO barbershops (nombre, slug, email, telefono, direccion, activo)
VALUES ('MexaBarber CDMX', 'mexabarber-cdmx', 'contacto@mexabarber.com', '5512345678', 'Av. Insurgentes Sur 1234, Col. Del Valle, CDMX', 1)
ON CONFLICT (slug) DO NOTHING;

-- Insertar usuarios para los 3 barberos (password: 123456 hasheado con bcrypt)
INSERT INTO usuarios (nombre, apellido, email, password, telefono, admin, confirmado, token, barbershop_id)
VALUES
    ('Raúl', 'Hernández', 'raul@mexabarber.com', '$2y$10$dF8GxGpqLVzJxOxJJGxQxeQPHCvJFhWRxDfG7QxkE6K8nR5fT1ZHi', '5598761234', 0, 1, '', (SELECT id FROM barbershops WHERE slug = 'mexabarber-cdmx')),
    ('Juan', 'López', 'juan@mexabarber.com', '$2y$10$dF8GxGpqLVzJxOxJJGxQxeQPHCvJFhWRxDfG7QxkE6K8nR5fT1ZHi', '5587654321', 0, 1, '', (SELECT id FROM barbershops WHERE slug = 'mexabarber-cdmx')),
    ('Pedro', 'García', 'pedro@mexabarber.com', '$2y$10$dF8GxGpqLVzJxOxJJGxQxeQPHCvJFhWRxDfG7QxkE6K8nR5fT1ZHi', '5576543210', 0, 1, '', (SELECT id FROM barbershops WHERE slug = 'mexabarber-cdmx'))
ON CONFLICT (email) DO NOTHING;

-- Insertar los 3 barberos vinculados a sus usuarios
INSERT INTO barberos (usuario_id, barbershop_id, especialidad, activo)
VALUES
    ((SELECT id FROM usuarios WHERE email = 'raul@mexabarber.com'), (SELECT id FROM barbershops WHERE slug = 'mexabarber-cdmx'), 'Cortes clásicos y fade', 1),
    ((SELECT id FROM usuarios WHERE email = 'juan@mexabarber.com'), (SELECT id FROM barbershops WHERE slug = 'mexabarber-cdmx'), 'Barbas y diseños artísticos', 1),
    ((SELECT id FROM usuarios WHERE email = 'pedro@mexabarber.com'), (SELECT id FROM barbershops WHERE slug = 'mexabarber-cdmx'), 'Colorimetría y alisados', 1);

-- Asociar barbershop_id a los servicios existentes
UPDATE servicios SET barbershop_id = (SELECT id FROM barbershops WHERE slug = 'mexabarber-cdmx')
WHERE barbershop_id IS NULL;
