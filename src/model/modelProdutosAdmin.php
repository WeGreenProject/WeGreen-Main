<?php

require_once 'connection.php';

class ProdutosAdmin{

    function getDadosPerfil($ID_User){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT * from utilizadores where id =".$ID_User;
        
        $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                    $msg  = "<div class='profile-avatar'>";
                    $msg .= "<img src='" .$row["foto"]. "' alt='User Photo' id='userPhoto'>";
                    $msg .= "</div>";
                    $msg  .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>".$row["nome"]."</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='profile-avatar'>";
                    $msg .= "<img src='src/img/default_user.png' alt='Erro a encontrar foto' id='userPhoto'>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
            }
        $conn->close();
        
        return ($msg);

    }
    function getProdutosAprovar(){

        global $conn;
        $msg = "";
        $sql = "SELECT Produtos.*,Tipo_Produtos.descricao As TipoProdutoNome,Utilizadores.nome AS nomeAnunciante from Produtos,Tipo_Produtos,Utilizadores where Produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = Produtos.anunciante_id AND Produtos.estado Like 'Pendente';";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['id']."</th>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td>".$row['nomeAnunciante']."</td>";
                $msg .= "<td>".$row['TipoProdutoNome']."</td>";
                $msg .= "<td>".$row['data_criacao']."</td>";
                $msg .= "<td>".$row['estado']."</td>";
                $msg .= "<td><button class='btn btn-success' onclick ='pagarDividasPagar(".$row['id'].")'><i class='fa fa-trash'>Aceitar</i></button></td>";
                $msg .= "<td><button class='btn btn-danger' onclick ='recusarDividasPagar(".$row['id'].")'><i class='fa fa-trash'>Recusar</i></button></td>";         
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
            $msg .= "</tr>";
        }
        $conn->close();

        return ($msg);
    }
        function getProdutosPendentes(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As ProdutosCount from Produtos  where produtos.estado like 'Pendente';";
        
        $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    
                    $msg .= "<div class='approval-stat-icon'>⏳</div>";
                    $msg .= "<div class='approval-stat-info'>";
                    $msg  .= "<div class='approval-stat-value'>".$row["ProdutosCount"]."</div>";
                    $msg .= "<div class='approval-stat-label'>Pendentes</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='profile-avatar'>";
                    $msg .= "<img src='src/img/default_user.png' alt='Erro a encontrar foto' id='userPhoto'>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
            }
        $conn->close();
        
        return ($msg);

    }
}
?>