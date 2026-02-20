<?php
require_once __DIR__ . '/connection.php';

class Notifications {

    private $conn;
    private $enumTiposNotificacaoGarantido = false;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function normalizarTipoNotificacao($tipo_notificacao) {
        $tipo = (string)$tipo_notificacao;

        if ($tipo === 'stock_baixo' || $tipo === 'stock_esgotado' || $tipo === 'produto_rejeitado') {
            return 'produto';
        }

        if (in_array($tipo, ['encomenda', 'devolucao', 'utilizador', 'produto', 'suporte', 'chat'], true)) {
            return $tipo;
        }

        return 'produto';
    }

    private function garantirTiposNotificacaoNoEnum(array $tiposNecessarios = ['chat', 'suporte']) {
        if ($this->enumTiposNotificacaoGarantido) {
            return true;
        }

        $sql = "SHOW COLUMNS FROM notificacoes_lidas LIKE 'tipo_notificacao'";
        $result = $this->conn->query($sql);

        if (!$result || $result->num_rows === 0) {
            return false;
        }

        $row = $result->fetch_assoc();
        $typeDef = (string)($row['Type'] ?? '');

        preg_match_all("/'([^']+)'/", $typeDef, $matches);
        $tiposAtuais = $matches[1] ?? [];

        if (empty($tiposAtuais)) {
            return false;
        }

        $faltantes = array_values(array_diff($tiposNecessarios, $tiposAtuais));

        if (empty($faltantes)) {
            $this->enumTiposNotificacaoGarantido = true;
            return true;
        }

        $tiposFinais = array_values(array_unique(array_merge($tiposAtuais, $tiposNecessarios)));
        $enumSql = implode(',', array_map(function ($tipo) {
            return "'" . $this->conn->real_escape_string($tipo) . "'";
        }, $tiposFinais));

        $alterSql = "ALTER TABLE notificacoes_lidas MODIFY COLUMN tipo_notificacao ENUM($enumSql) NOT NULL";

        if (!$this->conn->query($alterSql)) {
            return false;
        }

        $this->enumTiposNotificacaoGarantido = true;
        return true;
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

        $sql_encomendas = "SELECT COUNT(DISTINCT e.id) as total
                          FROM Encomendas e
                          INNER JOIN Vendas v ON v.encomenda_id = e.id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                          WHERE v.anunciante_id = ?
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


          $sql_confirmacao_recepcao = "SELECT COUNT(*) as total
                      FROM (
                          SELECT (500000000 + MIN(e.id)) as notificacao_ref
                          FROM Encomendas e
                          INNER JOIN Vendas v ON v.encomenda_id = e.id
                          WHERE v.anunciante_id = ?
                          AND e.estado = 'Entregue'
                          AND e.data_confirmacao_recepcao IS NOT NULL
                          AND DATE(e.data_confirmacao_recepcao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          GROUP BY e.codigo_encomenda
                      ) conf
                      LEFT JOIN notificacoes_lidas nl ON (
                          nl.utilizador_id = ?
                          AND nl.tipo_notificacao = 'encomenda'
                          AND nl.referencia_id = conf.notificacao_ref
                      )
                      WHERE nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_confirmacao_recepcao);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count += (int)$row['total'];
        }


        $sql_devolucoes = "SELECT COUNT(*) as total
                          FROM devolucoes d
                          LEFT JOIN notificacoes_lidas nl ON (
                              nl.utilizador_id = ?
                              AND nl.tipo_notificacao = 'devolucao'
                              AND nl.referencia_id = (
                                  CASE
                                      WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                                      WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                                      WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                                      WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                                      WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                                      ELSE d.id
                                  END
                              )
                          )
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


