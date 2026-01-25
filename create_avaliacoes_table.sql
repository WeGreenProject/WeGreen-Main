-- Tabela de Avaliações de Produtos
CREATE TABLE IF NOT EXISTS Avaliacoes_Produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    utilizador_id INT NOT NULL,
    encomenda_codigo VARCHAR(50) NOT NULL,
    avaliacao INT NOT NULL CHECK (avaliacao >= 1 AND avaliacao <= 5),
    comentario TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES Produtos(Produto_id) ON DELETE CASCADE,
    FOREIGN KEY (utilizador_id) REFERENCES Utilizadores(id) ON DELETE CASCADE,
    UNIQUE KEY unique_avaliacao (utilizador_id, produto_id, encomenda_codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para melhor performance
CREATE INDEX idx_produto_id ON Avaliacoes_Produtos(produto_id);
CREATE INDEX idx_utilizador_id ON Avaliacoes_Produtos(utilizador_id);
CREATE INDEX idx_data_criacao ON Avaliacoes_Produtos(data_criacao DESC);
