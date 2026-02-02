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

        $msg  = "<div class='stat-card-compact'>";
        $msg .= "  <div class='stat-icon' style='background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);'>";
        $msg .= "      <i class='fas fa-comments'></i>";
        $msg .= "  </div>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-label'>TOTAL COMENTÁRIOS</div>";
        $msg .= "      <div class='stat-value'>".$rowComents["TotalComents"]."</div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $msg .= "<div class='stat-card-compact'>";
        $msg .= "  <div class='stat-icon' style='background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);'>";
        $msg .= "      <i class='fas fa-check-circle'></i>";
        $msg .= "  </div>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-label'>PRODUTOS</div>";
        $msg .= "      <div class='stat-value'>".$rowProdutos["NProdutos"]."</div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $msg .= "<div class='stat-card-compact'>";
        $msg .= "  <div class='stat-icon' style='background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);'>";
        $msg .= "      <i class='fas fa-flag'></i>";
        $msg .= "  </div>";
        $msg .= "  <div class='stat-content'>";
        $msg .= "      <div class='stat-label'>REPORTS ATIVOS</div>";
        $msg .= "      <div class='stat-value'>".$row["denuncias"]."</div>";
        $msg .= "  </div>";
        $msg .= "</div>";

        $conn->close();
        return $msg;
    }
    function getButaoNav(){
        global $conn;
        $sqlProdutos = "SELECT COUNT(DISTINCT produto_id) AS TotalProdutosComComentarios FROM avaliacoes_produtos;";
        $result = $conn->query($sqlProdutos);
        $rowProdutos = $result->fetch_assoc();
        $conn->close();
        return $rowProdutos["TotalProdutosComComentarios"];
    }
    function getButaoReports(){
        global $conn;
        $sqlDenuncias = "SELECT COUNT(*) AS Total FROM denuncias;";
        $result = $conn->query($sqlDenuncias);
        $rowDenuncias = $result->fetch_assoc();
        $conn->close();
        return $rowDenuncias["Total"];
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
                $msg .= "<td><button class='btn-icon' onclick='getComentariosModal(".$row['IDProduto'].")'><i class='fas fa-eye'></i> Ver</button></td>";
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

    function getComentariosProduto($idProduto){
        global $conn;
        $msg = "";

        $sql = "SELECT ap.*, u.nome AS nome_utilizador, u.email, p.nome AS nome_produto
                FROM avaliacoes_produtos ap
                LEFT JOIN utilizadores u ON ap.utilizador_id = u.id
                LEFT JOIN produtos p ON ap.produto_id = p.produto_id
                WHERE ap.produto_id = ".$idProduto."
                ORDER BY ap.id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $msg .= "<div class='comentarios-list'>";
            while($row = $result->fetch_assoc()) {
                $msg .= "<div class='comentario-item'>";
                $msg .= "<div class='comentario-header'>";
                $msg .= "<strong>".$row['nome_utilizador']."</strong>";
                $msg .= "</div>";
                $msg .= "<div class='comentario-rating'>";
                for($i = 1; $i <= 5; $i++) {
                    if($i <= $row['avaliacao']) {
                        $msg .= "<i class='fas fa-star' style='color: #ffc107;'></i>";
                    } else {
                        $msg .= "<i class='far fa-star' style='color: #ddd;'></i>";
                    }
                }
                $msg .= " <span>(".$row['avaliacao']."/5)</span>";
                $msg .= "</div>";
                $msg .= "<div class='comentario-texto'>".$row['comentario']."</div>";
                $msg .= "</div>";
            }
            $msg .= "</div>";
        } else {
            $msg .= "<p>Nenhum comentário encontrado para este produto.</p>";
        }

        $conn->close();
        return $msg;
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
