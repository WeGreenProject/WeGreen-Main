<?php
/**
 * Model Favoritos - Gestão de Lista de Desejos/Wishlist
 *
 * Responsável por gerir a lista de produtos favoritos dos clientes
 */

require_once 'connection.php';

class Favoritos {

    /**
     * Adicionar produto aos favoritos
     *
     * @param int $cliente_id ID do cliente
     * @param int $produto_id ID do produto
     * @return array Resultado da operação
     */
    public function adicionarFavorito($cliente_id, $produto_id) {
        global $conn;

        // Verificar se o produto existe e está ativo
        $sqlProduto = "SELECT Produto_id, nome, ativo FROM produtos WHERE Produto_id = ?";
        $stmtProduto = $conn->prepare($sqlProduto);
        $stmtProduto->bind_param("i", $produto_id);
        $stmtProduto->execute();
        $resultProduto = $stmtProduto->get_result();

        if ($resultProduto->num_rows === 0) {
            return json_encode(['success' => false, 'message' => 'Produto não encontrado.']);
        }

        $produto = $resultProduto->fetch_assoc();
        if ($produto['ativo'] != 1) {
            return json_encode(['success' => false, 'message' => 'Este produto não está mais disponível.']);
        }

        // Verificar se já existe nos favoritos
        $sqlCheck = "SELECT id FROM favoritos WHERE cliente_id = ? AND produto_id = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $cliente_id, $produto_id);
        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {
            return json_encode(['success' => false, 'message' => 'Este produto já está nos seus favoritos.']);
        }

        // Adicionar aos favoritos
        $sql = "INSERT INTO favoritos (cliente_id, produto_id, data_adicao) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $produto_id);

        if ($stmt->execute()) {
            return json_encode([
                'success' => true,
                'message' => 'Produto adicionado aos favoritos!',
                'produto_nome' => $produto['nome']
            ]);
        } else {
            return json_encode(['success' => false, 'message' => 'Erro ao adicionar aos favoritos.']);
        }
    }

    /**
     * Remover produto dos favoritos
     *
     * @param int $cliente_id ID do cliente
     * @param int $produto_id ID do produto
     * @return array Resultado da operação
     */
    public function removerFavorito($cliente_id, $produto_id) {
        global $conn;

        $sql = "DELETE FROM favoritos WHERE cliente_id = ? AND produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $produto_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            return json_encode(['success' => true, 'message' => 'Produto removido dos favoritos.']);
        } else {
            return json_encode(['success' => false, 'message' => 'Produto não encontrado nos favoritos.']);
        }
    }

    /**
     * Listar todos os favoritos de um cliente
     *
     * @param int $cliente_id ID do cliente
     * @return array Lista de produtos favoritos
     */
    public function listarFavoritos($cliente_id) {
        global $conn;

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

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $favoritos = [];
        while ($row = $result->fetch_assoc()) {
            $favoritos[] = $row;
        }

        return json_encode(['success' => true, 'data' => $favoritos, 'total' => count($favoritos)]);
    }

    /**
     * Verificar se um produto está nos favoritos
     *
     * @param int $cliente_id ID do cliente
     * @param int $produto_id ID do produto
     * @return bool True se está nos favoritos
     */
    public function verificarFavorito($cliente_id, $produto_id) {
        global $conn;

        $sql = "SELECT id FROM favoritos WHERE cliente_id = ? AND produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $produto_id);
        $stmt->execute();

        $isFavorito = $stmt->get_result()->num_rows > 0;
        return json_encode(['success' => true, 'isFavorito' => $isFavorito]);
    }

    /**
     * Contar total de favoritos de um cliente
     *
     * @param int $cliente_id ID do cliente
     * @return int Total de favoritos
     */
    public function contarFavoritos($cliente_id) {
        global $conn;

        $sql = "SELECT COUNT(*) as total FROM favoritos WHERE cliente_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return json_encode(['success' => true, 'total' => (int)$row['total']]);
    }

    /**
     * Limpar favoritos inativos (produtos que já não existem ou estão inativos)
     *
     * @param int $cliente_id ID do cliente
     * @return array Resultado da limpeza
     */
    public function limparFavoritosInativos($cliente_id) {
        global $conn;

        $sql = "DELETE f FROM favoritos f
                LEFT JOIN produtos p ON f.produto_id = p.Produto_id
                WHERE f.cliente_id = ? AND (p.Produto_id IS NULL OR p.ativo = 0)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();

        $removidos = $stmt->affected_rows;

        return json_encode([
            'success' => true,
            'message' => "Removidos $removidos produtos inativos dos favoritos.",
            'removidos' => $removidos
        ]);
    }
}
?>
