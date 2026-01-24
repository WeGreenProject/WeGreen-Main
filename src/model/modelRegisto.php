<?php

require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class Registo{

    function registaUser($nome, $apelido, $email, $nif, $morada, $password, $tipoUtilizador = 3){

        global $conn;
        $msg = "";
        $flag = false;

        // Validações
        if (empty($nome) || empty($apelido) || empty($email) || empty($password) || empty($morada)) {
            return json_encode([
                "flag" => false,
                "msg" => "Todos os campos obrigatórios devem ser preenchidos"
            ]);
        }

        if (strlen($morada) < 10) {
            return json_encode([
                "flag" => false,
                "msg" => "A morada deve ter pelo menos 10 caracteres"
            ]);
        }

        if (strlen($password) < 6) {
            return json_encode([
                "flag" => false,
                "msg" => "A password deve ter pelo menos 6 caracteres"
            ]);
        }

        // Validar NIF (se fornecido)
        if (!empty($nif) && !preg_match('/^\d{9}$/', $nif)) {
            return json_encode([
                "flag" => false,
                "msg" => "NIF deve conter exatamente 9 dígitos numéricos"
            ]);
        }

        // Verificar se o utilizador já tem AMBAS as contas (Cliente E Anunciante)
        $checkTotal = $conn->prepare("SELECT COUNT(*) as total FROM Utilizadores WHERE email = ?");
        $checkTotal->bind_param("s", $email);
        $checkTotal->execute();
        $resultTotal = $checkTotal->get_result();
        $rowTotal = $resultTotal->fetch_assoc();
        $checkTotal->close();

        if ($rowTotal['total'] >= 2) {
            return json_encode([
                "flag" => false,
                "msg" => "Este email já possui ambas as contas (Cliente e Anunciante). Não é possível criar mais contas."
            ]);
        }

        // Verificar se email já existe COM O MESMO TIPO de utilizador
        $checkEmail = $conn->prepare("SELECT id FROM Utilizadores WHERE email = ? AND tipo_utilizador_id = ?");
        $checkEmail->bind_param("si", $email, $tipoUtilizador);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows > 0) {
            $checkEmail->close();
            return json_encode([
                "flag" => false,
                "msg" => "Já existe uma conta " . ($tipoUtilizador == 2 ? 'Cliente' : 'Anunciante') . " com este email"
            ]);
        }
        $checkEmail->close();

        $foto = "src/img/pexels-beccacorreiaph-31095884.jpg";
        $plano_id = 1;
        $ranking_id = 1;
        $pontos_conf = 0;
        $tipo_utilizador_id = intval($tipoUtilizador); // 2 = Anunciante, 3 = Cliente
        $data_criacao = date('Y-m-d');
        $password = md5($password);

        $stmt = $conn->prepare("INSERT INTO Utilizadores
            (nome, apelido, email, nif, morada, foto, password, tipo_utilizador_id, plano_id, ranking_id, pontos_conf, data_criacao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssiiiis", $nome, $apelido, $email, $nif, $morada, $foto, $password, $tipo_utilizador_id, $plano_id, $ranking_id, $pontos_conf, $data_criacao);

        if ($stmt->execute()) {
            $msg = "Conta criada! Verifique o seu email para confirmar a conta.";
            $flag = true;

            $user_id = $conn->insert_id;

            // Gerar token de verificação
            $token = bin2hex(random_bytes(32));
            $expira_em = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Atualizar token na BD
            $stmtToken = $conn->prepare("UPDATE Utilizadores SET token_verificacao = ?, token_expira_em = ? WHERE id = ?");
            $stmtToken->bind_param("ssi", $token, $expira_em, $user_id);
            $stmtToken->execute();
            $stmtToken->close();

            // Enviar email de verificação
            try {
                $emailService = new EmailService();
                $nomeCompleto = $nome . ' ' . $apelido;
                $link_verificacao = 'http://localhost/WeGreen-Main/verificar_email.html?token=' . urlencode($token);

                $emailEnviado = $emailService->sendVerificacaoEmail($email, $nomeCompleto, $link_verificacao);

                if (!$emailEnviado) {
                    error_log("Aviso: Email de verificação não foi enviado para {$email}");
                }
            } catch (Exception $e) {
                error_log("Erro ao enviar email de verificação: " . $e->getMessage());
            }
        } else {
            $msg = "Erro ao registar utilizador: " . $stmt->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));

        $stmt->close();
        $conn->close();

        return($resp);

    }
}
?>
