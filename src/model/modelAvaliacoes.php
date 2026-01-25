<?php
require_once 'connection.php';

class Avaliacoes {

    /**
     * Criar nova avaliação de produto
     */
    function criarAvaliacao($produto_id, $utilizador_id, $encomenda_codigo, $avaliacao, $comentario = null) {
        global $conn;

        // Validações
        if (empty($produto_id) || empty($utilizador_id) || empty($encomenda_codigo)) {
            return ['success' => false, 'message' => 'Dados obrigatórios não fornecidos'];
        }

        if ($avaliacao < 1 || $avaliacao > 5) {
            return ['success' => false, 'message' => 'Avaliação deve ser entre 1 e 5 estrelas'];
        }

        // Verificar se utilizador comprou o produto nesta encomenda
        $sqlVerificar = "SELECT v.id
                        FROM vendas v
                        INNER JOIN encomendas e ON v.encomenda_id = e.id
                        WHERE e.codigo_encomenda = ?
                        AND e.cliente_id = ?
                        AND v.produto_id = ?
                        AND e.estado = 'Entregue'";

        $stmtVerificar = $conn->prepare($sqlVerificar);
        $stmtVerificar->bind_param("sii", $encomenda_codigo, $utilizador_id, $produto_id);
        $stmtVerificar->execute();
        $resultVerificar = $stmtVerificar->get_result();

        if ($resultVerificar->num_rows === 0) {
            return ['success' => false, 'message' => 'Não pode avaliar este produto'];
        }

        // Verificar se já avaliou este produto desta encomenda
        $sqlDuplicado = "SELECT id FROM Avaliacoes_Produtos
                        WHERE produto_id = ? AND utilizador_id = ? AND encomenda_codigo = ?";

        $stmtDuplicado = $conn->prepare($sqlDuplicado);
        $stmtDuplicado->bind_param("iis", $produto_id, $utilizador_id, $encomenda_codigo);
        $stmtDuplicado->execute();
        $resultDuplicado = $stmtDuplicado->get_result();

        if ($resultDuplicado->num_rows > 0) {
            return ['success' => false, 'message' => 'Já avaliou este produto'];
        }

        // Inserir avaliação
        $sql = "INSERT INTO Avaliacoes_Produtos (produto_id, utilizador_id, encomenda_codigo, avaliacao, comentario, data_criacao)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisis", $produto_id, $utilizador_id, $encomenda_codigo, $avaliacao, $comentario);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Avaliação registada com sucesso'];
        } else {
            return ['success' => false, 'message' => 'Erro ao registar avaliação'];
        }
    }

    /**
     * Obter todas as avaliações de um produto
     */
    function obterAvaliacoesProduto($produto_id) {
        global $conn;

        $sql = "SELECT
                    a.id,
                    a.avaliacao,
                    a.comentario,
                    a.data_criacao,
                    u.nome as utilizador_nome,
                    u.apelido as utilizador_apelido
                FROM Avaliacoes_Produtos a
                LEFT JOIN Utilizadores u ON a.utilizador_id = u.id
                WHERE a.produto_id = ?
                ORDER BY a.data_criacao DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $avaliacoes = [];
        while($row = $result->fetch_assoc()) {
            // Ocultar parte do apelido para privacidade
            if (!empty($row['utilizador_apelido'])) {
                $row['utilizador_apelido'] = substr($row['utilizador_apelido'], 0, 1) . '.';
            }
            $avaliacoes[] = $row;
        }

        return $avaliacoes;
    }

    /**
     * Obter estatísticas de avaliações de um produto
     */
    function obterEstatisticasProduto($produto_id) {
        global $conn;

        $sql = "SELECT
                    COUNT(*) as total,
                    AVG(avaliacao) as media,
                    SUM(CASE WHEN avaliacao = 5 THEN 1 ELSE 0 END) as estrelas_5,
                    SUM(CASE WHEN avaliacao = 4 THEN 1 ELSE 0 END) as estrelas_4,
                    SUM(CASE WHEN avaliacao = 3 THEN 1 ELSE 0 END) as estrelas_3,
                    SUM(CASE WHEN avaliacao = 2 THEN 1 ELSE 0 END) as estrelas_2,
                    SUM(CASE WHEN avaliacao = 1 THEN 1 ELSE 0 END) as estrelas_1
                FROM Avaliacoes_Produtos
                WHERE produto_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $row['media'] = round($row['media'], 1);
            return $row;
        }

        return [
            'total' => 0,
            'media' => 0,
            'estrelas_5' => 0,
            'estrelas_4' => 0,
            'estrelas_3' => 0,
            'estrelas_2' => 0,
            'estrelas_1' => 0
        ];
    }

    /**
     * Verificar se utilizador já avaliou produto de uma encomenda
     */
    function verificarSeAvaliou($produto_id, $utilizador_id, $encomenda_codigo) {
        global $conn;

        $sql = "SELECT id FROM Avaliacoes_Produtos
                WHERE produto_id = ? AND utilizador_id = ? AND encomenda_codigo = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $produto_id, $utilizador_id, $encomenda_codigo);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    /**
     * Obter produtos de uma encomenda que podem ser avaliados
     */
    function obterProdutosParaAvaliar($encomenda_codigo, $cliente_id) {
        global $conn;

        $sql = "SELECT
                    p.Produto_id,
                    p.nome,
                    p.foto,
                    v.quantidade,
                    CASE
                        WHEN a.id IS NOT NULL THEN 1
                        ELSE 0
                    END as avaliado,
                    a.avaliacao as avaliacao_dada,
                    a.comentario as comentario_dado
                FROM vendas v
                INNER JOIN encomendas e ON v.encomenda_id = e.id
                INNER JOIN produtos p ON v.produto_id = p.Produto_id
                LEFT JOIN Avaliacoes_Produtos a ON p.Produto_id = a.produto_id
                    AND a.utilizador_id = ?
                    AND a.encomenda_codigo = ?
                WHERE e.codigo_encomenda = ?
                AND e.cliente_id = ?
                AND e.estado = 'Entregue'";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $cliente_id, $encomenda_codigo, $encomenda_codigo, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $produtos = [];
        while($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        return $produtos;
    }
}
?>
