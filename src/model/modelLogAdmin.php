<?php

require_once __DIR__ . '/connection.php';

class LogAdmin{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getTabelaLog(){
        try {

        $msg = "";
        $sql = "SELECT logs_acesso.*, utilizadores.nome, utilizadores.email, utilizadores.foto
                FROM logs_acesso
                INNER JOIN utilizadores ON logs_acesso.utilizador_id = utilizadores.id
                ORDER BY logs_acesso.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

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

        $stmt->close();

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
function getCardLog() {
        try {

    $msg = "";

    $sqlSessoes = "SELECT COUNT(*) AS TodasSessoes FROM logs_acesso, utilizadores WHERE logs_acesso.utilizador_id = utilizadores.id";

    $sqlSessoes = "SELECT COUNT(*) AS TodasSessoes FROM logs_acesso";
    $stmtSessoes = $this->conn->prepare($sqlSessoes);
    $stmtSessoes->execute();
    $result = $stmtSessoes->get_result();
    $row = $result->fetch_assoc();
    $todasSessoes = $row['TodasSessoes'];

    $sqlLogin = "SELECT COUNT(*) AS total_logins FROM logs_acesso WHERE acao = 'login'";
    $stmtLogin = $this->conn->prepare($sqlLogin);
    $stmtLogin->execute();
    $result = $stmtLogin->get_result();
    $row = $result->fetch_assoc();
    $totalLogins = $row['total_logins'];

    $sqlLogout = "SELECT COUNT(*) AS total_logouts FROM logs_acesso WHERE acao = 'logout'";
    $stmtLogout = $this->conn->prepare($sqlLogout);
    $stmtLogout->execute();
    $result = $stmtLogout->get_result();
    $row = $result->fetch_assoc();
    $totalLogouts = $row['total_logouts'];


    $sqlHoje = "SELECT COUNT(*) AS atividades_hoje FROM logs_acesso WHERE DATE(data_hora) = CURDATE()";
    $stmtHoje = $this->conn->prepare($sqlHoje);
    $stmtHoje->execute();
    $result = $stmtHoje->get_result();
    $row = $result->fetch_assoc();
    $atividadesHoje = $row['atividades_hoje'];


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

    $stmtSessoes->close();
    $stmtLogin->close();
    $stmtLogout->close();
    $stmtHoje->close();

    return $msg;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

}
?>
