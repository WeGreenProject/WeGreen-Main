<?php

require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class ModelDevolucoes {

    private $conn;

    function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    function solicitarDevolucao($encomenda_id, $cliente_id, $motivo, $motivo_detalhe = '', $notas_cliente = '', $fotos = []) {
        try {
            // 1. Verificar elegibilidade
            $elegibilidade = $this->verificarElegibilidade($encomenda_id, $cliente_id);
            if (!$elegibilidade['elegivel']) {
                return [
                    'success' => false,
                    'message' => $elegibilidade['motivo']
                ];
            }

            $encomenda = $elegibilidade['encomenda'];

            // 2. Gerar código único de devolução
            $codigo_devolucao = $this->gerarCodigoDevolucao();

            // 3. Preparar dados
            $fotos_json = json_encode($fotos);

            // 4. Inserir devolução
            $sql = "INSERT INTO devolucoes (
                        codigo_devolucao, encomenda_id, cliente_id, anunciante_id, produto_id,
                        valor_reembolso, motivo, motivo_detalhe, notas_cliente, fotos,
                        payment_intent_id, estado, data_solicitacao
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'solicitada', NOW())";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                'siiiidssss',
                $codigo_devolucao,
                $encomenda_id,
                $cliente_id,
                $encomenda['anunciante_id'],
                $encomenda['produto_id'],
                $encomenda['valor'],
                $motivo,
                $motivo_detalhe,
                $notas_cliente,
                $fotos_json,
                $encomenda['payment_id']
            );

            if (!$stmt->execute()) {
                throw new Exception("Erro ao inserir devolução: " . $stmt->error);
            }

            $devolucao_id = $stmt->insert_id;

            // 5. Registrar no histórico
            $this->registrarHistorico($devolucao_id, null, 'solicitada', 'cliente', 'Devolução solicitada pelo cliente');

            // 6. Enviar notificações
            $this->enviarNotificacaoSolicitacao($devolucao_id);

            return [
                'success' => true,
                'message' => 'Devolução solicitada com sucesso!',
                'codigo_devolucao' => $codigo_devolucao,
                'devolucao_id' => $devolucao_id
            ];

        } catch (Exception $e) {
            error_log("Erro em solicitarDevolucao: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao solicitar devolução: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar se encomenda é elegível para devolução
     *
     * @param int $encomenda_id
     * @param int $cliente_id
     * @return array
     */
    public function verificarElegibilidade($encomenda_id, $cliente_id) {
        // Buscar dados da encomenda
        $sql = "SELECT e.*, v.valor, e.payment_id
                FROM encomendas e
                LEFT JOIN vendas v ON e.id = v.encomenda_id
                WHERE e.id = ? AND e.cliente_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $encomenda_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return [
                'elegivel' => false,
                'motivo' => 'Encomenda não encontrada ou não pertence a este cliente.'
            ];
        }

        $encomenda = $result->fetch_assoc();

        // Verificar se já existe devolução
        $sqlCheck = "SELECT id FROM devolucoes WHERE encomenda_id = ? AND estado NOT IN ('rejeitada', 'cancelada')";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param('i', $encomenda_id);
        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {
            return [
                'elegivel' => false,
                'motivo' => 'Já existe uma devolução em processamento para esta encomenda.'
            ];
        }

        // Verificar estado da encomenda
        if ($encomenda['estado'] !== 'Entregue') {
            return [
                'elegivel' => false,
                'motivo' => 'Apenas encomendas com estado "Entregue" podem ser devolvidas.'
            ];
        }

        // Verificar prazo (14 dias após entrega)
        if (!empty($encomenda['data_envio'])) {
            $data_entrega = new DateTime($encomenda['data_envio']);
            $hoje = new DateTime();
            $diff = $hoje->diff($data_entrega);
            $dias = $diff->days;

            if ($dias > 14) {
                return [
                    'elegivel' => false,
                    'motivo' => 'O prazo para devolução (14 dias após a entrega) expirou.'
                ];
            }
        }

        return [
            'elegivel' => true,
            'encomenda' => $encomenda
        ];
    }

    /**
     * Listar devoluções por cliente
     *
     * @param int $cliente_id
     * @return array
     */
    public function listarDevolucoesPorCliente($cliente_id) {
        $sql = "SELECT
                    d.*,
                    e.codigo_encomenda,
                    e.data_envio,
                    p.nome as produto_nome,
                    p.foto as produto_imagem,
                    u.nome as anunciante_nome
                FROM devolucoes d
                INNER JOIN encomendas e ON d.encomenda_id = e.id
                INNER JOIN produtos p ON d.produto_id = p.Produto_id
                INNER JOIN Utilizadores u ON d.anunciante_id = u.id
                WHERE d.cliente_id = ?
                ORDER BY d.data_solicitacao DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $devolucoes = [];
        while ($row = $result->fetch_assoc()) {
            // Decodificar JSON de fotos
            if (!empty($row['fotos'])) {
                $row['fotos'] = json_decode($row['fotos'], true);
            } else {
                $row['fotos'] = [];
            }
            $devolucoes[] = $row;
        }

        return $devolucoes;
    }

    /**
     * Listar devoluções por anunciante
     *
     * @param int $anunciante_id
     * @param string $filtro_estado (opcional)
     * @return array
     */
    public function listarDevolucoesPorAnunciante($anunciante_id, $filtro_estado = null) {
        $sql = "SELECT
                    d.*,
                    e.codigo_encomenda,
                    e.data_envio
                FROM devolucoes d
                LEFT JOIN encomendas e ON d.encomenda_id = e.id
                LEFT JOIN Utilizadores u ON d.cliente_id = u.id
                WHERE d.anunciante_id = ?";

        if ($filtro_estado) {
            $sql .= " AND d.estado = ?";
        }

        $sql .= " ORDER BY
                    CASE WHEN d.estado = 'solicitada' THEN 0 ELSE 1 END,
                    d.data_solicitacao DESC";

        $stmt = $this->conn->prepare($sql);

        if ($filtro_estado) {
            $stmt->bind_param('is', $anunciante_id, $filtro_estado);
        } else {
            $stmt->bind_param('i', $anunciante_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $devolucoes = [];
        while ($row = $result->fetch_assoc()) {
            // Buscar informações do cliente
            $sql_cliente = "SELECT nome, email FROM Utilizadores WHERE id = ?";
            $stmt_cliente = $this->conn->prepare($sql_cliente);
            $stmt_cliente->bind_param('i', $row['cliente_id']);
            $stmt_cliente->execute();
            $result_cliente = $stmt_cliente->get_result();
            $cliente = $result_cliente->fetch_assoc();
            $stmt_cliente->close();

            $row['cliente_nome'] = $cliente ? $cliente['nome'] : 'Cliente não encontrado';
            $row['cliente_email'] = $cliente ? $cliente['email'] : '';

            // Buscar produtos da devolução
            // Se a devolução tem produto_id específico, usar esse
            // Senão, buscar todos os produtos da encomenda
            if (!empty($row['produto_id'])) {
                $sql_produto = "SELECT nome, foto FROM Produtos WHERE Produto_id = ?";
                $stmt_produto = $this->conn->prepare($sql_produto);
                $stmt_produto->bind_param('i', $row['produto_id']);
                $stmt_produto->execute();
                $result_produto = $stmt_produto->get_result();
                $produto = $result_produto->fetch_assoc();
                $stmt_produto->close();

                $row['produto_nome'] = $produto ? $produto['nome'] : 'Produto Removido';
                $row['produto_imagem'] = $produto ? $produto['foto'] : null;
            } else {
                // Buscar produtos da encomenda
                $sql_produtos = "SELECT p.nome, p.foto
                                FROM Vendas v
                                INNER JOIN Produtos p ON v.produto_id = p.Produto_id
                                WHERE v.encomenda_id = ?
                                LIMIT 1";
                $stmt_produtos = $this->conn->prepare($sql_produtos);
                $stmt_produtos->bind_param('i', $row['encomenda_id']);
                $stmt_produtos->execute();
                $result_produtos = $stmt_produtos->get_result();
                $primeiro_produto = $result_produtos->fetch_assoc();
                $stmt_produtos->close();

                $row['produto_nome'] = $primeiro_produto ? $primeiro_produto['nome'] : 'Produto Removido';
                $row['produto_imagem'] = $primeiro_produto ? $primeiro_produto['foto'] : null;
            }

            if (!empty($row['fotos'])) {
                $row['fotos'] = json_decode($row['fotos'], true);
            } else {
                $row['fotos'] = [];
            }
            $devolucoes[] = $row;
        }

        return $devolucoes;
    }

    /**
     * Obter detalhes de uma devolução
     *
     * @param int $devolucao_id
     * @return array|null
     */
    public function obterDetalhes($devolucao_id) {
        $sql = "SELECT * FROM view_devolucoes_completa WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $devolucao_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $devolucao = $result->fetch_assoc();
            if (!empty($devolucao['fotos'])) {
                $devolucao['fotos'] = json_decode($devolucao['fotos'], true);
            }
            return $devolucao;
        }

        return null;
    }

    /**
     * Aprovar devolução
     *
     * @param int $devolucao_id
     * @param int $anunciante_id
     * @param string $notas_anunciante
     * @return array
     */
    public function aprovarDevolucao($devolucao_id, $anunciante_id, $notas_anunciante = '') {
        try {
            // Verificar se a devolução pertence ao anunciante
            $devolucao = $this->obterDetalhes($devolucao_id);

            if (!$devolucao) {
                return ['success' => false, 'message' => 'Devolução não encontrada.'];
            }

            if ($devolucao['anunciante_id'] != $anunciante_id) {
                return ['success' => false, 'message' => 'Sem permissão para aprovar esta devolução.'];
            }

            if ($devolucao['estado'] !== 'solicitada') {
                return ['success' => false, 'message' => 'Apenas devoluções solicitadas podem ser aprovadas.'];
            }

            // Atualizar estado
            $sql = "UPDATE devolucoes
                    SET estado = 'aprovada',
                        notas_anunciante = ?,
                        data_aprovacao = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $notas_anunciante, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao aprovar devolução");
            }

            // Registrar histórico
            $this->registrarHistorico($devolucao_id, 'solicitada', 'aprovada', 'anunciante', $notas_anunciante);

            // Enviar notificação ao cliente
            $this->enviarNotificacaoAprovacao($devolucao_id);

            return [
                'success' => true,
                'message' => 'Devolução aprovada com sucesso! Aguardando recebimento do produto.'
            ];

        } catch (Exception $e) {
            error_log("Erro em aprovarDevolucao: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao aprovar devolução.'];
        }
    }

    /**
     * Rejeitar devolução
     *
     * @param int $devolucao_id
     * @param int $anunciante_id
     * @param string $notas_anunciante
     * @return array
     */
    public function rejeitarDevolucao($devolucao_id, $anunciante_id, $notas_anunciante) {
        try {
            $devolucao = $this->obterDetalhes($devolucao_id);

            if (!$devolucao) {
                return ['success' => false, 'message' => 'Devolução não encontrada.'];
            }

            if ($devolucao['anunciante_id'] != $anunciante_id) {
                return ['success' => false, 'message' => 'Sem permissão para rejeitar esta devolução.'];
            }

            if ($devolucao['estado'] !== 'solicitada') {
                return ['success' => false, 'message' => 'Apenas devoluções solicitadas podem ser rejeitadas.'];
            }

            $sql = "UPDATE devolucoes
                    SET estado = 'rejeitada',
                        notas_anunciante = ?,
                        data_rejeicao = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $notas_anunciante, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao rejeitar devolução");
            }

            $this->registrarHistorico($devolucao_id, 'solicitada', 'rejeitada', 'anunciante', $notas_anunciante);

            // Enviar notificação ao cliente
            $this->enviarNotificacaoRejeicao($devolucao_id);

            return [
                'success' => true,
                'message' => 'Devolução rejeitada.'
            ];

        } catch (Exception $e) {
            error_log("Erro em rejeitarDevolucao: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao rejeitar devolução.'];
        }
    }

    /**
     * Confirmar envio do produto pelo cliente
     *
     * @param int $devolucao_id
     * @param int $cliente_id
     * @param string $codigo_rastreio
     * @return array
     */
    public function confirmarEnvioCliente($devolucao_id, $cliente_id, $codigo_rastreio = '') {
        try {
            $devolucao = $this->obterDetalhes($devolucao_id);

            if (!$devolucao) {
                return ['success' => false, 'message' => 'Devolução não encontrada.'];
            }

            if ($devolucao['cliente_id'] != $cliente_id) {
                return ['success' => false, 'message' => 'Sem permissão para confirmar esta devolução.'];
            }

            if ($devolucao['estado'] !== 'aprovada') {
                return ['success' => false, 'message' => 'Apenas devoluções aprovadas podem ter envio confirmado.'];
            }

            // Atualizar estado
            $sql = "UPDATE devolucoes
                    SET estado = 'enviada',
                        codigo_rastreio = ?,
                        data_envio_cliente = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $codigo_rastreio, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao confirmar envio");
            }

            // Registrar histórico
            $obs = $codigo_rastreio ? "Código de rastreio: {$codigo_rastreio}" : "Sem código de rastreio";
            $this->registrarHistorico($devolucao_id, 'aprovada', 'enviada', 'cliente', $obs);

            // Enviar notificação ao anunciante
            $this->enviarNotificacaoEnvio($devolucao_id);

            return [
                'success' => true,
                'message' => 'Envio confirmado! O vendedor será notificado.'
            ];

        } catch (Exception $e) {
            error_log("Erro em confirmarEnvioCliente: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao confirmar envio.'];
        }
    }

    /**
     * Confirmar recebimento do produto pelo vendedor
     *
     * @param int $devolucao_id
     * @param int $anunciante_id
     * @param string $notas_recebimento
     * @return array
     */
    public function confirmarRecebimentoVendedor($devolucao_id, $anunciante_id, $notas_recebimento = '') {
        try {
            $devolucao = $this->obterDetalhes($devolucao_id);

            if (!$devolucao) {
                return ['success' => false, 'message' => 'Devolução não encontrada.'];
            }

            if ($devolucao['anunciante_id'] != $anunciante_id) {
                return ['success' => false, 'message' => 'Sem permissão para confirmar esta devolução.'];
            }

            if ($devolucao['estado'] !== 'enviada') {
                return ['success' => false, 'message' => 'Produto ainda não foi enviado pelo cliente.'];
            }

            // Atualizar estado
            $sql = "UPDATE devolucoes
                    SET estado = 'recebida',
                        notas_recebimento = ?,
                        data_recebimento = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('si', $notas_recebimento, $devolucao_id);

            if (!$stmt->execute()) {
                throw new Exception("Erro ao confirmar recebimento");
            }

            // Registrar histórico
            $this->registrarHistorico($devolucao_id, 'enviada', 'recebida', 'anunciante', $notas_recebimento);

            // Enviar notificação ao cliente
            $this->enviarNotificacaoRecebimento($devolucao_id);

            return [
                'success' => true,
                'message' => 'Recebimento confirmado! Agora você pode processar o reembolso.'
            ];

        } catch (Exception $e) {
            error_log("Erro em confirmarRecebimentoVendedor: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao confirmar recebimento.'];
        }
    }

    /**
     * Processar reembolso via Stripe
     *
     * @param int $devolucao_id
     * @return array
     */
    public function processarReembolso($devolucao_id) {
        try {
            $devolucao = $this->obterDetalhes($devolucao_id);

            if (!$devolucao) {
                return ['success' => false, 'message' => 'Devolução não encontrada.'];
            }

            // Verificar se já foi reembolsada
            if ($devolucao['estado'] === 'reembolsada') {
                return ['success' => false, 'message' => 'Esta devolução já foi reembolsada.'];
            }

            // VALIDAÇÃO: Só processar reembolso se produto foi recebido
            if ($devolucao['estado'] !== 'recebida') {
                return ['success' => false, 'message' => 'Você precisa confirmar o recebimento do produto antes de processar o reembolso.'];
            }

            // Verificar se tem payment_intent
            if (empty($devolucao['payment_intent_id'])) {
                return ['success' => false, 'message' => 'ID de pagamento não encontrado.'];
            }

            // Configurar Stripe
            require_once __DIR__ . '/../../vendor/autoload.php';
            \Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

            // Criar reembolso
            $refund = \Stripe\Refund::create([
                'payment_intent' => $devolucao['payment_intent_id'],
                'amount' => intval($devolucao['valor_reembolso'] * 100), // Converter para centavos
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'devolucao_id' => $devolucao_id,
                    'codigo_devolucao' => $devolucao['codigo_devolucao'],
                    'codigo_encomenda' => $devolucao['codigo_encomenda']
                ]
            ]);

            // Atualizar devolução
            $sql = "UPDATE devolucoes
                    SET reembolso_stripe_id = ?,
                        reembolso_status = ?,
                        estado = 'reembolsada',
                        data_reembolso = NOW()
                    WHERE id = ?";

            $stmt = $this->conn->prepare($sql);
            $refund_status = $refund->status;
            $stmt->bind_param('ssi', $refund->id, $refund_status, $devolucao_id);
            $stmt->execute();

            // Reverter comissão e vendas
            $this->reverterVendaEComissao($devolucao['encomenda_id'], $devolucao['valor_reembolso']);

            // Registrar histórico
            $this->registrarHistorico(
                $devolucao_id,
                $devolucao['estado'],
                'reembolsada',
                'sistema',
                "Reembolso Stripe ID: {$refund->id} - Status: {$refund->status}"
            );

            // Enviar notificação
            $this->enviarNotificacaoReembolso($devolucao_id);

            return [
                'success' => true,
                'message' => 'Reembolso processado com sucesso!',
                'refund_id' => $refund->id,
                'status' => $refund->status
            ];

        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log("Erro Stripe em processarReembolso: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar reembolso: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("Erro em processarReembolso: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar reembolso.'
            ];
        }
    }

    /**
     * Reverter venda e comissão no sistema
     *
     * @param int $encomenda_id
     * @param float $valor_reembolso
     */
    private function reverterVendaEComissao($encomenda_id, $valor_reembolso) {
        try {
            // Atualizar estado da encomenda para "Devolvido"
            $sqlEncomenda = "UPDATE encomendas SET estado = 'Devolvido' WHERE id = ?";
            $stmtEncomenda = $this->conn->prepare($sqlEncomenda);
            $stmtEncomenda->bind_param('i', $encomenda_id);
            $stmtEncomenda->execute();

            // Marcar venda como reembolsada
            $sql = "UPDATE vendas SET reembolsada = 1, valor_reembolso = ? WHERE encomenda_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('di', $valor_reembolso, $encomenda_id);
            $stmt->execute();

            // Reverter comissão (6%)
            $comissao = $valor_reembolso * 0.06;
            $sqlRendimento = "INSERT INTO rendimento (tipo, valor, data_registo, descricao)
                              VALUES ('reembolso', ?, NOW(), CONCAT('Reversão de comissão - Encomenda ID: ', ?))";
            $stmtRendimento = $this->conn->prepare($sqlRendimento);
            $comissao_negativa = -$comissao;
            $stmtRendimento->bind_param('di', $comissao_negativa, $encomenda_id);
            $stmtRendimento->execute();

        } catch (Exception $e) {
            error_log("Erro ao reverter venda/comissão: " . $e->getMessage());
        }
    }

    /**
     * Atualizar estado da devolução
     *
     * @param int $devolucao_id
     * @param string $novo_estado
     * @param string $observacao
     * @return bool
     */
    public function atualizarEstadoDevolucao($devolucao_id, $novo_estado, $observacao = '') {
        $sql = "UPDATE devolucoes SET estado = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('si', $novo_estado, $devolucao_id);

        if ($stmt->execute()) {
            $this->registrarHistorico($devolucao_id, null, $novo_estado, 'sistema', $observacao);
            return true;
        }

        return false;
    }

    /**
     * Gerar código único de devolução
     *
     * @return string
     */
    private function gerarCodigoDevolucao() {
        $prefix = 'DEV';
        $timestamp = date('YmdHis');
        $random = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }

    /**
     * Registrar no histórico de devoluções
     *
     * @param int $devolucao_id
     * @param string|null $estado_anterior
     * @param string $estado_novo
     * @param string $alterado_por
     * @param string $observacao
     */
    private function registrarHistorico($devolucao_id, $estado_anterior, $estado_novo, $alterado_por, $observacao = '') {
        $sql = "INSERT INTO historico_devolucoes (devolucao_id, estado_anterior, estado_novo, alterado_por, observacao)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('issss', $devolucao_id, $estado_anterior, $estado_novo, $alterado_por, $observacao);
        $stmt->execute();
    }

    /**
     * Enviar notificação de solicitação de devolução
     */
    private function enviarNotificacaoSolicitacao($devolucao_id) {
        try {
            $emailService = new EmailService();
            $devolucao = $this->obterDetalhes($devolucao_id);

            // Email para cliente
            $emailService->enviarEmail(
                $devolucao['cliente_email'],
                'devolucao_solicitada',
                $devolucao,
                $devolucao['cliente_id'],
                'cliente'
            );

            // Email para anunciante
            $emailService->enviarEmail(
                $devolucao['anunciante_email'],
                'nova_devolucao_anunciante',
                $devolucao,
                $devolucao['anunciante_id'],
                'anunciante'
            );

        } catch (Exception $e) {
            error_log("Erro ao enviar notificação de solicitação: " . $e->getMessage());
        }
    }

    /**
     * Enviar notificação de aprovação
     */
    private function enviarNotificacaoAprovacao($devolucao_id) {
        try {
            $emailService = new EmailService();
            $devolucao = $this->obterDetalhes($devolucao_id);

            // 1. Enviar email
            $emailService->enviarEmail(
                $devolucao['cliente_email'],
                'devolucao_aprovada',
                $devolucao,
                $devolucao['cliente_id'],
                'cliente'
            );

            // 2. Criar notificação in-app para o cliente
            $this->criarNotificacaoSistema(
                $devolucao['cliente_id'],
                'devolucao_aprovada',
                "✅ Devolução Aprovada",
                "Sua devolução #{$devolucao['codigo_devolucao']} foi aprovada! Por favor, envie o produto de volta e confirme o envio no sistema.",
                $devolucao_id
            );

        } catch (Exception $e) {
            error_log("Erro ao enviar notificação de aprovação: " . $e->getMessage());
        }
    }

    /**
     * Enviar notificação de rejeição
     */
    private function enviarNotificacaoRejeicao($devolucao_id) {
        try {
            $emailService = new EmailService();
            $devolucao = $this->obterDetalhes($devolucao_id);

            $emailService->enviarEmail(
                $devolucao['cliente_email'],
                'devolucao_rejeitada',
                $devolucao,
                $devolucao['cliente_id'],
                'cliente'
            );

        } catch (Exception $e) {
            error_log("Erro ao enviar notificação de rejeição: " . $e->getMessage());
        }
    }

    /**
     * Enviar notificação de reembolso
     */
    private function enviarNotificacaoReembolso($devolucao_id) {
        try {
            $emailService = new EmailService();
            $devolucao = $this->obterDetalhes($devolucao_id);

            $emailService->enviarEmail(
                $devolucao['cliente_email'],
                'reembolso_processado',
                $devolucao,
                $devolucao['cliente_id'],
                'cliente'
            );

        } catch (Exception $e) {
            error_log("Erro ao enviar notificação de reembolso: " . $e->getMessage());
        }
    }

    /**
     * Enviar notificação de envio confirmado (para anunciante)
     */
    private function enviarNotificacaoEnvio($devolucao_id) {
        try {
            $emailService = new EmailService();
            $devolucao = $this->obterDetalhes($devolucao_id);

            // 1. Enviar email para anunciante
            $emailService->enviarEmail(
                $devolucao['anunciante_email'],
                'devolucao_enviada',
                $devolucao,
                $devolucao['anunciante_id'],
                'anunciante'
            );

            // 2. Criar notificação in-app para o anunciante
            $rastreio = !empty($devolucao['codigo_rastreio']) ? " (Rastreio: {$devolucao['codigo_rastreio']})" : "";
            $this->criarNotificacaoSistema(
                $devolucao['anunciante_id'],
                'devolucao_enviada',
                "📦 Cliente Enviou Produto",
                "O cliente confirmou o envio do produto da devolução #{$devolucao['codigo_devolucao']}{$rastreio}. Aguarde recebimento e confirme no sistema.",
                $devolucao_id
            );

        } catch (Exception $e) {
            error_log("Erro ao enviar notificação de envio: " . $e->getMessage());
        }
    }

    /**
     * Enviar notificação de recebimento confirmado (para cliente)
     */
    private function enviarNotificacaoRecebimento($devolucao_id) {
        try {
            $emailService = new EmailService();
            $devolucao = $this->obterDetalhes($devolucao_id);

            // 1. Enviar email para cliente
            $emailService->enviarEmail(
                $devolucao['cliente_email'],
                'devolucao_recebida',
                $devolucao,
                $devolucao['cliente_id'],
                'cliente'
            );

            // 2. Criar notificação in-app para o cliente
            $this->criarNotificacaoSistema(
                $devolucao['cliente_id'],
                'devolucao_recebida',
                "✅ Produto Recebido",
                "O vendedor confirmou o recebimento do produto da devolução #{$devolucao['codigo_devolucao']}. O reembolso será processado em breve (5-10 dias úteis).",
                $devolucao_id
            );

        } catch (Exception $e) {
            error_log("Erro ao enviar notificação de recebimento: " . $e->getMessage());
        }
    }

    /**
     * Criar notificação in-app no sistema WeGreen
     * (Armazena na própria tabela devolucoes, será exibida pelo sistema de notificações)
     *
     * @param int $utilizador_id ID do utilizador que receberá a notificação
     * @param string $tipo Tipo da notificação (devolucao_aprovada, devolucao_enviada, etc.)
     * @param string $titulo Título da notificação
     * @param string $mensagem Mensagem da notificação
     * @param int $referencia_id ID da devolução relacionada
     */
    private function criarNotificacaoSistema($utilizador_id, $tipo, $titulo, $mensagem, $referencia_id) {
        try {
            // O sistema WeGreen exibe notificações baseadas nas tabelas devolucoes e encomendas
            // Não há tabela separada de notificações, então apenas logamos
            error_log("[NOTIFICAÇÃO SISTEMA] User: $utilizador_id | Tipo: $tipo | Título: $titulo | Devolução ID: $referencia_id");

            // A notificação será exibida automaticamente pelo ModelNotifications
            // que consulta a tabela devolucoes com estados aprovada/enviada/recebida/reembolsada

        } catch (Exception $e) {
            error_log("Erro ao criar notificação in-app: " . $e->getMessage());
        }
    }

    /**
     * Obter estatísticas de devoluções por anunciante
     *
     * @param int $anunciante_id
     * @return array
     */
    public function obterEstatisticas($anunciante_id) {
        $sql = "SELECT * FROM stats_devolucoes_anunciante WHERE anunciante_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return [
            'total_devolucoes' => 0,
            'pendentes' => 0,
            'aprovadas' => 0,
            'rejeitadas' => 0,
            'reembolsadas' => 0,
            'valor_total_reembolsado' => 0
        ];
    }
}
