-- Agregar columna barbershop_id a tablas existentes
ALTER TABLE usuarios ADD COLUMN barbershop_id INT DEFAULT 1;
ALTER TABLE servicios ADD COLUMN barbershop_id INT DEFAULT 1;
ALTER TABLE citas ADD COLUMN barbershop_id INT DEFAULT 1;
ALTER TABLE citas ADD COLUMN barbero_id INT;
ALTER TABLE citas ADD COLUMN estado VARCHAR(20) DEFAULT 'pendiente';
ALTER TABLE citas ADD COLUMN metodo_pago VARCHAR(20);
ALTER TABLE citas ADD COLUMN pago_estado VARCHAR(20) DEFAULT 'pendiente';
ALTER TABLE citas ADD COLUMN pago_referencia VARCHAR(255);
ALTER TABLE citas ADD COLUMN turno INT;
