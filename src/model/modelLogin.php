<?php

require_once 'connection.php';

class Login{

    function login1($username, $pw){

    global $conn;
    $msg = "";
    $flag = true;
    session_start();
    $stmt = $conn->prepare("SELECT * FROM Utilizadores WHERE nome = ? AND password = ?");
    $stmt->bind_param("ss", $username, $pw);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){
    $row = $result->fetch_assoc();
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
        "flag" => $flag
        )));
    }
}
?>
