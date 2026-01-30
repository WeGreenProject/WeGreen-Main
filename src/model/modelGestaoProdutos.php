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
                $msg .= "<td><button class='btn-edit' onclick='getDadosInativos(".$row['Produto_id'].")' title='Verificar Produto'><i class='fas fa-search'></i> Verificar</button></td>";
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
                $msg .= "<button class='btn-edit' onclick='getDadosInativos(".$row['Produto_id'].")' title='Verificar Produto' style='margin-bottom: 8px;'><i class='fas fa-search'></i> Verificar</button><br>";
                $msg .= "<button class='btn-desativar' onclick='getDesativacao(".$row['Produto_id'].")' title='Desativar Produto'><i class='fas fa-times-circle'></i> Desativar</button>";
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
    function getDesativacao($Produto_id){

        global $conn;
        $msg = "";
        $flag = true;
        $sql = "";


        $sql = "UPDATE Produtos
                SET ativo = 0
                WHERE Produto_id = " . $Produto_id;

        if ($conn->query($sql) === TRUE) {
            $msg = "Desativado com Sucesso";
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
function adicionarProdutos($listaVendedor, $listaCategoria, $nomeprod, $estadoprod, $quantidade, $preco, $marca, $tamanho, $selectestado, $foto){

    global $conn;
    $msg = "";
    $flag = true;
    $sql = "";

    $resp = $this->uploads($foto, $nomeprod);
    $resp = json_decode($resp, TRUE);

    if($resp['flag']){
$sql = "INSERT INTO Produtos (tipo_produto_id, preco, foto, genero,anunciante_id, marca, tamanho, estado, descricao, ativo, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssissssii", $listaCategoria, $preco, $resp['target'], $genero,$listaVendedor, $marca, $tamanho, $estadoprod, $descricao, $ativo,$quantidade);
    } else {
        $sql = "INSERT INTO Produtos (tipo_produto_id, preco, anunciante_id, marca, tamanho, estado, nome, ativo, genero, descricao)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, 'Descrição do produto')";
        $stmt = $conn->prepare($sql);
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
    ));

    $conn->close();

    return $resp;
}
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

        // Buscar foto principal do produto
        $sqlMain = "SELECT foto FROM produtos WHERE Produto_id = ".$Produto_id;
        $resultMain = $conn->query($sqlMain);
        $fotoPrincipal = "";

        if ($resultMain && $resultMain->num_rows > 0) {
            $rowMain = $resultMain->fetch_assoc();
            $fotoPrincipal = $rowMain['foto'];
        }

        // Buscar fotos adicionais
        $sql = "SELECT * FROM produto_fotos WHERE produto_id = ".$Produto_id;
        $result = $conn->query($sql);

        // Criar carrossel Bootstrap
        $msg .= "<div id='productGalleryVerify' class='carousel slide' data-bs-ride='carousel' style='max-width: 100%; margin: 0 auto;'>";
        $msg .= "<div class='carousel-inner rounded-4 shadow-sm'>";

        // Primeira imagem (foto principal) - sempre ativa
        if (!empty($fotoPrincipal)) {
            $msg .= "<div class='carousel-item active'>";
            $msg .= "<img src='".$fotoPrincipal."' class='d-block w-100 rounded-4' style='height: 400px; object-fit: cover;' alt='Foto Principal'>";
            $msg .= "</div>";
        }

        // Fotos adicionais
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<div class='carousel-item'>";
                $msg .= "<img src='".$row["foto"]."' class='d-block w-100 rounded-4' style='height: 400px; object-fit: cover;' alt='Foto Adicional'>";
                $msg .= "</div>";
            }
        }

        // Se não houver fotos, mostrar placeholder
        if (empty($fotoPrincipal) && (!$result || $result->num_rows == 0)) {
            $msg .= "<div class='carousel-item active'>";
            $msg .= "<div class='d-flex align-items-center justify-content-center' style='height: 400px; background: #f3f4f6; border-radius: 12px;'>";
            $msg .= "<div style='text-align: center; color: #64748b;'>";
            $msg .= "<i class='fas fa-image' style='font-size: 48px; margin-bottom: 10px;'></i>";
            $msg .= "<p style='margin: 0;'>Sem fotos disponíveis</p>";
            $msg .= "</div></div></div>";
        }

        $msg .= "</div>"; // Fim carousel-inner

        // Controles do carrossel (apenas se houver mais de uma foto)
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

        $msg .= "</div>"; // Fim carousel

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
    function getTopTipoGrafico() {
    global $conn;
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
    $result = $conn->query($sql);

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
    ));

    $conn->close();
    return $resp;
}
    function getProdutoVendidos() {
    global $conn;
    $dados1 = [];
    $dados2 = [];
    $msg = "";
    $flag = false;

    $sql = "SELECT Utilizadores.nome AS Anunciante_Nome, SUM(Vendas.quantidade) AS Produtos_Vendidos FROM Vendas,Utilizadores where Utilizadores.id = Vendas.anunciante_id
    GROUP BY Utilizadores.id, Utilizadores.nome;";
    $result = $conn->query($sql);

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
    ));

    $conn->close();
    return $resp;
}
}
?>
