<?php
/**
 * Model Dashboard Cliente - Estatísticas e dados do dashboard
 */

require_once __DIR__ . '/../../connection.php';

class DashboardCliente {

    /**
     * Obter estatísticas gerais do cliente
     */
    public function getEstatisticasCliente($cliente_id) {
        global $conn;

        // Encomendas ativas (não entregues/canceladas)
        $sqlAtivas = "SELECT COUNT(*) as total FROM encomendas
                      WHERE cliente_id = ? AND estado NOT IN ('Entregue', 'Cancelado')";
        $stmtAtivas = $conn->prepare($sqlAtivas);
        $stmtAtivas->bind_param("i", $cliente_id);
        $stmtAtivas->execute();
        $encomendas_ativas = $stmtAtivas->get_result()->fetch_assoc()['total'];

        // Total gasto
        $sqlGasto = "SELECT COALESCE(SUM(v.valor), 0) as total
                     FROM vendas v
                     INNER JOIN encomendas e ON v.encomenda_id = e.id
                     WHERE e.cliente_id = ? AND e.estado != 'Cancelado'";
        $stmtGasto = $conn->prepare($sqlGasto);
        $stmtGasto->bind_param("i", $cliente_id);
        $stmtGasto->execute();
        $total_gasto = $stmtGasto->get_result()->fetch_assoc()['total'];

        // Produtos comprados (vendas)
        $sqlProdutos = "SELECT COUNT(*) as total
                        FROM vendas v
                        INNER JOIN encomendas e ON v.encomenda_id = e.id
                        WHERE e.cliente_id = ?";
        $stmtProdutos = $conn->prepare($sqlProdutos);
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
        ]);
    }

    /**
     * Obter encomendas recentes do cliente
     */
    public function getEncomendasRecentes($cliente_id, $limit = 5) {
        global $conn;

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

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $encomendas = [];
        while($row = $result->fetch_assoc()) {
            // Adicionar nome da transportadora (conforme carrinho.js)
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

        return json_encode(['success' => true, 'data' => $encomendas]);
    }

    /**
     * Obter produtos adquiridos recentemente pelo cliente
     * Mostra os últimos produtos comprados com suas informações
     */
    public function getProdutosRecomendados($cliente_id, $limit = 6) {
        global $conn;

        // Buscar produtos comprados recentemente pelo cliente
        $sql = "SELECT DISTINCT
                    p.Produto_id,
                    p.nome,
                    p.preco,
                    p.foto,
                    tp.descricao as categoria,
                    v.data_venda,
                    e.data_envio,
                    e.codigo_encomenda
                FROM produtos p
                INNER JOIN vendas v ON p.Produto_id = v.produto_id
                INNER JOIN encomendas e ON v.encomenda_id = e.id
                LEFT JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id
                WHERE e.cliente_id = ?
                ORDER BY v.data_venda DESC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $cliente_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $produtos = [];
        while($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        return json_encode(['success' => true, 'data' => $produtos]);
    }
}
?>
