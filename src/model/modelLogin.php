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
        $_SESSION['utilizador'] = $row['id'];
        $_SESSION['tipo'] = $row['tipo_utilizador_id'];
        $_SESSION['foto'] = $row['foto'];
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