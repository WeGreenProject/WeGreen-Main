<?php

require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class PasswordReset {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function solicitarRecuperacao($email) {

        $email = trim($email);

        $stmt = $this->conn->prepare("SELECT id, nome FROM Utilizadores WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return json_encode([
                'flag' => false,
                'msg' => 'Email não encontrado no sistema.'
            ], JSON_UNESCAPED_UNICODE);
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);

        
        $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));

        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        
        $stmt = $this->conn->prepare("UPDATE password_resets SET usado = 1 WHERE utilizador_id = ? AND usado = 0");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $stmt->close();

        
        $stmt = $this->conn->prepare("INSERT INTO password_resets (utilizador_id, email, token, expira_em, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $user['id'], $email, $token_hash, $expira_em, $ip_address, $user_agent);

        if (!$stmt->execute()) {
            $stmt->close();
            return json_encode([
                'flag' => false,
                'msg' => 'Erro ao criar pedido de recuperação: ' . $stmt->error
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();

        
        $base_url = 'http://localhost/WeGreen-Main';
        $reset_link = $base_url . '/reset_password.html?token=' . urlencode($token);

        
        try {
            $emailService = new EmailService($this->conn);
            $emailEnviado = $emailService->sendResetPassword($email, $user['nome'], $reset_link);

            if ($emailEnviado) {
                return json_encode([
                    'flag' => true,
                    'msg' => 'Email de recuperação enviado! Verifique a sua caixa de entrada.'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode([
                    'flag' => false,
                    'msg' => 'Erro ao enviar email de recuperação.'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            return json_encode([
                'flag' => false,
                'msg' => 'Erro ao enviar email: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    function validarToken($token) {
        try {

        $token_hash = hash('sha256', $token);
        $agora = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("
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
            return json_encode([
                'flag' => false,
                'msg' => 'Token inválido ou expirado.'
            ], JSON_UNESCAPED_UNICODE);
        }

        $data = $result->fetch_assoc();
        $stmt->close();

        return json_encode([
            'flag' => true,
            'msg' => 'Token válido.',
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function redefinirPassword($token, $nova_password) {
        try {

        
        $validacao = json_decode($this->validarToken($token), true);

        if (!$validacao['flag']) {
            return json_encode($validacao, JSON_UNESCAPED_UNICODE);
        }

        $token_hash = hash('sha256', $token);
        $utilizador_id = $validacao['data']['utilizador_id'];

        $password_hash = md5($nova_password);

        $stmt = $this->conn->prepare("UPDATE Utilizadores SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $utilizador_id);

        if (!$stmt->execute()) {
            $stmt->close();
            return json_encode([
                'flag' => false,
                'msg' => 'Erro ao atualizar password: ' . $stmt->error
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();

        $usado_em = date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("UPDATE password_resets SET usado = 1, usado_em = ? WHERE token = ?");
        $stmt->bind_param("ss", $usado_em, $token_hash);
        $stmt->execute();
        $stmt->close();

        return json_encode([
            'flag' => true,
            'msg' => 'Password redefinida com sucesso! Pode agora fazer login com a nova password.'
        ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function redefinirPasswordComValidacao($token, $nova_password) {
        try {

        
        if (strlen($nova_password) < 6) {
            return json_encode([
                'flag' => false,
                'msg' => 'A password deve ter pelo menos 6 caracteres.'
            ], JSON_UNESCAPED_UNICODE);
        }

        
        return $this->redefinirPassword($token, $nova_password);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function limparTokensExpirados() {
        try {

        $agora = date('Y-m-d H:i:s');

        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE expira_em < ? OR usado = 1");
        $stmt->bind_param("s", $agora);
        $stmt->execute();

        $removidos = $stmt->affected_rows;
        $stmt->close();

        return $removidos;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
