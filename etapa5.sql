-- Executar uma única vez no banco biblioteca.
USE biblioteca;
ALTER TABLE usuarios ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER perfil;
