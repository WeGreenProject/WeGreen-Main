<?php

require_once 'connection.php';

class ProdutosAdmin{

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
    function getProdutosAprovar($estado){
        try {

        $msg = "";
            if($estado == "Todos")
            {
                $sql = "SELECT
                Encomendas.*,
                Tipo_Produtos.descricao AS TipoProdutoNome,
                Cliente.nome AS nomeCliente,
                Anunciante.nome AS nomeAnunciante,
                Produtos.preco AS Preco,
                Produtos.foto AS Foto
            FROM
                Encomendas
                INNER JOIN Vendas ON Encomendas.id = Vendas.encomenda_id
                INNER JOIN Tipo_Produtos ON Encomendas.TipoProdutoNome = Tipo_Produtos.id
                INNER JOIN Produtos ON Encomendas.produto_id = Produtos.Produto_id
                INNER JOIN Utilizadores AS Cliente ON Cliente.id = Encomendas.cliente_id
                INNER JOIN Utilizadores AS Anunciante ON Anunciante.id = Vendas.anunciante_id
            GROUP BY Encomendas.id";
            }
            else if($estado == 'Pendente' || $estado == 'Entregue' ||  $estado == 'Cancelada')
            {
                $sql = "SELECT
                Encomendas.*,
                Tipo_Produtos.descricao AS TipoProdutoNome,
                Cliente.nome AS nomeCliente,
                Anunciante.nome AS nomeAnunciante,
                Produtos.preco AS Preco,
                Produtos.foto AS Foto
            FROM
                Encomendas
                INNER JOIN Vendas ON Encomendas.id = Vendas.encomenda_id
                INNER JOIN Tipo_Produtos ON Encomendas.TipoProdutoNome = Tipo_Produtos.id
                INNER JOIN Produtos ON Encomendas.produto_id = Produtos.Produto_id
                INNER JOIN Utilizadores AS Cliente ON Cliente.id = Encomendas.cliente_id
                INNER JOIN Utilizadores AS Anunciante ON Anunciante.id = Vendas.anunciante_id
            WHERE Encomendas.estado LIKE ?
            GROUP BY Encomendas.id";

                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $estado);
                $stmt->execute();
                $result = $stmt->get_result();
            }
        if (!isset($result)) {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['id']."</th>";
                $msg .= "<td>".$row['codigo_encomenda']."</td>";
                $msg .= "<td>".$row['Preco']."€</td>";
                $msg .= "<td>".$row['nomeAnunciante']."</td>";
                $msg .= "<td>".$row['nomeCliente']."</td>";
                $msg .= "<td>".$row['plano_rastreio']."</td>";
                $msg .= "<td>".$row['TipoProdutoNome']."</td>";
                $msg .= "<td>".$row['data_envio']."</td>";
                $msg .= "<td>".$row['morada']."</td>";
                $msg .= "<td>".$row['estado']."</td>";
                $msg .= "<td><button class='btn btn-success' onclick ='pagarDividasPagar(".$row['id'].")'><i class='fa fa-trash'>Aceitar</i></button></td>";
                $msg .= "<td><button class='btn btn-danger' onclick ='recusarDividasPagar(".$row['id'].")'><i class='fa fa-trash'>Recusar</i></button></td>";
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
    function getProdutosPendentes(){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As EncomendasCount from Encomendas  where Encomendas.estado like 'Pendente';";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $msg .= "<div class='approval-stat-icon'>⏳</div>";
                    $msg .= "<div class='approval-stat-info'>";
                    $msg  .= "<div class='approval-stat-value'>".$row["EncomendasCount"]."</div>";
                    $msg .= "<div class='approval-stat-label'>Pendentes</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='profile-avatar'>";
                    $msg .= "<img src='src/img/default_user.png' alt='Erro a encontrar foto' id='userPhoto'>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
            }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
        function getProdutosAprovado(){
            try {

        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As EncomendasCount from Encomendas  where Encomendas.estado like 'Entregue';";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $msg .= "<div class='approval-stat-icon'>✅</div>";
                    $msg .= "<div class='approval-stat-info'>";
                    $msg  .= "<div class='approval-stat-value'>".$row["EncomendasCount"]."</div>";
                    $msg .= "<div class='approval-stat-label'>Entregues</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='profile-avatar'>";
                    $msg .= "<img src='src/img/default_user.png' alt='Erro a encontrar foto' id='userPhoto'>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
            }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);

            } catch (Exception $e) {
                return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
            }
    }
    function getProdutosRejeitado(){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As EncomendasCount from Encomendas  where Encomendas.estado like 'Cancelada';";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $msg .= "<div class='approval-stat-icon'>❌</div>";
                    $msg .= "<div class='approval-stat-info'>";
                    $msg  .= "<div class='approval-stat-value'>".$row["EncomendasCount"]."</div>";
                    $msg .= "<div class='approval-stat-label'>Cancelados</div>";
                    $msg .= "</div>";
                }
            }
            else
            {
                    $msg .= "<div class='profile-avatar'>";
                    $msg .= "<img src='src/img/default_user.png' alt='Erro a encontrar foto' id='userPhoto'>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
            }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
