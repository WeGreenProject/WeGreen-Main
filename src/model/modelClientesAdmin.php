<?php

require_once __DIR__ . '/connection.php';

class ClientesAdmin {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function obterNomeTipoUtilizador($tipo) {
        $tipoInt = (int)$tipo;
        if ($tipoInt === 1) return 'Administrador';
        if ($tipoInt === 2) return 'Cliente';
        if ($tipoInt === 3) return 'Anunciante';
        return 'Utilizador';
    }

    private function obterTiposPorEmail($email, $ignorarId = null) {
        $sql = "SELECT DISTINCT tipo_utilizador_id FROM utilizadores WHERE LOWER(email) = LOWER(?)";
        if ($ignorarId !== null) {
            $sql .= " AND id <> ?";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if ($ignorarId !== null) {
            $stmt->bind_param("si", $email, $ignorarId);
        } else {
            $stmt->bind_param("s", $email);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $tipos = [];
        while ($row = $result->fetch_assoc()) {
            $tipos[] = (int)$row['tipo_utilizador_id'];
        }

        $stmt->close();
        return $tipos;
    }

    private function validarRegraTipoPorEmail($email, $tipo, $ignorarId = null) {
        $tiposExistentes = $this->obterTiposPorEmail($email, $ignorarId);
        $tipo = (int)$tipo;

        if (in_array($tipo, $tiposExistentes, true)) {
            $nomeTipo = $this->obterNomeTipoUtilizador($tipo);
            return [
                'ok' => false,
                'msg' => "Já existe uma conta de {$nomeTipo} com este email."
            ];
        }

        $temAdmin = in_array(1, $tiposExistentes, true);
        $temCliente = in_array(2, $tiposExistentes, true);

        if (($tipo === 1 && $temCliente) || ($tipo === 2 && $temAdmin)) {
            return [
                'ok' => false,
                'msg' => 'Este email já está associado a um perfil incompatível (Administrador/Cliente). Use um email diferente.'
            ];
        }

        return ['ok' => true, 'msg' => 'OK'];
    }

    function getClientes($ID_Utilizador){
        try {

        $msg = "";
        $sql = "SELECT utilizadores.*,Tipo_utilizadores.descricao As TipoUtilizadores from utilizadores,Tipo_utilizadores where tipo_utilizadores.id = utilizadores.tipo_utilizador_id AND utilizadores.id != ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Utilizador);
        $stmt->execute();
        $result = $stmt->get_result();

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
                    $msg .= "<button class='btn-action btn-edit' onclick='getDadosCliente(".$row["id"].")' title='Editar'>";
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

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getDadosCliente($ID_Utilizador){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT * FROM utilizadores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Utilizador);
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
    function guardaEditCliente($nome, $email, $telefone, $tipo,$nif,$plano,$rank,$ID_Utilizador){
        try {

        $msg = "";
        $flag = true;
        $sql = "";

        $sql = "UPDATE utilizadores SET nome = ?, email = ?, telefone = ?, tipo_utilizador_id = ?, plano_id = ?, nif = ?, ranking_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssissii", $nome, $email, $telefone, $tipo, $plano, $nif, $rank, $ID_Utilizador);

        if ($stmt->execute()) {
            $msg = "Editado com Sucesso";
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
    function registaClientes($nome, $email, $telefone, $tipo, $nif, $password, $morada = '', $foto = null){
        try {

    $msg = "";
    $flag = true;
    $sql = "";

    $nome = trim((string)$nome);
    $email = trim((string)$email);
    $telefone = trim((string)$telefone);
    $nif = trim((string)$nif);
    $morada = trim((string)$morada);
    $password = (string)$password;
    $tipo = (int)$tipo;

    if ($nome === '' || $email === '' || $tipo <= 0 || $password === '') {
        return json_encode([
            "flag" => false,
            "msg" => "Preencha os campos obrigatórios: nome, email, tipo de utilizador e senha."
        ], JSON_UNESCAPED_UNICODE);
    }

    if (!in_array($tipo, [1, 2, 3], true)) {
        return json_encode([
            "flag" => false,
            "msg" => "Tipo de utilizador inválido. Selecione Administrador, Cliente ou Anunciante."
        ], JSON_UNESCAPED_UNICODE);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return json_encode([
            "flag" => false,
            "msg" => "Email inválido."
        ], JSON_UNESCAPED_UNICODE);
    }

    $regraTipo = $this->validarRegraTipoPorEmail($email, $tipo);
    if (!$regraTipo['ok']) {
        return json_encode([
            "flag" => false,
            "msg" => $regraTipo['msg']
        ], JSON_UNESCAPED_UNICODE);
    }

    if (strlen($password) < 6) {
        return json_encode([
            "flag" => false,
            "msg" => "A senha deve ter pelo menos 6 caracteres."
        ], JSON_UNESCAPED_UNICODE);
    }

    $password_temporaria = $password;
    $password_hash = md5($password);

    $resp = $this->uploads($foto, $nome);
    $resp = json_decode($resp, TRUE);

    if($resp['flag']){
        $sql = "INSERT INTO utilizadores (nome, email, telefone, tipo_utilizador_id, nif, password, morada, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssissss", $nome, $email, $telefone, $tipo, $nif, $password_hash, $morada, $resp['target']);
    } else {
        $sql = "INSERT INTO utilizadores (nome, email, telefone, tipo_utilizador_id, nif, password, morada) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssisss", $nome, $email, $telefone, $tipo, $nif, $password_hash, $morada);
    }

    $nomeTipo = $this->obterNomeTipoUtilizador($tipo);

    if ($stmt->execute()) {
        $msg = $nomeTipo . " registado com sucesso!";

        try {
            require_once __DIR__ . '/../services/EmailService.php';
            $emailService = new EmailService($this->conn);
            $emailEnviado = $emailService->sendContaCriadaAdmin($email, $nome, $password_temporaria, $tipo);

            if ($emailEnviado) {
                $msg .= " Email enviado com as credenciais de acesso.";
            } else {
                $msg .= " Aviso: Email não foi enviado.";
            }
        } catch (Exception $e) {
            $msg .= " Aviso: Erro ao enviar email.";
        }
    } else {
        $flag = false;
        if ((int)$stmt->errno === 1062) {
            $erroLower = strtolower((string)$stmt->error);
            if (strpos($erroLower, 'email') !== false) {
                $msg = "Já existe um utilizador registado com este email.";
            } elseif (strpos($erroLower, 'nif') !== false) {
                $msg = "Já existe um utilizador registado com este NIF.";
            } else {
                $msg = "Já existe um utilizador com esses dados. Verifique email/NIF.";
            }
        } else {
            $msg = "Não foi possível registar o " . strtolower($nomeTipo) . ".";
        }
    }
    $stmt->close();

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg
    ), JSON_UNESCAPED_UNICODE);

    return $resp;
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro interno do servidor ao registar utilizador.'], JSON_UNESCAPED_UNICODE);
        }
}
    function removerClientes($ID_Cliente){
        try {

        $msg = "";
        $flag = true;

        $sql = "DELETE FROM utilizadores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Cliente);

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

    return json_encode(array(
        "flag" => $flag,
        "target" => $targetBD
    ), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
    function getCardUtilizadores(){
        try {

        $msg = "";
        $sql = "        SELECT
            COUNT(*) AS total,
            SUM(tipo_utilizador_id = 2) AS clientes,
            SUM(tipo_utilizador_id = 3) AS anunciantes,
            SUM(tipo_utilizador_id = 1) AS admins
        FROM utilizadores;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $msg  = '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Total Utilizadores</div>
                                    <div class="stat-value" id="totalClients">'.$row["total"].'</div>
                                </div>
                            </div>';

                    $msg .= '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Clientes</div>
                                    <div class="stat-value" id="totalNormalClients">'.$row["clientes"].'</div>
                                </div>
                            </div>';

                    $msg .= '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Anunciantes</div>
                                    <div class="stat-value" id="totalAdvertisers">'.$row["anunciantes"].'</div>
                                </div>
                            </div>';

                    $msg .= '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Administradores</div>
                                    <div class="stat-value" id="totalAdmins">'.$row["admins"].'</div>
                                </div>
                            </div>';
                }
            }
        else {
                                $msg  = '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Total Utilizadores</div>
                                    <div class="stat-value" id="totalClients">Não encontrado</div>
                                </div>
                            </div>';

                    $msg .= '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Clientes</div>
                                    <div class="stat-value" id="totalNormalClients">Não encontrado</div>
                                </div>
                            </div>';

                    $msg .= '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Anunciantes</div>
                                    <div class="stat-value" id="totalAdvertisers">Não encontrado</div>
                                </div>
                            </div>';

                    $msg .= '<div class="stat-card-compact">
                                <div class="stat-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="stat-label">Administradores</div>
                                    <div class="stat-value" id="totalAdmins">Não encontrado</div>
                                </div>
                            </div>';
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);
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

}
?>
