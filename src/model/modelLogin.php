<?php

require_once 'connection.php';

class Login {

    function login1($email, $pw) {
        try {
            global $conn;
            $msg = "";
            $flag = true;

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $stmt = $conn->prepare(
                "SELECT * FROM Utilizadores WHERE email = ? AND password = ?"
            );

            if (!$stmt) {
                error_log("Erro prepare: " . $conn->error);
                return json_encode([
                    "flag" => false,
                    "msg" => "Erro ao processar login"
                ]);
            }

            $stmt->bind_param("ss", $email, $pw);
            $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            // Email não verificado
            if (isset($row['email_verificado']) && $row['email_verificado'] == 0) {

                $msg = "Por favor, verifique o seu email antes de fazer login.";
                $flag = false;

                $stmt->close();
                $conn->close();

                return json_encode([
                    "flag" => false,
                    "msg" => $msg,
                    "email_nao_verificado" => true,
                    "email" => $row['email']
                ]);
            }

            // Login OK
            $msg = "Bem vindo " . $row['nome'];

        $_SESSION['utilizador'] = $row['id'];
        $_SESSION['nome'] = $row['nome'];
        $_SESSION['tipo'] = $row['tipo_utilizador_id'];
        $_SESSION['PontosConf'] = $row['pontos_conf'];
        $_SESSION['foto'] = $row['foto'];
        $_SESSION['raking'] = $row['ranking_id'];
        $_SESSION['plano'] = $row['plano_id'];
        $_SESSION['data'] = $row['data_criacao'];
        $_SESSION['email'] = $row['email'];

            $acao = "login";

            $stmtLog = $conn->prepare(
                "INSERT INTO logs_acesso (utilizador_id, acao, email, data_hora)
                 VALUES (?, ?, ?, NOW())"
            );

            if (!$stmtLog) {
                error_log("Erro prepare log: " . $conn->error);
                // Continua mesmo com erro no log
            } else {
                $stmtLog->bind_param(
                    "iss",
                    $_SESSION['utilizador'],
                    $acao,
                    $_SESSION['email']
                );

                if (!$stmtLog->execute()) {
                    error_log("Erro insert log: " . $stmtLog->error);
                    // Continua mesmo com erro no log
                }

                $stmtLog->close();
            }

        } else {
            $flag = false;
            $msg = "Erro! Dados Inválidos";
        }

        $stmt->close();
        $conn->close();

        return json_encode([
            "msg" => $msg,
            "flag" => $flag,
            "tipo_utilizador" => $row['tipo_utilizador_id'] ?? null,
            "perfil_duplo" => $_SESSION['perfil_duplo'] ?? false
        ]);

        } catch (Exception $e) {
            error_log("Erro login1: " . $e->getMessage());
            return json_encode([
                "flag" => false,
                "msg" => "Erro ao processar login"
            ]);
        }
    }
}
?>
