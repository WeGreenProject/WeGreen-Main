<?php
require_once 'connection.php';

class SearchAutocomplete {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function searchProdutos($query) {
        try {

        if (strlen($query) < 2) {
            return json_encode(['success' => true, 'produtos' => []], JSON_UNESCAPED_UNICODE);
        }

        $sql = "SELECT
                    p.Produto_id,
                    p.nome,
                    p.preco,
                    p.foto,
                    p.genero
                FROM Produtos p
                WHERE p.ativo = 1
                AND p.anunciante_id IS NOT NULL
                AND p.nome != ''
                AND p.preco IS NOT NULL
                AND p.stock > 0
                AND (
                    p.nome LIKE CONCAT('%', ?, '%')
                    OR p.marca LIKE CONCAT('%', ?, '%')
                    OR p.descricao LIKE CONCAT('%', ?, '%')
                )
                ORDER BY p.nome ASC
                LIMIT 8";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $query, $query, $query);
        $stmt->execute();
        $result = $stmt->get_result();

        $produtos = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $produtos[] = [
                    'id' => $row['Produto_id'],
                    'nome' => $row['nome'],
                    'preco' => number_format($row['preco'], 2, ',', '.'),
                    'foto' => $row['foto'],
                    'genero' => $row['genero']
                ];
            }
        }

        $stmt->close();

        return json_encode([
            'success' => true,
            'produtos' => $produtos
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }
}
?>
