<?php

require_once __DIR__ . '/connection.php';

class Favoritos {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function adicionarFavorito($cliente_id, $produto_id) {
        try {

        $sqlProduto = "SELECT Produto_id, nome, ativo FROM produtos WHERE Produto_id = ?";
        $stmtProduto = $this->conn->prepare($sqlProduto);
        $stmtProduto->bind_param("i", $produto_id);
        $stmtProduto->execute();
        $resultProduto = $stmtProduto->get_result();

        if ($resultProduto->num_rows === 0) {
            return json_encode(['success' => false, 'message' => 'Produto não encontrado.'], JSON_UNESCAPED_UNICODE);
        }

        $produto = $resultProduto->fetch_assoc();
        if ($produto['ativo'] != 1) {
            return json_encode(['success' => false, 'message' => 'Este produto não está mais disponível.'], JSON_UNESCAPED_UNICODE);
        }

        $sqlCheck = "SELECT id FROM favoritos WHERE cliente_id = ? AND produto_id = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $cliente_id, $produto_id);
        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {
            return json_encode(['success' => false, 'message' => 'Este produto já está nos seus favoritos.'], JSON_UNESCAPED_UNICODE);
        }

        $sql = "INSERT INTO favoritos (cliente_id, produto_id, data_adicao) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $produto_id);

        if ($stmt->execute()) {
            return json_encode([
                'success' => true,
                'message' => 'Produto adicionado aos favoritos!',
                'produto_nome' => $produto['nome']
            ], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['success' => false, 'message' => 'Erro ao adicionar aos favoritos.'], JSON_UNESCAPED_UNICODE);
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function removerFavorito($cliente_id, $produto_id) {
        try {

        $sql = "DELETE FROM favoritos WHERE cliente_id = ? AND produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $produto_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return json_encode(['success' => true, 'message' => 'Produto removido dos favoritos.'], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['success' => false, 'message' => 'Produto não encontrado nos favoritos.'], JSON_UNESCAPED_UNICODE);
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarFavoritos($cliente_id) {
        try {

        $sql = "SELECT
                    f.id as favorito_id,
                    f.data_adicao,
                    p.Produto_id as produto_id,
                    p.nome,
                    p.descricao,
                    p.preco,
                    p.foto,
                    p.marca,
                    p.tamanho,
                    p.estado,
                    p.stock,
                    p.ativo,
                    u.nome as anunciante_nome,
                    u.id as anunciante_id,
                    tp.descricao as categoria
                FROM favoritos f
                INNER JOIN produtos p ON f.produto_id = p.Produto_id
                INNER JOIN utilizadores u ON p.anunciante_id = u.id
                LEFT JOIN tipo_produtos tp ON p.tipo_produto_id = tp.id
                WHERE f.cliente_id = ?
                ORDER BY f.data_adicao DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $favoritos = [];
        while ($row = $result->fetch_assoc()) {
            $favoritos[] = $row;
        }

        return json_encode(['success' => true, 'data' => $favoritos, 'total' => count($favoritos)], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function verificarFavorito($cliente_id, $produto_id) {
        try {

        $sql = "SELECT id FROM favoritos WHERE cliente_id = ? AND produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $produto_id);
        $stmt->execute();

        $isFavorito = $stmt->get_result()->num_rows > 0;
        return json_encode(['success' => true, 'isFavorito' => $isFavorito], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function contarFavoritos($cliente_id) {
        try {

        $sql = "SELECT COUNT(*) as total FROM favoritos WHERE cliente_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return json_encode(['success' => true, 'total' => (int)$row['total']], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function limparFavoritosInativos($cliente_id) {
        try {

        $sql = "DELETE f FROM favoritos f
                LEFT JOIN produtos p ON f.produto_id = p.Produto_id
                WHERE f.cliente_id = ? AND (p.Produto_id IS NULL OR p.ativo = 0)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();

        $removidos = $stmt->affected_rows;

        return json_encode([
            'success' => true,
            'message' => "Removidos $removidos produtos inativos dos favoritos.",
            'removidos' => $removidos
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
