<?php
require_once 'connection.php'

class Mulher {

    function getProdutosMulher($marca, $preco, $tamanho, $cor, $estado, $material){
        global $conn;
         $msg = "";
         $row = "";

        $sql = "SELECT * FROM produtos_mulher WHERE 1=1";

        if ($marca != "") {
            $sql .= " AND marca_id = '$marca'";
        }

        if($preco != "") {
            $sql .= " AND preco_id = $preco'";
        }

        if ($tamanho != "") {
            $sql .= " AND tamanho_id = '$tamanho'";
    }

        if ($cor != "") {
                $sql .= " AND cor_id = '$cor'";
    }

        if ($estado != "") {
            $sql .= " AND estado_id = '$estado'";

    }

     if ($material != "") {
            $sql .= " AND material_id = '$material'";

    }


        $result = $conn->query($sql);

         if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $msg .= "<div class='col-md-3 col-sm-6'>";
                $msg .= "<div class='card border-0 shadow-sm rounded-4 h-100'>";
                $msg .= "<img src='src/img/" . $row['foto'] . "' class='card-img-top rounded-top-4' alt='" . $row['nome'] . "'>";
                $msg .= "<div class='card-body text-center'>";
                $msg .= "<h6 class='fw-bold mb-1'>" . $row['nome'] . "</h6>";
                $msg .= "<p class='text-muted mb-1'>" . $row['marca_nome'] . " · " . $row['tamanho_nome'] . " · " . $row['estado_nome'] . "</p>";
                $msg .= "<p class='fw-semibold'>€" . $row['preco'] . "</p>";
                $msg .= "<a href='#' class='btn btn-wegreen-accent rounded-pill'>Ver Produto</a>";
                $msg .= "</div></div></div>";
            }
        } else {
            $msg = "<p class='text-center text-muted'>Nenhum produto encontrado.</p>";
        }

        $conn->close();
        return $msg;
    }
}
?>