<?php
require_once __DIR__ . '/connection.php';

class Avaliacoes {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    function criarAvaliacao($produto_id, $utilizador_id, $encomenda_codigo, $avaliacao, $comentario = null) {
        try {

        if (empty($produto_id) || empty($utilizador_id) || empty($encomenda_codigo)) {
            return ['success' => false, 'message' => 'Dados obrigatórios não fornecidos'];
        }

        if ($avaliacao < 1 || $avaliacao > 5) {
            return ['success' => false, 'message' => 'Avaliação deve ser entre 1 e 5 estrelas'];
        }


        $sqlVerificar = "SELECT v.id
                        FROM vendas v
                        INNER JOIN encomendas e ON v.encomenda_id = e.id
                        WHERE e.codigo_encomenda = ?
                        AND e.cliente_id = ?
                        AND v.produto_id = ?
                        AND e.estado = 'Entregue'";

        $stmtVerificar = $this->conn->prepare($sqlVerificar);
        $stmtVerificar->bind_param("sii", $encomenda_codigo, $utilizador_id, $produto_id);
        $stmtVerificar->execute();
        $resultVerificar = $stmtVerificar->get_result();

        if ($resultVerificar->num_rows === 0) {
            return ['success' => false, 'message' => 'Não pode avaliar este produto'];
        }


        $sqlDuplicado = "SELECT id FROM Avaliacoes_Produtos
                        WHERE produto_id = ? AND utilizador_id = ? AND encomenda_codigo = ?";

        $stmtDuplicado = $this->conn->prepare($sqlDuplicado);
        $stmtDuplicado->bind_param("iis", $produto_id, $utilizador_id, $encomenda_codigo);
        $stmtDuplicado->execute();
        $resultDuplicado = $stmtDuplicado->get_result();

        if ($resultDuplicado->num_rows > 0) {
            return ['success' => false, 'message' => 'Já avaliou este produto'];
        }

        $sql = "INSERT INTO Avaliacoes_Produtos (produto_id, utilizador_id, encomenda_codigo, avaliacao, comentario, data_criacao)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisis", $produto_id, $utilizador_id, $encomenda_codigo, $avaliacao, $comentario);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Avaliação registada com sucesso'];
        }

        return ['success' => false, 'message' => 'Erro ao registar avaliação'];
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function obterAvaliacoesProduto($produto_id) {
        try {

        if (!$this->conn) {
            return [];
        }

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

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $produto_id);
        if (!$stmt->execute()) {
            return [];
        }

        $result = $stmt->get_result();
        if (!$result) {
            return [];
        }

        $avaliacoes = [];
        while($row = $result->fetch_assoc()) {

            if (!empty($row['utilizador_apelido'])) {
                $row['utilizador_apelido'] = substr($row['utilizador_apelido'], 0, 1) . '.';
            }
            $avaliacoes[] = $row;
        }

        return $avaliacoes;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function obterEstatisticasProduto($produto_id) {
        try {

        if (!$this->conn) {
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

        $sql = "SELECT
                    COUNT(*) as total,
                    COALESCE(AVG(avaliacao), 0) as media,
                    SUM(CASE WHEN avaliacao = 5 THEN 1 ELSE 0 END) as estrelas_5,
                    SUM(CASE WHEN avaliacao = 4 THEN 1 ELSE 0 END) as estrelas_4,
                    SUM(CASE WHEN avaliacao = 3 THEN 1 ELSE 0 END) as estrelas_3,
                    SUM(CASE WHEN avaliacao = 2 THEN 1 ELSE 0 END) as estrelas_2,
                    SUM(CASE WHEN avaliacao = 1 THEN 1 ELSE 0 END) as estrelas_1
                FROM Avaliacoes_Produtos
                WHERE produto_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
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

        $stmt->bind_param("i", $produto_id);
        if (!$stmt->execute()) {
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

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $row['total'] = (int)($row['total'] ?? 0);
            $row['media'] = round((float)($row['media'] ?? 0), 1);
            $row['estrelas_5'] = (int)($row['estrelas_5'] ?? 0);
            $row['estrelas_4'] = (int)($row['estrelas_4'] ?? 0);
            $row['estrelas_3'] = (int)($row['estrelas_3'] ?? 0);
            $row['estrelas_2'] = (int)($row['estrelas_2'] ?? 0);
            $row['estrelas_1'] = (int)($row['estrelas_1'] ?? 0);
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
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function verificarSeAvaliou($produto_id, $utilizador_id, $encomenda_codigo) {
        try {

        $sql = "SELECT id FROM Avaliacoes_Produtos
                WHERE produto_id = ? AND utilizador_id = ? AND encomenda_codigo = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iis", $produto_id, $utilizador_id, $encomenda_codigo);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function obterProdutosParaAvaliar($encomenda_codigo, $cliente_id) {
        try {

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
                AND e.estado = 'Entregue'
                AND a.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issi", $cliente_id, $encomenda_codigo, $encomenda_codigo, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $produtos = [];
        while($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        return $produtos;
        } catch (Exception $e) {
            return [];
        }
    }
}
?>
