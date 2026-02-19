<?php

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class VerificarEmail {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function verificarToken($token) {
        try {

        $stmt = $this->conn->prepare("
            SELECT id, nome, apelido, email, email_verificado, token_expira_em
            FROM Utilizadores
            WHERE token_verificacao = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return json_encode([
                'flag' => false,
                'msg' => 'Token de verificação inválido ou já utilizado.'
            ], JSON_UNESCAPED_UNICODE);
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user['email_verificado'] == 1) {
            return json_encode([
                'flag' => false,
                'msg' => 'Este email já foi verificado anteriormente.'
            ], JSON_UNESCAPED_UNICODE);
        }


        $agora = new DateTime();
        $expira = new DateTime($user['token_expira_em']);

        if ($agora > $expira) {
            return json_encode([
                'flag' => false,
                'msg' => 'O link de verificação expirou. Por favor, solicite um novo email de verificação.'
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt = $this->conn->prepare("
            UPDATE Utilizadores
            SET email_verificado = 1,
                token_verificacao = NULL,
                token_expira_em = NULL
            WHERE id = ?
        ");
        $stmt->bind_param("i", $user['id']);

        if ($stmt->execute()) {
            $stmt->close();
            return json_encode([
                'flag' => true,
                'msg' => 'Email verificado com sucesso! A sua conta está agora ativa.'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            $stmt->close();
            return json_encode([
                'flag' => false,
                'msg' => 'Erro ao verificar email. Tente novamente.'
            ], JSON_UNESCAPED_UNICODE);
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function reenviarVerificacao($email) {

        $stmt = $this->conn->prepare("
            SELECT id, nome, apelido, email, email_verificado
            FROM Utilizadores
            WHERE email = ?
            LIMIT 1
        ");
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

        if ($user['email_verificado'] == 1) {
            return json_encode([
                'flag' => false,
                'msg' => 'Este email já está verificado. Pode fazer login normalmente.'
            ], JSON_UNESCAPED_UNICODE);
        }

        $token = bin2hex(random_bytes(32));
        $expira_em = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $stmt = $this->conn->prepare("
            UPDATE Utilizadores
            SET token_verificacao = ?,
                token_expira_em = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $token, $expira_em, $user['id']);
        $stmt->execute();
        $stmt->close();

        try {
            $emailService = new EmailService($this->conn);
            $nomeCompleto = $user['nome'] . ' ' . $user['apelido'];
            $link_verificacao = 'http://localhost/WeGreen-Main/verificar_email.html?token=' . urlencode($token);

            $emailEnviado = $emailService->sendVerificacaoEmail($email, $nomeCompleto, $link_verificacao);

            if ($emailEnviado) {
                return json_encode([
                    'flag' => true,
                    'msg' => 'Email de verificação reenviado! Verifique a sua caixa de entrada.'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode([
                    'flag' => false,
                    'msg' => 'Erro ao enviar email de verificação.'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            return json_encode([
                'flag' => false,
                'msg' => 'Erro ao enviar email: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
