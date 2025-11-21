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
                else if($row['ativo'] == 2)
                {
                    $text = "Rejeitado";
                    $text2 = 'status-rejected';
                }
                else
                {
                    $text = "Inativo";
                    $text2 = 'status-rejected';
                }

                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
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
    function getInativos(){
        global $conn;
        $msg = "";
        $sql = "SELECT produtos.*,Tipo_Produtos.descricao As ProdutosNome, Utilizadores.nome  As NomeAnunciante from produtos,Tipo_Produtos,Utilizadores where produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = produtos.anunciante_id AND produtos.anunciante_id AND produtos.ativo = 0;";
        $result = $conn->query($sql);
        $text = "";
        $text2 = "";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                if($row['ativo'] == 1)
                {
                    $text = "Ativo";
                    $text2 = 'status-approved';
                }
                else if($row['ativo'] == 2)
                {
                    $text = "Rejeitado";
                    $text2 = 'status-rejected';
                }
                else
                {
                    $text = "Inativo";
                    $text2 = 'status-rejected';
                }
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
                $msg .= "<td><img src=".$row['foto']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['genero']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td><span class='status-badge ".$text2."'>".$text."</span></td>";
                $msg .= "<td><button class='btn-info' onclick='getDadosInativos(".$row['Produto_id'].")'>ℹ️ Editar</button></td>";  
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
    function getDadosProduto($ID_Produto){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT Produtos.*,Tipo_Produtos.id As Valuecategoria,utilizadores.nome As vendedor FROM Produtos,Tipo_Produtos,Utilizadores WHERE Produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = Produtos.anunciante_id AND Produto_id =".$ID_Produto;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        $conn->close();

        return (json_encode($row));

    }
    function getDadosInativos($ID_Produto){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT Produtos.*,Tipo_Produtos.id As Valuecategoria,utilizadores.nome As vendedor FROM Produtos,Tipo_Produtos,Utilizadores WHERE Produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = Produtos.anunciante_id AND Produto_id =".$ID_Produto;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        $conn->close();

        return (json_encode($row));

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
                else if($row['ativo'] == 2)
                {
                    $text = "Rejeitado";
                    $text2 = 'status-rejected';
                }
                else
                {
                    $text = "Inativo";
                    $text2 = 'status-rejected';
                }

                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
                $msg .= "<td><img src=".$row['foto']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['NomeAnunciante']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td><span class='status-badge ".$text2."'>".$text."</span></td>";
                $msg .= "<td>".$row['marca']."</td>";  
                $msg .= "<td><button class='btn-info' onclick='getDadosProduto(".$row['Produto_id'].")'>ℹ️ Editar</button><br><br><button class='btn-info' onclick='getDesativacao(".$row['Produto_id'].")'>❌ Desativar</button></td>";  
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
    function rejeitaEditProduto($Produto_id){
        
        global $conn;
        $msg = "";
        $flag = true;
        $sql = "";


        $sql = "UPDATE Produtos 
        SET ativo = 2 WHERE Produto_id = ".$Produto_id;   

        if ($conn->query($sql) === TRUE) {
            $msg = "Rejeitado com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));
          
        $conn->close();

        return($resp);

    }
    function guardaEditProduto($nome, $categoria, $marca, $tamanho,$preco,$genero,$vendedor,$Produto_id){
        
        global $conn;
        $msg = "";
        $flag = true;
        $sql = "";


        $sql = "UPDATE Produtos 
        SET nome = '".$nome."', 
            tipo_produto_id = '".$categoria."', 
            marca = '".$marca."', 
            tamanho = '".$tamanho."', 
            preco = '".$preco."', 
            genero = '".$genero."', 
            anunciante_id = '".$vendedor."', 
            ativo = 1 
        WHERE Produto_id = ".$Produto_id;   

        if ($conn->query($sql) === TRUE) {
            $msg = "Aprovado com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));
          
        $conn->close();

        return($resp);

    }
    function guardaDadosEditProduto($nome, $categoria, $marca, $tamanho,$preco,$genero,$vendedor,$Produto_id){
        
        global $conn;
        $msg = "";
        $flag = true;
        $sql = "";


        $sql = "UPDATE Produtos 
        SET nome = '".$nome."', 
            tipo_produto_id = '".$categoria."', 
            marca = '".$marca."', 
            tamanho = '".$tamanho."', 
            preco = '".$preco."', 
            genero = '".$genero."', 
            anunciante_id = '".$vendedor."'
        WHERE Produto_id = ".$Produto_id;   

        if ($conn->query($sql) === TRUE) {
            $msg = "Aprovado com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ));
          
        $conn->close();

        return($resp);

    }
    function getFotosSection($Produto_id)
    {
        global $conn;
        $msg = "";
        $Produto_id = intval($Produto_id);
        $sql = "SELECT * FROM produto_fotos WHERE produto_id = ".$Produto_id;
        $result = $conn->query($sql);
        $text = "";
        $text2 = "";
        $labels = [
            "Foto Frontal",
            "Foto Traseira",
            "Etiqueta",
            "Detalhes"
            ];

        if ($result->num_rows > 0) {
            $msg .= "<h3>📸 Fotos do Produto</h3>";
            $msg .= "<div class='photos-grid' id='photosGrid'>";
            $i = 0;
            while($row = $result->fetch_assoc()) {
                $msg .= "<div class='photo-item'>";
                $msg .= "<img src=".$row["foto"]." alt='Foto Frontal'>";
                $msg .= "<div class='photo-label'>".$labels[$i]."</div>";
                $msg .= "</div>";

                $i++;
            }
            $msg .= "</div>";   
        } else {
                $msg .= "<h3>📸 Não existem fotos</h3>";
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