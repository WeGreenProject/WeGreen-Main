<?php

require_once 'connection.php';

class ProdutosAdmin{

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
        $conn->close();

        return ($msg);

    }
    function getProdutosAprovar($estado){
        global $conn;
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
            WHERE Encomendas.estado LIKE '$estado'
            GROUP BY Encomendas.id";
            }
        $result = $conn->query($sql);

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
        $conn->close();

        return ($msg);
    }
    function getProdutosPendentes(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As EncomendasCount from Encomendas  where Encomendas.estado like 'Pendente';";

        $result = $conn->query($sql);

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
        $conn->close();

        return ($msg);

    }
        function getProdutosAprovado(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As EncomendasCount from Encomendas  where Encomendas.estado like 'Entregue';";

        $result = $conn->query($sql);

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
        $conn->close();

        return ($msg);

    }
    function getProdutosRejeitado(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As EncomendasCount from Encomendas  where Encomendas.estado like 'Cancelada';";

        $result = $conn->query($sql);

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
        $conn->close();

        return ($msg);

    }
function getFiltro() {
    global $conn;
    $msg = "";
    $sql1 = "SELECT COUNT(*) AS EncomendasCount1 FROM Encomendas";
    $sql2 = "SELECT COUNT(*) AS EncomendasCount2 FROM Encomendas WHERE estado = 'Cancelada';";
    $sql3 = "SELECT COUNT(*) AS EncomendasCount3 FROM Encomendas WHERE estado = 'Entregue';";
    $sql4 = "SELECT COUNT(*) AS EncomendasCount4 FROM Encomendas WHERE estado = 'Pendente';";

    $row1 = $conn->query($sql1)->fetch_assoc();
    $row2 = $conn->query($sql2)->fetch_assoc();
    $row3 = $conn->query($sql3)->fetch_assoc();
    $row4 = $conn->query($sql4)->fetch_assoc();

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

    $conn->close();

    return $msg;
}
    function getProdutosTodos(){

        global $conn;
        $msg = "";
        $sql = "SELECT Produtos.*,Tipo_Produtos.descricao As TipoProdutoNome,Utilizadores.nome AS nomeAnunciante from Produtos,Tipo_Produtos,Utilizadores where Produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = Produtos.anunciante_id;";
        $result = $conn->query($sql);

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
        $conn->close();

        return ($msg);
    }
    function getFiltros(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT Count(*) As ProdutosCount from Produtos  where produtos.estado like 'Rejeitado';";

        $result = $conn->query($sql);

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
        $conn->close();

        return ($msg);

    }
}
?>
