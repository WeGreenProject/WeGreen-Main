-- Script SQL para criar tabela de preferências de notificações
-- WeGreen Marketplace - Sistema de Notificações

-- Tabela de preferências de notificações por utilizador
CREATE TABLE IF NOT EXISTS `notificacoes_preferencias` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `tipo_user` ENUM('cliente', 'anunciante') NOT NULL,

    -- Preferências para Clientes
    `email_confirmacao` TINYINT(1) DEFAULT 1 COMMENT 'Receber email de confirmação de encomenda',
    `email_processando` TINYINT(1) DEFAULT 1 COMMENT 'Receber email quando encomenda está a ser processada',
    `email_enviado` TINYINT(1) DEFAULT 1 COMMENT 'Receber email quando encomenda é enviada',
    `email_entregue` TINYINT(1) DEFAULT 1 COMMENT 'Receber email quando encomenda é entregue',
    `email_cancelamento` TINYINT(1) DEFAULT 1 COMMENT 'Receber email quando encomenda é cancelada',

    -- Preferências para Anunciantes
    `email_novas_encomendas_anunciante` TINYINT(1) DEFAULT 1 COMMENT 'Receber email de novas encomendas',
    `email_encomendas_urgentes` TINYINT(1) DEFAULT 1 COMMENT 'Receber alertas de encomendas pendentes urgentes',

    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_user_preferencias` (`user_id`, `tipo_user`),
    FOREIGN KEY (`user_id`) REFERENCES `Utilizadores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Preferências de notificações por email dos utilizadores';

-- Inserir preferências padrão para utilizadores existentes (opcional)
-- Isto cria registos com todas as notificações ativadas por padrão

-- Para Clientes (tipo_utilizador_id = 2)
INSERT IGNORE INTO `notificacoes_preferencias`
    (`user_id`, `tipo_user`, `email_confirmacao`, `email_processando`, `email_enviado`, `email_entregue`, `email_cancelamento`)
SELECT
    `id`,
    'cliente',
    1, 1, 1, 1, 1
FROM `Utilizadores`
WHERE `tipo_utilizador_id` = 2;

-- Para Anunciantes (tipo_utilizador_id = 3)
INSERT IGNORE INTO `notificacoes_preferencias`
    (`user_id`, `tipo_user`, `email_novas_encomendas_anunciante`, `email_encomendas_urgentes`)
SELECT
    `id`,
    'anunciante',
    1, 1
FROM `Utilizadores`
WHERE `tipo_utilizador_id` = 3;

-- Para Administradores que também podem ser anunciantes (tipo_utilizador_id = 1)
INSERT IGNORE INTO `notificacoes_preferencias`
    (`user_id`, `tipo_user`, `email_novas_encomendas_anunciante`, `email_encomendas_urgentes`)
SELECT
    `id`,
    'anunciante',
    1, 1
FROM `Utilizadores`
WHERE `tipo_utilizador_id` = 1;
