<?php

require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class GestaoProdutos {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getMeusProdutos($ID_User){
        try {

        $msg = "";
        $sql = "SELECT produtos.*,Tipo_Produtos.descricao As ProdutosNome, Utilizadores.nome  As NomeAnunciante from produtos,Tipo_Produtos,Utilizadores where produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = produtos.anunciante_id AND produtos.anunciante_id = ?";
        $text = "";
        $text2 = "";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['ativo'] == 1)
                {
                    $text = "<i class='fas fa-check-circle'></i> ATIVO";
                    $text2 = 'badge-ativo';
                }
                else if($row['ativo'] == 2)
                {
                    $text = "<i class='fas fa-times-circle'></i> REJEITADO";
                    $text2 = 'badge-rejeitado';
                }
                else
                {
                    $text = "<i class='fas fa-clock'></i> INATIVO";
                    $text2 = 'badge-inativo';
                }

                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
                $msg .= "<td><img src='".$row['foto']."' class='rounded-circle profile-img-small me-1' width='100px' style='object-fit: cover;'></td>";
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

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getInativos(){
        try {

        $msg = "";
        $sql = "SELECT produtos.*,Tipo_Produtos.descricao As ProdutosNome, Utilizadores.nome  As NomeAnunciante from produtos,Tipo_Produtos,Utilizadores where produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = produtos.anunciante_id AND produtos.anunciante_id AND produtos.ativo = 0;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $text = "";
        $text2 = "";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                if($row['ativo'] == 1)
                {
                    $text = "Ativo";
                    $text2 = 'badge-ativo';
                }
                else if($row['ativo'] == 2)
                {
                    $text = "Rejeitado";
                    $text2 = 'badge-rejeitado';
                }
                else
                {
                    $text = "Inativo";
                    $text2 = 'badge-inativo';
                }
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
                $msg .= "<td><img src='".$row['foto']."' class='rounded-circle profile-img-small me-1' width='100px' style='object-fit: cover;'></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['genero']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td><span class='status-badge ".$text2."'>".$text."</span></td>";

                if (!empty($row['motivo_rejeicao']) && $row['motivo_rejeicao'] === 'AGUARDAR_RESPOSTA_ANUNCIANTE') {
                    $msg .= "<td><button class='btn-edit' disabled title='A aguardar resposta do anunciante' style='opacity:0.6;cursor:not-allowed;'><i class='fas fa-hourglass-half'></i> A aguardar anunciante</button></td>";
                } else {
                    $msg .= "<td><button class='btn-edit' onclick='getDadosInativos(".$row['Produto_id'].")' title='Verificar Produto'><i class='fas fa-search'></i> Verificar</button></td>";
                }

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

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getDadosProduto($ID_Produto){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT Produtos.*,Tipo_Produtos.id As Valuecategoria,utilizadores.nome As vendedor FROM Produtos,Tipo_Produtos,Utilizadores WHERE Produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = Produtos.anunciante_id AND Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Produto);
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
    function getDadosInativos($ID_Produto){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT Produtos.*,Tipo_Produtos.id As Valuecategoria,utilizadores.nome As vendedor FROM Produtos,Tipo_Produtos,Utilizadores WHERE Produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = Produtos.anunciante_id AND Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_Produto);
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
    function getProdutos(){
        try {

        $msg = "";
        $sql = "SELECT produtos.*,Tipo_Produtos.descricao As ProdutosNome, Utilizadores.nome  As NomeAnunciante from produtos,Tipo_Produtos,Utilizadores where produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = produtos.anunciante_id AND produtos.anunciante_id;";
        $text = "";
        $text2 = "";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['ativo'] == 1)
                {
                    $text = "Ativo";
                    $text2 = 'badge-ativo';
                }
                else if($row['ativo'] == 2)
                {
                    $text = "Rejeitado";
                    $text2 = 'badge-rejeitado';
                }
                else
                {
                    $text = "Inativo";
                    $text2 = 'badge-inativo';
                }

                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
                $msg .= "<td><img src='".$row['foto']."' class='rounded-circle profile-img-small me-1' width='100px' style='object-fit: cover;'></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['NomeAnunciante']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td><span class='status-badge ".$text2."'>".$text."</span></td>";
                $msg .= "<td>".$row['marca']."</td>";
                $msg .= "<td>";

                if ($row['ativo'] == 0) {
                    if (!empty($row['motivo_rejeicao']) && $row['motivo_rejeicao'] === 'AGUARDAR_RESPOSTA_ANUNCIANTE') {
                        $msg .= "<button class='btn-edit' disabled title='A aguardar resposta do anunciante' style='margin-bottom: 8px;opacity:0.6;cursor:not-allowed;'><i class='fas fa-hourglass-half'></i> A aguardar anunciante</button><br>";
                    } else {
                        $msg .= "<button class='btn-edit' onclick='getDadosInativos(".$row['Produto_id'].")' title='Verificar Produto' style='margin-bottom: 8px;'><i class='fas fa-search'></i> Verificar</button><br>";
                    }
                } elseif ($row['ativo'] == 1) {
                    $msg .= "<button class='btn-edit' disabled title='Produto já aprovado' style='margin-bottom: 8px;opacity:0.6;cursor:not-allowed;'><i class='fas fa-check-circle'></i> Aprovado</button><br>";
                } else {
                    $msg .= "<button class='btn-edit' disabled title='Produto rejeitado' style='margin-bottom: 8px;opacity:0.6;cursor:not-allowed;background:#dc2626;'><i class='fas fa-times-circle'></i> Rejeitado</button><br>";
                }
                if ($row['ativo'] == 1) {
                    $msg .= "<button class='btn-desativar' onclick='getDesativacao(".$row['Produto_id'].")' title='Desativar Produto'><i class='fas fa-times-circle'></i> Desativar</button>";
                } else {
                    $msg .= "<button class='btn-desativar' disabled title='Produto já inativo ou rejeitado' style='opacity:0.6;cursor:not-allowed;'><i class='fas fa-times-circle'></i> Desativar</button>";
                }
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
    function getListaVendedores(){
        try {

        $msg = "";
        $sql = "SELECT utilizadores.nome As NomeUtilizadores ,utilizadores.id As ValueUtilizador FROM utilizadores,Tipo_utilizadores where Tipo_utilizadores.id = utilizadores.tipo_utilizador_id AND Tipo_utilizadores.id IN (1, 3);";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $msg .= "<option value='-1'>Selecionar cliente...</option>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {

                $msg .= "<option value=".$row["ValueUtilizador"].">".$row["NomeUtilizadores"]."</option>";
            }
        } else {
                $msg .= "<option value='-1'>Selecionar cliente...</option>";
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
    function getDesativacao($Produto_id){
        try {

        $msg = "";
        $flag = true;
        $sql = "";

        $sql = "UPDATE Produtos
            SET ativo = 0,
                motivo_rejeicao = 'AGUARDAR_RESPOSTA_ANUNCIANTE'
                WHERE Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $Produto_id);

        if ($stmt->execute()) {
            $msg = "Desativado com Sucesso";
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
    function rejeitaEditProduto($Produto_id, $motivo_rejeicao = ''){
        try {

        $msg = "";
        $flag = true;


        $sqlDados = "SELECT p.nome AS nome_produto, u.email, u.nome AS nome_anunciante
                     FROM Produtos p
                     INNER JOIN Utilizadores u ON p.anunciante_id = u.id
                     WHERE p.Produto_id = ?";
        $stmtDados = $this->conn->prepare($sqlDados);
        $stmtDados->bind_param("i", $Produto_id);
        $stmtDados->execute();
        $resultDados = $stmtDados->get_result();
        $dadosAnunciante = $resultDados->fetch_assoc();

        $sql = "UPDATE Produtos
        SET ativo = 2, motivo_rejeicao = ? WHERE Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $motivo_rejeicao, $Produto_id);

        if ($stmt->execute()) {
            $msg = "Rejeitado com Sucesso";


            if ($dadosAnunciante && !empty($dadosAnunciante['email'])) {
                $this->enviarEmailRejeicaoProduto(
                    $dadosAnunciante['email'],
                    $dadosAnunciante['nome_anunciante'],
                    $dadosAnunciante['nome_produto'],
                    $Produto_id,
                    $motivo_rejeicao
                );
            }
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

    function aprovarProduto($Produto_id){
        try {
        $msg = "";
        $flag = true;

        $sql = "UPDATE Produtos SET ativo = 1, motivo_rejeicao = NULL WHERE Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $Produto_id);

        if ($stmt->execute()) {
            $msg = "Produto aprovado com sucesso";
        } else {
            $flag = false;
            $msg = "Erro ao aprovar produto";
        }

        return json_encode(array("flag" => $flag, "msg" => $msg), JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    private function enviarEmailRejeicaoProduto($email, $nomeAnunciante, $nomeProduto, $produtoId, $motivo) {
        try {
            $emailService = new EmailService($this->conn);
            $subject = "WeGreen - Produto Rejeitado: " . $nomeProduto;

            $htmlBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: "Inter", "Segoe UI", Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
                    .header { background: linear-gradient(135deg, #2e8b57, #3cb371); padding: 30px; text-align: center; }
                    .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
                    .content { padding: 30px; }
                    .content h2 { color: #1f2937; font-size: 20px; margin-top: 0; }
                    .content p { color: #4b5563; line-height: 1.6; }
                    .reason-box { background: #fef2f2; border-left: 4px solid #dc2626; padding: 15px 20px; margin: 20px 0; border-radius: 0 8px 8px 0; }
                    .reason-box strong { color: #dc2626; }
                    .reason-box p { color: #374151; margin: 8px 0 0 0; }
                    .product-info { background: #f9fafb; padding: 15px; border-radius: 8px; margin: 15px 0; }
                    .product-info span { color: #6b7280; font-size: 14px; }
                    .product-info strong { color: #1f2937; }
                    .btn { display: inline-block; padding: 12px 24px; background: #2e8b57; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 15px; }
                    .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>WeGreen</h1>
                    </div>
                    <div class="content">
                        <h2>Produto Rejeitado</h2>
                        <p>Olá <strong>' . htmlspecialchars($nomeAnunciante) . '</strong>,</p>
                        <p>Informamos que o seu produto foi analisado pela nossa equipa e, infelizmente, não foi aprovado.</p>
                        <div class="product-info">
                            <span>Produto:</span><br>
                            <strong>' . htmlspecialchars($nomeProduto) . '</strong> (ID: ' . $produtoId . ')
                        </div>
                        <div class="reason-box">
                            <strong>Motivo da Rejeição:</strong>
                            <p>' . htmlspecialchars($motivo ?: 'Sem motivo especificado.') . '</p>
                        </div>
                        <p>Pode editar o produto e submetê-lo novamente para aprovação.</p>
                        <a href="https://wegreen.pt/gestaoProdutosAnunciante.php" class="btn">Gerir Produtos</a>
                    </div>
                    <div class="footer">
                        <p>&copy; ' . date('Y') . ' WeGreen. Todos os direitos reservados.</p>
                    </div>
                </div>
            </body>
            </html>';

            $emailService->send($email, $subject, $htmlBody);
        } catch (Exception $e) {
        }
    }
    function guardaEditProduto($nome, $categoria, $marca, $tamanho,$preco,$genero,$vendedor,$Produto_id){
        try {

        $msg = "";
        $flag = true;
        $sql = "";

        $sql = "UPDATE Produtos
        SET nome = ?,
            tipo_produto_id = ?,
            marca = ?,
            tamanho = ?,
            preco = ?,
            genero = ?,
            anunciante_id = ?,
            ativo = 1
        WHERE Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sissdsii", $nome, $categoria, $marca, $tamanho, $preco, $genero, $vendedor, $Produto_id);

        if ($stmt->execute()) {
            $msg = "Aprovado com Sucesso";
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
function adicionarProdutos($listaVendedor, $listaCategoria, $nomeprod, $estadoprod, $quantidade, $preco, $marca, $tamanho, $selectestado, $foto){
        try {

    $msg = "";
    $flag = true;
    $sql = "";
    $genero = $selectestado;
    $descricao = $nomeprod;
    $ativo = 1;

    $resp = $this->uploads($foto, $nomeprod);
    $resp = json_decode($resp, TRUE);

    if($resp['flag']){
$sql = "INSERT INTO Produtos (tipo_produto_id, preco, foto, genero,anunciante_id, marca, tamanho, estado, descricao, ativo, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdssissssii", $listaCategoria, $preco, $resp['target'], $genero,$listaVendedor, $marca, $tamanho, $estadoprod, $descricao, $ativo,$quantidade);
    } else {
        $sql = "INSERT INTO Produtos (tipo_produto_id, preco, anunciante_id, marca, tamanho, estado, nome, ativo, genero, descricao)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, 'Descrição do produto')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdssssss", $listaCategoria, $preco, $listaVendedor, $marca, $tamanho, $estadoprod, $nomeprod, $selectestado);
    }

    if ($stmt->execute()) {
        $msg = "Produto adicionado com sucesso!";
    } else {
        $flag = false;
        $msg = "Error: " . $stmt->error;
    }
    $stmt->close();

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg
    ), JSON_UNESCAPED_UNICODE);

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
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
                    $msg .= "<span class='avatar-placeholder'>👤</span>";
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
    ), JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

    function guardaDadosEditProduto($nome, $categoria, $marca, $tamanho,$preco,$genero,$vendedor,$Produto_id){
        try {

        $msg = "";
        $flag = true;
        $sql = "";

        $sql = "UPDATE Produtos
        SET nome = ?,
            tipo_produto_id = ?,
            marca = ?,
            tamanho = ?,
            preco = ?,
            genero = ?,
            anunciante_id = ?
        WHERE Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sissdsii", $nome, $categoria, $marca, $tamanho, $preco, $genero, $vendedor, $Produto_id);

        if ($stmt->execute()) {
            $msg = "Aprovado com Sucesso";
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
    function getFotosSection($Produto_id) {
        try {

        $msg = "";
        $Produto_id = intval($Produto_id);


        $sqlMain = "SELECT foto FROM produtos WHERE Produto_id = ?";
        $stmtMain = $this->conn->prepare($sqlMain);
        $stmtMain->bind_param("i", $Produto_id);
        $stmtMain->execute();
        $resultMain = $stmtMain->get_result();
        $fotoPrincipal = "";

        if ($resultMain && $resultMain->num_rows > 0) {
            $rowMain = $resultMain->fetch_assoc();
            $fotoPrincipal = $rowMain['foto'];
        }


        $sql = "SELECT * FROM produto_fotos WHERE produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $Produto_id);
        $stmt->execute();
        $result = $stmt->get_result();


        $msg .= "<div id='productGalleryVerify' class='carousel slide' data-bs-ride='carousel' style='max-width: 100%; margin: 0 auto;'>";
        $msg .= "<div class='carousel-inner rounded-4 shadow-sm'>";


        if (!empty($fotoPrincipal)) {
            $msg .= "<div class='carousel-item active'>";
            $msg .= "<img src='".$fotoPrincipal."' class='d-block w-100 rounded-4' style='height: 400px; object-fit: cover;' alt='Foto Principal'>";
            $msg .= "</div>";
        }


        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<div class='carousel-item'>";
                $msg .= "<img src='".$row["foto"]."' class='d-block w-100 rounded-4' style='height: 400px; object-fit: cover;' alt='Foto Adicional'>";
                $msg .= "</div>";
            }
        }


        if (empty($fotoPrincipal) && (!$result || $result->num_rows == 0)) {
            $msg .= "<div class='carousel-item active'>";
            $msg .= "<div class='d-flex align-items-center justify-content-center' style='height: 400px; background: #f3f4f6; border-radius: 12px;'>";
            $msg .= "<div style='text-align: center; color: #64748b;'>";
            $msg .= "<i class='fas fa-image' style='font-size: 48px; margin-bottom: 10px;'></i>";
            $msg .= "<p style='margin: 0;'>Sem fotos disponíveis</p>";
            $msg .= "</div></div></div>";
        }

        $msg .= "</div>";


        $totalFotos = ($result ? $result->num_rows : 0) + (!empty($fotoPrincipal) ? 1 : 0);
        if ($totalFotos > 1) {
            $msg .= "<button class='carousel-control-prev' type='button' data-bs-target='#productGalleryVerify' data-bs-slide='prev'>";
            $msg .= "<span class='carousel-control-prev-icon' aria-hidden='true'></span>";
            $msg .= "<span class='visually-hidden'>Anterior</span>";
            $msg .= "</button>";
            $msg .= "<button class='carousel-control-next' type='button' data-bs-target='#productGalleryVerify' data-bs-slide='next'>";
            $msg .= "<span class='carousel-control-next-icon' aria-hidden='true'></span>";
            $msg .= "<span class='visually-hidden'>Próximo</span>";
            $msg .= "</button>";
        }

        $msg .= "</div>";

        if (isset($stmtMain) && $stmtMain) {
            $stmtMain->close();
        }
        if (isset($stmt) && $stmt) {
            $stmt->close();
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
    function getTopTipoGrafico() {
        try {
    $dados1 = [];
    $dados2 = [];
    $msg = "";
    $flag = false;

    $sql = "SELECT
        utilizadores.id AS Cliente_ID,
        utilizadores.nome AS Cliente_Nome,
        COUNT(produtos.produto_id) AS Produtos_Anunciados
        FROM
        utilizadores,produtos where utilizadores.id = produtos.anunciante_id
        GROUP BY
        utilizadores.id, utilizadores.nome;";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dados1[] = $row['Cliente_Nome'];
            $dados2[] = $row['Produtos_Anunciados'];
        }
        $flag = true;
    } else {
        $msg = "Nenhum Serviço encontrado.";
    }

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg,
        "dados1" => $dados1,
        "dados2" => $dados2
    ), JSON_UNESCAPED_UNICODE);

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
    function getProdutoVendidos() {
        try {

    $dados1 = [];
    $dados2 = [];
    $msg = "";
    $flag = false;

    $sql = "SELECT Utilizadores.nome AS Anunciante_Nome, SUM(Vendas.quantidade) AS Produtos_Vendidos FROM Vendas,Utilizadores where Utilizadores.id = Vendas.anunciante_id
    GROUP BY Utilizadores.id, Utilizadores.nome;";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dados1[] = $row['Anunciante_Nome'];
            $dados2[] = $row['Produtos_Vendidos'];
        }
        $flag = true;
    } else {
        $msg = "Nenhum Serviço encontrado.";
    }

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg,
        "dados1" => $dados1,
        "dados2" => $dados2
    ), JSON_UNESCAPED_UNICODE);

    if (isset($stmt) && $stmt) {
        $stmt->close();
    }

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
}
?>
