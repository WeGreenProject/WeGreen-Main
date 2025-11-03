<?php

require_once 'connection.php';

class Registo{

    function registaUser($username, $email, $nif,$foto,$password){

    global $conn;
    $msg = "";
    $flag = false;

    $resp = $this -> uploads($foto, $email, $username);
    $resp = json_decode($resp, TRUE);

    $foto = "src/img/pexels-beccacorreiaph-31095884.jpg";
    $plano_id = 1;
    $ranking_id = 0;
    $pontos_conf = 0;
    $tipo_utilizador_id = 2;
    
    $stmt = $conn->prepare("INSERT INTO Utilizadores (nome, email,nif,foto,password,tipo_utilizador_id,plano_id,ranking_id,pontos_conf)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("ssssssiii", $username, $email, $nif, $foto,$password,$tipo_utilizador_id,$plano_id,$ranking_id,$pontos_conf);

    $stmt->execute();

    $pw = md5($pw);
    $msg = "Registado com sucesso!";
    $flag = true;

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