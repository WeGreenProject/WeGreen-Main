<?php

require_once 'connection.php';

class DashboardAnunciante{

    function getDadosPlanos($ID_User,$plano){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = " . $ID_User;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                if($plano == 1)
                {
                    $msg  = "<div class='stat-icon'>⭐</div>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='plan-badge'>Free</div>";
                    $msg .= "<div class='stat-change'>Plano Atual Infinito</div>";
                }
                else if($plano == 2)
                {
                    $msg  = "<div class='stat-icon'>⭐</div>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='plan-badge'>Premium</div>";
                    $msg .= "<div class='stat-change'>Renovação em 23 dias</div>";
                }
                else if($plano == 3)
                {
                    $msg  = "<div class='stat-icon'>⭐</div>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='plan-badge'>EnterPrise</div>";
                    $msg .= "<div class='stat-change'>Renovação em 23 dias</div>";
                }

            }
            
        }
        else
        {
            $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
            $msg .= "<h6 class='mb-0 text-wegreen-accent'>Dectetamos um erro na sua conta!</h6>";
            $msg .= "</div></li>";
            $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
        }
        $conn->close();
        
        return ($msg);

    }
    function CarregaProdutos($ID_User){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT COUNT(*) As StockProdutos FROM Produtos WHERE anunciante_id = " . $ID_User;
        $result = $conn->query($sql);

        
        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                
                $msg  = "<div class='stat-icon'>📦</div>";
                $msg .= "<div class='stat-label'>Produtos em Stock</div>";
                $msg .= "<div class='stat-value'>".$row["StockProdutos"]."</div>";
                $msg .= "<div class='stat-change'>+3 produtos novos</div>";

            }
            
        }
        else
        {
                $msg  = "<div class='stat-icon'>📦</div>";
                $msg .= "<div class='stat-label'>Produtos em Stock</div>";
                $msg .= "<div class='plan-badge'>Erro a Encontrar Produtos</div>";
                $msg .= "<div class='stat-change'>+3 produtos novos</div>";
        }
        $conn->close();

        return ($msg);

    }
    function CarregaPontos($ID_User){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = " . $ID_User;
        $result = $conn->query($sql);

        
        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                
                $msg  = "<div class='stat-icon'>🎯</div>";
                $msg .= "<div class='stat-label'>Pontos de Confiança</div>";
                $msg .= "<div class='stat-value'>".$row['pontos_conf']."</div>";
                $msg .= "<div class='stat-change'>↑ Baseado nas suas vendas</div>";

            }
            
        }
        else
        {
                $msg  = "<div class='stat-icon'>🎯</div>";
                $msg .= "<div class='stat-label'>Pontos de Confiança</div>";
                $msg .= "<div class='stat-value'>Pontos de Confiança nao encontrado!</div>";
                $msg .= "<div class='stat-change'>↑ Baseado nas suas vendas</div>";
        }
        $conn->close();

        return ($msg);

    }
}
?>