-- ====================================
-- Migration: Sistema de Devoluções (CORRIGIDO)
-- Data: 16/01/2026
-- Descrição: Cria tabela de devoluções e estende notificações
-- ====================================

-- Criar tabela de devoluções (SEM foreign keys primeiro)
CREATE TABLE IF NOT EXISTS `devolucoes` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `encomenda_id` INT(11) NOT NULL,
  `codigo_devolucao` VARCHAR(50) UNIQUE NOT NULL,
  `cliente_id` INT(11) NOT NULL,
  `anunciante_id` INT(11) NOT NULL,
  `produto_id` INT(11) NOT NULL,
  `valor_reembolso` DECIMAL(10,2) NOT NULL,

  -- Motivo e detalhes
  `motivo` ENUM('defeituoso', 'tamanho_errado', 'nao_como_descrito', 'arrependimento', 'outro') NOT NULL,
  `motivo_detalhe` TEXT,
  `notas_cliente` TEXT,
  `notas_anunciante` TEXT,

  -- Estado e processamento
  `estado` ENUM('solicitada', 'aprovada', 'rejeitada', 'produto_recebido', 'reembolsada', 'cancelada') DEFAULT 'solicitada',

  -- Stripe e reembolso
  `payment_intent_id` VARCHAR(100),
  `reembolso_stripe_id` VARCHAR(100),
  `reembolso_status` VARCHAR(50) COMMENT 'pending, succeeded, failed, canceled',

  -- Fotos de evidência
  `fotos` JSON COMMENT 'Array de URLs das fotos do produto/defeito',

  -- Rastreamento de devolução
  `codigo_envio_devolucao` VARCHAR(100),
  `transportadora_devolucao_id` INT(11),

  -- Timestamps
  `data_solicitacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `data_aprovacao` TIMESTAMP NULL,
  `data_rejeicao` TIMESTAMP NULL,
  `data_produto_recebido` TIMESTAMP NULL,
  `data_reembolso` TIMESTAMP NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  -- Índices para performance
  INDEX `idx_encomenda` (`encomenda_id`),
  INDEX `idx_cliente` (`cliente_id`),
  INDEX `idx_anunciante` (`anunciante_id`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_data_solicitacao` (`data_solicitacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Estender tabela de preferências de notificações
-- ====================================

-- Verificar se colunas já existem antes de adicionar
SET @col_exists = (SELECT COUNT(*)
                   FROM information_schema.COLUMNS
                   WHERE TABLE_SCHEMA = 'wegreen'
                   AND TABLE_NAME = 'notificacoes_preferencias'
                   AND COLUMN_NAME = 'email_devolucao_solicitada');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `notificacoes_preferencias`
     ADD COLUMN `email_devolucao_solicitada` TINYINT(1) DEFAULT 1 COMMENT ''Cliente: Confirmação de pedido de devolução'',
     ADD COLUMN `email_devolucao_aprovada` TINYINT(1) DEFAULT 1 COMMENT ''Cliente: Devolução aprovada'',
     ADD COLUMN `email_devolucao_rejeitada` TINYINT(1) DEFAULT 1 COMMENT ''Cliente: Devolução rejeitada'',
     ADD COLUMN `email_reembolso_processado` TINYINT(1) DEFAULT 1 COMMENT ''Cliente: Reembolso processado'',
     ADD COLUMN `email_nova_devolucao_anunciante` TINYINT(1) DEFAULT 1 COMMENT ''Anunciante: Nova devolução solicitada''',
    'SELECT ''Colunas já existem'' AS info');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ====================================
-- Criar tabela de histórico de devoluções
-- ====================================

CREATE TABLE IF NOT EXISTS `historico_devolucoes` (
  `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `devolucao_id` INT(11) NOT NULL,
  `estado_anterior` VARCHAR(50),
  `estado_novo` VARCHAR(50) NOT NULL,
  `observacao` TEXT,
  `alterado_por` VARCHAR(50) COMMENT 'cliente, anunciante, sistema',
  `data_alteracao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  INDEX `idx_devolucao` (`devolucao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Atualizar preferências dos utilizadores existentes
-- ====================================

UPDATE `notificacoes_preferencias`
SET
  `email_devolucao_solicitada` = 1,
  `email_devolucao_aprovada` = 1,
  `email_devolucao_rejeitada` = 1,
  `email_reembolso_processado` = 1,
  `email_nova_devolucao_anunciante` = 1
WHERE 1=1;

-- ====================================
-- Views úteis
-- ====================================

-- View para estatísticas de devoluções por anunciante
CREATE OR REPLACE VIEW `stats_devolucoes_anunciante` AS
SELECT
  d.anunciante_id,
  COUNT(d.id) as total_devolucoes,
  SUM(CASE WHEN d.estado = 'solicitada' THEN 1 ELSE 0 END) as pendentes,
  SUM(CASE WHEN d.estado = 'aprovada' THEN 1 ELSE 0 END) as aprovadas,
  SUM(CASE WHEN d.estado = 'rejeitada' THEN 1 ELSE 0 END) as rejeitadas,
  SUM(CASE WHEN d.estado = 'reembolsada' THEN 1 ELSE 0 END) as reembolsadas,
  COALESCE(SUM(d.valor_reembolso), 0) as valor_total_reembolsado
FROM devolucoes d
GROUP BY d.anunciante_id;

-- View para devoluções com detalhes completos
CREATE OR REPLACE VIEW `view_devolucoes_completa` AS
SELECT
  d.*,
  e.codigo_encomenda,
  e.data_envio as data_entrega_original,
  p.nome as produto_nome,
  p.foto as produto_foto
FROM devolucoes d
INNER JOIN encomendas e ON d.encomenda_id = e.id
INNER JOIN produtos p ON d.produto_id = p.Produto_id;

-- ====================================
-- Triggers para auditoria
-- ====================================

DELIMITER $$

DROP TRIGGER IF EXISTS `after_devolucao_update`$$

CREATE TRIGGER `after_devolucao_update`
AFTER UPDATE ON `devolucoes`
FOR EACH ROW
BEGIN
  IF OLD.estado != NEW.estado THEN
    INSERT INTO historico_devolucoes (devolucao_id, estado_anterior, estado_novo, alterado_por, observacao)
    VALUES (
      NEW.id,
      OLD.estado,
      NEW.estado,
      'sistema',
      CONCAT('Estado alterado de ', OLD.estado, ' para ', NEW.estado)
    );
  END IF;
END$$

DELIMITER ;

-- ====================================
-- Verificação final
-- ====================================

SELECT 'Migration executada com sucesso!' AS status;
SELECT COUNT(*) as tabela_devolucoes FROM information_schema.TABLES WHERE TABLE_SCHEMA='wegreen' AND TABLE_NAME='devolucoes';
SELECT COUNT(*) as tabela_historico FROM information_schema.TABLES WHERE TABLE_SCHEMA='wegreen' AND TABLE_NAME='historico_devolucoes';
