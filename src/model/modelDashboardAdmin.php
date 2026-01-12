<?php

require_once 'connection.php';

class DashboardAdmin{

    function getDadosPlanos($ID_User,$plano){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT COUNT(*) AS TotalAtivos FROM Utilizadores WHERE Utilizadores.plano_id IN (2, 3);";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {

                $msg  = " <div class='stat-icon'><i class='fas fa-crown'></i></div>";
                $msg .= "<div class='stat-label'>Planos Ativos</div>";
                $msg .= "<div class='stat-value'>".$row["TotalAtivos"]."</div>";

            }
        }
        else
        {
                $msg  = " <div class='stat-icon'><i class='fas fa-crown'></i></div>";
                $msg .= "<div class='stat-label'>Planos Ativos</div>";
                $msg .= "<div class='stat-value'>Não Encontrado</div>";
        }
        $conn->close();

        return ($msg);

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
    function getUtilizadores($ID_User){
        global $conn;
        $msg = "";
        $row = "";
        $novos = $this->getNovosUtilizadores();
        $sql = "SELECT count(*) As TotalUtilizadores from Utilizadores,Tipo_Utilizadores where Utilizadores.tipo_utilizador_id = Tipo_Utilizadores.id AND utilizadores.tipo_utilizador_id IN (2, 3);";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {

                $msg  = "<div class='stat-icon'><i class='fas fa-users'></i></div>";
                $msg .= "<div class='stat-label'>Utilizadores Ativos</div>";
                $msg .= "<div class='stat-value'>".$row["TotalUtilizadores"]."</div>";
                $msg .= "<div class='stat-change'><i class='fas fa-arrow-up'></i> ".$novos." Novos utilizadores</div>";
            }
        }
        else
        {
                $msg  = "<div class='stat-icon'>👥</div>";
                $msg .= "<div class='stat-label'>Utilizadores</div>";
                $msg .= "<div class='stat-value'>Nao Encontrado!</div>";
                $msg .= "<div class='stat-change'>+ X Novos utilizadores</div>";
        }
        $conn->close();

        return ($msg);

    }
    function getInfoUserDropdown($ID_User){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT * from Utilizadores where id = ".$ID_User;

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {

                $msg  = "<div class='dropdown-header'>";
                $msg .= "    <img src='" . $row['foto']."' alt='Usuário' class='dropdown-avatar'>";
                $msg .= "    <div>";
                $msg .= "        <div class='dropdown-name'>" . $row['nome']. "</div>";
                $msg .= "        <div class='dropdown-email'>" . $row['email']. "</div>";
                $msg .= "    </div>";
                $msg .= "</div>";

                $msg .= "<div class='dropdown-divider'></div>";

                $msg .= "<button class='dropdown-item' onclick=\"showPage('profile', null); closeUserDropdown();\">";
                $msg .= "    <i class='fas fa-user'></i>";
                $msg .= "    <span><a href='perfilAdmin.php'>Meu Perfil</a></span>";
                $msg .= "</button>";

                $msg .= "<button class='dropdown-item' onclick='showPasswordModal()'>";
                $msg .= "    <i class='fas fa-key'></i>";
                $msg .= "    <span>Alterar Senha</span>";
                $msg .= "</button>";

                $msg .= "<div class='dropdown-divider'></div>";

                $msg .= "<button class='dropdown-item dropdown-item-danger' onclick='logout()'>";
                $msg .= "    <i class='fas fa-sign-out-alt'></i>";
                $msg .= "    <span>Sair</span>";
                $msg .= "</button>";
            }
        }
        else
        {
                $msg  = "<div class='stat-icon'>👥</div>";
                $msg .= "<div class='stat-label'>Utilizadores</div>";
                $msg .= "<div class='stat-value'>Nao Encontrado!</div>";
                $msg .= "<div class='stat-change'>+ X Novos utilizadores</div>";
        }
        $conn->close();

        return ($msg);

    }
    function logout(){

        session_start();
        session_destroy();

        return("Obrigado!");
    }
    function getNovosUtilizadores(){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT COUNT(*) AS novos FROM Utilizadores WHERE data_criacao >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novos = $row["novos"];
        }

        return $novos;

    }
    function getRendimentos(){
            global $conn;
            $msg = "";
            $row = "";
            $novos = $this->getNovosRendimentos();
            $sql = "SELECT Sum(rendimento.valor) AS TotalRendimentos FROM rendimento";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $msg  = "<div class='stat-icon'><i class='fas fa-euro-sign'></i></div>";
                    $msg .= "<div class='stat-label'>Rendimentos Totais</div>";
                    $msg .= "<div class='stat-value'>".$row["TotalRendimentos"]."€</div>";
                    $msg .= "<div class='stat-change'><i class='fas fa-arrow-up'></i> ".$novos."€ Ganhos rencentes</div>";

                }
            }
            else
            {
                    $msg  = "<div class='stat-icon>📈</div>";
                    $msg .= "<div class='stat-label'>Rendimentos</div>";
                    $msg .= "<div class='stat-value'>Rendimentos não Encontrado!</div>";
            }
            $conn->close();

            return ($msg);

        }
        function getAdminPerfil($ID_User){
    global $conn;
    $msg = "";
    $row = "";
    $sql = "SELECT * from utilizadores where id = ".$ID_User;

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $msg  = "<div class='user-avatar'><img src='".$row["foto"]."' alt='Avatar'></div>";
            $msg .= "<div class='user-info'>";
            $msg .= "<span class='user-name'>".$row["nome"]."</span>";
            $msg .= "<span class='user-role'>Administrador</span>";
            $msg .= "</div>";
        }
    }
    else
    {
        $msg  = "<div class='user-avatar'>A</div>";
        $msg .= "<div class='user-info'>";
        $msg .= "<span class='user-name'>Administrador</span>";
        $msg .= "<span class='user-role'>Admin</span>";
        $msg .= "</div>";
    }
    $conn->close();

    return ($msg);
}
        function getNovosRendimentos(){
        global $conn;
        $msg = "";
        $row = "";
        $sql = "SELECT sum(rendimento.valor) NovoRendimento from rendimento where data_registo >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);";

        $result = $conn->query($sql);


        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novos = $row["NovoRendimento"];
        }

        return $novos;

    }
    function getGastos(){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT Sum(gastos.valor) As TotalGastos FROM gastos";

        $result = $conn->query($sql);
        $novos = $this->getNovosGastos();
        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $msg  = "<div class='stat-icon'><i class='fas fa-credit-card'></i></div>";
                $msg .= "<div class='stat-label'>Gastos Totais</div>";
                $msg .= "<div class='stat-value'>".$row["TotalGastos"]."€</div>";
                $msg .= "<div class='stat-change'><i class='fas fa-arrow-down'></i> ".$novos."€ Gastos rencentes</div>";

            }
        }
        else
        {
                $msg  = "<div class='stat-icon>💸</div>";
                $msg .= "<div class='stat-label'>Gastos</div>";
                $msg .= "<div class='stat-value'>Gastos não Encontrado!</div>";
        }
        $conn->close();

        return ($msg);

    }
    function getNovosGastos(){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT sum(gastos.valor) NovoGastos from Gastos where data_registo >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);";

        $result = $conn->query($sql);


        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novos = $row["NovoGastos"];
        }

        return $novos;

    }
