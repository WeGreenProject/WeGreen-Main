<?php
require_once 'connection.php';

class Encomendas {

    function listarPorCliente($cliente_id) {
        global $conn;
        $encomendas = [];

        $sql = "SELECT
                    e.id,
                    e.codigo_encomenda,
                    e.data_envio,
                    e.estado,
                    e.morada,
                    e.plano_rastreio,
                    GROUP_CONCAT(DISTINCT p.nome SEPARATOR ', ') as produtos,
                    MIN(p.foto) as foto_produto,
                    SUM(p.preco) as total
                FROM encomendas e
                LEFT JOIN Produtos p ON e.produto_id = p.Produto_id
                WHERE e.cliente_id = $cliente_id
                GROUP BY e.codigo_encomenda
                ORDER BY e.data_envio DESC";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Obter nome da transportadora
                $transportadora = $this->obterTransportadora($row['id']);
                $row['transportadora'] = $transportadora;
                $encomendas[] = $row;
            }
        }

        return $encomendas;
    }

    function obterDetalhes($codigo_encomenda, $cliente_id) {
        global $conn;

        $sql = "SELECT
                    e.*,
                    u.nome as cliente_nome,
                    u.email as cliente_email
                FROM encomendas e
                LEFT JOIN Utilizadores u ON e.cliente_id = u.id
                WHERE e.codigo_encomenda = '$codigo_encomenda'
                AND e.cliente_id = $cliente_id
                LIMIT 1";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $encomenda = $result->fetch_assoc();

            // Buscar transportadora
            $encomenda['transportadora'] = $this->obterTransportadora($encomenda['id']);

            // Buscar produtos da encomenda
            $encomenda['produtos_detalhes'] = $this->obterProdutosEncomenda($codigo_encomenda);

            // Calcular total
            $encomenda['total'] = $this->calcularTotal($codigo_encomenda);

            return $encomenda;
        }

        return false;
    }

    private function obterTransportadora($encomenda_id) {
        global $conn;

        // Assumindo que existe relação com transportadora_id
        $sql = "SELECT transportadora_id FROM encomendas WHERE id = $encomenda_id LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $transportadora_id = $row['transportadora_id'];

            // Mapear IDs para nomes (baseado nos dados de exemplo)
            $transportadoras = [
                1 => 'CTT',
                2 => 'DPD',
                3 => 'UPS',
                4 => 'Chronopost',
                5 => 'Entrega WeGreen'
            ];

            return $transportadoras[$transportadora_id] ?? 'Transportadora Desconhecida';
        }

        return 'N/A';
    }

    private function obterProdutosEncomenda($codigo_encomenda) {
        global $conn;

        $sql = "SELECT
                    p.nome,
                    p.preco,
                    p.descricao,
                    p.foto
                FROM encomendas e
                INNER JOIN Produtos p ON e.produto_id = p.Produto_id
                WHERE e.codigo_encomenda = '$codigo_encomenda'";

        $result = $conn->query($sql);

        $html = '<table style="width: 100%; border-collapse: collapse;">';
        $html .= '<tr style="background: #f5f5f5; font-weight: bold;">';
        $html .= '<th style="padding: 10px; text-align: left;">Produto</th>';
        $html .= '<th style="padding: 10px; text-align: right;">Preço</th>';
        $html .= '</tr>';

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $html .= '<tr style="border-bottom: 1px solid #eee;">';
                $html .= '<td style="padding: 10px;">' . htmlspecialchars($row['nome']) . '</td>';
                $html .= '<td style="padding: 10px; text-align: right;">€' . number_format($row['preco'], 2) . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</table>';

        return $html;
    }

    private function calcularTotal($codigo_encomenda) {
        global $conn;

        $sql = "SELECT SUM(p.preco) as total
                FROM encomendas e
                INNER JOIN Produtos p ON e.produto_id = p.Produto_id
                WHERE e.codigo_encomenda = '$codigo_encomenda'";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }

        return 0;
    }

    function cancelar($codigo_encomenda, $cliente_id) {
        global $conn;

        $sql = "UPDATE encomendas
                SET estado = 'cancelado'
                WHERE codigo_encomenda = '$codigo_encomenda'
                AND cliente_id = $cliente_id
                AND estado IN ('pendente', 'processando')";

        $result = $conn->query($sql);

        return $result;
    }

    function obterEstatisticas($cliente_id) {
        global $conn;

        $stats = [
            'total' => 0,
            'pendentes' => 0,
            'processando' => 0,
            'enviadas' => 0,
            'entregues' => 0,
            'valor_total' => 0
        ];

        $sql = "SELECT
                    COUNT(DISTINCT codigo_encomenda) as total,
                    SUM(CASE WHEN estado = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                    SUM(CASE WHEN estado = 'processando' THEN 1 ELSE 0 END) as processando,
                    SUM(CASE WHEN estado = 'enviado' THEN 1 ELSE 0 END) as enviadas,
                    SUM(CASE WHEN estado = 'entregue' THEN 1 ELSE 0 END) as entregues
                FROM encomendas
                WHERE cliente_id = $cliente_id";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stats = array_merge($stats, $row);
        }

        // Calcular valor total gasto
        $sql2 = "SELECT SUM(p.preco) as valor_total
                FROM encomendas e
                INNER JOIN Produtos p ON e.produto_id = p.Produto_id
                WHERE e.cliente_id = $cliente_id";

        $result2 = $conn->query($sql2);

        if ($result2 && $result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            $stats['valor_total'] = $row2['valor_total'] ?? 0;
        }

        return $stats;
    }
}
?>
