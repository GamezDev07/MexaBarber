-- Agregar campos faltantes a la tabla usuarios
ALTER TABLE usuarios ADD COLUMN password VARCHAR(60) NULL AFTER email;
ALTER TABLE usuarios ADD COLUMN admin TINYINT(1) DEFAULT 0 AFTER password;
ALTER TABLE usuarios ADD COLUMN confirmado TINYINT(1) DEFAULT 0 AFTER admin;
ALTER TABLE usuarios ADD COLUMN token VARCHAR(15) DEFAULT '' AFTER confirmado;
