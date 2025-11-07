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
                $msg .= "<div class='plan-badge'>".$row["TotalAtivos"]."</div>";

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
}
?>