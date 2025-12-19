<?php

require_once 'connection.php';

class ClienteAdmin{

    function getClientes($ID_Utilizador){
        global $conn;
        $msg = "";
        $sql = "SELECT utilizadores.*,Tipo_utilizadores.descricao As TipoUtilizadores from utilizadores,Tipo_utilizadores where tipo_utilizadores.id = utilizadores.tipo_utilizador_id AND utilizadores.id != ".$ID_Utilizador;
        $result = $conn->query($sql);


            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $msg .= "<tr>";
                    $msg .= "<td>".$row["id"]."</td>";
                    $msg .= "<td>";
                    $msg .= "<div class='client-info'>";
                    $msg .= "<img src='".$row["foto"]."' alt='".$row["nome"]."' class='client-avatar'>";
                    $msg .= "<div class='client-details'>";
                    $msg .= "<span class='client-name'>".$row["nome"]."</span>";
                    $msg .= "<span class='client-email'>".$row["email"]."</span>";
                    $msg .= "</div>";
                    $msg .= "</div>";
                    $msg .= "</td>";
                    $msg .= "<td>".$row["TipoUtilizadores"]."</td>";
                    $msg .= "<td>".$row["telefone"]."</td>";
                    $msg .= "<td>".$row["data_criacao"]."</td>";
                    $msg .= "<td>";
                    $msg .= "<div class='action-buttons'>";
                    $msg .= "<button class='btn-action btn-view' onclick='viewClient(".$row["id"].")' title='Ver Detalhes'>";
                    $msg .= "<i class='fas fa-eye'></i>";
                    $msg .= "</button>";
                    $msg .= "<button class='btn-action btn-edit' onclick='editClient(".$row["id"].")' title='Editar'>";
                    $msg .= "<i class='fas fa-edit'></i>";
                    $msg .= "</button>";
                    $msg .= "<button class='btn-action btn-delete' onclick='removerClientes(".$row["id"].")' title='Eliminar'>";
                    $msg .= "<i class='fas fa-trash'></i>";
                    $msg .= "</button>";
                    $msg .= "</div>";
                    $msg .= "</td>";
                    $msg .= "</tr>";
                }
            }
        else {
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
function registaClientes($nome, $email, $telefone, $tipo, $nif, $password, $foto){
    global $conn;
    $msg = "";
    $flag = true;
    $sql = "";
    $resp = $this->uploads($foto, $nome);
    $resp = json_decode($resp, TRUE);

    if($resp['flag']){
        $sql = "INSERT INTO utilizadores (nome, email, telefone, tipo_utilizador_id, nif, password, foto) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisss", $nome, $email, $telefone, $tipo, $nif, $password, $resp['target']);
    } else {
        $sql = "INSERT INTO utilizadores (nome, email, telefone, tipo_utilizador_id, nif, password) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiss", $nome, $email, $telefone, $tipo, $nif, $password);
    }

    if ($stmt->execute()) {
        $msg = "Cliente registado com sucesso!";
    } else {
        $flag = false;
        $msg = "Error: " . $stmt->error;
    }
    $stmt->close();
   
    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg
    ));
      
    $conn->close();

    return $resp;
}
    function removerClientes($ID_Cliente){
        global $conn;
        $msg = "";
        $flag = true;

        $sql = "DELETE FROM utilizadores WHERE id = ".$ID_Cliente;

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
    function getCardUtilizadores(){
        global $conn;
        $msg = "";
        $sql = "        SELECT
            COUNT(*) AS total,
            SUM(tipo_utilizador_id = 2) AS clientes,
            SUM(tipo_utilizador_id = 3) AS anunciantes,
            SUM(tipo_utilizador_id = 1) AS admins
        FROM utilizadores;";
        $result = $conn->query($sql);


            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $msg  = '<div class="stat-card">
                                <i class="stat-icon fas fa-users"></i>
                                <div class="stat-label">Total Utilizadores</div>
                                <div class="stat-value" id="totalClients">'.$row["total"].'</div>
                            </div>';

                    $msg .= '<div class="stat-card">
                                <i class="stat-icon fas fa-user"></i>
                                <div class="stat-label">Clientes</div>
                                <div class="stat-value" id="totalNormalClients">'.$row["clientes"].'</div>
                            </div>';

                    $msg .= '<div class="stat-card">
                                <i class="stat-icon fas fa-store"></i>
                                <div class="stat-label">Anunciantes</div>
                                <div class="stat-value" id="totalAdvertisers">'.$row["anunciantes"].'</div>
                            </div>';

                    $msg .= '<div class="stat-card">
                                <i class="stat-icon fas fa-user-shield"></i>
                                <div class="stat-label">Administradores</div>
                                <div class="stat-value" id="totalAdmins">'.$row["admins"].'</div>
                            </div>';
                }
            }
        else {
                                $msg  = '<div class="stat-card">
                                <i class="stat-icon fas fa-users"></i>
                                <div class="stat-label">Total Utilizadores</div>
                                <div class="stat-value" id="totalClients">Não encontrado</div>
                            </div>';

                    $msg .= '<div class="stat-card">
                                <i class="stat-icon fas fa-user"></i>
                                <div class="stat-label">Clientes</div>
                                <div class="stat-value" id="totalNormalClients">Não encontrado</div>
                            </div>';

                    $msg .= '<div class="stat-card">
                                <i class="stat-icon fas fa-store"></i>
                                <div class="stat-label">Anunciantes</div>
                                <div class="stat-value" id="totalAdvertisers">Não encontrado</div>
                            </div>';

                    $msg .= '<div class="stat-card">
                                <i class="stat-icon fas fa-user-shield"></i>
                                <div class="stat-label">Administradores</div>
                                <div class="stat-value" id="totalAdmins">Não encontrado</div>
                            </div>';
        }
        $conn->close();

        return ($msg);
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

}
?>