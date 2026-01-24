<?php
require_once 'connection.php';

class ModelNotifications {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        if (!$this->conn) {
            error_log("[ModelNotifications] ERRO: Falha na conexão com banco de dados");
            throw new Exception("Falha na conexão com banco de dados");
        }
        error_log("[ModelNotifications] Conexão estabelecida com sucesso");
    }

    /**
     * Contar notificações pendentes para o anunciante
     * - Encomendas pendentes (não processadas)
     * - Devoluções solicitadas
     *
     * @param int $anunciante_id
     * @return int
     */
    public function contarNotificacoesAnunciante($anunciante_id) {
        error_log("[ModelNotifications] contarNotificacoesAnunciante - ID: $anunciante_id");
        $count = 0;

        // 1. Contar encomendas pendentes (excluindo lidas)
        $sql_encomendas = "SELECT COUNT(*) as total
                          FROM Encomendas e
                          INNER JOIN produtos p ON e.produto_id = p.Produto_id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                          WHERE p.anunciante_id = ?
                          AND e.estado IN ('Pendente', 'Processando')
                          AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_encomendas);
        if (!$stmt) {
            error_log("[ModelNotifications] ERRO prepare encomendas: " . $this->conn->error);
            return 0;
        }
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $encomendas_count = (int)$row['total'];
        error_log("[ModelNotifications] Encomendas pendentes: $encomendas_count");
        $count += $encomendas_count;

        // 2. Contar devoluções solicitadas e enviadas (excluindo lidas)
        $sql_devolucoes = "SELECT COUNT(*) as total
                          FROM devolucoes d
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                          WHERE d.anunciante_id = ?
                          AND d.estado IN ('solicitada', 'enviada')
                          AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql_devolucoes);
        if (!$stmt) {
            error_log("[ModelNotifications] ERRO prepare devolucoes: " . $this->conn->error);
            return $count;
        }
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $devolucoes_count = (int)$row['total'];
        error_log("[ModelNotifications] Devoluções (solicitadas e enviadas): $devolucoes_count");
        $count += $devolucoes_count;

        error_log("[ModelNotifications] Total notificações anunciante: $count");
        return $count;
    }

    /**
     * Listar notificações detalhadas para o anunciante
     *
     * @param int $anunciante_id
     * @return array
     */
    public function listarNotificacoesAnunciante($anunciante_id) {
        $notificacoes = [];

        // 1. Encomendas pendentes
        $sql_encomendas = "SELECT
                            e.id,
                            e.codigo_encomenda,
                            e.estado,
                            e.data_envio,
                            p.nome as produto_nome,
                            u.nome as cliente_nome
                          FROM Encomendas e
                          INNER JOIN produtos p ON e.produto_id = p.Produto_id
                          INNER JOIN Utilizadores u ON e.cliente_id = u.id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'encomenda' AND nl.referencia_id = e.id)
                          WHERE p.anunciante_id = ?
                          AND e.estado IN ('Pendente', 'Processando')
                          AND nl.id IS NULL
                          ORDER BY e.data_envio DESC
                          LIMIT 5";

        $stmt = $this->conn->prepare($sql_encomendas);
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'titulo' => 'Encomenda ' . $row['estado'],
                'mensagem' => 'Encomenda #' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'gestaoEncomendasAnunciante.php'
            ];
        }

        // 2. Devoluções (solicitadas e enviadas)
        $sql_devolucoes = "SELECT
                            d.id,
                            d.codigo_devolucao,
                            d.estado,
                            d.data_solicitacao,
                            d.codigo_rastreio,
                            p.nome as produto_nome,
                            u.nome as cliente_nome
                          FROM devolucoes d
                          INNER JOIN produtos p ON d.produto_id = p.Produto_id
                          INNER JOIN Utilizadores u ON d.cliente_id = u.id
                          LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                          WHERE d.anunciante_id = ?
                          AND d.estado IN ('solicitada', 'enviada')
                          AND nl.id IS NULL
                          ORDER BY d.data_solicitacao DESC
                          LIMIT 10";

        $stmt = $this->conn->prepare($sql_devolucoes);
        $stmt->bind_param('ii', $anunciante_id, $anunciante_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $icone = '📦';
            $titulo = 'Devolução Solicitada';
            $mensagem = 'Devolução #' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'];

            // Personalizar para estado "enviada"
            if ($row['estado'] === 'enviada') {
                $icone = '🚚';
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
                'link' => 'gestaoDevolucoesAnunciante.php'
            ];
        }

        // Ordenar por data
        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return array_slice($notificacoes, 0, 10);
    }

    /**
     * Contar notificações pendentes para o cliente
     * - Atualizações de encomendas
     * - Devoluções aprovadas/rejeitadas
     *
     * @param int $cliente_id
     * @return int
     */
    public function contarNotificacoesCliente($cliente_id) {
        $count = 0;

        // 1. Encomendas com atualizações recentes (últimas 7 dias) - excluindo lidas
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

        // 2. Devoluções aprovadas/enviadas/recebidas/rejeitadas/reembolsadas (últimas 14 dias) - excluindo lidas
        $sql = "SELECT COUNT(*) as total
                FROM devolucoes d
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'devolucao' AND nl.referencia_id = d.id)
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'enviada', 'recebida', 'rejeitada', 'reembolsada')
                AND DATE(d.data_solicitacao) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];

        return $count;
    }

    /**
     * Listar notificações detalhadas para o cliente
     *
     * @param int $cliente_id
     * @return array
     */
    public function listarNotificacoesCliente($cliente_id) {
        $notificacoes = [];

        // 1. Encomendas recentes (excluindo lidas)
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
            $icone = '📦';
            $titulo = 'Encomenda ' . $row['estado'];

            if ($row['estado'] == 'Enviado') {
                $icone = '🚚';
                $titulo = 'Encomenda Enviada';
            } elseif ($row['estado'] == 'Entregue') {
                $icone = '✅';
                $titulo = 'Encomenda Entregue';
            }

            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => '#' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'minhasEncomendas.php'
            ];
        }

        // 2. Devoluções recentes (excluindo lidas)
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
                AND d.estado IN ('aprovada', 'enviada', 'recebida', 'rejeitada', 'reembolsada')
                AND DATE(d.data_solicitacao) >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                AND nl.id IS NULL
                ORDER BY d.data_solicitacao DESC
                LIMIT 5";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $icone = '📦';
            $titulo = 'Devolução ' . ucfirst($row['estado']);
            $mensagem = '#' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'];

            // Personalizar ícone e mensagem por estado
            switch($row['estado']) {
                case 'aprovada':
                    $icone = '✅';
                    $titulo = 'Devolução Aprovada';
                    $mensagem .= ' - Por favor, envie o produto e confirme no sistema.';
                    break;
                case 'enviada':
                    $icone = '🚚';
                    $titulo = 'Devolução Enviada';
                    $mensagem .= ' - Aguardando confirmação do vendedor.';
                    break;
                case 'recebida':
                    $icone = '✅';
                    $titulo = 'Produto Recebido';
                    $mensagem .= ' - Reembolso será processado em 5-10 dias úteis.';
                    break;
                case 'rejeitada':
                    $icone = '❌';
                    $titulo = 'Devolução Rejeitada';
                    $mensagem .= !empty($row['notas_anunciante']) ? ' - ' . $row['notas_anunciante'] : '';
                    break;
                case 'reembolsada':
                    $icone = '💰';
                    $titulo = 'Reembolso Processado';
                    $mensagem .= ' - Reembolso concluído!';
                    break;
            }

            $notificacoes[] = [
                'tipo' => 'devolucao',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => $mensagem,
                'data' => $row['data_solicitacao'],
                'link' => 'minhasEncomendas.php?tab=devolucoes'
            ];
        }

        // Ordenar por data
        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return array_slice($notificacoes, 0, 10);
    }

    /**
     * Contar notificações para admin
     * - Novos utilizadores
     * - Produtos pendentes
     *
     * @return int
     */
    public function contarNotificacoesAdmin() {
        $count = 0;
        // Admin ID fixo = 1 (ajustar conforme necessário)
        $admin_id = 1;

        // 1. Utilizadores não verificados - excluindo lidos
        $sql = "SELECT COUNT(*) as total
                FROM Utilizadores u
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'utilizador' AND nl.referencia_id = u.id)
                WHERE u.email_verificado = 0
                AND nl.id IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count += (int)$row['total'];

        return $count;
    }

    /**
     * Listar notificações para admin
     *
     * @return array
     */
    public function listarNotificacoesAdmin() {
        $notificacoes = [];

        // 1. Utilizadores não verificados
        $sql = "SELECT
                    id,
                    nome,
                    email,
                    tipo_utilizador_id,
                    data_criacao
                FROM Utilizadores
                WHERE email_verificado = 0
                ORDER BY data_criacao DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tipo_texto = $row['tipo_utilizador_id'] == 2 ? 'Cliente' : 'Anunciante';

            $notificacoes[] = [
                'tipo' => 'utilizador',
                'id' => $row['id'],
                'icone' => '👤',
                'titulo' => 'Novo ' . $tipo_texto,
                'mensagem' => $row['nome'] . ' - ' . $row['email'],
                'data' => $row['data_criacao'],
                'link' => 'gestaoCliente.php'
            ];
        }

        // 2. Produtos inativos
        $sql = "SELECT
                    p.Produto_id,
                    p.nome,
                    p.data_criacao,
                    u.nome as anunciante_nome
                FROM produtos p
                INNER JOIN Utilizadores u ON p.anunciante_id = u.id
                WHERE p.ativo = 0
                ORDER BY p.data_criacao DESC
                LIMIT 10";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = [
                'tipo' => 'produto',
                'id' => $row['Produto_id'],
                'icone' => '📦',
                'titulo' => 'Produto Inativo',
                'mensagem' => $row['nome'] . ' - ' . $row['anunciante_nome'],
                'data' => $row['data_criacao'],
                'link' => 'gestaoProdutosAdmin.php'
            ];
        }

        // Ordenar por data
        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return array_slice($notificacoes, 0, 10);
    }

    /**
     * Marcar notificação como lida
     *
     * @param int $utilizador_id
     * @param string $tipo_notificacao
     * @param int $referencia_id
     * @return bool
     */
    public function marcarComoLida($utilizador_id, $tipo_notificacao, $referencia_id) {
        $sql = "INSERT INTO notificacoes_lidas (utilizador_id, tipo_notificacao, referencia_id)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE data_leitura = CURRENT_TIMESTAMP";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('isi', $utilizador_id, $tipo_notificacao, $referencia_id);

        if ($stmt->execute()) {
            error_log("[ModelNotifications] Notificação marcada como lida: user=$utilizador_id, tipo=$tipo_notificacao, ref=$referencia_id");
            return true;
        }

        error_log("[ModelNotifications] ERRO ao marcar como lida: " . $stmt->error);
        return false;
    }

    /**
     * Marcar todas como lidas
     *
     * @param int $utilizador_id
     * @param int $tipo_utilizador
     * @return bool
     */
    public function marcarTodasComoLidas($utilizador_id, $tipo_utilizador) {
        // Buscar todas as notificações atuais e marcar como lidas
        $notificacoes = [];

        switch($tipo_utilizador) {
            case 1:
                $notificacoes = $this->listarNotificacoesAdmin();
                break;
            case 2:
                $notificacoes = $this->listarNotificacoesCliente($utilizador_id);
                break;
            case 3:
                $notificacoes = $this->listarNotificacoesAnunciante($utilizador_id);
                break;
        }

        foreach ($notificacoes as $notif) {
            $this->marcarComoLida($utilizador_id, $notif['tipo'], $notif['id']);
        }

        return true;
    }

    /**
     * Listar TODAS as notificações do cliente (incluindo lidas)
     * Para a página de histórico
     *
     * @param int $cliente_id
     * @return array
     */
    public function listarTodasNotificacoesCliente($cliente_id) {
        $notificacoes = [];

        // 1. Encomendas (últimos 30 dias)
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
            $icone = '📦';
            $titulo = 'Encomenda ' . $row['estado'];

            if ($row['estado'] == 'Enviado') {
                $icone = '🚚';
                $titulo = 'Encomenda Enviada';
            } elseif ($row['estado'] == 'Entregue') {
                $icone = '✅';
                $titulo = 'Encomenda Entregue';
            }

            $notificacoes[] = [
                'tipo' => 'encomenda',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => '#' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'minhasEncomendas.php',
                'lida' => (bool)$row['lida']
            ];
        }

        // 2. Devoluções (últimos 30 dias)
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
                WHERE d.cliente_id = ?
                AND d.estado IN ('aprovada', 'rejeitada', 'reembolsada')
                AND DATE(d.data_solicitacao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY d.data_solicitacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ii', $cliente_id, $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $icone = $row['estado'] == 'aprovada' ? '✅' : '❌';
            $titulo = $row['estado'] == 'aprovada' ? 'Devolução Aprovada' : 'Devolução Rejeitada';

            $notificacoes[] = [
                'tipo' => 'devolucao',
                'id' => $row['id'],
                'icone' => $icone,
                'titulo' => $titulo,
                'mensagem' => '#' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_solicitacao'],
                'link' => 'minhasEncomendas.php?tab=devolucoes',
                'lida' => (bool)$row['lida']
            ];
        }

        // Ordenar por data
        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $notificacoes;
    }

    /**
     * Listar TODAS as notificações do anunciante (incluindo lidas)
     *
     * @param int $anunciante_id
     * @return array
     */
    public function listarTodasNotificacoesAnunciante($anunciante_id) {
        $notificacoes = [];

        // 1. Encomendas (últimos 30 dias)
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
                'icone' => '📦',
                'titulo' => 'Encomenda ' . $row['estado'],
                'mensagem' => 'Encomenda #' . $row['codigo_encomenda'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_envio'],
                'link' => 'gestaoEncomendasAnunciante.php',
                'lida' => (bool)$row['lida']
            ];
        }

        // 2. Devoluções (últimos 30 dias)
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
            $notificacoes[] = [
                'tipo' => 'devolucao',
                'id' => $row['id'],
                'icone' => '↩️',
                'titulo' => 'Devolução Solicitada',
                'mensagem' => 'Devolução #' . $row['codigo_devolucao'] . ' - ' . $row['produto_nome'],
                'data' => $row['data_solicitacao'],
                'link' => 'gestaoDevolucoesAnunciante.php',
                'lida' => (bool)$row['lida']
            ];
        }

        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $notificacoes;
    }

    /**
     * Listar TODAS as notificações do admin (incluindo lidas)
     *
     * @return array
     */
    public function listarTodasNotificacoesAdmin() {
        $notificacoes = [];
        $admin_id = 1;

        // Utilizadores não verificados (últimos 30 dias)
        $sql = "SELECT
                    u.id,
                    u.nome,
                    u.email,
                    u.data_criacao,
                    CASE WHEN nl.id IS NOT NULL THEN 1 ELSE 0 END as lida
                FROM Utilizadores u
                LEFT JOIN notificacoes_lidas nl ON (nl.utilizador_id = ? AND nl.tipo_notificacao = 'utilizador' AND nl.referencia_id = u.id)
                WHERE u.email_verificado = 0
                AND DATE(u.data_criacao) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                ORDER BY u.data_criacao DESC
                LIMIT 50";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $notificacoes[] = [
                'tipo' => 'utilizador',
                'id' => $row['id'],
                'icone' => '👤',
                'titulo' => 'Novo Utilizador',
                'mensagem' => $row['nome'] . ' (' . $row['email'] . ') - Aguarda verificação',
                'data' => $row['data_criacao'],
                'link' => 'gestaoCliente.php',
                'lida' => (bool)$row['lida']
            ];
        }

        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        return $notificacoes;
    }
}
?>
