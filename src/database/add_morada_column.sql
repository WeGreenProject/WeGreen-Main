-- Script para adicionar campo morada à tabela Utilizadores
-- Execute este script no phpMyAdmin ou MySQL Workbench

USE WeGreen;

ALTER TABLE Utilizadores
ADD COLUMN morada VARCHAR(255) NULL AFTER telefone;

-- Verificar se foi adicionado
DESCRIBE Utilizadores;
