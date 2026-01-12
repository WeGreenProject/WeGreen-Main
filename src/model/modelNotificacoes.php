<?php
/**
 * Model Notificacoes - Gestão de preferências de notificações
 *
 * Responsável por gerir as preferências de notificações dos utilizadores
 * (clientes e anunciantes) na base de dados.
 */

require_once 'connection.php';

class Notificacoes {

    /**
     * Obter preferências de notificações de um utilizador
     *
     * @param int $user_id ID do utilizador
     * @param string $tipo_user 'cliente' ou 'anunciante'
     * @return array|null Array com preferências ou null se não existir
     */
    public function getPreferencias($user_id, $tipo_user) {
        global $conn;

        $sql = "SELECT * FROM notificacoes_preferencias
                WHERE user_id = ? AND tipo_user = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        // Se não existir, criar preferências padrão
        return $this->criarPreferenciasDefault($user_id, $tipo_user);
    }

    /**
     * Criar preferências padrão para um novo utilizador
     *
     * @param int $user_id ID do utilizador
     * @param string $tipo_user 'cliente' ou 'anunciante'
     * @return array|null Preferências criadas
     */
    private function criarPreferenciasDefault($user_id, $tipo_user) {
        global $conn;

        $sql = "INSERT INTO notificacoes_preferencias
                (user_id, tipo_user, email_confirmacao, email_processando,
                 email_enviado, email_entregue, email_cancelamento,
                 email_novas_encomendas_anunciante, email_encomendas_urgentes)
                VALUES (?, ?, 1, 1, 1, 1, 1, 1, 1)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);

        if ($stmt->execute()) {
            return $this->getPreferencias($user_id, $tipo_user);
        }

        return null;
    }

    /**
     * Atualizar preferências de notificações
     *
     * @param int $user_id ID do utilizador
     * @param string $tipo_user 'cliente' ou 'anunciante'
     * @param array $preferencias Array com as preferências a atualizar
     * @return bool True se atualizado com sucesso
     */
    public function atualizarPreferencias($user_id, $tipo_user, $preferencias) {
        global $conn;

        // Verificar se já existem preferências
        $existing = $this->getPreferencias($user_id, $tipo_user);

        if (!$existing) {
            // Criar se não existir
            $this->criarPreferenciasDefault($user_id, $tipo_user);
        }

        // Construir query de UPDATE dinâmica
        $updates = [];
        $types = "";
        $values = [];

        // Campos permitidos
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

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);

        return $stmt->execute();
    }

    /**
     * Verificar se uma notificação específica está ativa
     *
     * @param int $user_id ID do utilizador
     * @param string $tipo_user 'cliente' ou 'anunciante'
     * @param string $tipo_notificacao Nome do campo (ex: 'email_confirmacao')
     * @return bool True se ativa
     */
    public function isNotificacaoAtiva($user_id, $tipo_user, $tipo_notificacao) {
        $preferencias = $this->getPreferencias($user_id, $tipo_user);

        if (!$preferencias) {
            return true; // Default: enviar se não configurado
        }

        return isset($preferencias[$tipo_notificacao]) && $preferencias[$tipo_notificacao] == 1;
    }

    /**
     * Desativar todas as notificações de um utilizador
     *
     * @param int $user_id ID do utilizador
     * @param string $tipo_user 'cliente' ou 'anunciante'
     * @return bool True se atualizado com sucesso
     */
    public function desativarTodasNotificacoes($user_id, $tipo_user) {
        global $conn;

        $sql = "UPDATE notificacoes_preferencias
                SET email_confirmacao = 0,
                    email_processando = 0,
                    email_enviado = 0,
                    email_entregue = 0,
                    email_cancelamento = 0,
                    email_novas_encomendas_anunciante = 0,
                    email_encomendas_urgentes = 0
                WHERE user_id = ? AND tipo_user = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);

        return $stmt->execute();
    }

    /**
     * Ativar todas as notificações de um utilizador
     *
     * @param int $user_id ID do utilizador
     * @param string $tipo_user 'cliente' ou 'anunciante'
     * @return bool True se atualizado com sucesso
     */
    public function ativarTodasNotificacoes($user_id, $tipo_user) {
        global $conn;

        $sql = "UPDATE notificacoes_preferencias
                SET email_confirmacao = 1,
                    email_processando = 1,
                    email_enviado = 1,
                    email_entregue = 1,
                    email_cancelamento = 1,
                    email_novas_encomendas_anunciante = 1,
                    email_encomendas_urgentes = 1
                WHERE user_id = ? AND tipo_user = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $tipo_user);

        return $stmt->execute();
    }

    /**
     * Obter estatísticas de preferências (para admin)
     *
     * @return array Estatísticas de ativação por tipo
     */
    public function getEstatisticasPreferencias() {
        global $conn;

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

        $result = $conn->query($sql);
        $stats = [];

        while ($row = $result->fetch_assoc()) {
            $stats[$row['tipo_user']] = $row;
        }

        return $stats;
    }

    /**
     * Eliminar preferências de um utilizador (usado ao eliminar conta)
     *
     * @param int $user_id ID do utilizador
     * @return bool True se eliminado com sucesso
     */
    public function eliminarPreferencias($user_id) {
        global $conn;

        $sql = "DELETE FROM notificacoes_preferencias WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);

        return $stmt->execute();
    }
}
