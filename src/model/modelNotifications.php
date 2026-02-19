<?php
require_once __DIR__ . '/connection.php';

class Notifications {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function normalizarTipoNotificacao($tipo_notificacao) {
        $tipo = (string)$tipo_notificacao;

        if ($tipo === 'stock_baixo' || $tipo === 'stock_esgotado' || $tipo === 'produto_rejeitado') {
            return 'produto';
        }

        if (in_array($tipo, ['encomenda', 'devolucao', 'utilizador', 'produto'], true)) {
            return $tipo;
        }

        return 'produto';
    }

    function contarNotificacoesPorTipoJson($utilizador_id, $tipo_utilizador) {
        try {
        $count = $this->contarNotificacoesPorTipo($utilizador_id, $tipo_utilizador);
        return json_encode(['flag' => true, 'msg' => 'OK', 'count' => $count], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarNotificacoesPorTipoJson($utilizador_id, $tipo_utilizador) {
        try {
        $notificacoes = $this->listarNotificacoesPorTipo($utilizador_id, $tipo_utilizador);
        return json_encode(['flag' => true, 'msg' => 'OK', 'count' => count($notificacoes), 'notificacoes' => $notificacoes], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarTodasNotificacoesPorTipoJson($utilizador_id, $tipo_utilizador) {
        try {
        $notificacoes = $this->listarTodasNotificacoesPorTipo($utilizador_id, $tipo_utilizador);
        return json_encode(['flag' => true, 'msg' => 'OK', 'count' => count($notificacoes), 'notificacoes' => $notificacoes], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function marcarComoLidaJson($utilizador_id, $tipo_notificacao, $referencia_id) {
        try {
        $resultado = $this->marcarComoLida($utilizador_id, $tipo_notificacao, $referencia_id);
        return json_encode(['flag' => $resultado, 'msg' => $resultado ? 'Marcada com sucesso' : 'Erro ao marcar'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function marcarTodasComoLidasJson($utilizador_id, $tipo_utilizador) {
        try {
        $resultado = $this->marcarTodasComoLidas($utilizador_id, $tipo_utilizador);
        return json_encode(['flag' => $resultado, 'msg' => $resultado ? 'Marcadas com sucesso' : 'Erro ao marcar'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function contarNotificacoesAnunciante($anunciante_id) {
        try {

        $count = 0;


        $sql_encomendas = "SELECT COUNT(*) as total
                          FROM Encomendas e
                          INNER JOIN produtos p ON e.produto_id = p.Produto_id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                          WHERE p.anunciante_id = ?
                          AND e.estado IN ('Pendente', 'Processando')
                          AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_encomendas);
        if (!$stmt) {
            return 0;
        }
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $encomendas_count = (int)$row['total'];
        $count += $encomendas_count;


        $sql_devolucoes = "SELECT COUNT(*) as total
                          FROM devolucoes d
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                          WHERE d.anunciante_id = ?
                          AND d.estado IN ('solicitada', 'produto_enviado')
                          AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_devolucoes);
        if (!$stmt) {
            return $count;
        }
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $devolucoes_count = (int)$row['total'];
        $count += $devolucoes_count;


        $sql_stock = "SELECT COUNT(*) as total
                      FROM Produtos p
                      INNER JOIN Utilizadores u ON p.anunciante_id = u.id
                      LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'stock_baixo') AND nl.referencia_id = p.Produto_id)
                      WHERE p.anunciante_id = ?
                      AND p.stock > 0 AND p.stock <= 5
                      AND u.plano_id >= 2
                      AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_stock);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count += (int)$row['total'];
        }


        $sql_esgotados = "SELECT COUNT(*) as total
                          FROM Produtos p
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'stock_esgotado') AND nl.referencia_id = p.Produto_id)
                          WHERE p.anunciante_id = ?
                          AND p.stock = 0
                          AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_esgotados);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count += (int)$row['total'];
        }


        $sql_rejeitados = "SELECT COUNT(*) as total
                          FROM Produtos p
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'produto_rejeitado') AND nl.referencia_id = p.Produto_id)
                          WHERE p.anunciante_id = ?
                          AND p.ativo = 2
                          AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_rejeitados);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count += (int)$row['total'];
        }

        return $count;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarNotificacoesAnunciante($anunciante_id) {
        try {

        $notificacoes = [];


        $sql_encomendas = "SELECT
                            e.id,
                            e.codigo_encomenda,
                            e.estado,
                            e.data_envio,
                            COALESCE(p.nome, 'Produto não encontrado') as produto_nome,
                            COALESCE(u.nome, 'Cliente não encontrado') as cliente_nome
                          FROM Encomendas e
                          LEFT JOIN produtos p ON e.produto_id = p.Produto_id
                          LEFT JOIN Utilizadores u ON e.cliente_id = u.id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                          WHERE p.anunciante_id = ?
                          AND e.estado IN ('Pendente', 'Processando')
                          AND nl.id IS NULL
                          ORDER BY e.data_envio DESC
                          LIMIT 5";

        $stmt = $this->conn->prepare($sql_encomendas);
        if (!$stmt) {
            return $notificacoes;
        }
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $encomendas_encontradas = 0;
        while ($row = $result->fetch_assoc()) {
            $encomendas_encontradas++;
            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'titulo' => 'Encomenda ' . $row['estado'],
                'mensagem' => 'Encomenda #' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'gestaoEncomendasAnunciante.php',
                'lida' => false
            ];
        }


        $sql_devolucoes = "SELECT
                            d.id,
                            d.codigo_devolucao,
                            d.estado,
                            d.data_solicitacao,
                            d.codigo_rastreio,
                            COALESCE(p.nome, 'Produto não encontrado') as produto_nome,
                            COALESCE(u.nome, 'Cliente não encontrado') as cliente_nome
                          FROM devolucoes d
                          LEFT JOIN produtos p ON d.produto_id = p.Produto_id
                          LEFT JOIN Utilizadores u ON d.cliente_id = u.id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                          WHERE d.anunciante_id = ?
                          AND d.estado IN ('solicitada', 'produto_enviado')
                          AND nl.id IS NULL
                          ORDER BY d.data_solicitacao DESC
                          LIMIT 10";

        $stmt = $this->conn->prepare($sql_devolucoes);
        if (!$stmt) {
            return $notificacoes;
        }
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $devolucoes_encontradas = 0;
        while ($row = $result->fetch_assoc()) {
            $devolucoes_encontradas++;
            $icone = 'fa-undo';
            $titulo = 'Devolução Solicitada';
            $mensagem = 'Devolução #' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'];


            if ($row['estado'] === 'produto_enviado') {
                $icone = 'fa-shipping-fast';
                $titulo = 'Produto Enviado';
                $mensagem .= ' - Cliente enviou o produto.';
                if (!empty($row['codigo_rastreio'])) {
                    $mensagem .= ' Rastreio: ' . $row['codigo_rastreio'];
                }
                $mensagem .= ' Confirme o recebimento!';
            }

            $notificacoes[] = [
                'tipo' => 'devolucao',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data' => $row['data_solicitacao'],
                'link' => 'gestaoDevolucoesAnunciante.php',
                'lida' => false
            ];
        }


        $sql_stock = "SELECT
                        p.Produto_id as id,
                        p.nome as produto_nome,
                        p.stock,
                        p.data_criacao
                      FROM Produtos p
                      INNER JOIN Utilizadores u ON p.anunciante_id = u.id
                      LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'stock_baixo') AND nl.referencia_id = p.Produto_id)
                      WHERE p.anunciante_id = ?
                      AND p.stock > 0 AND p.stock <= 5
                      AND u.plano_id >= 2
                      AND nl.id IS NULL
                      ORDER BY p.stock ASC
                      LIMIT 5";

        $stmt = $this->conn->prepare($sql_stock);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $notificacoes[] = [
                    'tipo' => 'stock_baixo',
                    'id' => $row['id'],
                    'icone' => 'fa-exclamation-triangle',
                    'titulo' => 'Stock Baixo',
                    'mensagem' => $row['produto_nome'] . ' — apenas ' . $row['stock'] . ' unidade(s) em stock.',
                    'data' => $row['data_criacao'],
                    'link' => 'gestaoProdutosAnunciante.php',
                    'lida' => false
                ];
            }
        }


        $sql_esgotados = "SELECT
                            p.Produto_id as id,
                            p.nome as produto_nome,
                            p.data_criacao
                          FROM Produtos p
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'stock_esgotado') AND nl.referencia_id = p.Produto_id)
                          WHERE p.anunciante_id = ?
                          AND p.stock = 0
                          AND nl.id IS NULL
                          ORDER BY p.data_criacao DESC
                          LIMIT 5";

