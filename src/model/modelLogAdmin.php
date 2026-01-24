<?php

require_once 'connection.php';

class LogAdmin{

    function getTabelaLog(){
        global $conn;
        $msg = "";
        $sql = "SELECT * from logs_acesso,utilizadores where logs_acesso.utilizador_id = utilizadores.id";
        $result = $conn->query($sql);


            if ($result->num_rows > 0) {
while($row = $result->fetch_assoc()) {

    $msg .= "<div class='log-item'>";

        $msg .= "<div class='log-avatar'><img src='".$row["foto"]."' height='100px' class='d-block w-100 rounded-4' alt='Produto'></div>";

        $msg .= "<div class='log-info'>";
            $msg .= "<div class='log-user'>".$row["nome"]."</div>";
            $msg .= "<div class='log-details'>";
                $msg .= "<span class='log-detail-item'>";
                    $msg .= "<i class='fas fa-envelope'></i> " . $row["email"];
                $msg .= "</span>";
            $msg .= "</div>";
        $msg .= "</div>";
        $msg .= "<span class='log-badge'>" .$row["acao"] . "</span>";
        $msg .= "<span class='log-time'>" . $row["data_hora"] . "</span>";
    $msg .= "</div>";
}
            }
        else {
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
function getCardLog() {
    global $conn;
    $msg = "";

    // Total sessões
    $sqlSessoes = "SELECT COUNT(*) AS TodasSessoes FROM logs_acesso, utilizadores WHERE logs_acesso.utilizador_id = utilizadores.id";
    $result = $conn->query($sqlSessoes);
    $row = $result->fetch_assoc();
    $todasSessoes = $row['TodasSessoes'];

    // Total logins
    $sqlLogin = "SELECT COUNT(*) AS total_logins FROM logs_acesso WHERE acao = 'login'";
    $result = $conn->query($sqlLogin);
    $row = $result->fetch_assoc();
    $loginsHoje = $row['total_logins'];

    // Total logouts
    $sqlLogout = "SELECT COUNT(*) AS total_logouts FROM logs_acesso WHERE acao = 'logout'";
    $result = $conn->query($sqlLogout);
    $row = $result->fetch_assoc();
    $logoutsHoje = $row['total_logouts'];

    // Cards HTML
    $msg .= "<div class='stat-card'>
                <div class='stat-header'>
                    <div>
                        <div class='stat-label'>Logins</div>
                        <div class='stat-value'>{$loginsHoje}</div>
                    </div>
                    <div class='stat-icon login'>
                        <i class='fas fa-sign-in-alt'></i>
                    </div>
                </div>
            </div>";

    $msg .= "<div class='stat-card'>
                <div class='stat-header'>
                    <div>
                        <div class='stat-label'>Logouts</div>
                        <div class='stat-value'>{$logoutsHoje}</div>
                    </div>
                    <div class='stat-icon logout'>
                        <i class='fas fa-sign-out-alt'></i>
                    </div>
                </div>
            </div>";

    $msg .= "<div class='stat-card'>
                <div class='stat-header'>
                    <div>
                        <div class='stat-label'>Total de Sessões</div>
                        <div class='stat-value'>{$todasSessoes}</div>
                    </div>
                    <div class='stat-icon total'>
                        <i class='fas fa-clipboard-list'></i>
                    </div>
                </div>
            </div>";

    $conn->close();

    return $msg;
}

}
?>