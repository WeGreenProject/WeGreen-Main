<?php

require_once 'connection.php';

class Registo{

    function registaUser($username, $email, $nif,$password){

        global $conn;
        $msg = "";
        $flag = false;
        
        $foto = "src/img/pexels-beccacorreiaph-31095884.jpg";
        $plano_id = 1;
        $ranking_id = 1;
        $pontos_conf = 0;
        $tipo_utilizador_id = 2;
        $data_criacao = date('Y-m-d');
        
            $stmt = $conn->prepare("INSERT INTO Utilizadores 
                (nome, email, nif, foto, password, tipo_utilizador_id, plano_id, ranking_id, pontos_conf, data_criacao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssiiiis", $username, $email, $nif, $foto,$password,$tipo_utilizador_id,$plano_id,$ranking_id,$pontos_conf, $data_criacao);

        $stmt->execute();


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