        $stmt = $this->conn->prepare($sql_esgotados);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $notificacoes[] = [
                    'tipo' => 'stock_esgotado',
                    'id' => $row['id'],
                    'icone' => 'fa-times-circle',
                    'titulo' => 'Produto Esgotado',
                    'mensagem' => $row['produto_nome'] . ' — sem stock. Produto removido do marketplace.',
                    'data' => $row['data_criacao'],
                    'link' => 'gestaoProdutosAnunciante.php',
                    'lida' => false
                ];
            }
        }


        $sql_rejeitados = "SELECT
                            p.Produto_id as id,
                            p.nome as produto_nome,
                            p.motivo_rejeicao,
                            p.data_criacao
                          FROM Produtos p
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'produto_rejeitado') AND nl.referencia_id = p.Produto_id)
                          WHERE p.anunciante_id = ?
                          AND p.ativo = 2
                          AND nl.id IS NULL
                          ORDER BY p.data_criacao DESC
                          LIMIT 5";

        $stmt = $this->conn->prepare($sql_rejeitados);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $motivo = !empty($row['motivo_rejeicao']) ? ' Motivo: ' . $row['motivo_rejeicao'] : '';
                $notificacoes[] = [
                    'tipo' => 'produto_rejeitado',
                    'id' => $row['id'],
                    'icone' => 'fa-times-circle',
                    'titulo' => 'Produto Rejeitado',
                    'mensagem' => $row['produto_nome'] . ' foi rejeitado.' . $motivo,
                    'data' => $row['data_criacao'],
                    'link' => 'gestaoProdutosAnunciante.php',
                    'lida' => false
                ];
            }
        }


        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        $total_notificacoes = count($notificacoes);

        $final = array_slice($notificacoes, 0, 10);

        return $final;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function contarNotificacoesCliente($cliente_id) {
        try {

        $count = 0;


        $sql = "SELECT COUNT(*) as total
                FROM Encomendas e
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                WHERE e.cliente_id = ?
                AND e.estado IN ('Processando', 'Enviado', 'Entregue')
                AND DATE(e.data_envio) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];


        $sql = "SELECT COUNT(*) as total
                FROM devolucoes d
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'produto_enviado', 'produto_recebido', 'rejeitada', 'reembolsada')
                AND DATE(d.data_solicitacao) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];

        return $count;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarNotificacoesCliente($cliente_id) {
        try {

        $notificacoes = [];


        $sql = "SELECT
                    e.id,
                    e.codigo_encomenda,
                    e.estado,
                    e.data_envio,
                    e.codigo_rastreio,
                    p.nome as produto_nome
                FROM Encomendas e
                INNER JOIN produtos p ON e.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                WHERE e.cliente_id = ?
                AND DATE(e.data_envio) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND nl.id IS NULL
                ORDER BY e.data_envio DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $icone = 'fa-shopping-bag';
            $titulo = 'Encomenda ' . $row['estado'];

            if ($row['estado'] == 'Enviado') {
                $icone = 'fa-shipping-fast';
                $titulo = 'Encomenda Enviada';
            } elseif ($row['estado'] == 'Entregue') {
                $icone = 'fa-check-circle';
                $titulo = 'Encomenda Entregue';
            }

            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => '#' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'minhasEncomendas.php?encomenda=' . rawurlencode($row['codigo_encomenda']),
                'lida' => false
            ];
        }


        $sql = "SELECT
                    d.id,
                    d.codigo_devolucao,
                    d.estado,
                    d.data_solicitacao,
                    d.codigo_rastreio,
                    d.notas_anunciante,
                    d.notas_recebimento,
                    p.nome as produto_nome
                FROM devolucoes d
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'produto_enviado', 'produto_recebido', 'rejeitada', 'reembolsada')
                AND DATE(d.data_solicitacao) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                AND nl.id IS NULL
                ORDER BY d.data_solicitacao DESC
                LIMIT 5";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $icone = 'fa-undo';
            $titulo = 'Devolução ' . ucfirst($row['estado']);
            $mensagem = '#' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'];


            if ($row['estado'] === 'aprovada') {
                $icone = 'fa-check-circle';
                $titulo = 'Devolução Aprovada';
                $mensagem .= ' - Por favor, envie o produto e confirme no sistema.';
            } elseif ($row['estado'] === 'produto_enviado') {
                $icone = 'fa-shipping-fast';
                $titulo = 'Devolução Enviada';
                $mensagem .= ' - Aguardando confirmação do vendedor.';
            } elseif ($row['estado'] === 'produto_recebido') {
                $icone = 'fa-box-open';
                $titulo = 'Produto Recebido';
                $mensagem .= ' - Reembolso será processado em 5-10 dias úteis.';
            } elseif ($row['estado'] === 'rejeitada') {
                $icone = 'fa-times-circle';
                $titulo = 'Devolução Rejeitada';
                $mensagem .= !empty($row['notas_anunciante']) ? ' - ' . $row['notas_anunciante'] : '';
            } elseif ($row['estado'] === 'reembolsada') {
                $icone = 'fa-euro-sign';
                $titulo = 'Reembolso Processado';
                $mensagem .= ' - Reembolso concluído!';
            }

            $notificacoes[] = [
                'tipo' => 'devolucao',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data' => $row['data_solicitacao'],
                'link' => 'minhasEncomendas.php?tab=devolucoes',
                'lida' => false
            ];
        }


        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return array_slice($notificacoes, 0, 10);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function contarNotificacoesPorTipo($utilizador_id, $tipo_utilizador) {
        try {

        if ($tipo_utilizador == 1) {
            return $this->contarNotificacoesAdmin($utilizador_id);
        } elseif ($tipo_utilizador == 2) {
            return $this->contarNotificacoesCliente($utilizador_id);
        } elseif ($tipo_utilizador == 3) {
            return $this->contarNotificacoesAnunciante($utilizador_id);
        }
        return 0;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarNotificacoesPorTipo($utilizador_id, $tipo_utilizador) {
        try {

        if ($tipo_utilizador == 1) {
            return $this->listarNotificacoesAdmin($utilizador_id);
        } elseif ($tipo_utilizador == 2) {
            return $this->listarNotificacoesCliente($utilizador_id);
        } elseif ($tipo_utilizador == 3) {
            return $this->listarNotificacoesAnunciante($utilizador_id);
        }
        return [];
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function listarTodasNotificacoesPorTipo($utilizador_id, $tipo_utilizador) {
        try {

        if ($tipo_utilizador == 1) {
            return $this->listarTodasNotificacoesAdmin($utilizador_id);
        } elseif ($tipo_utilizador == 2) {
            return $this->listarTodasNotificacoesCliente($utilizador_id);
        } elseif ($tipo_utilizador == 3) {
            return $this->listarTodasNotificacoesAnunciante($utilizador_id);
        }
        return [];
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function contarNotificacoesAdmin($utilizador_id) {
        try {

        $count = 0;


        $sql = "SELECT COUNT(*) as total
                FROM Utilizadores u
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'utilizador' AND nl.referencia_id = u.id)
                WHERE u.email_verificado = 0
                AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];


        $sql = "SELECT COUNT(*) as total
                FROM produtos p
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'produto' AND nl.referencia_id = p.Produto_id)
                WHERE p.ativo = 0
            AND p.motivo_rejeicao = 'PENDENTE_REVISAO_ANUNCIANTE'
                AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];

        return $count;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarNotificacoesAdmin($utilizador_id) {
        try {

        $notificacoes = [];


        $sql = "SELECT
                    u.id,
                    u.nome,
                    u.email,
                    u.tipo_utilizador_id,
                    u.data_criacao
                FROM Utilizadores u
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'utilizador' AND nl.referencia_id = u.id)
                WHERE u.email_verificado = 0
                AND nl.id IS NULL
                ORDER BY u.data_criacao DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tipo_texto = $row['tipo_utilizador_id'] == 2 ? 'Cliente' : 'Anunciante';

            $notificacoes[] = [
                'tipo' => 'utilizador',
                'id' => $row['id'],
                'icone' => 'fa-user',
                'titulo' => 'Novo ' . $tipo_texto,
                'mensagem' => $row['nome'] . ' - ' . $row['email'],
                'data' => $row['data_criacao'],
                'link' => 'gestaoCliente.php',
                'lida' => false
            ];
        }


        $sql = "SELECT
                    p.Produto_id,
                    p.nome,
                    p.data_criacao,
                    u.nome as anunciante_nome
                FROM produtos p
                INNER JOIN Utilizadores u ON p.anunciante_id = u.id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'produto' AND nl.referencia_id = p.Produto_id)
                WHERE p.ativo = 0
            AND p.motivo_rejeicao = 'PENDENTE_REVISAO_ANUNCIANTE'
                AND nl.id IS NULL
                ORDER BY p.data_criacao DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = [
                'tipo' => 'produto',
                'id' => $row['Produto_id'],
                'icone' => 'fa-box',
                'titulo' => 'Produto Alterado',
                'mensagem' => $row['nome'] . ' foi alterado por ' . $row['anunciante_nome'],
                'data' => $row['data_criacao'],
                'link' => 'gestaoProdutosAdmin.php',
                'lida' => false
            ];
        }


        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return array_slice($notificacoes, 0, 10);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function marcarComoLida($utilizador_id, $tipo_notificacao, $referencia_id) {
        try {

        $tipo_notificacao_normalizado = $this->normalizarTipoNotificacao($tipo_notificacao);

        $sql = "INSERT INTO notificacoes_lidas (utilizador_id, tipo_notificacao, referencia_id)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE data_leitura = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('isi', $utilizador_id, $tipo_notificacao_normalizado, $referencia_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function marcarTodasComoLidas($utilizador_id, $tipo_utilizador) {
        try {



        $notificacoes = $this->listarTodasNotificacoesPorTipo($utilizador_id, $tipo_utilizador);

        if (!is_array($notificacoes)) {
            return false;
        }

        foreach ($notificacoes as $notif) {
            if (!empty($notif['lida'])) {
                continue;
            }

            if (!isset($notif['tipo'], $notif['id'])) {
                continue;
            }

            $this->marcarComoLida($utilizador_id, $notif['tipo'], (int)$notif['id']);
        }

        return true;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarTodasNotificacoesCliente($cliente_id) {
        try {

        $notificacoes = [];


        $sql = "SELECT
                    e.id,
                    e.codigo_encomenda,
                    e.estado,
                    e.data_envio,
                    p.nome as produto_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM Encomendas e
                INNER JOIN produtos p ON e.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                WHERE e.cliente_id = ?
                AND DATE(e.data_envio) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY e.data_envio DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $icone = 'fa-shopping-bag';
            $titulo = 'Encomenda ' . $row['estado'];

            if ($row['estado'] == 'Enviado') {
                $icone = 'fa-shipping-fast';
                $titulo = 'Encomenda Enviada';
            } elseif ($row['estado'] == 'Entregue') {
                $icone = 'fa-check-circle';
                $titulo = 'Encomenda Entregue';
            }

            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => '#' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'minhasEncomendas.php?encomenda=' . rawurlencode($row['codigo_encomenda']),
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    d.id,
                    d.codigo_devolucao,
                    d.estado,
                    d.data_solicitacao,
                    d.notas_anunciante,
                    p.nome as produto_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM devolucoes d
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'produto_enviado', 'produto_recebido', 'rejeitada', 'reembolsada')
                AND DATE(d.data_solicitacao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY d.data_solicitacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $icone = 'fa-undo';
            $titulo = 'Devolução ' . ucfirst($row['estado']);
            $mensagem = '#' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'];


            if ($row['estado'] === 'aprovada') {
                $icone = 'fa-check-circle';
                $titulo = 'Devolução Aprovada';
                $mensagem .= ' - Por favor, envie o produto e confirme no sistema.';
            } elseif ($row['estado'] === 'produto_enviado') {
                $icone = 'fa-shipping-fast';
                $titulo = 'Devolução Enviada';
                $mensagem .= ' - Aguardando confirmação do vendedor.';
            } elseif ($row['estado'] === 'produto_recebido') {
                $icone = 'fa-box-open';
                $titulo = 'Produto Recebido';
                $mensagem .= ' - Reembolso será processado em 5-10 dias úteis.';
            } elseif ($row['estado'] === 'rejeitada') {
                $icone = 'fa-times-circle';
                $titulo = 'Devolução Rejeitada';
                $mensagem .= !empty($row['notas_anunciante']) ? ' - ' . $row['notas_anunciante'] : '';
            } elseif ($row['estado'] === 'reembolsada') {
                $icone = 'fa-euro-sign';
                $titulo = 'Reembolso Processado';
                $mensagem .= ' - Reembolso concluído!';
            }

            $notificacoes[] = [
                'tipo' => 'devolucao',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data' => $row['data_solicitacao'],
                'link' => 'minhasEncomendas.php?tab=devolucoes',
                'lida' => (bool)$row['lida']
            ];
        }


        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $notificacoes;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarTodasNotificacoesAnunciante($anunciante_id) {
        try {

        $notificacoes = [];


        $sql = "SELECT
                    e.id,
                    e.codigo_encomenda,
                    e.estado,
                    e.data_envio,
                    p.nome as produto_nome,
                    u.nome as cliente_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM Encomendas e
                INNER JOIN produtos p ON e.produto_id = p.Produto_id
                INNER JOIN Utilizadores u ON e.cliente_id = u.id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                WHERE p.anunciante_id = ?
                AND DATE(e.data_envio) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY e.data_envio DESC
                LIMIT 100";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'icone' => 'fa-shopping-bag',
                'titulo' => 'Encomenda ' . $row['estado'],
                'mensagem' => 'Encomenda #' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'gestaoEncomendasAnunciante.php',
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    d.id,
                    d.codigo_devolucao,
                    d.estado,
                    d.data_solicitacao,
                    p.nome as produto_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM devolucoes d
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                WHERE d.anunciante_id = ?
                AND DATE(d.data_solicitacao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY d.data_solicitacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {

            $icone = 'fa-undo';
            $titulo = 'Devolução ' . ucfirst($row['estado']);

            if ($row['estado'] === 'solicitada') {
                $icone = 'fa-undo';
                $titulo = 'Devolução Solicitada';
            } elseif ($row['estado'] === 'aprovada') {
                $icone = 'fa-check-circle';
                $titulo = 'Devolução Aprovada';
            } elseif ($row['estado'] === 'produto_enviado') {
                $icone = 'fa-shipping-fast';
                $titulo = 'Produto Enviado pelo Cliente';
            } elseif ($row['estado'] === 'produto_recebido') {
                $icone = 'fa-box-open';
                $titulo = 'Produto Recebido';
            } elseif ($row['estado'] === 'rejeitada') {
                $icone = 'fa-times-circle';
                $titulo = 'Devolução Rejeitada';
            } elseif ($row['estado'] === 'reembolsada') {
                $icone = 'fa-euro-sign';
                $titulo = 'Reembolso Processado';
            }

            $notificacoes[] = [
                'tipo' => 'devolucao',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => 'Devolução #' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_solicitacao'],
                'link' => 'gestaoDevolucoesAnunciante.php',
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    p.Produto_id as id,
                    p.nome as produto_nome,
                    p.motivo_rejeicao,
                    p.data_criacao,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM produtos p
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'produto_rejeitado') AND nl.referencia_id = p.Produto_id)
                WHERE p.anunciante_id = ?
                AND p.ativo = 2
                ORDER BY p.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $mensagem = 'O produto "' . $row['produto_nome'] . '" foi rejeitado.';
            if (!empty($row['motivo_rejeicao'])) {
                $mensagem .= ' Motivo: ' . $row['motivo_rejeicao'];
            }
            $notificacoes[] = [
                'tipo' => 'produto_rejeitado',
                'id' => $row['id'],
                'icone' => 'fa-times-circle',
                'titulo' => 'Produto Rejeitado',
                'mensagem' => $mensagem,
                'data' => $row['data_criacao'],
                'link' => 'gestaoProdutosAnunciante.php',
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    p.Produto_id as id,
                    p.nome as produto_nome,
                    p.stock,
                    p.data_criacao,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM produtos p
                INNER JOIN Utilizadores u ON p.anunciante_id = u.id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'stock_baixo') AND nl.referencia_id = p.Produto_id)
                WHERE p.anunciante_id = ?
                AND u.plano_id >= 2
                AND p.stock > 0 AND p.stock <= 5
                ORDER BY p.stock ASC, p.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $notificacoes[] = [
                    'tipo' => 'stock_baixo',
                    'id' => $row['id'],
                    'icone' => 'fa-exclamation-triangle',
                    'titulo' => 'Stock Baixo',
                    'mensagem' => $row['produto_nome'] . ' — apenas ' . $row['stock'] . ' unidade(s) em stock.',
                    'data' => $row['data_criacao'],
                    'link' => 'gestaoProdutosAnunciante.php',
                    'lida' => (bool)$row['lida']
                ];
            }
        }


        $sql = "SELECT
                    p.Produto_id as id,
                    p.nome as produto_nome,
                    p.data_criacao,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM produtos p
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'stock_esgotado') AND nl.referencia_id = p.Produto_id)
                WHERE p.anunciante_id = ?
                AND p.stock = 0
                ORDER BY p.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $notificacoes[] = [
                    'tipo' => 'stock_esgotado',
                    'id' => $row['id'],
                    'icone' => 'fa-times-circle',
                    'titulo' => 'Produto Esgotado',
                    'mensagem' => $row['produto_nome'] . ' — sem stock. Produto removido do marketplace.',
                    'data' => $row['data_criacao'],
                    'link' => 'gestaoProdutosAnunciante.php',
                    'lida' => (bool)$row['lida']
                ];
            }
        }

        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $notificacoes;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function listarTodasNotificacoesAdmin($utilizador_id) {
        try {

        $notificacoes = [];


        $sql = "SELECT
                    u.id,
                    u.nome,
                    u.email,
                    u.data_criacao,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM Utilizadores u
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'utilizador' AND nl.referencia_id = u.id)
                WHERE u.email_verificado = 0
                ORDER BY u.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = [
                'tipo' => 'utilizador',
                'id' => $row['id'],
                'icone' => 'fa-user',
                'titulo' => 'Novo Utilizador',
                'mensagem' => $row['nome'] . ' (' . $row['email'] . ') - Aguarda verificação',
                'data' => $row['data_criacao'],
                'link' => 'gestaoCliente.php',
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    p.Produto_id,
                    p.nome,
                    p.data_criacao,
                    u.nome as anunciante_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM produtos p
                INNER JOIN Utilizadores u ON p.anunciante_id = u.id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'produto' AND nl.referencia_id = p.Produto_id)
                WHERE p.ativo = 0
            AND p.motivo_rejeicao = 'PENDENTE_REVISAO_ANUNCIANTE'
                ORDER BY p.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = [
                'tipo' => 'produto',
                'id' => $row['Produto_id'],
                'icone' => 'fa-box',
                'titulo' => 'Produto Alterado',
                'mensagem' => $row['nome'] . ' foi alterado por ' . $row['anunciante_nome'],
                'data' => $row['data_criacao'],
                'link' => 'gestaoProdutosAdmin.php',
                'lida' => (bool)$row['lida']
            ];
        }

        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $notificacoes;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
