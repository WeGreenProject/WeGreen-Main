<?php

require_once 'connection.php';

class Comentarios{

    function getCards(){
        global $conn;

        $sqlComentarios = "SELECT COUNT(*) AS TotalComents FROM avaliacoes_produtos";
        $result = $conn->query($sqlComentarios);
        $rowComents = $result->fetch_assoc();

        $sqlProdutos = "SELECT COUNT(*) AS NProdutos FROM produtos WHERE ativo = 1";
        $result = $conn->query($sqlProdutos);
        $rowProdutos = $result->fetch_assoc();


        $sqlDenuncias = "SELECT COUNT(*) AS denuncias FROM denuncias";
        $result = $conn->query($sqlDenuncias);
        $row = $result->fetch_assoc();

        $msg  = "<div class='stat-card stat-primary'>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-icon'><i class='fas fa-comments'></i></div>";
        $msg .= "      <div class='stat-info'>";
        $msg .= "          <div class='stat-label'>Total Comentários</div>";
        $msg .= "          <div class='stat-value' id='totalComentarios'>".$rowComents["TotalComents"]."</div>";
        $msg .= "      </div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $msg .= "<div class='stat-card stat-success'>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-icon'><i class='fas fa-check-circle'></i></div>";
        $msg .= "      <div class='stat-info'>";
        $msg .= "          <div class='stat-label'>Produtos</div>";
        $msg .= "          <div class='stat-value' id='comentariosAprovados'>".$rowProdutos["NProdutos"]."</div>";
        $msg .= "      </div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $msg .= "<div class='stat-card stat-danger'>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-icon'><i class='fas fa-flag'></i></div>";
        $msg .= "      <div class='stat-info'>";
        $msg .= "          <div class='stat-label'>Reports Ativos</div>";
        $msg .= "          <div class='stat-value' id='reportsAtivos'>".$row["denuncias"]."</div>";
        $msg .= "      </div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $conn->close();
        return $msg;
    }
    function getButaoNav(){
        global $conn;
        $msg = "";
        $sqlProdutos = "SELECT COUNT(DISTINCT produto_id) AS TotalProdutosComComentarios FROM avaliacoes_produtos;";
        $result = $conn->query($sqlProdutos);
        $rowProdutos = $result->fetch_assoc();

        $sqlDenuncias = "SELECT COUNT(*) AS Total FROM denuncias;";
        $result = $conn->query($sqlDenuncias);
        $rowDenuncias = $result->fetch_assoc();
        
        $msg .= "    <i class='fas fa-comments'></i>";
        $msg .= "    <span>Comentários</span>";
        $msg .= "    <span class='tab-badge' id='badgeComentarios'>".$rowProdutos["TotalProdutosComComentarios"]."</span>";        $msg .= "</button>";

        return ($msg);
    }
    function getButaoReports(){
        global $conn;
        $msg = "";

        $sqlDenuncias = "SELECT COUNT(*) AS Total FROM denuncias;";
        $result = $conn->query($sqlDenuncias);
        $rowDenuncias = $result->fetch_assoc();
        
        $msg .= "    <i class='fas fa-flag'></i>";
        $msg .= "    <span>Reports</span>";
        $msg .= "    <span class='tab-badge alert' id='badgeReports'>".$rowDenuncias["Total"]."</span>";

        return ($msg);
    }
    function getProdutos(){
            global $conn;
        $msg = "";
        $sql = "SELECT *, avaliacoes_produtos.id As IdProd,produtos.produto_id As IDProduto,produtos.nome AS NomeProd, produtos.foto As ProdFoto,produtos.data_criacao As ProdData  from avaliacoes_produtos,produtos,utilizadores where produtos.produto_id = avaliacoes_produtos.produto_id group by avaliacoes_produtos.produto_id;";
        $result = $conn->query($sql);
        $text = "";
        $text2 = "";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['IdProd']."</th>";
                $msg .= "<td><img src=".$row['ProdFoto']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                $msg .= "<td>".$row['NomeProd']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td>".$row['avaliacao']."</td>";
                $msg .= "<td>".$row['ProdData']."</td>";
                $msg .= "<td><button class='btn-info' onclick='getComentariosModal(".$row['IDProduto'].")'><i class='fas fa-edit'></i> Ver</button></td>";
                $msg .= "</tr>";
            }
        } else {
            $msg .= "<tr>";
            $msg .= "<td>Sem Registos</td>";
            $msg .= "<th scope='row'></th>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "</tr>";
        }
        $conn->close();

        return ($msg);
    }
        function getComentariosModal($idProduto){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM avaliacoes_produtos WHERE produto_id = ".$idProduto.";";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        $conn->close();

        return (json_encode($row));

    }
    function getReports(){
            global $conn;
        $msg = "";
        $sql = "SELECT denuncias.*,u1.nome AS nome_denunciante,u2.nome AS nome_denunciado FROM denuncias, utilizadores u1, utilizadores u2
        WHERE u1.id = denuncias.denunciante_id AND u2.id = denuncias.denunciado_id;";
        $result = $conn->query($sql);
        $text = "";
        $text2 = "";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['id']."</th>";
                if($row['imagem_anexo'] != null)
                {
                    $msg .= "<td><img src=".$row['imagem_anexo']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                }
                else
                {
                    $msg .= "<td>Não existe imagem!</td>";
                }
                    
                $msg .= "<td>".$row['nome_denunciante']."</td>";
                $msg .= "<td>".$row['descricao']."</td>";
                $msg .= "<td>".$row['nome_denunciado']."</td>";
                $msg .= "<td>".$row['estado']."</td>";
                $msg .= "<td>".$row['data_registo']."</td>";
                $msg .= "<td><button class='btn-info' onclick='getComentariosModal(".$row['id'].")'><i class='fas fa-edit'></i> Ver</button></td>";
                $msg .= "</tr>";
            }
        } else {
            $msg .= "<tr>";
            $msg .= "<td>Sem Registos</td>";
            $msg .= "<th scope='row'></th>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "<td></td>";
            $msg .= "</tr>";
        }
        $conn->close();

        return ($msg);
    }
}

?>