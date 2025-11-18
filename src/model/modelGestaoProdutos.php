<?php

require_once 'connection.php';

class Vendas{

    function getMeusProdutos($ID_User){
        global $conn;
        $msg = "";
        $sql = "SELECT produtos.*,Tipo_Produtos.descricao As ProdutosNome, Utilizadores.nome  As NomeAnunciante from produtos,Tipo_Produtos,Utilizadores where produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = produtos.anunciante_id AND produtos.anunciante_id = ".$ID_User;
        $text = "";
        $text2 = "";
        $result = $conn->query($sql);



        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['ativo'] == 1)
                {
                    $text = "Ativo";
                    $text2 = 'status-approved';
                }
                else
                {
                    $text = "Inativo";
                    $text2 = 'status-rejected';
                }

                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['id']."</th>";
                $msg .= "<td><img src=".$row['foto']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['genero']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td><span class='status-badge ".$text2."'>".$text."</span></td>";
                $msg .= "<td>".$row['marca']."</td>";  
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
    function getProdutos(){
        global $conn;
        $msg = "";
        $sql = "SELECT produtos.*,Tipo_Produtos.descricao As ProdutosNome, Utilizadores.nome  As NomeAnunciante from produtos,Tipo_Produtos,Utilizadores where produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = produtos.anunciante_id AND produtos.anunciante_id;";
        $text = "";
        $text2 = "";
        $result = $conn->query($sql);



        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['ativo'] == 1)
                {
                    $text = "Ativo";
                    $text2 = 'status-approved';
                }
                else
                {
                    $text = "Inativo";
                    $text2 = 'status-rejected';
                }

                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['id']."</th>";
                $msg .= "<td><img src=".$row['foto']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['NomeAnunciante']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td><span class='status-badge ".$text2."'>".$text."</span></td>";
                $msg .= "<td>".$row['marca']."</td>";  
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
    function getListaVendedores(){
        global $conn;
        $msg = "";
        $sql = "SELECT utilizadores.nome As NomeUtilizadores ,utilizadores.id As ValueUtilizador FROM utilizadores,Tipo_utilizadores where Tipo_utilizadores.id = utilizadores.tipo_utilizador_id AND Tipo_utilizadores.id IN (1, 3);";
        $result = $conn->query($sql);


        $msg .= "<option value='-1'>Selecionar cliente...</option>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $msg .= "<option value=".$row["ValueUtilizador"].">".$row["NomeUtilizadores"]."</option>";
            }
        } else {
                $msg .= "<option value='-1'>Selecionar cliente...</option>";
                $msg .= "<option value='1'>Sem Registos</option>";
        }
        $conn->close();

        return ($msg);
    }
    function getListaCategoria(){
        global $conn;
        $msg = "";
        $sql = "SELECT tipo_produtos.descricao As NomeProduto ,tipo_produtos.id As ValueProduto FROM tipo_produtos;";
        $result = $conn->query($sql);


        $msg .= "<option value='-1'>Selecionar Categoria...</option>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $msg .= "<option value=".$row["ValueProduto"].">".$row["NomeProduto"]."</option>";
            }
        } else {
                $msg .= "<option value='-1'>Selecionar Categoria...</option>";
                $msg .= "<option value='1'>Sem Registos</option>";
        }
        $conn->close();

        return ($msg);
    }
}
?>