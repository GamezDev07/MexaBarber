-- Verificar tablas existentes
SHOW TABLES;

-- Verificar si existe tabla barbershops
SELECT COUNT(*) as table_exists 
FROM information_schema.tables 
WHERE table_schema = 'appsalon' 
AND table_name = 'barbershops';

-- Verificar si existe tabla barberos
SELECT COUNT(*) as table_exists 
FROM information_schema.tables 
WHERE table_schema = 'appsalon' 
AND table_name = 'barberos';

-- Verificar columnas de usuarios
DESCRIBE usuarios;
