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
                
                $msg  = "<div class='stat-icon'>⭐</div>";
                $msg .= "<div class='stat-label'>Anunciantes com Planos Ativos</div>";
                $msg .= "<div class='stat-value'>".$row["TotalAtivos"]."</div>";

            }
        }
        else
        {
                $msg  = "<div class='stat-icon'>⭐</div>";
                $msg .= "<div class='stat-label'>Anunciantes com Planos Ativos</div>";
                $msg .= "<div class='plan-badge'>Não Encontrado</div>";
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
                
                $msg  = "<div class='stat-icon'>👥</div>";
                $msg .= "<div class='stat-label'>Utilizadores</div>";
                $msg .= "<div class='stat-value'>".$row["TotalUtilizadores"]."</div>";
                $msg .= "<div class='stat-change'>+ ".$novos." Novos utilizadores</div>";
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

        $sql = "SELECT Sum(rendimento.valor) AS TotalRendimentos FROM rendimento";
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $msg  = "<div class='stat-icon'>📈</div>";
                $msg .= "<div class='stat-label'>Rendimentos</div>";
                $msg .= "<div class='stat-value'>".$row["TotalRendimentos"]."€</div>";

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
    function getGastos(){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT Sum(gastos.valor) As TotalGastos FROM gastos";
        
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $msg  = "<div class='stat-icon'>💸</div>";
                $msg .= "<div class='stat-label'>Gastos</div>";
                $msg .= "<div class='stat-value'>".$row["TotalGastos"]."€</div>";

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
}
?>