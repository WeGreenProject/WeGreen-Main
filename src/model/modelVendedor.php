<?php

require_once 'connection.php';

class Vendedor {

    function getPerfilVendedora($utilizador_id) {
        global $conn;
        $msg = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = $utilizador_id LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $msg .= "<section class='container my-5'>";
            $msg .= "<div class='p-4 rounded-4 shadow-sm bg-white border border-success-subtle d-flex justify-content-between align-items-center flex-wrap'>";
            $msg .= "<div class='d-flex align-items-center'>";
            $msg .= "<img src='".$row["FotoPerfil"]."' class='rounded-circle border border-2 border-success shadow-sm' width='110' height='110' style='object-fit: cover;'>";
            $msg .= "<div class='ms-3'>";
            $msg .= "<h4 class='fw-bold text-success mb-1'>".$row["nome"]."</h4>";
            $msg .= "<p class='text-muted mb-1 d-flex align-items-center'><i class='bi bi-geo-alt-fill text-success me-1'></i>".$row["localizacao"]."</p>";
            $msg .= "<p class='mb-1'><span class='badge bg-success-subtle text-success border border-success fw-semibold px-3'>".$row["rank"]."</span></p>";
            $msg .= "<p class='text-muted small mb-1'><strong>".$row["PontosConfianca"]."</strong> Pontos · <strong>".$row["vendas"]."</strong> Vendas</p>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</section>";
       }
        
        return ($msg);

    }
}

    function getProdutosVendedora($ID_Produto) {
    global $conn;
    $msg = "";
    $rowProduto = "";
    $rowFoto = "";

    $sql = "SELECT Produtos.foto AS FotoProduto, Produtos.* 
            FROM Produtos 
            WHERE Produtos.anunciante_id = $ID_Produto";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $msg .= "<section class='container my-5'><div class='row g-4'>";

        while ($row = $result->fetch_assoc()) {
            $rowProduto = $row;
            $fotoProduto = $row["FotoProduto"];

            $msg .= "<div class='col-md-4'>";
            $msg .= "<div class='card border-0 shadow rounded-4'>";
            $msg .= "<img src='".$fotoProduto."' class='card-img-top rounded-top-4' alt='".$rowProduto["nome"]."'>";
            $msg .= "<div class='card-body'>";
            $msg .= "<h5 class='fw-bold'>".$rowProduto["nome"]."</h5>";
            $msg .= "<p class='text-muted mb-1'>".$rowProduto["marca"]." · ".$rowProduto["tamanho"]." · ".$rowProduto["estado"]."</p>";
            $msg .= "<p class='fw-bold text-success'>".$rowProduto["preco"]."€</p>";
            $msg .= "<a href='ProdutoMulherMostrar.html?id=".$rowProduto['Produto_id']."' class='btn btn-lime rounded-pill w-100 mt-2'>Ver Produto</a>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
        }

        $msg .= "</div></section>";
    }

    $conn->close();

    return ($msg);
}