        $sql_produtos_aprovados = "SELECT COUNT(*) as total
                          FROM Produtos p
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'produto_aprovado') AND nl.referencia_id = p.Produto_id)
                          WHERE p.anunciante_id = ?
                          AND p.ativo = 1
                          AND p.motivo_rejeicao = 'APROVADO_ADMIN'
                          AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_produtos_aprovados);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $count += (int)$row['total'];
        }


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


        $sql_chat = "SELECT COUNT(*) as total
                     FROM MensagensAdmin m
                     LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
                     WHERE m.destinatario_id = ?
                     AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_chat);
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
                          AND p.ativo = 0
                          AND p.motivo_rejeicao IS NOT NULL
                          AND p.motivo_rejeicao <> ''
                          AND p.motivo_rejeicao <> 'PENDENTE_REVISAO_ANUNCIANTE'
                          AND p.motivo_rejeicao <> 'APROVADO_ADMIN'
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
                                                        GROUP_CONCAT(DISTINCT COALESCE(p.nome, 'Produto não encontrado') ORDER BY p.nome SEPARATOR ', ') as produto_nome,
                            COALESCE(u.nome, 'Cliente não encontrado') as cliente_nome
                          FROM Encomendas e
                                                    INNER JOIN Vendas v ON v.encomenda_id = e.id
                                                    LEFT JOIN produtos p ON p.Produto_id = v.produto_id
                          LEFT JOIN Utilizadores u ON e.cliente_id = u.id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                                                    WHERE v.anunciante_id = ?
                          AND e.estado IN ('Pendente', 'Processando')
                          AND nl.id IS NULL
                                                    GROUP BY e.id, e.codigo_encomenda, e.estado, e.data_envio, u.nome
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
            $mensagemProdutos = trim((string)($row['produto_nome'] ?? ''));
            if ($mensagemProdutos === '') {
                $mensagemProdutos = 'Produtos da encomenda';
            }
            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'titulo' => 'Encomenda ' . $row['estado'],
                'mensagem' => 'Encomenda #' . $row['codigo_encomenda'] . ' - ' . $mensagemProdutos,
                'data' => $row['data_envio'],
                'link' => 'gestaoEncomendasAnunciante.php',
                'lida' => false
            ];
        }


          $sql_confirmacao_recepcao = "SELECT
                        conf.notificacao_ref,
                        conf.codigo_encomenda,
                        conf.data_confirmacao,
                        conf.produto_nome,
                        conf.cliente_nome
                      FROM (
                          SELECT
                            (500000000 + MIN(e.id)) as notificacao_ref,
                            e.codigo_encomenda,
                            MAX(e.data_confirmacao_recepcao) as data_confirmacao,
                            GROUP_CONCAT(DISTINCT COALESCE(p.nome, 'Produto não encontrado') ORDER BY p.nome SEPARATOR ', ') as produto_nome,
                            COALESCE(MAX(u.nome), 'Cliente não encontrado') as cliente_nome
                          FROM Encomendas e
                          INNER JOIN Vendas v ON v.encomenda_id = e.id
                          LEFT JOIN produtos p ON p.Produto_id = v.produto_id
                          LEFT JOIN Utilizadores u ON e.cliente_id = u.id
                          WHERE v.anunciante_id = ?
                          AND e.estado = 'Entregue'
                          AND e.data_confirmacao_recepcao IS NOT NULL
                          AND DATE(e.data_confirmacao_recepcao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          GROUP BY e.codigo_encomenda
                      ) conf
                      LEFT JOIN notificacoes_lidas nl ON (
                          nl.utilizador_id = ?
                          AND nl.tipo_notificacao = 'encomenda'
                          AND nl.referencia_id = conf.notificacao_ref
                      )
                      WHERE nl.id IS NULL
                      ORDER BY conf.data_confirmacao DESC
                      LIMIT 5";

        $stmt = $this->conn->prepare($sql_confirmacao_recepcao);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $mensagemProdutos = trim((string)($row['produto_nome'] ?? ''));
                if ($mensagemProdutos === '') {
                    $mensagemProdutos = 'Produtos da encomenda';
                }

                $notificacoes[] = [
                    'tipo' => 'encomenda',
                    'id' => (int)$row['notificacao_ref'],
                    'icone' => 'fa-check-circle',
                    'titulo' => 'Receção Confirmada pelo Cliente',
                    'mensagem' => 'Encomenda #' . $row['codigo_encomenda'] . ' confirmada por ' . ($row['cliente_nome'] ?? 'cliente') . ' - ' . $mensagemProdutos,
                    'data' => $row['data_confirmacao'],
                    'link' => 'gestaoEncomendasAnunciante.php',
                    'lida' => false
                ];
            }
        }


        $sql_devolucoes = "SELECT
                            d.id,
                            CASE
                                WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                                WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                                WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                                WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                                WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                                ELSE d.id
                            END as notificacao_ref,
                            d.codigo_devolucao,
                            d.estado,
                            d.data_solicitacao,
                            d.codigo_rastreio,
                            COALESCE(p.nome, 'Produto não encontrado') as produto_nome,
                            COALESCE(u.nome, 'Cliente não encontrado') as cliente_nome
                          FROM devolucoes d
                          LEFT JOIN produtos p ON d.produto_id = p.Produto_id
                          LEFT JOIN Utilizadores u ON d.cliente_id = u.id
                          LEFT JOIN notificacoes_lidas nl ON (
                              nl.utilizador_id = ?
                              AND nl.tipo_notificacao = 'devolucao'
                              AND nl.referencia_id = (
                                  CASE
                                      WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                                      WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                                      WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                                      WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                                      WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                                      ELSE d.id
                                  END
                              )
                          )
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
                'id' => (int)$row['notificacao_ref'],
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


        $sql_chat = "SELECT
                        m.id,
                                m.remetente_id,
                        m.mensagem,
                        m.created_at,
                        u.nome AS remetente_nome
                     FROM MensagensAdmin m
                     LEFT JOIN Utilizadores u ON u.id = m.remetente_id
                     LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
                     WHERE m.destinatario_id = ?
                     AND nl.id IS NULL
                     ORDER BY m.created_at DESC
                     LIMIT 10";

        $stmt = $this->conn->prepare($sql_chat);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $mensagemCurta = trim((string)($row['mensagem'] ?? ''));
                if (mb_strlen($mensagemCurta) > 80) {
                    $mensagemCurta = mb_substr($mensagemCurta, 0, 80) . '...';
                }

                $notificacoes[] = [
                    'tipo' => 'chat',
                    'id' => $row['id'],
                    'icone' => 'fa-comments',
                    'titulo' => 'Nova Mensagem no Chat',
                    'mensagem' => ($row['remetente_nome'] ?? 'Utilizador') . ' - ' . $mensagemCurta,
                    'data' => $row['created_at'],
                    'link' => 'ChatAnunciante.php?utilizador=' . (int)$row['remetente_id'],
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
                          AND p.ativo = 0
                          AND p.motivo_rejeicao IS NOT NULL
                          AND p.motivo_rejeicao <> ''
                          AND p.motivo_rejeicao <> 'PENDENTE_REVISAO_ANUNCIANTE'
                          AND p.motivo_rejeicao <> 'APROVADO_ADMIN'
                          AND nl.id IS NULL
                          ORDER BY p.data_criacao DESC
                          LIMIT 5";

        $stmt = $this->conn->prepare($sql_rejeitados);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $motivoTexto = trim((string)($row['motivo_rejeicao'] ?? ''));
                if ($motivoTexto === '') {
                    $motivoTexto = 'Sem motivo detalhado informado.';
                }
                $notificacoes[] = [
                    'tipo' => 'produto_rejeitado',
                    'id' => $row['id'],
                    'icone' => 'fa-times-circle',
                    'titulo' => 'Produto Rejeitado',
                    'mensagem' => $row['produto_nome'] . ' foi rejeitado. Motivo: ' . $motivoTexto,
                    'data' => $row['data_criacao'],
                    'link' => 'gestaoProdutosAnunciante.php',
                    'lida' => false
                ];
            }


            $sql_aprovados = "SELECT
                                p.Produto_id as id,
                                p.nome as produto_nome,
                                p.data_criacao
                              FROM Produtos p
                              LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'produto_aprovado') AND nl.referencia_id = p.Produto_id)
                              WHERE p.anunciante_id = ?
                              AND p.ativo = 1
                              AND p.motivo_rejeicao = 'APROVADO_ADMIN'
                              AND nl.id IS NULL
                              ORDER BY p.data_criacao DESC
                              LIMIT 5";

            $stmt = $this->conn->prepare($sql_aprovados);
            if ($stmt) {
                $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $notificacoes[] = [
                        'tipo' => 'produto_aprovado',
                        'id' => $row['id'],
                        'icone' => 'fa-check-circle',
                        'titulo' => 'Produto Aprovado',
                        'mensagem' => $row['produto_nome'] . ' foi aprovado pelo admin e está ativo no marketplace.',
                        'data' => $row['data_criacao'],
                        'link' => 'gestaoProdutosAnunciante.php',
                        'lida' => false
                    ];
                }
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
                FROM Historico_Produtos h
                INNER JOIN Encomendas e ON e.id = h.encomenda_id
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'encomenda'
                    AND nl.referencia_id = h.id
                )
                WHERE e.cliente_id = ?
                AND h.estado_encomenda IN ('Processando', 'Enviado', 'Entregue')
                AND DATE(h.data_atualizacao) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];


        $sql = "SELECT COUNT(*) as total
            FROM MensagensAdmin m
            LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
            WHERE m.destinatario_id = ?
            AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];


        $sql = "SELECT COUNT(*) as total
                FROM devolucoes d
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'devolucao'
                    AND nl.referencia_id = (
                        CASE
                            WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                            WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                            WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                            WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                            WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                            ELSE d.id
                        END
                    )
                )
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'produto_enviado', 'produto_recebido', 'rejeitada', 'reembolsada')
                AND DATE(COALESCE(d.data_reembolso, d.data_recebimento, d.data_produto_recebido, d.data_rejeicao, d.data_aprovacao, d.data_envio_cliente, d.data_solicitacao)) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
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
                    h.id as notificacao_ref,
                    e.codigo_encomenda,
                    h.estado_encomenda as estado,
                    h.data_atualizacao as data_notificacao,
                    COALESCE(p.nome, e.TipoProdutoNome, 'Produto não encontrado') as produto_nome,
                    h.descricao
                FROM Historico_Produtos h
                INNER JOIN Encomendas e ON e.id = h.encomenda_id
                LEFT JOIN produtos p ON e.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'encomenda'
                    AND nl.referencia_id = h.id
                )
                WHERE e.cliente_id = ?
                AND DATE(h.data_atualizacao) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND h.estado_encomenda IN ('Processando', 'Enviado', 'Entregue', 'Cancelado', 'Cancelada', 'Pendente')
                AND nl.id IS NULL
                ORDER BY h.id DESC
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
                'id' => (int)$row['notificacao_ref'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => '#' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'] . (!empty($row['descricao']) ? ' - ' . $row['descricao'] : ''),
                'data' => $row['data_notificacao'],
                'link' => 'minhasEncomendas.php?encomenda=' . rawurlencode($row['codigo_encomenda']),
                'lida' => false
            ];
        }


        $sql = "SELECT
                    m.id,
                    m.remetente_id,
                    m.mensagem,
                    m.created_at,
                    u.nome AS remetente_nome
                FROM MensagensAdmin m
                LEFT JOIN Utilizadores u ON u.id = m.remetente_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
                WHERE m.destinatario_id = ?
                AND nl.id IS NULL
                ORDER BY m.created_at DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $mensagemCurta = trim((string)($row['mensagem'] ?? ''));
            if (mb_strlen($mensagemCurta) > 80) {
                $mensagemCurta = mb_substr($mensagemCurta, 0, 80) . '...';
            }

            $notificacoes[] = [
                'tipo' => 'chat',
                'id' => $row['id'],
                'icone' => 'fa-comments',
                'titulo' => 'Nova Mensagem no Chat',
                'mensagem' => ($row['remetente_nome'] ?? 'Utilizador') . ' - ' . $mensagemCurta,
                'data' => $row['created_at'],
                'link' => 'ChatCliente.php?vendedor=' . (int)$row['remetente_id'],
                'lida' => false
            ];
        }


        $sql = "SELECT
                    d.id,
                    CASE
                        WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                        WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                        WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                        WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                        WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                        ELSE d.id
                    END as notificacao_ref,
                    d.codigo_devolucao,
                    d.estado,
                    d.data_solicitacao,
                    COALESCE(d.data_reembolso, d.data_recebimento, d.data_produto_recebido, d.data_rejeicao, d.data_aprovacao, d.data_envio_cliente, d.data_solicitacao) as data_evento,
                    d.codigo_rastreio,
                    d.notas_anunciante,
                    d.notas_recebimento,
                    p.nome as produto_nome
                FROM devolucoes d
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'devolucao'
                    AND nl.referencia_id = (
                        CASE
                            WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                            WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                            WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                            WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                            WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                            ELSE d.id
                        END
                    )
                )
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'produto_enviado', 'produto_recebido', 'rejeitada', 'reembolsada')
                AND DATE(COALESCE(d.data_reembolso, d.data_recebimento, d.data_produto_recebido, d.data_rejeicao, d.data_aprovacao, d.data_envio_cliente, d.data_solicitacao)) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                AND nl.id IS NULL
                ORDER BY data_evento DESC
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
                'id' => (int)$row['notificacao_ref'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data' => $row['data_evento'],
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
            FROM mensagensadmin m
            LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'suporte' AND nl.referencia_id = m.id)
            WHERE m.destinatario_id = ?
            AND (m.mensagem LIKE 'Assunto:%' OR m.mensagem LIKE 'Nome:%')
            AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $utilizador_id, $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];


        $sql = "SELECT COUNT(*) as total
            FROM mensagensadmin m
            LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
            WHERE m.destinatario_id = ?
            AND m.mensagem NOT LIKE 'Assunto:%'
            AND m.mensagem NOT LIKE 'Nome:%'
            AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $utilizador_id, $utilizador_id);
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
                    m.id,
                    m.remetente_id,
                    m.mensagem,
                    m.created_at,
                    u.nome AS remetente_nome
                FROM mensagensadmin m
                LEFT JOIN utilizadores u ON u.id = m.remetente_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'suporte' AND nl.referencia_id = m.id)
                WHERE m.destinatario_id = ?
                AND (m.mensagem LIKE 'Assunto:%' OR m.mensagem LIKE 'Nome:%')
                AND nl.id IS NULL
                ORDER BY m.created_at DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $utilizador_id, $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $mensagemCurta = trim((string)($row['mensagem'] ?? ''));
            if (mb_strlen($mensagemCurta) > 80) {
                $mensagemCurta = mb_substr($mensagemCurta, 0, 80) . '...';
            }

            $notificacoes[] = [
                'tipo' => 'suporte',
                'id' => $row['id'],
                'icone' => 'fa-life-ring',
                'titulo' => 'Nova Mensagem de Suporte',
                'mensagem' => ($row['remetente_nome'] ?? 'Utilizador') . ' - ' . $mensagemCurta,
                'data' => $row['created_at'],
                'link' => 'ChatAdmin.php?utilizador=' . (int)$row['remetente_id'],
                'lida' => false
            ];
        }


        $sql = "SELECT
                    m.id,
                    m.remetente_id,
                    m.mensagem,
                    m.created_at,
                    u.nome AS remetente_nome
                FROM mensagensadmin m
                LEFT JOIN utilizadores u ON u.id = m.remetente_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
                WHERE m.destinatario_id = ?
                AND m.mensagem NOT LIKE 'Assunto:%'
                AND m.mensagem NOT LIKE 'Nome:%'
                AND nl.id IS NULL
                ORDER BY m.created_at DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $utilizador_id, $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $mensagemCurta = trim((string)($row['mensagem'] ?? ''));
            if (mb_strlen($mensagemCurta) > 80) {
                $mensagemCurta = mb_substr($mensagemCurta, 0, 80) . '...';
            }

            $notificacoes[] = [
                'tipo' => 'chat',
                'id' => $row['id'],
                'icone' => 'fa-comments',
                'titulo' => 'Nova Mensagem no Chat',
                'mensagem' => ($row['remetente_nome'] ?? 'Utilizador') . ' - ' . $mensagemCurta,
                'data' => $row['created_at'],
                'link' => 'ChatAdmin.php?utilizador=' . (int)$row['remetente_id'],
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
                'titulo' => 'Produto Pendente de Aprovação',
                'mensagem' => $row['nome'] . ' aguarda aprovação (anunciante: ' . $row['anunciante_nome'] . ')',
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

        if (in_array($tipo_notificacao_normalizado, ['chat', 'suporte'], true)) {
            if (!$this->garantirTiposNotificacaoNoEnum(['chat', 'suporte'])) {
                return false;
            }
        }

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
                    h.id as notificacao_ref,
                    e.codigo_encomenda,
                    h.estado_encomenda as estado,
                    h.data_atualizacao as data_notificacao,
                    COALESCE(p.nome, e.TipoProdutoNome, 'Produto não encontrado') as produto_nome,
                    h.descricao,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM Historico_Produtos h
                INNER JOIN Encomendas e ON e.id = h.encomenda_id
                LEFT JOIN produtos p ON e.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'encomenda'
                    AND nl.referencia_id = h.id
                )
                WHERE e.cliente_id = ?
                AND DATE(h.data_atualizacao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                AND h.estado_encomenda IS NOT NULL
                AND TRIM(h.estado_encomenda) <> ''
                ORDER BY h.id DESC
                LIMIT 100";

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
                'id' => (int)$row['notificacao_ref'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => '#' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'] . (!empty($row['descricao']) ? ' - ' . $row['descricao'] : ''),
                'data' => $row['data_notificacao'],
                'link' => 'minhasEncomendas.php?encomenda=' . rawurlencode($row['codigo_encomenda']),
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    m.id,
                    m.remetente_id,
                    m.mensagem,
                    m.created_at,
                    u.nome AS remetente_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM MensagensAdmin m
                LEFT JOIN Utilizadores u ON u.id = m.remetente_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
                WHERE m.destinatario_id = ?
                ORDER BY m.created_at DESC
                LIMIT 100";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $mensagemCurta = trim((string)($row['mensagem'] ?? ''));
            if (mb_strlen($mensagemCurta) > 80) {
                $mensagemCurta = mb_substr($mensagemCurta, 0, 80) . '...';
            }

            $notificacoes[] = [
                'tipo' => 'chat',
                'id' => $row['id'],
                'icone' => 'fa-comments',
                'titulo' => 'Nova Mensagem no Chat',
                'mensagem' => ($row['remetente_nome'] ?? 'Utilizador') . ' - ' . $mensagemCurta,
                'data' => $row['created_at'],
                'link' => 'ChatCliente.php?vendedor=' . (int)$row['remetente_id'],
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    d.id,
                    CASE
                        WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                        WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                        WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                        WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                        WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                        ELSE d.id
                    END as notificacao_ref,
                    d.codigo_devolucao,
                    d.estado,
                    d.data_solicitacao,
                    COALESCE(d.data_reembolso, d.data_recebimento, d.data_produto_recebido, d.data_rejeicao, d.data_aprovacao, d.data_envio_cliente, d.data_solicitacao) as data_evento,
                    d.notas_anunciante,
                    p.nome as produto_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM devolucoes d
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'devolucao'
                    AND nl.referencia_id = (
                        CASE
                            WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                            WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                            WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                            WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                            WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                            ELSE d.id
                        END
                    )
                )
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'produto_enviado', 'produto_recebido', 'rejeitada', 'reembolsada')
                AND DATE(COALESCE(d.data_reembolso, d.data_recebimento, d.data_produto_recebido, d.data_rejeicao, d.data_aprovacao, d.data_envio_cliente, d.data_solicitacao)) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY data_evento DESC
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
                'id' => (int)$row['notificacao_ref'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data' => $row['data_evento'],
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
                    GROUP_CONCAT(DISTINCT p.nome ORDER BY p.nome SEPARATOR ', ') as produto_nome,
                    u.nome as cliente_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM Encomendas e
                INNER JOIN Vendas v ON v.encomenda_id = e.id
                INNER JOIN produtos p ON p.Produto_id = v.produto_id
                INNER JOIN Utilizadores u ON e.cliente_id = u.id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                WHERE v.anunciante_id = ?
                AND DATE(e.data_envio) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY e.id, e.codigo_encomenda, e.estado, e.data_envio, u.nome, nl.id
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
                    conf.notificacao_ref,
                    conf.codigo_encomenda,
                    conf.data_confirmacao,
                    conf.produto_nome,
                    conf.cliente_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM (
                    SELECT
                        (500000000 + MIN(e.id)) as notificacao_ref,
                        e.codigo_encomenda,
                        MAX(e.data_confirmacao_recepcao) as data_confirmacao,
                        GROUP_CONCAT(DISTINCT p.nome ORDER BY p.nome SEPARATOR ', ') as produto_nome,
                        MAX(u.nome) as cliente_nome
                    FROM Encomendas e
                    INNER JOIN Vendas v ON v.encomenda_id = e.id
                    INNER JOIN produtos p ON p.Produto_id = v.produto_id
                    INNER JOIN Utilizadores u ON e.cliente_id = u.id
                    WHERE v.anunciante_id = ?
                    AND e.estado = 'Entregue'
                    AND e.data_confirmacao_recepcao IS NOT NULL
                    AND DATE(e.data_confirmacao_recepcao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY e.codigo_encomenda
                ) conf
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'encomenda'
                    AND nl.referencia_id = conf.notificacao_ref
                )
                ORDER BY conf.data_confirmacao DESC
                LIMIT 100";

        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $mensagemProdutos = trim((string)($row['produto_nome'] ?? ''));
                if ($mensagemProdutos === '') {
                    $mensagemProdutos = 'Produtos da encomenda';
                }

                $notificacoes[] = [
                    'tipo' => 'encomenda',
                    'id' => (int)$row['notificacao_ref'],
                    'icone' => 'fa-check-circle',
                    'titulo' => 'Receção Confirmada pelo Cliente',
                    'mensagem' => 'Encomenda #' . $row['codigo_encomenda'] . ' confirmada por ' . ($row['cliente_nome'] ?? 'cliente') . ' - ' . $mensagemProdutos,
                    'data' => $row['data_confirmacao'],
                    'link' => 'gestaoEncomendasAnunciante.php',
                    'lida' => (bool)$row['lida']
                ];
            }
        }


        $sql = "SELECT
                    d.id,
                    CASE
                        WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                        WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                        WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                        WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                        WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                        ELSE d.id
                    END as notificacao_ref,
                    d.codigo_devolucao,
                    d.estado,
                    d.data_solicitacao,
                    p.nome as produto_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM devolucoes d
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                LEFT JOIN notificacoes_lidas nl ON (
                    nl.utilizador_id = ?
                    AND nl.tipo_notificacao = 'devolucao'
                    AND nl.referencia_id = (
                        CASE
                            WHEN d.estado = 'produto_enviado' THEN 100000000 + d.id
                            WHEN d.estado = 'aprovada' THEN 200000000 + d.id
                            WHEN d.estado = 'produto_recebido' THEN 300000000 + d.id
                            WHEN d.estado = 'rejeitada' THEN 400000000 + d.id
                            WHEN d.estado = 'reembolsada' THEN 500000000 + d.id
                            ELSE d.id
                        END
                    )
                )
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
                'id' => (int)$row['notificacao_ref'],
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
                AND p.ativo = 0
                AND p.motivo_rejeicao IS NOT NULL
                AND p.motivo_rejeicao <> ''
                AND p.motivo_rejeicao <> 'PENDENTE_REVISAO_ANUNCIANTE'
                AND p.motivo_rejeicao <> 'APROVADO_ADMIN'
                ORDER BY p.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $motivoTexto = trim((string)($row['motivo_rejeicao'] ?? ''));
            if ($motivoTexto === '') {
                $motivoTexto = 'Sem motivo detalhado informado.';
            }
            $mensagem = 'O produto "' . $row['produto_nome'] . '" foi rejeitado. Motivo: ' . $motivoTexto;
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
                    p.data_criacao,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM produtos p
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao IN ('produto', 'produto_aprovado') AND nl.referencia_id = p.Produto_id)
                WHERE p.anunciante_id = ?
                AND p.ativo = 1
                AND p.motivo_rejeicao = 'APROVADO_ADMIN'
                ORDER BY p.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $notificacoes[] = [
                    'tipo' => 'produto_aprovado',
                    'id' => $row['id'],
                    'icone' => 'fa-check-circle',
                    'titulo' => 'Produto Aprovado',
                    'mensagem' => 'O produto "' . $row['produto_nome'] . '" foi aprovado pelo admin e está ativo no marketplace.',
                    'data' => $row['data_criacao'],
                    'link' => 'gestaoProdutosAnunciante.php',
                    'lida' => (bool)$row['lida']
                ];
            }
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
                    m.id,
                    m.remetente_id,
                    m.mensagem,
                    m.created_at,
                    u.nome AS remetente_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM mensagensadmin m
                LEFT JOIN utilizadores u ON u.id = m.remetente_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'suporte' AND nl.referencia_id = m.id)
                WHERE m.destinatario_id = ?
                AND (m.mensagem LIKE 'Assunto:%' OR m.mensagem LIKE 'Nome:%')
                ORDER BY m.created_at DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $utilizador_id, $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $mensagemCurta = trim((string)($row['mensagem'] ?? ''));
            if (mb_strlen($mensagemCurta) > 80) {
                $mensagemCurta = mb_substr($mensagemCurta, 0, 80) . '...';
            }

            $notificacoes[] = [
                'tipo' => 'suporte',
                'id' => $row['id'],
                'icone' => 'fa-life-ring',
                'titulo' => 'Nova Mensagem de Suporte',
                'mensagem' => ($row['remetente_nome'] ?? 'Utilizador') . ' - ' . $mensagemCurta,
                'data' => $row['created_at'],
                'link' => 'ChatAdmin.php?utilizador=' . (int)$row['remetente_id'],
                'lida' => (bool)$row['lida']
            ];
        }


        $sql = "SELECT
                    m.id,
                    m.remetente_id,
                    m.mensagem,
                    m.created_at,
                    u.nome AS remetente_nome,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM mensagensadmin m
                LEFT JOIN utilizadores u ON u.id = m.remetente_id
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'chat' AND nl.referencia_id = m.id)
                WHERE m.destinatario_id = ?
                AND m.mensagem NOT LIKE 'Assunto:%'
                AND m.mensagem NOT LIKE 'Nome:%'
                ORDER BY m.created_at DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $utilizador_id, $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $mensagemCurta = trim((string)($row['mensagem'] ?? ''));
            if (mb_strlen($mensagemCurta) > 80) {
                $mensagemCurta = mb_substr($mensagemCurta, 0, 80) . '...';
            }

            $notificacoes[] = [
                'tipo' => 'chat',
                'id' => $row['id'],
                'icone' => 'fa-comments',
                'titulo' => 'Nova Mensagem no Chat',
                'mensagem' => ($row['remetente_nome'] ?? 'Utilizador') . ' - ' . $mensagemCurta,
                'data' => $row['created_at'],
                'link' => 'ChatAdmin.php?utilizador=' . (int)$row['remetente_id'],
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
                'titulo' => 'Produto Pendente de Aprovação',
                'mensagem' => $row['nome'] . ' aguarda aprovação (anunciante: ' . $row['anunciante_nome'] . ')',
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
