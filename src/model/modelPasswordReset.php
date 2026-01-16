<?php
/**
 * Model para gestão de recuperação de password
 * Sistema WeGreen Marketplace
 *
 * Responsável por:
 * - Criar tokens de recuperação
 * - Validar tokens
 * - Atualizar passwords
 */

require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class PasswordReset {

    /**
     * Solicita recuperação de password
     * Cria token e envia email
     *
     * @param string $email Email do utilizador
     * @return array Resultado da operação
     */
    public function solicitarRecuperacao($email) {
        global $conn;

        $email = trim($email);

        // Verificar se email existe
        $stmt = $conn->prepare("SELECT id, nome FROM Utilizadores WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return [
                'flag' => false,
                'msg' => 'Email não encontrado no sistema.'
            ];
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Gerar token único
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);

        // Token expira em 1 hora
        $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Obter IP e User Agent
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Invalidar tokens anteriores deste utilizador
        $stmt = $conn->prepare("UPDATE password_resets SET usado = 1 WHERE utilizador_id = ? AND usado = 0");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $stmt->close();

        // Inserir novo token
        $stmt = $conn->prepare("INSERT INTO password_resets (utilizador_id, email, token, expira_em, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user['id'], $email, $token_hash, $expira_em, $ip_address, $user_agent);

        if (!$stmt->execute()) {
            $stmt->close();
            return [
                'flag' => false,
                'msg' => 'Erro ao criar pedido de recuperação: ' . $stmt->error
            ];
        }

        $stmt->close();

        // Construir link de reset
        $base_url = 'http://localhost/WeGreen-Main';
        $reset_link = $base_url . '/reset_password.html?token=' . urlencode($token);

        // Enviar email
        try {
            $emailService = new EmailService();
            $emailEnviado = $emailService->sendResetPassword($email, $user['nome'], $reset_link);

            if ($emailEnviado) {
                return [
                    'flag' => true,
                    'msg' => 'Email de recuperação enviado! Verifique a sua caixa de entrada.'
                ];
            } else {
                return [
                    'flag' => false,
                    'msg' => 'Erro ao enviar email de recuperação.'
                ];
            }
        } catch (Exception $e) {
            error_log("Erro ao enviar email de recuperação: " . $e->getMessage());
            return [
                'flag' => false,
                'msg' => 'Erro ao enviar email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Valida token de recuperação
     *
     * @param string $token Token a validar
     * @return array Resultado da validação
     */
    public function validarToken($token) {
        global $conn;

        $token_hash = hash('sha256', $token);
        $agora = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("
            SELECT pr.id, pr.utilizador_id, pr.email, u.nome
            FROM password_resets pr
            JOIN Utilizadores u ON pr.utilizador_id = u.id
            WHERE pr.token = ?
            AND pr.usado = 0
            AND pr.expira_em > ?
            LIMIT 1
        ");
        $stmt->bind_param("ss", $token_hash, $agora);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return [
                'flag' => false,
                'msg' => 'Token inválido ou expirado.'
            ];
        }

        $data = $result->fetch_assoc();
        $stmt->close();

        return [
            'flag' => true,
            'msg' => 'Token válido.',
            'data' => $data
        ];
    }

    /**
     * Redefine password usando token válido
     *
     * @param string $token Token de recuperação
     * @param string $nova_password Nova password
     * @return array Resultado da operação
     */
    public function redefinirPassword($token, $nova_password) {
        global $conn;

        // Validar token primeiro
        $validacao = $this->validarToken($token);

        if (!$validacao['flag']) {
            return $validacao;
        }

        $token_hash = hash('sha256', $token);
        $utilizador_id = $validacao['data']['utilizador_id'];

        // Hash da nova password
        $password_hash = md5($nova_password);

        // Atualizar password do utilizador
        $stmt = $conn->prepare("UPDATE Utilizadores SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $utilizador_id);

        if (!$stmt->execute()) {
            $stmt->close();
            return [
                'flag' => false,
                'msg' => 'Erro ao atualizar password: ' . $stmt->error
            ];
        }

        $stmt->close();

        // Marcar token como usado
        $usado_em = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE password_resets SET usado = 1, usado_em = ? WHERE token = ?");
        $stmt->bind_param("ss", $usado_em, $token_hash);
        $stmt->execute();
        $stmt->close();

        return [
            'flag' => true,
            'msg' => 'Password redefinida com sucesso! Pode agora fazer login com a nova password.'
        ];
    }

    /**
     * Limpa tokens expirados (manutenção)
     * Deve ser executado periodicamente
     *
     * @return int Número de tokens removidos
     */
    public function limparTokensExpirados() {
        global $conn;

        $agora = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("DELETE FROM password_resets WHERE expira_em < ? OR usado = 1");
        $stmt->bind_param("s", $agora);
        $stmt->execute();

        $removidos = $stmt->affected_rows;
        $stmt->close();

        return $removidos;
    }
}
?>
