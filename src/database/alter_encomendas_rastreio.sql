-- Adicionar campo código de rastreio à tabela Encomendas
ALTER TABLE encomendas
ADD COLUMN codigo_rastreio VARCHAR(100) NULL COMMENT 'Código de rastreamento da transportadora' AFTER plano_rastreio;

-- Adicionar índice para busca rápida
CREATE INDEX idx_codigo_rastreio ON encomendas(codigo_rastreio);
