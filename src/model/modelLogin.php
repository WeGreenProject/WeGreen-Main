<?php

require_once 'connection.php';

class Login{

    function login1($email, $pw){

    global $conn;
    $msg = "";
    $flag = true;
    session_start();
    $stmt = $conn->prepare("SELECT * FROM Utilizadores WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $pw);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){
    $row = $result->fetch_assoc();

        // Verificar se o email foi verificado
        if (isset($row['email_verificado']) && $row['email_verificado'] == 0) {
            $msg = "Por favor, verifique o seu email antes de fazer login. Verifique a sua caixa de entrada ou spam.";
            $flag = false;

            $resp = json_encode(array(
                "flag" => $flag,
                "msg" => $msg,
                "email_nao_verificado" => true,
                "email" => $row['email']
            ));

            $stmt->close();
            $conn->close();
            return($resp);
        }

        $msg = "Bem vindo ".$row['nome'];

        // Guardar ID temporário antes de sobrescrever
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

        // Verificar se tem perfil duplo (Cliente e Anunciante)
        $tipo_atual = $row['tipo_utilizador_id'];
        $tipo_oposto = ($tipo_atual == 2) ? 3 : 2;

        $stmt2 = $conn->prepare("SELECT id FROM Utilizadores WHERE email = ? AND tipo_utilizador_id = ?");
        $stmt2->bind_param("si", $row['email'], $tipo_oposto);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows > 0) {
            // Tem perfil duplo - marcar na sessão para redirecionar para página de escolha
            $_SESSION['perfil_duplo'] = true;
            $stmt2->close();
        } else {
            $_SESSION['perfil_duplo'] = false;
            $stmt2->close();
        }

        // Transferir carrinho temporário se existir
        if ($temp_user_id !== null) {
            require_once 'modelCarrinho.php';
            $carrinho = new Carrinho();
            $carrinho->transferirCarrinhoTemporario($temp_user_id, $row['id']);
            unset($_SESSION['temp_user_id']);
        }


    }else{
        $flag = false;
        $msg = "Erro! Dados Inválidos";
    }

    $stmt->close();
    $conn->close();

    return (json_encode(array(
        "msg" => $msg,
        "flag" => $flag,
        "perfil_duplo" => isset($_SESSION['perfil_duplo']) ? $_SESSION['perfil_duplo'] : false
        )));
    }
}
?>
