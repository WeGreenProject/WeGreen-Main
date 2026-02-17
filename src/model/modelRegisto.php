<?php

require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class Registo{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function registaUser($nome, $apelido, $email, $nif, $morada, $password, $tipoUtilizador = 3, $codigo_postal = null, $distrito = null, $localidade = null){

        $msg = "";
        $flag = false;

        
        if (empty($nome) || empty($apelido) || empty($email) || empty($password) || empty($morada) || empty($codigo_postal) || empty($distrito) || empty($localidade)) {
            return json_encode([
                "flag" => false,
                "msg" => "Todos os campos obrigatórios devem ser preenchidos"
            ], JSON_UNESCAPED_UNICODE);
        }

        if (!preg_match('/^[0-9]{4}-[0-9]{3}$/', $codigo_postal)) {
            return json_encode([
                "flag" => false,
                "msg" => "Código postal inválido (use XXXX-XXX)"
            ], JSON_UNESCAPED_UNICODE);
        }

        if (strlen($morada) < 10) {
            return json_encode([
                "flag" => false,
                "msg" => "A morada deve ter pelo menos 10 caracteres"
            ], JSON_UNESCAPED_UNICODE);
        }

        if (strlen($password) < 6) {
            return json_encode([
                "flag" => false,
                "msg" => "A password deve ter pelo menos 6 caracteres"
            ], JSON_UNESCAPED_UNICODE);
        }

        
        if (!empty($nif) && !preg_match('/^\d{9}$/', $nif)) {
            return json_encode([
                "flag" => false,
                "msg" => "NIF deve conter exatamente 9 dígitos numéricos"
            ], JSON_UNESCAPED_UNICODE);
        }

        
        $checkTotal = $this->conn->prepare("SELECT COUNT(*) as total FROM Utilizadores WHERE email = ?");
        $checkTotal->bind_param("s", $email);
        $checkTotal->execute();
        $resultTotal = $checkTotal->get_result();
        $rowTotal = $resultTotal->fetch_assoc();
        $checkTotal->close();

        if ($rowTotal['total'] >= 2) {
            return json_encode([
                "flag" => false,
                "msg" => "Este email já possui ambas as contas (Cliente e Anunciante). Não é possível criar mais contas."
            ], JSON_UNESCAPED_UNICODE);
        }

        
        $checkEmail = $this->conn->prepare("SELECT id FROM Utilizadores WHERE email = ? AND tipo_utilizador_id = ?");
        $checkEmail->bind_param("si", $email, $tipoUtilizador);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $checkEmail->close();
            return json_encode([
                "flag" => false,
                "msg" => "Já existe uma conta " . ($tipoUtilizador == 2 ? 'Cliente' : 'Anunciante') . " com este email"
            ], JSON_UNESCAPED_UNICODE);
        }
        $checkEmail->close();

        $foto = "src/img/pexels-beccacorreiaph-31095884.jpg";
        $plano_id = 1;
        $ranking_id = 1;
        $pontos_conf = 0;
        $tipo_utilizador_id = intval($tipoUtilizador); 
        $data_criacao = date('Y-m-d');
        $password = md5($password);

        $stmt = $this->conn->prepare("INSERT INTO Utilizadores
            (nome, apelido, email, nif, morada, codigo_postal, distrito, localidade, foto, password, tipo_utilizador_id, plano_id, ranking_id, pontos_conf, data_criacao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssiiiis", $nome, $apelido, $email, $nif, $morada, $codigo_postal, $distrito, $localidade, $foto, $password, $tipo_utilizador_id, $plano_id, $ranking_id, $pontos_conf, $data_criacao);

        if ($stmt->execute()) {
            $msg = "Conta criada! Verifique o seu email para confirmar a conta.";
            $flag = true;

            $user_id = $this->conn->insert_id;

            
            $token = bin2hex(random_bytes(32));
            $expira_em = date('Y-m-d H:i:s', strtotime('+24 hours'));

            
            $stmtToken = $this->conn->prepare("UPDATE Utilizadores SET token_verificacao = ?, token_expira_em = ? WHERE id = ?");
            $stmtToken->bind_param("ssi", $token, $expira_em, $user_id);
            $stmtToken->execute();
            $stmtToken->close();

            
            try {
                $emailService = new EmailService($this->conn);
                $nomeCompleto = $nome . ' ' . $apelido;
                $link_verificacao = 'http://localhost/WeGreen-Main/verificar_email.html?token=' . urlencode($token);

                $emailEnviado = $emailService->sendVerificacaoEmail($email, $nomeCompleto, $link_verificacao);

                if (!$emailEnviado) {
                }
            } catch (Exception $e) {
            }
        } else {
            $msg = "Erro ao registar utilizador: " . $stmt->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ), JSON_UNESCAPED_UNICODE);

        $stmt->close();

        return($resp);

    }
}
?>
