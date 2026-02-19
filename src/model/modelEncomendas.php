<?php
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../services/RankingService.php';

class Encomendas {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function listarPorCliente($cliente_id) {
        try {

        $encomendas = [];

        $sql = "SELECT
                    e.id,
                    e.codigo_encomenda,
                    e.data_envio,
                    e.estado,
                    e.morada,
                    e.plano_rastreio,
                    e.codigo_confirmacao_recepcao,
                    e.data_confirmacao_recepcao,
                    e.prazo_estimado_entrega,
                    (SELECT GROUP_CONCAT(DISTINCT p.nome SEPARATOR ', ')
                     FROM vendas v
                     INNER JOIN produtos p ON v.produto_id = p.Produto_id
                     WHERE v.encomenda_id = e.id) as produtos,
                    (SELECT p.foto
                     FROM vendas v
                     INNER JOIN produtos p ON v.produto_id = p.Produto_id
                     WHERE v.encomenda_id = e.id
                     LIMIT 1) as foto_produto,
                    (SELECT SUM(v.valor)
                     FROM vendas v
                     WHERE v.encomenda_id = e.id) as total,
                                        -- Informações agregadas de devolução ativa por encomenda
                                        (SELECT d1.id
                                         FROM devolucoes d1
                                         WHERE d1.encomenda_id = e.id
                                             AND d1.estado NOT IN ('rejeitada', 'cancelada', 'reembolsada')
                                         ORDER BY
                                             FIELD(d1.estado, 'solicitada', 'aprovada', 'produto_enviado', 'produto_recebido'),
                                             d1.data_solicitacao DESC,
                                             d1.id DESC
                                         LIMIT 1) as devolucao_id,
                                        (SELECT d2.codigo_devolucao
                                         FROM devolucoes d2
                                         WHERE d2.encomenda_id = e.id
                                             AND d2.estado NOT IN ('rejeitada', 'cancelada', 'reembolsada')
                                         ORDER BY
                                             FIELD(d2.estado, 'solicitada', 'aprovada', 'produto_enviado', 'produto_recebido'),
                                             d2.data_solicitacao DESC,
                                             d2.id DESC
                                         LIMIT 1) as devolucao_codigo,
                                        (SELECT
                                                CASE
                                                        WHEN SUM(CASE WHEN d3.estado = 'solicitada' THEN 1 ELSE 0 END) > 0 THEN 'solicitada'
                                                        WHEN SUM(CASE WHEN d3.estado = 'aprovada' THEN 1 ELSE 0 END) > 0 THEN 'aprovada'
                                                        WHEN SUM(CASE WHEN d3.estado = 'produto_enviado' THEN 1 ELSE 0 END) > 0 THEN 'produto_enviado'
                                                        WHEN SUM(CASE WHEN d3.estado = 'produto_recebido' THEN 1 ELSE 0 END) > 0 THEN 'produto_recebido'
                                                        ELSE NULL
                                                END
                                         FROM devolucoes d3
                                         WHERE d3.encomenda_id = e.id
                                             AND d3.estado NOT IN ('rejeitada', 'cancelada', 'reembolsada')) as devolucao_estado,
                                        (SELECT COUNT(*)
                                         FROM devolucoes d4
                                         WHERE d4.encomenda_id = e.id
                                             AND d4.estado NOT IN ('rejeitada', 'cancelada', 'reembolsada')) as devolucao_ativa,
                                        (SELECT COUNT(DISTINCT d5.produto_id)
                                         FROM devolucoes d5
                                         WHERE d5.encomenda_id = e.id
                                             AND d5.estado NOT IN ('rejeitada', 'cancelada', 'reembolsada')) as devolucao_num_produtos,
                                        (SELECT COUNT(*)
                                         FROM devolucoes d5a
                                         WHERE d5a.encomenda_id = e.id
                                             AND d5a.estado = 'aprovada') as devolucao_aprovada_qtd,
                                        (SELECT COUNT(*)
                                         FROM devolucoes d5b
                                         WHERE d5b.encomenda_id = e.id
                                             AND d5b.estado = 'solicitada') as devolucao_solicitada_qtd,
                                        (SELECT d5c.id
                                         FROM devolucoes d5c
                                         WHERE d5c.encomenda_id = e.id
                                             AND d5c.estado = 'aprovada'
                                         ORDER BY d5c.data_aprovacao DESC, d5c.data_solicitacao DESC, d5c.id DESC
                                         LIMIT 1) as devolucao_aprovada_id,
                                        (SELECT d5d.codigo_devolucao
                                         FROM devolucoes d5d
                                         WHERE d5d.encomenda_id = e.id
                                             AND d5d.estado = 'aprovada'
                                         ORDER BY d5d.data_aprovacao DESC, d5d.data_solicitacao DESC, d5d.id DESC
                                         LIMIT 1) as devolucao_aprovada_codigo,
                                        (SELECT GROUP_CONCAT(DISTINCT p2.nome ORDER BY p2.nome SEPARATOR ', ')
                                         FROM devolucoes d6
                                         INNER JOIN produtos p2 ON d6.produto_id = p2.Produto_id
                                         WHERE d6.encomenda_id = e.id
                                             AND d6.estado NOT IN ('rejeitada', 'cancelada', 'reembolsada')) as devolucao_produtos_nomes,
                                        (SELECT d7.estado
                                         FROM devolucoes d7
                                         WHERE d7.encomenda_id = e.id
                                         ORDER BY d7.data_solicitacao DESC, d7.id DESC
                                         LIMIT 1) as devolucao_ultima_estado,
                                        (SELECT d8.codigo_devolucao
                                         FROM devolucoes d8
                                         WHERE d8.encomenda_id = e.id
                                         ORDER BY d8.data_solicitacao DESC, d8.id DESC
                                         LIMIT 1) as devolucao_ultima_codigo,
                                        (SELECT d9.notas_anunciante
                                         FROM devolucoes d9
                                         WHERE d9.encomenda_id = e.id
                                         ORDER BY d9.data_solicitacao DESC, d9.id DESC
                                         LIMIT 1) as devolucao_ultima_notas_anunciante,
                                        (SELECT COUNT(*)
                                         FROM devolucoes d10
                                         WHERE d10.encomenda_id = e.id) as devolucao_existe
                FROM encomendas e
                WHERE e.cliente_id = ?
                ORDER BY e.data_envio DESC, e.id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $transportadora = $this->obterTransportadora($row['id']);
                $row['transportadora'] = $transportadora;

                $resumoAvaliacoes = $this->obterResumoAvaliacoesEncomenda($row['codigo_encomenda'], $cliente_id);
                $totalProdutosAvaliaveis = (int)($resumoAvaliacoes['total_produtos_avaliaveis'] ?? 0);
                $totalProdutosAvaliados = (int)($resumoAvaliacoes['total_produtos_avaliados'] ?? 0);

                $row['total_produtos_avaliaveis'] = $totalProdutosAvaliaveis;
                $row['total_produtos_avaliados'] = $totalProdutosAvaliados;
                $row['encomenda_totalmente_avaliada'] = ($totalProdutosAvaliaveis > 0 && $totalProdutosAvaliados >= $totalProdutosAvaliaveis) ? 1 : 0;


                $row['produtos_lista'] = $this->obterListaProdutos($row['id']);
                $row['num_produtos'] = count($row['produtos_lista']);

                $encomendas[] = $row;
            }
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return $encomendas;
        } catch (Exception $e) {
            return [];
        }
    }

    private function obterListaProdutos($encomenda_id) {
        try {

        $sql = "SELECT
                    p.Produto_id,
                    p.nome,
                    p.foto,
                    v.quantidade,
                    v.valor
                FROM vendas v
                INNER JOIN produtos p ON v.produto_id = p.Produto_id
            WHERE v.encomenda_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $encomenda_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produtos = [];

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $produtos[] = $row;
            }
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return $produtos;
        } catch (Exception $e) {
            return [];
        }
    }

    private function obterResumoAvaliacoesEncomenda($codigo_encomenda, $cliente_id) {
        try {
            $sql = "SELECT
                        COUNT(DISTINCT v.produto_id) as total_produtos_avaliaveis,
                        COUNT(DISTINCT a.produto_id) as total_produtos_avaliados
                    FROM encomendas e
                    INNER JOIN vendas v ON e.id = v.encomenda_id
                    LEFT JOIN avaliacoes_produtos a ON a.produto_id = v.produto_id
                        AND a.encomenda_codigo = e.codigo_encomenda
                        AND a.utilizador_id = ?
                    WHERE e.codigo_encomenda = ?
                    AND e.cliente_id = ?";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return [
                    'total_produtos_avaliaveis' => 0,
                    'total_produtos_avaliados' => 0
                ];
            }

            $stmt->bind_param("isi", $cliente_id, $codigo_encomenda, $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $stmt->close();
                return [
                    'total_produtos_avaliaveis' => (int)($row['total_produtos_avaliaveis'] ?? 0),
                    'total_produtos_avaliados' => (int)($row['total_produtos_avaliados'] ?? 0)
                ];
            }

            if (isset($stmt) && $stmt) {
                $stmt->close();
            }

            return [
                'total_produtos_avaliaveis' => 0,
                'total_produtos_avaliados' => 0
            ];
        } catch (Exception $e) {
            return [
                'total_produtos_avaliaveis' => 0,
                'total_produtos_avaliados' => 0
            ];
        }
    }

    function obterDetalhes($codigo_encomenda, $cliente_id) {
        try {

        $sql = "SELECT
                    e.*,
                    u.nome as cliente_nome,
                    u.email as cliente_email
                FROM encomendas e
                LEFT JOIN Utilizadores u ON e.cliente_id = u.id
            WHERE e.codigo_encomenda = ?
            AND e.cliente_id = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $codigo_encomenda, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $encomenda = $result->fetch_assoc();

            $encomenda['transportadora'] = $this->obterTransportadora($encomenda['id']);


            if (!isset($encomenda['tipo_entrega'])) {
                $encomenda['tipo_entrega'] = 'domicilio';
            }
            if (!isset($encomenda['morada_completa'])) {
                $encomenda['morada_completa'] = $encomenda['morada'];
            }


            $encomenda['produtos_detalhes'] = $this->obterProdutosEncomenda($codigo_encomenda);


            $encomenda['produtos_lista'] = $this->obterListaProdutos($encomenda['id']);

            $encomenda['total'] = $this->calcularTotal($codigo_encomenda);

            return $encomenda;
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return false;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function obterTransportadora($encomenda_id) {
        try {


        $sql = "SELECT transportadora_id FROM encomendas WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $encomenda_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $transportadora_id = $row['transportadora_id'];


            $transportadoras = [
                1 => 'CTT - Correios de Portugal',
                2 => 'CTT - Ponto de Recolha',
                3 => 'DPD - Entrega Rápida',
                4 => 'DPD - Ponto de Recolha',
                5 => 'Entrega em Casa'
            ];

            return $transportadoras[$transportadora_id] ?? 'Transportadora Desconhecida';
        }

        return 'N/A';
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function obterProdutosEncomenda($codigo_encomenda) {
        try {

        $sql = "SELECT
                    p.nome,
                    v.quantidade,
                    v.valor,
                    p.descricao,
                    p.foto
                FROM encomendas e
                INNER JOIN vendas v ON e.id = v.encomenda_id
                INNER JOIN produtos p ON v.produto_id = p.Produto_id
            WHERE e.codigo_encomenda = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $codigo_encomenda);
        $stmt->execute();
        $result = $stmt->get_result();

        $html = '<table style="width: 100%; border-collapse: collapse;">';
        $html .= '<tr style="background: #f5f5f5; font-weight: bold;">';
        $html .= '<th style="padding: 10px; text-align: left;">Produto</th>';
        $html .= '<th style="padding: 10px; text-align: center;">Qtd</th>';
        $html .= '<th style="padding: 10px; text-align: right;">Preço</th>';
        $html .= '</tr>';

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $html .= '<tr style="border-bottom: 1px solid #eee;">';
                $html .= '<td style="padding: 10px;">' . htmlspecialchars($row['nome']) . '</td>';
                $html .= '<td style="padding: 10px; text-align: center;">' . $row['quantidade'] . '</td>';
                $html .= '<td style="padding: 10px; text-align: right;">€' . number_format($row['valor'], 2) . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</table>';

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return $html;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function calcularTotal($codigo_encomenda) {
        try {

        $sql = "SELECT SUM(v.valor) as total
                FROM encomendas e
                INNER JOIN vendas v ON e.id = v.encomenda_id
            WHERE e.codigo_encomenda = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $codigo_encomenda);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $total = $row['total'] ?? 0;
            $stmt->close();
            return $total;
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return 0;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function cancelar($codigo_encomenda, $cliente_id) {
        try {

        $stmt = $this->conn->prepare("UPDATE encomendas
                SET estado = 'Cancelada'
                WHERE codigo_encomenda = ?
                AND cliente_id = ?
                AND estado IN ('Pendente', 'Processando', 'Em Processamento')");
        $stmt->bind_param("si", $codigo_encomenda, $cliente_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {

            try {
                $rankingService = new RankingService($this->conn);
                $stmtAnunc = $this->conn->prepare("SELECT DISTINCT v.anunciante_id FROM Vendas v INNER JOIN Encomendas e ON v.encomenda_id = e.id WHERE e.codigo_encomenda = ?");
                $stmtAnunc->bind_param("s", $codigo_encomenda);
                $stmtAnunc->execute();
                $resultAnunc = $stmtAnunc->get_result();
                while ($rowAnunc = $resultAnunc->fetch_assoc()) {
                    $rankingService->removerPontosCancelamento((int)$rowAnunc['anunciante_id']);
                }
                $stmtAnunc->close();
            } catch (Exception $rankEx) {
            }

            return [
                'success' => true,
                'message' => 'Encomenda cancelada com sucesso'
            ];
        }

        return [
            'success' => false,
            'message' => 'Esta encomenda não pode ser cancelada'
        ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro interno do servidor'];
        }
    }

    function confirmarRececao($codigo_confirmacao, $cliente_id, $ip = null) {
        try {
            $codigo = strtoupper(trim((string)$codigo_confirmacao));

            if ($codigo === '') {
                return [
                    'success' => false,
                    'message' => 'Código de confirmação inválido'
                ];
            }

            $sql = "SELECT id, estado
                    FROM encomendas
                    WHERE codigo_confirmacao_recepcao = ?
                    AND cliente_id = ?
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $codigo, $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result || $result->num_rows === 0) {
                if (isset($stmt) && $stmt) {
                    $stmt->close();
                }

                return [
                    'success' => false,
                    'message' => 'Código inválido ou não pertence à sua conta'
                ];
            }

            $encomenda = $result->fetch_assoc();
            if (isset($stmt) && $stmt) {
                $stmt->close();
            }

            $estado = strtolower(trim((string)($encomenda['estado'] ?? '')));
            if ($estado === 'entregue') {
                return [
                    'success' => false,
                    'message' => 'Esta encomenda já foi confirmada anteriormente'
                ];
            }

            $sqlUpdate = "UPDATE encomendas
                          SET estado = 'Entregue',
                              data_confirmacao_recepcao = NOW(),
                              ip_confirmacao = ?
                          WHERE id = ?
                          AND cliente_id = ?";

            $ipConfirmacao = $ip ? (string)$ip : '';
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sii", $ipConfirmacao, $encomenda['id'], $cliente_id);
            $stmtUpdate->execute();

            if ($stmtUpdate->affected_rows <= 0) {
                if (isset($stmtUpdate) && $stmtUpdate) {
                    $stmtUpdate->close();
                }

                return [
                    'success' => false,
                    'message' => 'Não foi possível confirmar a receção'
                ];
            }

            if (isset($stmtUpdate) && $stmtUpdate) {
                $stmtUpdate->close();
            }

            $sqlHist = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao, data_atualizacao)
                        VALUES (?, 'Entregue', 'Entrega confirmada pelo cliente', NOW())";
            $stmtHist = $this->conn->prepare($sqlHist);
            if ($stmtHist) {
                $stmtHist->bind_param("i", $encomenda['id']);
                $stmtHist->execute();
                $stmtHist->close();
            }

            return [
                'success' => true,
                'message' => 'Receção confirmada com sucesso'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro interno do servidor'
            ];
        }
    }

    function obterEstatisticas($cliente_id) {
        try {

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
            WHERE cliente_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stats = array_merge($stats, $row);
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        $sql2 = "SELECT SUM(p.preco) as valor_total
                FROM encomendas e
                INNER JOIN Produtos p ON e.produto_id = p.Produto_id
            WHERE e.cliente_id = ?";

        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("i", $cliente_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2 && $result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            $stats['valor_total'] = $row2['valor_total'] ?? 0;
        }

        if (isset($stmt2) && $stmt2) {
            $stmt2->close();
        }

        return $stats;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function gerarFaturaPDF($codigo_encomenda, $cliente_id) {
        try {
            $stmt = $this->conn->prepare("SELECT e.*, u.nome AS cliente_nome, u.email, u.morada AS cliente_morada, u.nif
                FROM Encomendas e
                INNER JOIN utilizadores u ON e.cliente_id = u.id
                WHERE e.codigo_encomenda = ? AND e.cliente_id = ?");
            $stmt->bind_param("si", $codigo_encomenda, $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return ['success' => false, 'message' => 'Encomenda não encontrada'];
            }

            $encomenda = $result->fetch_assoc();

            $stmt_prod = $this->conn->prepare("SELECT v.quantidade, v.preco_unitario, p.nome AS produto_nome
                FROM Vendas v
                INNER JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.codigo_encomenda = ?");
            $stmt_prod->bind_param("s", $codigo_encomenda);
            $stmt_prod->execute();
            $result_prod = $stmt_prod->get_result();

            $produtos = [];
            $total = 0;
            while ($row = $result_prod->fetch_assoc()) {
                $subtotal = $row['preco_unitario'] * $row['quantidade'];
                $row['subtotal'] = $subtotal;
                $produtos[] = $row;
                $total += $subtotal;
            }

            $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Fatura ' . htmlspecialchars($codigo_encomenda) . '</title>';
            $html .= '<style>body{font-family:Arial,sans-serif;margin:40px;color:#333}';
            $html .= '.header{text-align:center;border-bottom:2px solid #3cb371;padding-bottom:20px;margin-bottom:30px}';
            $html .= '.header h1{color:#3cb371;margin:0}table{width:100%;border-collapse:collapse;margin:20px 0}';
            $html .= 'th,td{padding:10px;text-align:left;border-bottom:1px solid #ddd}th{background:#f5f5f5}';
            $html .= '.total{text-align:right;font-size:18px;font-weight:bold;margin-top:20px}</style></head><body>';
            $html .= '<div class="header"><h1>WeGreen</h1><p>Fatura</p></div>';
            $html .= '<p><strong>Código:</strong> ' . htmlspecialchars($codigo_encomenda) . '</p>';
            $html .= '<p><strong>Cliente:</strong> ' . htmlspecialchars($encomenda['cliente_nome']) . '</p>';
            $html .= '<p><strong>Email:</strong> ' . htmlspecialchars($encomenda['email']) . '</p>';
            if (!empty($encomenda['nif'])) {
                $html .= '<p><strong>NIF:</strong> ' . htmlspecialchars($encomenda['nif']) . '</p>';
            }
            $html .= '<p><strong>Data:</strong> ' . htmlspecialchars($encomenda['data_encomenda'] ?? date('Y-m-d')) . '</p>';
            $html .= '<table><thead><tr><th>Produto</th><th>Qtd</th><th>Preço Unit.</th><th>Subtotal</th></tr></thead><tbody>';
            foreach ($produtos as $p) {
                $html .= '<tr><td>' . htmlspecialchars($p['produto_nome']) . '</td>';
                $html .= '<td>' . $p['quantidade'] . '</td>';
                $html .= '<td>' . number_format($p['preco_unitario'], 2, ',', '.') . '€</td>';
                $html .= '<td>' . number_format($p['subtotal'], 2, ',', '.') . '€</td></tr>';
            }
            $html .= '</tbody></table>';
            $html .= '<div class="total">Total: ' . number_format($total, 2, ',', '.') . '€</div>';
            $html .= '</body></html>';

            return ['success' => true, 'content' => $html];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erro ao gerar fatura'];
        }
    }
}
?>
