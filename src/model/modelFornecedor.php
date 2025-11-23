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
    function getFornecedores(){
        global $conn;
        $msg = "";
        $sql = "SELECT Fornecedores.*, tipo_produtos.descricao As Categoria from tipo_produtos,Fornecedores where Fornecedores.tipo_produtos_id = tipo_produtos.id;";
        $text = "";
        $text2 = "";
        $result = $conn->query($sql);


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
$msg .= "<tr>";

// 1ª COLUNA — INFO DO FORNECEDOR
$msg .= "<td>";
$msg .= "    <div class='supplier-info'>";
$msg .= "        <div class='supplier-avatar'>💻</div>"; // ou outro ícone
$msg .= "        <div class='supplier-details'>";
$msg .= "            <div class='supplier-name'>".$row["nome"]."</div>";
$msg .= "            <div class='supplier-category'>".$row["Categoria"]."</div>";
$msg .= "        </div>";
$msg .= "    </div>";
$msg .= "</td>";

// 2ª COLUNA — EMAIL + TELEFONE
$msg .= "<td>";
$msg .= "    <div>".$row["email"]."</div>";
$msg .= "    <div style='color:#888; font-size:13px; margin-top:4px;'>".$row["telefone"]."</div>";
$msg .= "</td>";

// 6ª COLUNA — BOTÕES
$msg .= "<td>";
$msg .= "    <div class='action-buttons'>";
$msg .= "        <button class='btn-icon btn-edit' onclick='editSupplier(".$row["id"].")' title='Editar'>✏️</button>";
$msg .= "        <button class='btn-icon btn-delete' onclick='deleteSupplier(".$row["id"].")' title='Excluir'>🗑️</button>";
$msg .= "    </div>";
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
}
?>