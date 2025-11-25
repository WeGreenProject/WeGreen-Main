<?php
require_once 'connection.php';

class Mulher {

    function getFiltrosMulher(){
    global $conn;
    $msg = "";

    $sql = "SELECT marca FROM Produtos WHERE genero LIKE 'Mulher' AND ativo = 1 AND marca IS NOT NULL ORDER BY marca ASC;";
    $result = $conn->query($sql);

    $msg .= "<option value=''>Marca</option>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $msg .= "<option value='".$row["marca"]."'>".$row["marca"]."</option>";
        }
    } else {
        $msg .= "<option value=''>Marca</option>";
        $msg .= "<option value=''>Sem Registos</option>";
    }
    
    
    $msg .= "<option value=''>Preço</option>";
    $msg .= "<option value='0-20'>Até 20€</option>";
    $msg .= "<option value='20-50'>20€ - 50€</option>";
    $msg .= "<option value='50-100'>50€ - 100€</option>";
    $msg .= "<option value='100-200'>100€ - 200€</option>";
    $msg .= "<option value='200-999999'>Mais de 200€</option>";
    
    $msg .= "|||SEPARADOR|||";
    
    $sql = "SELECT tamanho FROM Produtos WHERE Produtgenero LIKE 'Mulher' AND ativo = 1 AND tamanho IS NOT NULL ORDER BY tamanho ASC;";
    $result = $conn->query($sql);

    $msg .= "<option value=''>Tamanho</option>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $msg .= "<option value='".$row["tamanho"]."'>".$row["tamanho"]."</option>";
        }
    } else {
        $msg .= "<option value=''>Tamanho</option>";
        $msg .= "<option value=''>Sem Registos</option>";
    }
    

    $sql = "SELECT estado FROM Produtos WHERE genero LIKE 'Mulher' AND ativo = 1 AND estado IS NOT NULL ORDER BY estado ASC;";
    $result = $conn->query($sql);

    $msg .= "<option value=''>Estado</option>";
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $msg .= "<option value='".$row["estado"]."'>".$row["estado"]."</option>";
        }
    } else {
        $msg .= "<option value=''>Estado</option>";
        $msg .= "<option value=''>Sem Registos</option>";
    }
    
    $conn->close();
    return ($msg);
}

function getProdutosMulher(){
    global $conn;
    $msg = "";

    $sql = "SELECT * FROM Produtos WHERE genero LIKE 'Mulher' AND ativo = 1;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $msg .= "<div class='col-md-3 col-sm-6'>";
            $msg .= "<div class='card border-0 shadow-sm rounded-4 h-100'>";
            $msg .= "<img src='".$row["foto"]."' height='340px' class='card-img-top rounded-top-4' alt='".$row["nome"]."'>";
            $msg .= "<div class='card-body text-center'>";
            $msg .= "<h6 class='fw-bold mb-1'>".$row["nome"]."</h6>";
            $msg .= "<p class='text-muted mb-1'>".$row["marca"]." · ".$row["tamanho"]." · ".$row["estado"]."</p>";
            $msg .= "<p class='fw-semibold'>".$row["preco"]."€</p>";
            $msg .= "<a href='ProdutoMulherMostrar.html?id=".$row['Produto_id']."' class='btn btn-wegreen-accent rounded-pill'>Ver Produto</a>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
        }
    }
    
    $conn->close();
    return ($msg);
}

