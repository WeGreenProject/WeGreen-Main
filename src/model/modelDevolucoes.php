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
                    p.imagem as produto_imagem,
                    a.nome as anunciante_nome
                FROM devolucoes d
                INNER JOIN encomendas e ON d.encomenda_id = e.id
                INNER JOIN produtos p ON d.produto_id = p.id
                INNER JOIN anunciante a ON d.anunciante_id = a.id
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
                    e.data_envio,
                    p.nome as produto_nome,
                    p.imagem as produto_imagem,
                    c.nome as cliente_nome,
                    c.email as cliente_email
                FROM devolucoes d
                INNER JOIN encomendas e ON d.encomenda_id = e.id
                INNER JOIN produtos p ON d.produto_id = p.id
                INNER JOIN cliente c ON d.cliente_id = c.id
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

            $emailService->enviarEmail(
                $devolucao['cliente_email'],
                'devolucao_aprovada',
                $devolucao,
                $devolucao['cliente_id'],
                'cliente'
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
