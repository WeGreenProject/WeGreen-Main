-- ====================================
-- Migration: Sistema de Devoluções
-- Data: 16/01/2026
-- Descrição: Cria tabela de devoluções e estende notificações
-- ====================================

-- Criar tabela de devoluções
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

  -- Chaves estrangeiras
  FOREIGN KEY (`encomenda_id`) REFERENCES `encomendas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`cliente_id`) REFERENCES `cliente`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`anunciante_id`) REFERENCES `anunciante`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE,

  -- Índices para performance
  INDEX `idx_cliente` (`cliente_id`),
  INDEX `idx_anunciante` (`anunciante_id`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_data_solicitacao` (`data_solicitacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Estender tabela de preferências de notificações
-- ====================================

-- Adicionar colunas para notificações de devolução
ALTER TABLE `notificacoes_preferencias`
ADD COLUMN IF NOT EXISTS `email_devolucao_solicitada` TINYINT(1) DEFAULT 1 COMMENT 'Cliente: Confirmação de pedido de devolução',
ADD COLUMN IF NOT EXISTS `email_devolucao_aprovada` TINYINT(1) DEFAULT 1 COMMENT 'Cliente: Devolução aprovada',
ADD COLUMN IF NOT EXISTS `email_devolucao_rejeitada` TINYINT(1) DEFAULT 1 COMMENT 'Cliente: Devolução rejeitada',
ADD COLUMN IF NOT EXISTS `email_reembolso_processado` TINYINT(1) DEFAULT 1 COMMENT 'Cliente: Reembolso processado',
ADD COLUMN IF NOT EXISTS `email_nova_devolucao_anunciante` TINYINT(1) DEFAULT 1 COMMENT 'Anunciante: Nova devolução solicitada';

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

  FOREIGN KEY (`devolucao_id`) REFERENCES `devolucoes`(`id`) ON DELETE CASCADE,
  INDEX `idx_devolucao` (`devolucao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- Dados iniciais (opcional)
-- ====================================

-- Garantir que todos os usuários têm as novas preferências ativadas
UPDATE `notificacoes_preferencias`
SET
  `email_devolucao_solicitada` = 1,
  `email_devolucao_aprovada` = 1,
  `email_devolucao_rejeitada` = 1,
  `email_reembolso_processado` = 1,
  `email_nova_devolucao_anunciante` = 1
WHERE 1=1;

-- ====================================
-- Views úteis (opcional)
-- ====================================

-- View para estatísticas de devoluções por anunciante
CREATE OR REPLACE VIEW `stats_devolucoes_anunciante` AS
SELECT
  a.id as anunciante_id,
  a.nome as anunciante_nome,
  COUNT(d.id) as total_devolucoes,
  SUM(CASE WHEN d.estado = 'solicitada' THEN 1 ELSE 0 END) as pendentes,
  SUM(CASE WHEN d.estado = 'aprovada' THEN 1 ELSE 0 END) as aprovadas,
  SUM(CASE WHEN d.estado = 'rejeitada' THEN 1 ELSE 0 END) as rejeitadas,
  SUM(CASE WHEN d.estado = 'reembolsada' THEN 1 ELSE 0 END) as reembolsadas,
  SUM(d.valor_reembolso) as valor_total_reembolsado
FROM anunciante a
LEFT JOIN devolucoes d ON a.id = d.anunciante_id
GROUP BY a.id;

-- View para devoluções com detalhes completos
CREATE OR REPLACE VIEW `view_devolucoes_completa` AS
SELECT
  d.*,
  e.codigo_encomenda,
  e.data_envio as data_entrega_original,
  c.nome as cliente_nome,
  c.email as cliente_email,
  a.nome as anunciante_nome,
  a.email as anunciante_email,
  p.nome as produto_nome,
  p.imagem as produto_imagem
FROM devolucoes d
INNER JOIN encomendas e ON d.encomenda_id = e.id
INNER JOIN cliente c ON d.cliente_id = c.id
INNER JOIN anunciante a ON d.anunciante_id = a.id
INNER JOIN produtos p ON d.produto_id = p.id;

-- ====================================
-- Triggers para auditoria
-- ====================================

-- Trigger para registrar histórico de mudanças de estado
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS `after_devolucao_update`
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
-- Comentários finais
-- ====================================

-- Para executar esta migration:
-- 1. Conectar ao MySQL
-- 2. Selecionar o database WeGreen
-- 3. Executar: SOURCE c:/xampp/htdocs/WeGreen-Main/src/database/migration_devolucoes.sql
-- 4. Verificar com: SHOW TABLES LIKE '%devolucoes%';
-- 5. Confirmar colunas: DESCRIBE devolucoes;
