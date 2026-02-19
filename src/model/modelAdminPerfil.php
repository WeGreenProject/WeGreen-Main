<?php

require_once __DIR__ . '/connection.php';

class AdminPerfil {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getDadosTipoPerfilAdminInical($ID_User){
        try {
        $msg = "";
        $row = "";

        $stmt1 = $this->conn->prepare("SELECT utilizadores.foto AS FAdmin, utilizadores.*, tipo_utilizadores.descricao AS tipoUtilizador, ranking.nome As RankNome FROM utilizadores, tipo_utilizadores, ranking WHERE utilizadores.id = tipo_utilizadores.id AND ranking.id = utilizadores.ranking_id AND utilizadores.id = ?");
        $stmt1->bind_param("i", $ID_User);
        $stmt1->execute();
        $result1 = $stmt1->get_result();

        $stmt2 = $this->conn->prepare("SELECT Count(*) As NProdutos FROM produtos WHERE anunciante_id = ?");
        $stmt2->bind_param("i", $ID_User);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        $prod = $result2->fetch_assoc();
            if ($result1->num_rows > 0) {
                while ($row = $result1->fetch_assoc()) {

                    $msg  = "<div class='profile-header-card'>";
                    $msg .= "<div class='profile-avatar-large'>";
                    $msg .= "<img src='" . $row['FAdmin'] . "' alt='User Photo' id='userPhoto'>";
                    $msg .= "<button class='avatar-edit-btn' type='button'>";
                    $msg .= "<i class='fas fa-camera'></i>";
                    $msg .= "<input type='file' id='avatarUpload' class='avatar-file-input' ";
                    $msg .= "accept='image/jpeg,image/jpg,image/png,image/gif,image/webp' ";
                    $msg .= "onchange='adicionarFotoPerfil()' />";
                    $msg .= "</button>";
                    $msg .= "</div>";
                    $msg .= "<div class='avatar-wrapper'>";

                    $msg .= "<div class='profile-header-info'>";
                    $msg .= "<h1>" . $row['nome'] . "</h1>";
                    $msg .= "<span class='role-badge'>👑 " . $row['tipoUtilizador'] . "</span>";
                    $msg .= "<div class='profile-stats'>";
                    $msg .= "<div class='profile-stat'>";
                    $msg .= "<div class='profile-stat-value'>" . $prod['NProdutos'] . "</div>";
                    $msg .= "<div class='profile-stat-label'>Produtos Anunciados</div>";
                    $msg .= "</div>";
                    $msg .= "<div class='profile-stat'>";
                    $msg .= "<div class='profile-stat-value'>" . $row['RankNome'] . "</div>";
                    $msg .= "<div class='profile-stat-label'>Classificação</div>";
                    $msg .= "</div>";
                    $msg .= "<div class='profile-stat'>";
                    $msg .= "<div class='profile-stat-value'>" . $row['pontos_conf'] . "</div>";
                    $msg .= "<div class='profile-stat-label'>Pontos de Confiança</div>";
                    $msg .= "</div>";

                    $msg .= "</div>";

                    $msg .= "</div></div></div>";
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

    function uploads($foto, $nome){
        try {

        $dirFisico = __DIR__ . "/../img/";
        $dirWeb = "src/img/";
        $flag = false;
        $targetBD = "";

        if(!is_dir($dirFisico)){
            if(!mkdir($dirFisico, 0777, TRUE)){
                die("Erro não é possível criar o diretório");
            }
        }

        if(isset($foto) && is_array($foto) && !empty($foto['tmp_name']) && $foto['error'] === 0){

            if(is_uploaded_file($foto['tmp_name'])){
                $fonte = $foto['tmp_name'];
                $ficheiro = $foto['name'];
                $end = explode(".", $ficheiro);
                $extensao = end($end);

                $nomeLimpo = preg_replace('/[^a-zA-Z0-9]/', '_', $nome);
                $newName = "produto_" . $nomeLimpo . "_" . date("YmdHis") . "." . $extensao;

                $targetFisico = $dirFisico . $newName;
                $targetBD = $dirWeb . $newName;

                $flag = move_uploaded_file($fonte, $targetFisico);
            }
        }

        return [
            "flag" => $flag,
            "target" => $targetBD
        ];

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function adicionarFotoPerfil($ID_User, $foto){
        try {

        $msg = "";
        $flag = false;
        $resp = $this->uploads($foto, $ID_User);
        $fotoFinal = $resp["target"];

        $stmt = $this->conn->prepare("UPDATE utilizadores SET foto = ? WHERE id = ?");
        $stmt->bind_param("si", $fotoFinal, $ID_User);

        if ($stmt->execute()) {
            $flag = true;
            $msg = "Aprovado com Sucesso";
        } else {
            $msg = "Erro: " . $this->conn->error;
        }

        return json_encode(["flag" => $flag, "msg" => $msg, "target" => $fotoFinal], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getDadosTipoPerfil($ID_User){
        try {

        $msg = "";
        $row = "";
        $stmt = $this->conn->prepare("SELECT * FROM utilizadores WHERE id = ?");
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

    function ProfileDropCard($ID_User){
        try {

        $msg = "";
        $row = "";
        $stmt = $this->conn->prepare("SELECT * FROM utilizadores WHERE id = ?");
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                $msg  = "<div class='section-header'>";
                $msg .= "<h3><i class='fas fa-user'></i> Informações Pessoais</h3>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>Nome Completo</label>";
                $msg .= "<input type='text' id='nomeAdminEdit' value='".$row["nome"]."'>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>Email</label>";
                $msg .= "<input type='email' id='emailAdminEdit' value='".$row["email"]."'>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>NIF</label>";
                $msg .= "<input type='text' id='nifAdminEdit' value='".$row["nif"]."' placeholder='000000000' maxlength='9'>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>Telefone</label>";
                $msg .= "<input type='text' id='telefoneAdminEdit' value='".$row["telefone"]."' placeholder='900000000' maxlength='9'>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>Morada</label>";
                $msg .= "<input type='text' id='moradaAdminEdit' value='".$row["morada"]."' placeholder='Rua, Número, Código Postal, Cidade'>";
                $msg .= "</div>";

                $msg .= "<button class='btn btn-primary' onclick='guardarDadosPerfil()' style='margin-top: 20px; width: 100%;'>";
                $msg .= "<i class='fas fa-save'></i> Guardar Alterações";
                $msg .= "</button>";

                }
            }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function ProfileDropCard2($ID_User){
        try {

        $msg = "";
        $row = "";
        $stmt = $this->conn->prepare("SELECT * FROM utilizadores WHERE id = ?");
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $msg  .= "<div class='profile-header-card'>";

                    $msg .= "<div class='profile-avatar-large'>";
                    $msg .= "<img src='".$row["foto"]."' alt='Foto de Perfil' id='userPhoto'>";
                    $msg .= "<button class='avatar-edit-btn' type='button'>";
                    $msg .= "<i class='fas fa-camera'></i>";
                    $msg .= "<input type='file' id='avatarUpload' class='avatar-file-input' ";
                    $msg .= "accept='image/jpeg,image/jpg,image/png,image/gif,image/webp' ";
                    $msg .= "onchange='adicionarFotoPerfil()' />";
                    $msg .= "</button>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-header-info'>";

                    $msg .= "<div class='profile-header-left'>";
                    $msg .= "<h1>".$row["nome"]."</h1>";
                    $msg .= "<span class='role-badge'>Administrador</span>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-stats'>";

                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                }
            }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function guardarDadosPerfil($nome, $email, $nif, $telefone, $morada, $ID_Utilizador){
        try {

        $flag = true;
        $msg = "";

        $stmt = $this->conn->prepare("UPDATE utilizadores
                SET nome = ?,
                    email = ?,
                    nif = ?,
                    telefone = ?,
                    morada = ?
                WHERE id = ?");

        if(!$stmt){
            return json_encode([
                "flag" => false,
                "msg" => "Erro na preparação: " . $this->conn->error
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt->bind_param("sssssi",
            $nome,
            $email,
            $nif,
            $telefone,
            $morada,
            $ID_Utilizador
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

    function getDadosTipoPerfilAdminInfo($ID_User){
        try {

        $msg = "";
        $data = [];
        $stmt = $this->conn->prepare("SELECT * FROM utilizadores WHERE id = ?");
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                $msg .= "<div class='section-header'>";
                $msg .= "<h3>";
                $msg .= "<i class='fas fa-user'></i>";
                $msg .= " Informações Pessoais";
                $msg .= "</h3>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>Nome</label>";
                $msg .= "<input type='text' id='nomeAdmin'>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>Email</label>";
                $msg .= "<input type='text' id='emailAdmin''>";
                $msg .= "</div>";

                $msg .= "<div class='info-item'>";
                $msg .= "<label>NIF</label>";
                $msg .= "<input type='text' id='NIFadmin'>";
                $msg .= "</div>";
                $msg .= "<div class='info-item'>";
                $msg .= "<label>Telefone</label>";
                $msg .= "<input type='text' id='telAdmin'>";
                $msg .= "</div>";
                $msg .= "<div class='action-buttons' id='personalActions'>";
                $msg .= "<button class='btn-primary' id='btnGuardar2'\">";
                $msg .= "<i class='fas fa-save'></i> Salvar Alterações";
                $msg .= "</button>";
                $msg .= "<button class='btn-secondary' onclick=\"cancelEdit('personal')\">";
                $msg .= "<i class='fas fa-times'></i> Cancelar";
                $msg .= "</button>";
                $msg .= "</div>";

                $data = [
                "html"  => $msg,
                "nome"  => $row["nome"],
                "email" => $row["email"],
                "nif"   => $row["nif"],
                "telefone"   => $row["telefone"]
                ];
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

        return json_encode($data, JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function guardaDadosEditProduto($nome, $email, $nif, $telefone, $ID_User){
        try {

        $msg = "";
        $flag = true;

        $stmt = $this->conn->prepare("UPDATE utilizadores SET nome = ?, email = ?, nif = ?, telefone = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nome, $email, $nif, $telefone, $ID_User);

        if ($stmt->execute()) {
            $msg = "Aprovado com Sucesso";
        } else {
            $flag = false;
            $msg = "Error: " . $stmt->error;
        }

        return json_encode([
            "flag" => $flag,
            "msg" => $msg
        ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
