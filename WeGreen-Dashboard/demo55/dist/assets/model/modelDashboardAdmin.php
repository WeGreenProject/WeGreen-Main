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
    $msg = "";
    $flag = false;

    $sql = "SELECT Produtos.nome as Descricao,vendas.lucro As Saldo from Produtos,vendas where Produtos.id = vendas.produto_id;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dados1[] = $row['Descricao'];
            $dados2[] = $row['Saldo'];
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
function getTopTipoGrafico() {
    global $conn;
    $dados1 = [];
    $dados2 = [];
    $msg = "";
    $flag = false;

    $sql = "SELECT 
    tp.descricao AS Tipo_Produto, SUM(v.quantidade) AS Total_Vendido FROM Vendas v JOIN Produtos p ON v.produto_id = p.id JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id
    GROUP BY tp.id ORDER BY Total_Vendido DESC;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dados1[] = $row['Tipo_Produto'];
            $dados2[] = $row['Total_Vendido'];
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