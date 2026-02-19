<?php

require_once __DIR__ . '/connection.php';

class Notificacoes {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPreferencias($user_id, $tipo_user) {
        try {

        $sql = "SELECT * FROM notificacoes_preferencias
                WHERE user_id = ? AND tipo_user = ?
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }


        return $this->criarPreferenciasDefault($user_id, $tipo_user);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function criarPreferenciasDefault($user_id, $tipo_user) {
        try {

        $sql = "INSERT INTO notificacoes_preferencias
                (user_id, tipo_user, email_confirmacao, email_processando,
                 email_enviado, email_entregue, email_cancelamento,
                 email_novas_encomendas_anunciante, email_encomendas_urgentes)
                VALUES (?, ?, 1, 1, 1, 1, 1, 1, 1)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);

        if ($stmt->execute()) {
            return $this->getPreferencias($user_id, $tipo_user);
        }

        return null;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function atualizarPreferencias($user_id, $tipo_user, $preferencias) {
        try {


        $existing = $this->getPreferencias($user_id, $tipo_user);

        if (!$existing) {

            $this->criarPreferenciasDefault($user_id, $tipo_user);
        }


        $updates = [];
        $types = "";
        $values = [];


        $campos_permitidos = [
            'email_confirmacao',
            'email_processando',
            'email_enviado',
            'email_entregue',
            'email_cancelamento',
            'email_novas_encomendas_anunciante',
            'email_encomendas_urgentes'
        ];

        foreach ($preferencias as $campo => $valor) {
            if (in_array($campo, $campos_permitidos)) {
                $updates[] = "$campo = ?";
                $types .= "i";
                $values[] = $valor ? 1 : 0;
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE notificacoes_preferencias
                SET " . implode(', ', $updates) . "
                WHERE user_id = ? AND tipo_user = ?";

        $types .= "is";
        $values[] = $user_id;
        $values[] = $tipo_user;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function isNotificacaoAtiva($user_id, $tipo_user, $tipo_notificacao) {
        try {

        $preferencias = $this->getPreferencias($user_id, $tipo_user);

        if (!$preferencias) {
            return true;
        }

        return isset($preferencias[$tipo_notificacao]) && $preferencias[$tipo_notificacao] == 1;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function verificarPreferencias($user_id, $tipo_user, $template) {
        try {

        $tipoUserNormalizado = strtolower((string)$tipo_user) === 'anunciante' ? 'anunciante' : 'cliente';

        $mapaTemplateParaPreferencia = [
            'confirmacao_encomenda' => 'email_confirmacao',
            'status_processando' => 'email_processando',
            'status_enviado' => 'email_enviado',
            'status_entregue' => 'email_entregue',
            'cancelamento' => 'email_cancelamento',
            'nova_encomenda_anunciante' => 'email_novas_encomendas_anunciante',
            'encomendas_pendentes_urgentes' => 'email_encomendas_urgentes'
        ];

        if (!isset($mapaTemplateParaPreferencia[$template])) {
            return true;
        }

        $chavePreferencia = $mapaTemplateParaPreferencia[$template];
        return $this->isNotificacaoAtiva($user_id, $tipoUserNormalizado, $chavePreferencia);
        } catch (Exception $e) {
            return true;
        }
    }

    public function desativarTodasNotificacoes($user_id, $tipo_user) {
        try {

        $sql = "UPDATE notificacoes_preferencias
                SET email_confirmacao = 0,
                    email_processando = 0,
                    email_enviado = 0,
                    email_entregue = 0,
                    email_cancelamento = 0,
                    email_novas_encomendas_anunciante = 0,
                    email_encomendas_urgentes = 0
                WHERE user_id = ? AND tipo_user = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);

        return $stmt->execute();
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function ativarTodasNotificacoes($user_id, $tipo_user) {
        try {

        $sql = "UPDATE notificacoes_preferencias
                SET email_confirmacao = 1,
                    email_processando = 1,
                    email_enviado = 1,
                    email_entregue = 1,
                    email_cancelamento = 1,
                    email_novas_encomendas_anunciante = 1,
                    email_encomendas_urgentes = 1
                WHERE user_id = ? AND tipo_user = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);

        return $stmt->execute();
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getEstatisticasPreferencias() {
        try {

        $sql = "SELECT
                    tipo_user,
                    SUM(email_confirmacao) as confirmacao_ativas,
                    SUM(email_processando) as processando_ativas,
                    SUM(email_enviado) as enviado_ativas,
                    SUM(email_entregue) as entregue_ativas,
                    SUM(email_cancelamento) as cancelamento_ativas,
                    SUM(email_novas_encomendas_anunciante) as novas_encomendas_ativas,
                    SUM(email_encomendas_urgentes) as urgentes_ativas,
                    COUNT(*) as total_users
                FROM notificacoes_preferencias
                GROUP BY tipo_user";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
        $stats = [];

        while ($row = $result->fetch_assoc()) {
            $stats[$row['tipo_user']] = $row;
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return $stats;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function eliminarPreferencias($user_id) {
        try {

        $sql = "DELETE FROM notificacoes_preferencias WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);

        return $stmt->execute();
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
