<?php

require_once 'connection.php';

class LogAdmin{

    function getTabelaLog(){
        global $conn;
        $msg = "";
        $sql = "SELECT logs_acesso.*, utilizadores.nome, utilizadores.email, utilizadores.foto
                FROM logs_acesso
                INNER JOIN utilizadores ON logs_acesso.utilizador_id = utilizadores.id
                ORDER BY logs_acesso.id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $msg .= "<tr>";
                $msg .= "<td>" . $row["id"] . "</td>";
                $msg .= "<td><img src='" . $row["foto"] . "' style='width: 40px; height: 40px; border-radius: 50%; object-fit: cover;' alt='User'></td>";
                $msg .= "<td>" . $row["email"] . "</td>";
                $msg .= "<td><span class='badge' style='background: #3cb371; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px;'>" . $row["acao"] . "</span></td>";
                $msg .= "<td>" . date('d/m/Y H:i', strtotime($row["data_hora"])) . "</td>";
                $msg .= "</tr>";
            }
        } else {
            $msg .= "<tr>";
            $msg .= "<td colspan='5' style='text-align: center;'>Sem registos</td>";
            $msg .= "</tr>";
        }
        $conn->close();

        return ($msg);
    }
function getCardLog() {
    global $conn;
    $msg = "";

    $sqlSessoes = "SELECT COUNT(*) AS TodasSessoes FROM logs_acesso, utilizadores WHERE logs_acesso.utilizador_id = utilizadores.id";
    // Total sessões
    $sqlSessoes = "SELECT COUNT(*) AS TodasSessoes FROM logs_acesso";
    $result = $conn->query($sqlSessoes);
    $row = $result->fetch_assoc();
    $todasSessoes = $row['TodasSessoes'];

    $sqlLogin = "SELECT COUNT(*) AS total_logins FROM logs_acesso WHERE acao = 'login'";
    $result = $conn->query($sqlLogin);
    $row = $result->fetch_assoc();
    $totalLogins = $row['total_logins'];

    $sqlLogout = "SELECT COUNT(*) AS total_logouts FROM logs_acesso WHERE acao = 'logout'";
    $result = $conn->query($sqlLogout);
    $row = $result->fetch_assoc();
    $logoutsHoje = $row['total_logouts'];

    $msg .= "<div class='stat-card'>
                <div class='stat-header'>
                    <div>
                        <div class='stat-label'>Logins</div>
                        <div class='stat-value'>{$loginsHoje}</div>
                    </div>
                    <div class='stat-icon login'>
                        <i class='fas fa-sign-in-alt'></i>
                    </div>
    $totalLogouts = $row['total_logouts'];

    // Atividades hoje
    $sqlHoje = "SELECT COUNT(*) AS atividades_hoje FROM logs_acesso WHERE DATE(data_hora) = CURDATE()";
    $result = $conn->query($sqlHoje);
    $row = $result->fetch_assoc();
    $atividadesHoje = $row['atividades_hoje'];

    // Cards HTML - Mesmo estilo do gestaoCliente.php
    $msg .= '<div class="stat-card-compact">
                <div class="stat-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Logins</div>
                    <div class="stat-value">'.$totalLogins.'</div>
                </div>
            </div>';

    $msg .= '<div class="stat-card-compact">
                <div class="stat-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Logouts</div>
                    <div class="stat-value">'.$totalLogouts.'</div>
                </div>
            </div>';

    $msg .= '<div class="stat-card-compact">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Atividades Hoje</div>
                    <div class="stat-value">'.$atividadesHoje.'</div>
                </div>
            </div>';

    $msg .= '<div class="stat-card-compact">
                <div class="stat-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Sessões</div>
                    <div class="stat-value">'.$todasSessoes.'</div>
                </div>
            </div>';

    $conn->close();

    return $msg;
}

}
?>
