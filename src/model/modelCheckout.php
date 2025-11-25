<?php
require_once 'connection.php';

class Checkout {

function getPlanosComprar($utilizador,$plano){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * from utilizadores where id = ". $utilizador;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                if($plano == 2)
                {
                    $msg .= "<span class='detail-label'>Plano Adquirido</span>";
                    $msg .= "<span class='plan-badge plan-premium' id='planBadge'>";
                    $msg .= "<span class='plan-icon'>👑</span>";
                    $msg .= "<span id='planName'>Premium</span>";
                    $msg .= "</span>";
                }
                else if($plano == 3)
                {
                    $msg .= "<span class='detail-label'>Plano Adquirido</span>";
                    $msg .= "<span class='plan-badge plan-premium' id='planBadge'>";
                    $msg .= "<span class='plan-icon'>💼</span>";
                    $msg .= "<span id='planName'>Enterprise</span>";
                    $msg .= "</span>";
                }

    }
        $conn->close();
        
        return ($msg);

        }
        
    }
}