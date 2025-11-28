<?php

require_once 'connection.php';

class PerfilAdmin{

    function getDadosTipoPerfilAdminInical($ID_User){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT utilizadores.foto AS FAdmin, utilizadores.*,tipo_utilizadores.descricao AS tipoUtilizador,ranking.nome As RankNome from utilizadores,tipo_utilizadores,ranking where utilizadores.id = tipo_utilizadores.id AND ranking.id = utilizadores.ranking_id AND utilizadores.id = ".$ID_User;

        $sql2 = "SELECT Count(*) As NProdutos
                FROM produtos 
                WHERE anunciante_id = ".$ID_User;
    $result1 = $conn->query($sql);
    $result2 = $conn->query($sql2);
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
        $conn->close();
        
        return ($msg);

    }
    function uploads($foto, $nome){
    
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
        file_put_contents('debug_upload.txt', "Entrou na condição de upload\n", FILE_APPEND);
        
        if(is_uploaded_file($foto['tmp_name'])){
            file_put_contents('debug_upload.txt', "is_uploaded_file OK\n", FILE_APPEND);
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
    
    return json_encode(array(
        "flag" => $flag,
        "target" => $targetBD
    ));
}
function adicionarFotoPerfil($ID_User, $foto){
    global $conn;
    $msg = "";
    $flag = false;
    $resp = $this->uploads($foto, $ID_User);
    $resp = json_decode($resp, true);
    $fotoFinal = $resp["target"];
    
    $sql = "UPDATE utilizadores 
            SET foto = '".$fotoFinal."' 
            WHERE id = ".$ID_User;

    if ($conn->query($sql) === TRUE) {
        $flag = true;
        $msg = "Aprovado com Sucesso";
    } else {
        $msg = "Erro: " . $conn->error;
    }

    return json_encode([
        "flag" => $flag,
        "msg"  => $msg
    ]);
}
    function getDadosTipoPerfil($ID_User){
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
        function getDadosTipoPerfilAdminInfo($ID_User){
        global $conn;

        $msg = "";
        $data = [];
        $sql = "SELECT * from utilizadores where id =".$ID_User;
        
        $result = $conn->query($sql);

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
        

        $conn->close();
        
        return json_encode($data);

    }
    function guardaDadosEditProduto($nome, $email,$nif,$telefone,$ID_User){
        
        global $conn;
        $msg = "";
        $flag = true;
        $sql = "";


        $sql = "UPDATE utilizadores 
        SET nome = '".$nome."', 
            email = '".$email."', 
            nif = '".$nif."', 
            telefone = '".$telefone."' 
            WHERE id = ".$ID_User;   

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
}
?>