function getVendasGrafico() {
    global $conn;
    $dados1 = [];
    $dados2 = [];
    $dados3 = [];
    $msg = "";
    $flag = false;

        $meses = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];


    $sql = "SELECT rendimento.valor As ValorRendimentos,rendimento.data_registo As RegistoRendimento,gastos.data_registo As RegistoGastos,gastos.valor As ValorGastos from rendimento,gastos;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataGastos = strtotime($row['RegistoGastos']);
             $mesGastos = date('n', $dataGastos);
              $mesGastos = $meses[$mesGastos - 1];
            $dados1[] = $mesGastos;
            $dados2[] = $row['ValorRendimentos'];
            $dados3[] = $row['ValorGastos'];
        }
        $flag = true;
    } else {
        $msg = "Nenhum Serviço encontrado.";
    }

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg,
        "dados1" => $dados1,
        "dados2" => $dados2,
        "dados3" => $dados3
    ));

    $conn->close();
    return $resp;
}
    function getProdutosInvativo(){
        global $conn;
        $msg = "";
        $sql = "SELECT produtos.*,Tipo_Produtos.descricao As ProdutosNome, Utilizadores.nome  As NomeAnunciante from produtos,Tipo_Produtos,Utilizadores where produtos.tipo_produto_id = Tipo_Produtos.id AND Utilizadores.id = produtos.anunciante_id AND produtos.anunciante_id AND produtos.ativo = 0;";
        $result = $conn->query($sql);
        $text = "";
        $text2 = "";


        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
                $msg .= "<td><img src=".$row['foto']." class='rounded-circle profile-img-small me-1' width='100px'></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td>".$row['stock']."</td>";
                $msg .= "<td><button class='btn-info' onclick='getDadosInativos(".$row['Produto_id'].")'></button></td>";
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
function getTopTipoGrafico() {
    global $conn;
    $dados1 = [];
    $dados2 = [];
    $msg = "";
    $flag = false;

    $sql = "SELECT tipo_produtos.descricao As Tipo_Produto,count(*) As Vendido from vendas,tipo_produtos,produtos where produtos.produto_id = tipo_produtos.id AND produtos.produto_id = vendas.produto_id group BY tipo_produtos.descricao;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dados1[] = $row['Tipo_Produto'];
            $dados2[] = $row['Vendido'];
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