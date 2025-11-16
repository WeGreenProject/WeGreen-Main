<?php
require_once 'connection.php';

class Criança {

function getProdutosCriança(){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT produtos.* FROM produtos where produtos.genero LIKE 'criança';";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $msg .= "<div class='col-md-3 col-sm-6'>";
                $msg .= "<div class='card border-0 shadow-sm rounded-4 h-100'>";
                $msg .= "<img src='".$row["foto"]."' class='card-img-top rounded-top-4' alt='".$row["nome"]."'>";
                $msg .= "<div class='card-body text-center'>";
                $msg .= "<h6 class='fw-bold mb-1'>".$row["nome"]."</h6>";
                $msg .= "<p class='text-muted mb-1'>".$row["marca"]." · ".$row["tamanho"]." · ".$row["estado"]."</p>";
                $msg .= "<p class='fw-semibold'>".$row["preco"]."€</p>";
                $msg .= "<a href='ProdutoMulherMostrar.html?id=".$row['id']."' class='btn btn-wegreen-accent rounded-pill'>Ver Produto</a>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";
              }
        $conn->close();
        
        return ($msg);

    }
}
