<?php

require_once 'connection.php';

class DashboardCliente {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    function getEstatisticasCliente($cliente_id) {
        try {


        $sqlAtivas = "SELECT COUNT(*) as total FROM encomendas
                      WHERE cliente_id = ? AND estado NOT IN ('Entregue', 'Cancelado')";
        $stmtAtivas = $this->conn->prepare($sqlAtivas);
        $stmtAtivas->bind_param("i", $cliente_id);
        $stmtAtivas->execute();
        $encomendas_ativas = $stmtAtivas->get_result()->fetch_assoc()['total'];


        $sqlGasto = "SELECT COALESCE(SUM(v.valor), 0) as total
                     FROM vendas v
                     INNER JOIN encomendas e ON v.encomenda_id = e.id
                     WHERE e.cliente_id = ? AND e.estado != 'Cancelado'";
        $stmtGasto = $this->conn->prepare($sqlGasto);
        $stmtGasto->bind_param("i", $cliente_id);
        $stmtGasto->execute();
        $total_gasto = $stmtGasto->get_result()->fetch_assoc()['total'];


        $sqlProdutos = "SELECT COUNT(*) as total
                        FROM vendas v
                        INNER JOIN encomendas e ON v.encomenda_id = e.id
                        WHERE e.cliente_id = ?";
        $stmtProdutos = $this->conn->prepare($sqlProdutos);
        $stmtProdutos->bind_param("i", $cliente_id);
        $stmtProdutos->execute();
        $produtos_comprados = $stmtProdutos->get_result()->fetch_assoc()['total'];

        return json_encode([
            'success' => true,
            'data' => [
                'encomendas_ativas' => $encomendas_ativas,
                'total_gasto' => $total_gasto,
                'produtos_comprados' => $produtos_comprados
            ]
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function getEncomendasRecentes($cliente_id, $limit = 5) {
        try {

        $sql = "SELECT
                    e.id,
                    e.data_envio,
                    e.estado,
                    e.codigo_encomenda,
                    e.transportadora_id,
                    e.morada,
                    (SELECT COUNT(*) FROM vendas WHERE encomenda_id = e.id) as total_produtos,
                    (SELECT SUM(v.valor) FROM vendas v WHERE v.encomenda_id = e.id) as valor_total,
                    (SELECT GROUP_CONCAT(p.nome SEPARATOR ', ')
                     FROM produtos p
                     INNER JOIN vendas v ON p.Produto_id = v.produto_id
                     WHERE v.encomenda_id = e.id
                     LIMIT 3) as nomes_produtos,
                    (SELECT p.foto
                     FROM produtos p
                     INNER JOIN vendas v ON p.Produto_id = v.produto_id
                     WHERE v.encomenda_id = e.id
                     LIMIT 1) as foto_produto
                FROM encomendas e
                WHERE e.cliente_id = ?
                ORDER BY e.data_envio DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $encomendas = [];
        while($row = $result->fetch_assoc()) {

            $transportadoras = [
                1 => 'CTT - Correios de Portugal',
                2 => 'CTT - Ponto de Recolha',
                3 => 'DPD - Entrega Rápida',
                4 => 'DPD - Ponto de Recolha',
                5 => 'Entrega em Casa'
            ];
            $row['transportadora'] = $transportadoras[$row['transportadora_id']] ?? 'N/A';

            $encomendas[] = $row;
        }

        return json_encode(['success' => true, 'data' => $encomendas], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function getProdutosRecomendados($cliente_id, $limit = 6) {
        try {


                $sql = "SELECT
                                        p.Produto_id,
                                        p.nome,
                                        p.preco,
                                        p.foto,
                                        tp.descricao as categoria,
                                        MAX(v.data_venda) as data_venda,
                                        MAX(e.data_envio) as data_envio,
                                        MAX(e.codigo_encomenda) as codigo_encomenda
                                FROM produtos p
                                INNER JOIN vendas v ON p.Produto_id = v.produto_id
                                INNER JOIN encomendas e ON v.encomenda_id = e.id
                                LEFT JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id
                                WHERE e.cliente_id = ?
                                    AND p.ativo = 1
                                GROUP BY p.Produto_id, p.nome, p.preco, p.foto, tp.descricao
                                ORDER BY MAX(v.data_venda) DESC
                                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $produtos = [];
        while($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        return json_encode(['success' => true, 'data' => $produtos], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
