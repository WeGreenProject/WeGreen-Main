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
                    (SELECT COUNT(*) FROM vendas WHERE encomenda_id = e.id) as total_produtos,
                    (SELECT SUM(v.valor) FROM vendas v WHERE v.encomenda_id = e.id) as valor_total
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
            $encomendas[] = $row;
        }

        return json_encode(['success' => true, 'data' => $encomendas]);
    }

    /**
     * Obter produtos recomendados para o cliente
     * Baseado em: 1) Categorias compradas, 2) Novidades
     */
    public function getProdutosRecomendados($cliente_id, $limit = 6) {
        global $conn;

        $produtos = [];

        // 1. Buscar categorias das compras anteriores
        $sqlCategorias = "SELECT DISTINCT p.tipo_produto_id
                          FROM produtos p
                          INNER JOIN vendas v ON p.Produto_id = v.produto_id
                          INNER JOIN encomendas e ON v.encomenda_id = e.id
                          WHERE e.cliente_id = ?
                          LIMIT 3";

        $stmtCat = $conn->prepare($sqlCategorias);
        $stmtCat->bind_param("i", $cliente_id);
        $stmtCat->execute();
        $resultCat = $stmtCat->get_result();

        $categorias = [];
        while($row = $resultCat->fetch_assoc()) {
            $categorias[] = $row['tipo_produto_id'];
        }

        // 2. Se tem categorias de compras, buscar produtos dessas categorias
        if(count($categorias) > 0) {
            $placeholders = implode(',', array_fill(0, count($categorias), '?'));
            $sql = "SELECT DISTINCT p.Produto_id, p.nome, p.preco, p.foto,
                           tp.descricao as categoria
                    FROM produtos p
                    LEFT JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id
                    WHERE p.ativo = 1
                    AND p.tipo_produto_id IN ($placeholders)
                    AND p.Produto_id NOT IN (
                        SELECT v.produto_id
                        FROM vendas v
                        INNER JOIN encomendas e ON v.encomenda_id = e.id
                        WHERE e.cliente_id = ?
                    )
                    ORDER BY p.Produto_id DESC
                    LIMIT ?";

            $types = str_repeat('i', count($categorias)) . 'ii';
            $params = array_merge($categorias, [$cliente_id, $limit]);

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            while($row = $result->fetch_assoc()) {
                $produtos[] = $row;
            }
        }

        // 3. Se não encontrou suficientes, preencher com novidades
        if(count($produtos) < $limit) {
            $remaining = $limit - count($produtos);
            $excludeIds = array_column($produtos, 'Produto_id');

            if(count($excludeIds) > 0) {
                $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
                $sql2 = "SELECT p.Produto_id, p.nome, p.preco, p.foto,
                                tp.descricao as categoria
                         FROM produtos p
                         LEFT JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id
                         WHERE p.ativo = 1 AND p.Produto_id NOT IN ($placeholders)
                         ORDER BY p.Produto_id DESC
                         LIMIT ?";

                $types = str_repeat('i', count($excludeIds)) . 'i';
                $params = array_merge($excludeIds, [$remaining]);

                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param($types, ...$params);
            } else {
                $sql2 = "SELECT p.Produto_id, p.nome, p.preco, p.foto,
                                tp.descricao as categoria
                         FROM produtos p
                         LEFT JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id
                         WHERE p.ativo = 1
                         ORDER BY p.Produto_id DESC
                         LIMIT ?";

                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("i", $remaining);
            }

            $stmt2->execute();
            $result2 = $stmt2->get_result();

            while($row = $result2->fetch_assoc()) {
                $produtos[] = $row;
            }
        }

        return json_encode(['success' => true, 'data' => $produtos]);
    }
}
?>
