<?php

require_once 'connection.php';

class Login {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function iniciarSessao($email, $pw) {
        try {

            $msg = "";
            $flag = true;

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $stmt = $this->conn->prepare(
                "SELECT * FROM Utilizadores WHERE email = ? AND password = ?"
            );

            if (!$stmt) {
                return json_encode(["flag" => false, "msg" => "Erro ao processar login"], JSON_UNESCAPED_UNICODE);
            }

            $stmt->bind_param("ss", $email, $pw);
            $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            if (isset($row['email_verificado']) && $row['email_verificado'] == 0) {

                $msg = "Por favor, verifique o seu email antes de fazer login.";
                $flag = false;

                $stmt->close();

                return json_encode([
                    "flag" => false,
                    "msg" => $msg,
                    "email_nao_verificado" => true,
                    "email" => $row['email']
                ], JSON_UNESCAPED_UNICODE);
            }

            $msg = "Bem vindo " . $row['nome'];

            $temp_user_id = isset($_SESSION['temp_user_id']) ? $_SESSION['temp_user_id'] : null;

            $_SESSION['utilizador'] = $row['id'];
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['tipo'] = $row['tipo_utilizador_id'];
            $_SESSION['PontosConf'] = $row['pontos_conf'];
            $_SESSION['foto'] = $row['foto'];
            $_SESSION['raking'] = $row['ranking_id'];
            $_SESSION['plano'] = $row['plano_id'];
            $_SESSION['data'] = $row['data_criacao'];
            $_SESSION['email'] = $row['email'];

            
            if ($temp_user_id && $temp_user_id !== $row['id']) {
                require_once 'modelCarrinho.php';
                $carrinho = new Carrinho($this->conn);
                if ($row['tipo_utilizador_id'] == 2) {
                    
                    $carrinho->transferirCarrinhoTemporario($temp_user_id, $row['id']);
                } else {
                    
                    $stmtLimpar = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ?");
                    $stmtLimpar->bind_param("i", $temp_user_id);
                    $stmtLimpar->execute();
                    $stmtLimpar->close();
                }
                unset($_SESSION['temp_user_id']);
            }

            
            if ($row['tipo_utilizador_id'] == 1 || $row['tipo_utilizador_id'] == 3) {
                if (!isset($carrinho)) {
                    require_once 'modelCarrinho.php';
                    $carrinho = new Carrinho($this->conn);
                }
                $stmtLimparReal = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ?");
                $userId = $row['id'];
                $stmtLimparReal->bind_param("i", $userId);
                $stmtLimparReal->execute();
                $stmtLimparReal->close();
            }

            $acao = "login";

            $stmtLog = $this->conn->prepare(
                "INSERT INTO logs_acesso (utilizador_id, acao, email, data_hora)
                 VALUES (?, ?, ?, NOW())"
            );

            if (!$stmtLog) {
                
            } else {
                $stmtLog->bind_param(
                    "iss",
                    $_SESSION['utilizador'],
                    $acao,
                    $_SESSION['email']
                );

                if (!$stmtLog->execute()) {
                    
                }

                $stmtLog->close();
            }

        } else {
            $flag = false;
            $msg = "Erro! Dados Inválidos";
        }

        $stmt->close();

        return json_encode([
            "flag" => $flag,
            "msg" => $msg,
            "tipo_utilizador" => $row['tipo_utilizador_id'] ?? '',
            "perfil_duplo" => ($_SESSION['perfil_duplo'] ?? false) ? true : false
        ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(["flag" => false, "msg" => "Erro ao processar login"], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
