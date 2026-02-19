<?php

require_once __DIR__ . '/connection.php';

class Fornecedor{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getDadosPerfil($ID_User){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT * from utilizadores where id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

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

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
        function getListaCategoria(){
            try {

        $msg = "";
        $sql = "SELECT tipo_produtos.descricao As NomeProduto ,tipo_produtos.id As ValueProduto FROM tipo_produtos;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $msg .= "<option value='-1'>Selecionar Categoria...</option>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $msg .= "<option value=".$row["ValueProduto"].">".$row["NomeProduto"]."</option>";
            }
        } else {
                $msg .= "<option value='-1'>Selecionar Categoria...</option>";
                $msg .= "<option value='1'>Sem Registos</option>";
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);
            } catch (Exception $e) {
                return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
            }
    }
    function getFornecedores(){
        try {

        $msg = "";
        $sql = "SELECT Fornecedores.*, tipo_produtos.descricao As Categoria from tipo_produtos,Fornecedores where Fornecedores.tipo_produtos_id = tipo_produtos.id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

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

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
function guardaAdicionarFornecedor($nome, $categoria, $email, $telefone,$sede,$observacoes){
        try {

    $msg = "";
    $flag = false;

    $stmt = $this->conn->prepare("INSERT INTO Fornecedores (nome, tipo_produtos_id, descricao, email, telefone, morada) VALUES (?, ?, ?, ?, ?, ?)");
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
    ], JSON_UNESCAPED_UNICODE);

    $stmt->close();

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
function removerFornecedores($ID_Fornecedores){
        try {

        $msg = "";
        $flag = true;

        $sql = "DELETE FROM Fornecedores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Fornecedores);

        if ($stmt->execute()) {
            $msg = "Removido com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $sql . "<br>" . $this->conn->error;
        }

        $resp = json_encode(array(
            "flag" => $flag,
            "msg" => $msg
        ), JSON_UNESCAPED_UNICODE);

        return($resp);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getDadosFornecedores($ID_Fornecedores){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT tipo_produtos.descricao As Categoria, fornecedores.* from fornecedores,tipo_produtos where fornecedores.tipo_produtos_id = tipo_produtos.id AND fornecedores.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Fornecedores);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }

        return (json_encode($row, JSON_UNESCAPED_UNICODE));

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
function guardaEditDadosFornecedores($nome, $categoria, $email, $telefone, $morada, $descricao, $ID_Fornecedor){
        try {

    $flag = true;
    $msg = "";

    $sql = "UPDATE Fornecedores
            SET nome = ?,
                tipo_produtos_id = ?,
                email = ?,
                telefone = ?,
                morada = ?,
                descricao = ?
            WHERE id = ?";

    $stmt = $this->conn->prepare($sql);

    if(!$stmt){
        return json_encode([
            "flag" => false,
            "msg" => "Erro na preparação: " . $this->conn->error
        ], JSON_UNESCAPED_UNICODE);
    }

    $stmt->bind_param("sissssi",
        $nome,
        $categoria,
        $email,
        $telefone,
        $morada,
        $descricao,
        $ID_Fornecedor
    );

    if($stmt->execute()){
        $msg = "Editado com Sucesso";
    } else {
        $flag = false;
        $msg = "Erro na execução: " . $stmt->error;
    }

    $stmt->close();

    return json_encode([
        "flag" => $flag,
        "msg"  => $msg
    ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

}
?>
