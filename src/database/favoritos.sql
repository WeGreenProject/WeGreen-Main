-- Tabela de Favoritos/Wishlist
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `data_adicao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorito` (`cliente_id`, `produto_id`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_produto` (`produto_id`),
  KEY `idx_data` (`data_adicao`),
  CONSTRAINT `fk_favorito_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorito_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`Produto_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Índices para melhor performance
CREATE INDEX idx_cliente_data ON favoritos(cliente_id, data_adicao DESC);