function getProdutoMulherMostrar($ID_Produto){
    global $conn;
    $msg = "";

    $sql = "SELECT Produtos.foto AS FotoProduto, Produtos.*,utilizadores.nome AS NomeAnunciante,utilizadores.pontos_conf AS PontosConfianca, utilizadores.foto AS FotoPerfil,utilizadores.id As IdUtilizador,ranking.nome As RankNome,(SELECT COUNT(*) FROM Produtos WHERE Produtos.anunciante_id = utilizadores.id) AS TotalProdutosAnunciante,(SELECT COUNT(*) FROM Vendas WHERE Vendas.anunciante_id = utilizadores.id) AS TotalVendasAnunciante FROM Produtos,utilizadores,ranking WHERE Produtos.Produto_id = " . $ID_Produto." AND produtos.anunciante_id = utilizadores.id AND utilizadores.ranking_id = ranking.id";
    
    $sql2 = "SELECT foto AS ProdutoFoto FROM Produto_Fotos WHERE Produto_id = $ID_Produto";
    
    $sql3 = "SELECT Produto_id, nome, foto, marca, tamanho, estado, preco 
             FROM Produtos 
             WHERE genero = (SELECT genero FROM Produtos WHERE Produto_id = $ID_Produto) 
             AND Produto_id != $ID_Produto 
             AND ativo = 1 
             LIMIT 4";
    
    $result = $conn->query($sql);
    $result2 = $conn->query($sql2);
    $result3 = $conn->query($sql3); 
    
    if ($result->num_rows > 0) {
        while ($rowProduto = $result->fetch_assoc()) {
            $msg .= "<div class='col-md-6'>";
            $msg .= "<div class='card border-0 shadow-sm rounded-4 h-100'>";
            
            $msg .= "<div id='productGallery' class='carousel slide' data-bs-ride='carousel'>";
            
            $msg .= "<div class='carousel-inner rounded-4 shadow-sm'>";
            $msg .= "<div class='carousel-item active'>";
            $msg .= "<img src='".$rowProduto["FotoProduto"]."' height='700px' class='d-block w-100 rounded-4' alt='Produto'>";
            $msg .= "</div>";
            
            if ($result2 && $result2->num_rows > 0) {
                while ($rowFoto = $result2->fetch_assoc()) {
                    $msg .= "<div class='carousel-item'>";
                    $msg .= "<img src='".$rowFoto["ProdutoFoto"]."' height='700px' class='d-block w-100 rounded-4' alt='Produto'>";
                    $msg .= "</div>";
                }
            }
            $msg .= "</div>";
            
            $msg .= "<button class='carousel-control-prev' type='button' data-bs-target='#productGallery' data-bs-slide='prev'>";
            $msg .= "<span class='carousel-control-prev-icon'></span>";
            $msg .= "</button>";
            $msg .= "<button class='carousel-control-next' type='button' data-bs-target='#productGallery' data-bs-slide='next'>";
            $msg .= "<span class='carousel-control-next-icon'></span>";
            $msg .= "</button>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
            
            $msg .= "<div class='col-md-6'>";
            $msg .= "<h3 class='fw-bold mb-3'>".$rowProduto["nome"]."</h3>";
            $msg .= "<p class='text-muted mb-2'>Marca: <span class='fw-semibold'>".$rowProduto["marca"]."</span></p>";
            $msg .= "<p class='text-muted mb-2'>Tamanho: <span class='fw-semibold'>".$rowProduto["tamanho"]."</span></p>";
            $msg .= "<p class='text-muted mb-2'>Estado: <span class='fw-semibold'>".$rowProduto["estado"]."</span></p>";
            $msg .= "<h4 class='fw-bold text-success mb-3'>".$rowProduto["preco"]."€</h4>";
            $msg .= "<p class='mb-4'>".$rowProduto["descricao"]."</p>";
            
            $msg .= "<div class='d-flex gap-3 mb-4'>";
            $msg .= "<button class='btn btn-wegreen-accent rounded-pill px-4 py-2 fw-semibold shadow-sm btnComprarAgora' ";
            $msg .= "data-id='".$rowProduto['Produto_id']."'>";
            $msg .= "Comprar Agora";
            $msg .= "</button>";
            $msg .= "<button class='btn btn-outline-success rounded-pill px-4 py-2 fw-semibold'>Chat com o vendedor</button>";
            $msg .= "</div>";
            $msg .= "<div id='AnuncianteInfo' class='vendedora-card p-4 rounded-4 shadow-sm bg-white border border-success-subtle d-flex align-items-center justify-content-between flex-wrap mb-5'>";
            $msg .= "<div class='d-flex align-items-center'>";
            $msg .= "<div class='position-relative me-3'>";
            $msg .= "<img src='".$rowProduto["FotoPerfil"]."' class='rounded-circle border border-2 border-success shadow-sm' width='90' height='90' style='object-fit: cover;'>";
            $msg .= "</div>";
            $msg .= "<div>";
            $msg .= "<h5 class='fw-bold text-wegreen-accent mb-1'>".$rowProduto["NomeAnunciante"]."</h5>";
            $msg .= "<div class='text-muted small mb-2 d-flex align-items-center'><i class='bi bi-geo-alt-fill me-1 text-success'></i> Lisboa, Portugal</div>";
            $msg .= "<div class='mb-2'><span class='badge bg-success-subtle text-success border border-success fw-semibold rounded-pill px-3 py-1'><i class='bi bi-patch-check-fill'></i> Rank: ".$rowProduto["RankNome"]."</span></div>";
            $msg .= "<div class='text-muted small mb-2'>Anúncios: <span class='fw-semibold text-dark'>".$rowProduto["TotalProdutosAnunciante"]."</span> · Vendidos: <span class='fw-semibold text-dark'>".$rowProduto["TotalVendasAnunciante"]."</span></div>";
            $msg .= "<div class='text-muted small d-flex align-items-center'><i class='bi bi-stars text-success me-1'></i> Pontos de Confiança: <span class='fw-semibold text-dark ms-1'>".$rowProduto["PontosConfianca"]."</span></div>";
            $msg .= "<div class='progress my-2' style='height: 8px; border-radius: 8px; background-color: #e9f7ef;'><div class='progress-bar bg-success' role='progressbar' style='width: ".$rowProduto["PontosConfianca"]."%'></div></div>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "<div class='mt-3 mt-md-0'>";
            $msg .= "<a href='Vendedor.html?id=" . $rowProduto['IdUtilizador'] . "' class='btn btn-wegreen-accent rounded-pill fw-semibold shadow-sm px-4 py-2'>Ver Perfil</a>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</div>";
            
            $msg .= "<div class='mt-5'>";
            $msg .= "<h5 class='fw-bold mb-4'>Produtos Relacionados</h5>";
            $msg .= "<div class='row g-4'>";
            
            if ($result3 && $result3->num_rows > 0) {
                while ($rowRelacionados = $result3->fetch_assoc()) {
                    $msg .= "<div class='col-md-3 col-sm-6'>";
                    $msg .= "<div class='card border-0 shadow-sm rounded-4 h-100'>";
                    $msg .= "<img src='".$rowRelacionados["foto"]."' class='card-img-top rounded-top-4' alt='".$rowRelacionados["nome"]."' style='height: 300px; object-fit: cover;'>";
                    $msg .= "<div class='card-body text-center'>";
                    $msg .= "<h6 class='fw-bold mb-1'>".$rowRelacionados["nome"]."</h6>";
                    $msg .= "<p class='text-muted mb-1'>".$rowRelacionados["marca"]." · ".($rowRelacionados["tamanho"])." · ".$rowRelacionados["estado"]."</p>";
                    $msg .= "<p class='fw-semibold'>€".$rowRelacionados["preco"]. "€</p>";
                    $msg .= "<a href='ProdutoMulherMostrar.html?id=".$rowRelacionados["Produto_id"]."' class='btn btn-wegreen-accent rounded-pill'>Ver Produto</a>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                }
            } else {
                $msg .= "<div class='col-12'>";
                $msg .= "<p class='text-muted text-center'>Nenhum produto relacionado encontrado.</p>";
                $msg .= "</div>";
            }
            
            $msg .= "</div>"; 
            $msg .= "</div>"; 

        $msg .= "<div class='mt-3 mt-md-0'>";

        }
    } else {
        $msg = "<p class='text-center text-muted'>Produto não encontrado.</p>";
    }
    
    $conn->close();
    return $msg;
}
}
?>