function getFiltro() {
        try {

    $msg = "";
    $sql1 = "SELECT COUNT(*) AS EncomendasCount1 FROM Encomendas";
    $sql2 = "SELECT COUNT(*) AS EncomendasCount2 FROM Encomendas WHERE estado = 'Cancelada';";
    $sql3 = "SELECT COUNT(*) AS EncomendasCount3 FROM Encomendas WHERE estado = 'Entregue';";
    $sql4 = "SELECT COUNT(*) AS EncomendasCount4 FROM Encomendas WHERE estado = 'Pendente';";

    $stmt1 = $this->conn->prepare($sql1);
    $stmt1->execute();
    $row1 = $stmt1->get_result()->fetch_assoc();

    $stmt2 = $this->conn->prepare($sql2);
    $stmt2->execute();
    $row2 = $stmt2->get_result()->fetch_assoc();

    $stmt3 = $this->conn->prepare($sql3);
    $stmt3->execute();
    $row3 = $stmt3->get_result()->fetch_assoc();

    $stmt4 = $this->conn->prepare($sql4);
    $stmt4->execute();
    $row4 = $stmt4->get_result()->fetch_assoc();

    $msg .= "<div class='add-order-section'>";
    $msg .= "<div class='simple-add-section'>";
    $msg .= "<button class='btn-add-order' onclick='adicionarEncomenda()'>";
    $msg .= "<span class='btn-icon'>➕</span>";
    $msg .= "<span>Adicionar Encomenda</span>";
    $msg .= "</button>";
    $msg .= "</div>";
    $msg .= "  <button class='filter-btn active' onclick='getProdutosAprovar(\"Todos\")'>Todos <span class='filter-badge' id='pendingBadge'>".$row1['EncomendasCount1']."</span></button>";
    $msg .= "  <button class='filter-btn' onclick='getProdutosAprovar(\"Pendente\")'>Pendentes <span class='filter-badge' id='pendingBadge'>".$row4['EncomendasCount4']."</span></button>";
    $msg .= "  <button class='filter-btn' onclick='getProdutosAprovar(\"Entregue\")'>Entregues <span class='filter-badge' id='approvedBadge'>".$row3['EncomendasCount3']."</span></button>";
    $msg .= "  <button class='filter-btn' onclick='getProdutosAprovar(\"Cancelada\")'>Cancelados <span class='filter-badge' id='rejectedBadge'>".$row2['EncomendasCount2']."</span></button>";
    $msg .= "</div>";

    if (isset($stmt1) && $stmt1) {
        $stmt1->close();
    }
    if (isset($stmt2) && $stmt2) {
        $stmt2->close();
    }
    if (isset($stmt3) && $stmt3) {
        $stmt3->close();
    }
    if (isset($stmt4) && $stmt4) {
        $stmt4->close();
    }

    return $msg;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
    function getProdutosTodos(){
        try {

        $msg = "";
        $sql = "SELECT Produtos.*,Tipo_Produtos.descricao As TipoProdutoNome,Utilizadores.nome AS nomeAnunciante from Produtos,Tipo_Produtos,Utilizadores where Produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = Produtos.anunciante_id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['id']."</th>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td>".$row['nomeAnunciante']."</td>";
                $msg .= "<td>".$row['TipoProdutoNome']."</td>";
                $msg .= "<td>".$row['data_criacao']."</td>";
                $msg .= "<td>".$row['estado']."</td>";
                $msg .= "<td><button class='btn btn-success' onclick ='pagarDividasPagar(".$row['id'].")'><i class='fa fa-trash'>Aceitar</i></button></td>";
                $msg .= "<td><button class='btn btn-danger' onclick ='recusarDividasPagar(".$row['id'].")'><i class='fa fa-trash'>Recusar</i></button></td>";
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
    function getFiltros(){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As ProdutosCount from Produtos  where produtos.estado like 'Rejeitado';";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $msg .= "<button class='filter-btn active'>";
                    $msg .= "Todos <span class='filter-badge' onclick='getProdutosTodos()'>0</span>";
                    $msg  .= "</button>";
                    $msg .= "<button class='filter-btn'>";
                    $msg .= "Pendentes <span class='filter-badge' onclick='getProdutosAprovar()'>0</span>";
                    $msg .= "</button>";
                }
            }
            else
            {
                    $msg .= "<div class='profile-avatar'>";
                    $msg .= "<img src='src/img/default_user.png' alt='Erro a encontrar foto' id='userPhoto'>";
                    $msg .= "</div>";

                    $msg .= "<div class='profile-details'>";
                    $msg .= "<div class='profile-name'>Erro a encontrar nome</div>";
                    $msg .= "<div class='profile-role'>Administrador</div>";
                    $msg .= "</div>";
            }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
