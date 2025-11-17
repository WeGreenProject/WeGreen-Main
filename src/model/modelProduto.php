<?php

require_once 'connection.php';

class Produto{

    function getDadosProdutos($ID_User){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Produtos WHERE id = " . $ID_User;
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                    $msg .= "<img src='" >. $row["foto"] ."'>";
                    $msg  = "<h3 class='fw-bold mb-3'>".$row["nome"]."</h3>";
                    $msg .= "<p class='text-muted mb-2'>Tamanho: <span class='fw-semibold'>".$row["tamanho"]."</span></p>";
                    $msg .= "<p class='text-muted mb-2'>Estado: <span class='fw-semibold'>".$row["estado"]."</span></p>";
                    $msg .= "<h4 class='fw-bold text-success mb-3'>".$row["preco"]."</h4>";
                    $msg .= "<p class='mb-4'>";
                    $msg .= "".$row["descricao"]."";
                    $msg .= "</p>";
 
            }
           
        }
}
}