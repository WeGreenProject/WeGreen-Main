<?php

require_once 'connection.php';

class Vendedor {

    function getPerfilVendedora($utilizador_id) {
        global $conn;
        $msg = "";
        $row = "";
        
        $sql = "SELECT 
                    ranking.nome AS rankname,
                    utilizadores.*,
                    (SELECT COUNT(*) FROM Vendas WHERE anunciante_id = $utilizador_id) AS vendas
                FROM Utilizadores
                INNER JOIN ranking ON Utilizadores.ranking_id = ranking.id
                WHERE Utilizadores.id = $utilizador_id";
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $msg .= "<section class='container my-5'>";
                $msg .= "<div class='card border-0 shadow-sm rounded-4 p-4'>";
                $msg .= "<div class='d-flex align-items-center gap-4'>";
                $msg .= "<img src='".$row["foto"]."' class='rounded-circle border border-3 border-success shadow' width='120' height='120' style='object-fit: cover;'>";
                $msg .= "<div class='flex-grow-1'>";
                $msg .= "<h3 class='fw-bold text-success mb-2'>".$row["nome"]."</h3>";
                $msg .= "<div class='d-flex align-items-center gap-3 mb-3'>";
                $msg .= "<span class='badge bg-success-subtle text-success border border-success fw-semibold px-3 py-2'>";
                $msg .= "<i class='bi bi-patch-check-fill me-1'></i> ".$row["rankname"]."</span>";
                $msg .= "<span class='text-muted'><i class='bi bi-geo-alt-fill text-success me-1'></i> Lisboa, Portugal</span>";
                $msg .= "</div>";
                $msg .= "<div class='d-flex gap-4'>";
                $msg .= "<div class='text-center'>";
                $msg .= "<h5 class='fw-bold text-success mb-0'>".$row["pontos_conf"]."</h5>";
                $msg .= "<small class='text-muted'>Pontos de Confiança</small>";
                $msg .= "</div>";
                $msg .= "<div class='vr'></div>";
                $msg .= "<div class='text-center'>";
                $msg .= "<h5 class='fw-bold text-success mb-0'>".$row["vendas"]."</h5>";
                $msg .= "<small class='text-muted'>Vendas Realizadas</small>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>"; 
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</section>";
            }
        }
        
        return ($msg);
    }

    function getProdutosVendedora($utilizador_id) {
        global $conn;
        $msg = "";

        $sql = "SELECT Produtos.* FROM Produtos WHERE Produtos.anunciante_id = " . $utilizador_id . " AND Produtos.ativo = 1"; 
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $msg .= "<section class='container my-5'><div class='row g-4'>";

            while ($row = $result->fetch_assoc()) {
                $msg .= "<div class='col-md-3'>";
                $msg .= "<div class='card border-0 shadow rounded-4'>";
                $msg .= "<img src='".$row["foto"]."' class='card-img-top rounded-top-4' alt='".$row["nome"]."' style='height: 300px; object-fit: cover;'>";
                $msg .= "<div class='card-body'>";
                $msg .= "<h5 class='fw-bold'>".$row["nome"]."</h5>";
                $msg .= "<p class='text-muted mb-1'>".$row["marca"]." · ".$row["tamanho"]." · ".$row["estado"]."</p>";
                $msg .= "<p class='fw-bold text-success fs-5'>€".number_format($row["preco"], 2, ',', '.')."</p>";
                $msg .= "<a href='ProdutoMulherMostrar.html?id=".$row['Produto_id']."' class='btn btn-outline-success rounded-pill w-100 fw-semibold'>Ver Produto</a>";
                $msg .= "</div>";
                $msg .= "</div>";
                $msg .= "</div>";
            }

            $msg .= "</div></section>";
        } else {
            $msg .= "<section class='container my-5'>";
            $msg .= "<div class='alert alert-info text-center'>Este vendedor ainda não tem produtos à venda.</div>";
            $msg .= "</section>";
        }
        
        return ($msg);
    }

} 

?>