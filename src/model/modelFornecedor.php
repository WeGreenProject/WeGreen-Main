<?php

require_once 'connection.php';

class Fornecedor{

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
                    $msg .= "<span class='avatar-placeholder'></span>";
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
                    $msg .= "<span class='avatar-placeholder'>👤</span>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
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
    function getFornecedores(){
        global $conn;
        $msg = "";
        $sql = "SELECT Fornecedores.*, tipo_produtos.descricao As Categoria from tipo_produtos,Fornecedores where Fornecedores.tipo_produtos_id = tipo_produtos.id;";
        $result = $conn->query($sql);


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
            $msg .= "<tr>";
            $msg .= "<td>";
            $msg .= "<div class='supplier-info'>";
            if($row["Categoria"] == "Roupa")
            {
                $msg .= "<div class='supplier-avatar'>👕</div>";
            }
            else if($row["Categoria"] == "Calçado")
            {
                $msg .= "<div class='supplier-avatar'>👟</div>";
            }
            else if($row["Categoria"] == "Acessórios")
            {
                $msg .= "<div class='supplier-avatar'>👜</div>";
            }
            else if($row["Categoria"] == "Beleza")
            {
                $msg .= "<div class='supplier-avatar'>🧴</div>";
            }
            else if($row["Categoria"] == "Outros")
            {
                $msg .= "<div class='supplier-avatar'>📦</div>";
            }
            $msg .= "<div class='supplier-details'>";
            $msg .= "<div class='supplier-name'>".$row["nome"]."</div>";
            $msg .= "<div class='supplier-category'>".$row["Categoria"]."</div>";
            $msg .= "</div>";
            $msg .= "</div>";
            $msg .= "</td>";
            $msg .= "<td>";
            $msg .= "<div>".$row["email"]."</div>";
            $msg .= "<div style='color:#888; font-size:13px; margin-top:4px;'>".$row["telefone"]."</div>";
            $msg .= "</td>";
            $msg .= "<td>";
            $msg .= "<div>".$row["morada"]."</div>";
            $msg .= "</td>";
            $msg .= "<td>";
            $msg .= "<div class='action-buttons'>";
            $msg .= "<button class='btn-icon btn-edit' onclick='getDadosFornecedores(".$row["id"].")' title='Editar'>✏️</button>";
            $msg .= "<button class='btn-icon btn-delete' onclick='removerFornecedores(".$row["id"].")' title='Excluir'>🗑️</button>";
            $msg .= "</div>";
            $msg .= "</td>";
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
function guardaAdicionarFornecedor($nome, $categoria, $email, $telefone,$sede,$observacoes){
    global $conn;
    $msg = "";
    $flag = false;

    $stmt = $conn->prepare("INSERT INTO Fornecedores (nome, tipo_produtos_id, descricao, email, telefone, morada) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nome, $categoria, $observacoes, $email, $telefone, $sede);

    if($stmt->execute()){
        $msg = "Registado com sucesso!";
        $flag = true;
    } else {
        $msg = "Erro ao registar: " . $stmt->error;
        $flag = false;
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ]);

    $stmt->close();
    $conn->close();

    return $resp;
}
function removerFornecedores($ID_Fornecedores){
        global $conn;
        $msg = "";
        $flag = true;

        $sql = "DELETE FROM Fornecedores WHERE id = ".$ID_Fornecedores;

        if ($conn->query($sql) === TRUE) {
            $msg = "Removido com Sucesso";
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
    function getDadosFornecedores($ID_Fornecedores){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT tipo_produtos.descricao As Categoria, fornecedores.* from fornecedores,tipo_produtos where fornecedores.tipo_produtos_id = tipo_produtos.id AND fornecedores.id =".$ID_Fornecedores;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        $conn->close();

        return (json_encode($row));

    }
}
?>