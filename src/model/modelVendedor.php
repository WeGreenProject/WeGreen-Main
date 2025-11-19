<?php

require_once 'connection.php';

class Vendedor {

    function getPerfilVendedora($utilizador_id) {
        global $conn;
        $msg = "";

        $sql = "SELECT Utilizadores.* FROM Utilizadores";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                $msg .= "<section class='container my-5'>";
                $msg .= "<div class='p-4 rounded-4 shadow-sm bg-white border border-success-subtle d-flex justify-content-between align-items-center flex-wrap'>";
                $msg .= "<div class='d-flex align-items-center'>";
                $msg .= "<img src='".$rowProduto["FotoPerfil"]."' class='rounded-circle border border-2 border-success shadow-sm' width='110' height='110' style='object-fit: cover;'>";
                $msg .= "<div>";
                $msg .= "<h4 class='fw-bold text-success mb-1'>".$row["nome"]."</h4>";
                $msg .= "<p class='text-muted mb-1 d-flex align-items-center'>";
                $msg .= "<i class='bi bi-geo-alt-fill text-success me-1'></i> ".$row["localizacao"];
                $msg .= "</p>";
                $msg .= "<p class='mb-1'>";
                $msg .= "<span class='badge bg-success-subtle text-success border border-success fw-semibold px-3'>".$row["rank"]."</span>";
                $msg .= "</p>";
                $msg .= "<p class='text-muted small mb-1'>";
                $msg .= "<strong>".$row["PontosConfianca"]."</strong> Pontos · ";
                $msg .= "<strong>".$row["vendas"]."</strong> Vendas";
                $msg .= "</p>";
                $msg .= "</div>"; 
                $msg .= "</div>"; 
        }

        return $msg;
    }
}
        function getProdutosVendedora() {
        global $conn;
        $msg = "";

        $sql = "SELECT Produtos.* FROM Produtos WHERE Produtos.id = 1"; 
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $msg .= "<section class='container my-5'><div class='row g-4'>";

            while ($row = $result->fetch_assoc()) {
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card border-0 shadow rounded-4'>";
                $msg .= "<img src='".$row["foto"]."' class='card-img-top rounded-top-4' alt='".$row["nome"]."'>";
                $msg .= "<div class='card-body'>";
                $msg .= "<h5 class='fw-bold'>".$row["nome"]."</h5>";
                $msg .= "<p class='text-muted mb-1'>".$row["marca"]." · ".$row["tamanho"]." · ".$row["estado"]."</p>";
                $msg .= "<p class='fw-bold text-success'>".$row["preco"]."€</p>";
                $msg .= "<a href='ProdutoMulherMostrar.html?id=".$row['id']."' class='btn btn-lime rounded-pill w-100'>Ver Produto</a>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";
            }

            $msg .= "</div></section>";
        }

        return $msg;
    }
}
?>
?>

        


