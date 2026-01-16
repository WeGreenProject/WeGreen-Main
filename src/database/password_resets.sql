-- Tabela para armazenar tokens de recuperação de password
-- Criado em: 2026-01-13
-- Sistema: WeGreen Marketplace

CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilizador_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expira_em DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usado_em DATETIME NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    FOREIGN KEY (utilizador_id) REFERENCES Utilizadores(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_expira (expira_em),
    INDEX idx_usado (usado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comentários das colunas
ALTER TABLE password_resets
    MODIFY COLUMN id INT AUTO_INCREMENT COMMENT 'ID único do pedido de reset',
    MODIFY COLUMN utilizador_id INT NOT NULL COMMENT 'ID do utilizador que pediu reset',
    MODIFY COLUMN email VARCHAR(255) NOT NULL COMMENT 'Email do utilizador',
    MODIFY COLUMN token VARCHAR(255) NOT NULL COMMENT 'Token único de verificação (hash)',
    MODIFY COLUMN expira_em DATETIME NOT NULL COMMENT 'Data/hora de expiração do token (1 hora)',
    MODIFY COLUMN usado TINYINT(1) DEFAULT 0 COMMENT '0=não usado, 1=já utilizado',
    MODIFY COLUMN criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data/hora de criação do pedido',
    MODIFY COLUMN usado_em DATETIME NULL COMMENT 'Data/hora em que o token foi usado',
    MODIFY COLUMN ip_address VARCHAR(45) NULL COMMENT 'IP de origem do pedido',
    MODIFY COLUMN user_agent VARCHAR(255) NULL COMMENT 'User agent do browser';
