SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND COLUMN_NAME = 'tipo_entrega';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Encomendas ADD COLUMN tipo_entrega ENUM(''domicilio'', ''ponto_recolha'') DEFAULT ''domicilio'' COMMENT ''Tipo de entrega escolhido pelo cliente''',
    'SELECT ''Coluna tipo_entrega já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar ponto_recolha_id
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND COLUMN_NAME = 'ponto_recolha_id';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Encomendas ADD COLUMN ponto_recolha_id VARCHAR(100) NULL COMMENT ''ID do ponto de recolha (quando tipo_entrega = ponto_recolha)''',
    'SELECT ''Coluna ponto_recolha_id já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar nome_ponto_recolha
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND COLUMN_NAME = 'nome_ponto_recolha';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Encomendas ADD COLUMN nome_ponto_recolha VARCHAR(255) NULL COMMENT ''Nome do ponto de recolha''',
    'SELECT ''Coluna nome_ponto_recolha já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar morada_ponto_recolha
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND COLUMN_NAME = 'morada_ponto_recolha';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Encomendas ADD COLUMN morada_ponto_recolha TEXT NULL COMMENT ''Morada completa do ponto de recolha''',
    'SELECT ''Coluna morada_ponto_recolha já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar morada_completa
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND COLUMN_NAME = 'morada_completa';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Encomendas ADD COLUMN morada_completa TEXT NULL COMMENT ''Morada completa de entrega (formata: nome + morada atual)''',
    'SELECT ''Coluna morada_completa já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar nome_destinatario
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND COLUMN_NAME = 'nome_destinatario';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE Encomendas ADD COLUMN nome_destinatario VARCHAR(255) NULL COMMENT ''Nome completo do destinatário (firstName + lastName)''',
    'SELECT ''Coluna nome_destinatario já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Criar índices apenas se não existirem
SET @idx_exists = 0;
SELECT COUNT(*) INTO @idx_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND INDEX_NAME = 'idx_tipo_entrega';

SET @sql = IF(@idx_exists = 0,
    'CREATE INDEX idx_tipo_entrega ON Encomendas(tipo_entrega)',
    'SELECT ''Índice idx_tipo_entrega já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @idx_exists = 0;
SELECT COUNT(*) INTO @idx_exists
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'WeGreen' AND TABLE_NAME = 'Encomendas' AND INDEX_NAME = 'idx_ponto_recolha';

SET @sql = IF(@idx_exists = 0,
    'CREATE INDEX idx_ponto_recolha ON Encomendas(ponto_recolha_id)',
    'SELECT ''Índice idx_ponto_recolha já existe'' AS info');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Atualizar registros existentes apenas se tipo_entrega for NULL
UPDATE Encomendas
SET tipo_entrega = 'domicilio',
    morada_completa = COALESCE(morada_completa, morada),
    nome_destinatario = COALESCE(nome_destinatario, 'Cliente')
WHERE tipo_entrega IS NULL OR tipo_entrega = '';

-- Verificar estrutura atualizada
DESCRIBE Encomendas;

-- Query de teste para verificar os dados
SELECT
    codigo_encomenda,
    tipo_entrega,
    CASE
        WHEN tipo_entrega = 'ponto_recolha' THEN CONCAT(nome_ponto_recolha, ' - ', morada_ponto_recolha)
        ELSE morada_completa
    END as endereco_entrega,
    nome_destinatario,
    estado,
    data_envio
FROM Encomendas
ORDER BY data_envio DESC
LIMIT 10;
