<?php

require_once __DIR__ . '/connection.php';

class DashboardAdmin{

    private $conn;
    private $imagemProdutoFallback = 'src/img/pexels-beccacorreiaph-31095884.jpg';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function resolverCaminhoImagemProduto($foto) {
        $foto = trim((string)$foto);
        if ($foto === '') {
            return $this->imagemProdutoFallback;
        }

        if (preg_match('/^(https?:\/\/|data:)/i', $foto)) {
            return $foto;
        }

        $candidato = $foto;
        if (!preg_match('/^(src\/|assets\/)/i', $candidato)) {
            $candidato = 'src/img/' . ltrim($candidato, '/\\');
        }

        $caminhoFisico = realpath(__DIR__ . '/../../' . $candidato);
        if ($caminhoFisico && is_file($caminhoFisico)) {
            return str_replace('\\', '/', $candidato);
        }

        return $this->imagemProdutoFallback;
    }

    function getDadosPlanos($ID_User,$plano){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT COUNT(*) AS TotalAtivos FROM Utilizadores WHERE Utilizadores.plano_id IN (2, 3);";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {

                $msg  = "<div class='stat-icon'><i class='fas fa-crown'></i></div>";
                $msg .= "<div class='stat-content'>";
                $msg .= "<div class='stat-label'>PLANOS ATIVOS</div>";
                $msg .= "<div class='stat-value'>".$row["TotalAtivos"]."</div>";
                $msg .= "</div>";

            }
        }
        else
        {
                $msg  = "<div class='stat-icon'><i class='fas fa-crown'></i></div>";
                $msg .= "<div class='stat-content'>";
                $msg .= "<div class='stat-label'>PLANOS ATIVOS</div>";
                $msg .= "<div class='stat-value'>0</div>";
                $msg .= "</div>";
        }

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getDadosPerfil($ID_User){
        try {

        $msg = "";
        $row = "";
        $sql = "SELECT * from utilizadores where id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

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

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getUtilizadores($ID_User){
        try {

        $msg = "";
        $row = "";
        $novos = $this->getNovosUtilizadores();
        $sql = "SELECT count(*) As TotalUtilizadores from Utilizadores,Tipo_Utilizadores where Utilizadores.tipo_utilizador_id = Tipo_Utilizadores.id AND utilizadores.tipo_utilizador_id IN (2, 3);";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {

                $msg  = "<div class='stat-icon'><i class='fas fa-users'></i></div>";
                $msg .= "<div class='stat-content'>";
                $msg .= "<div class='stat-label'>UTILIZADORES ATIVOS</div>";
                $msg .= "<div class='stat-value'>".$row["TotalUtilizadores"]."</div>";
                $msg .= "<div class='stat-change'><i class='fas fa-arrow-up'></i> ".$novos." Novos utilizadores</div>";
                $msg .= "</div>";
            }
        }
        else
        {
                $msg  = "<div class='stat-icon'><i class='fas fa-users'></i></div>";
                $msg .= "<div class='stat-content'>";
                $msg .= "<div class='stat-label'>UTILIZADORES ATIVOS</div>";
                $msg .= "<div class='stat-value'>0</div>";
                $msg .= "</div>";
        }

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getInfoUserDropdown($ID_User){
        try {

        $msg = "";
        $row = "";
        $novos = 0;
        $sql = "SELECT * from Utilizadores where id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

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

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function logout(){
        try {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $acao = "logout";

        $stmtLog = $this->conn->prepare(
            "INSERT INTO logs_acesso (utilizador_id, acao, email, data_hora)
             VALUES (?, ?, ?, NOW())"
        );

            if (!$stmtLog) {
                die("Erro prepare log: " . $this->conn->error);
            }

        if (!$stmtLog) {
                    die("Erro prepare log: " . $this->conn->error);
                }

            $stmtLog->bind_param(
                "iss",
                $_SESSION['utilizador'],
                $acao,
                $_SESSION['email']
            );

            if (!$stmtLog->execute()) {
                die("Erro insert log: " . $stmtLog->error);
            }

            $stmtLog->close();

        $_SESSION = array();


        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }


        session_destroy();

        return("Obrigado!");
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getNovosUtilizadores(){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT COUNT(*) AS novos FROM Utilizadores WHERE data_criacao >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novos = $row["novos"];
        }

        $stmt->close();

        return $novos;

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getRendimentos(){
        try {

            $msg = "";
            $row = "";
            $novos = $this->getNovosRendimentos();
            $sql = "SELECT Sum(rendimento.valor) AS TotalRendimentos FROM rendimento";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $msg  = "<div class='stat-icon'><i class='fas fa-euro-sign'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>RENDIMENTOS TOTAIS</div>";
                    $msg .= "<div class='stat-value'>".$row["TotalRendimentos"]."€</div>";
                    $msg .= "<div class='stat-change'><i class='fas fa-arrow-up'></i> ".$novos."€ Gastos rencentes</div>";
                    $msg .= "</div>";

                }
            }
            else
            {
                    $msg  = "<div class='stat-icon'><i class='fas fa-euro-sign'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>RENDIMENTOS TOTAIS</div>";
                    $msg .= "<div class='stat-value'>0€</div>";
                    $msg .= "</div>";
            }

            $stmt->close();

            return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
        }
        function getAdminPerfil($ID_User){
            try {

    $msg = "";
    $row = "";
    $sql = "SELECT * from utilizadores where id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $ID_User);
    $stmt->execute();
    $result = $stmt->get_result();

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

    return ($msg);
            } catch (Exception $e) {
                return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
            }
}
        function getNovosRendimentos(){
            try {

        $msg = "";
        $row = "";
        $novos = 0;
        $sql = "SELECT sum(rendimento.valor) NovoRendimento from rendimento where data_registo >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novos = $row["NovoRendimento"];
        }

        $stmt->close();

        return $novos;

            } catch (Exception $e) {
                return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
            }
    }
    function getGastos(){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT Sum(gastos.valor) As TotalGastos FROM gastos";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $novos = $this->getNovosGastos();
        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $msg  = "<div class='stat-icon'><i class='fas fa-credit-card'></i></div>";
                $msg .= "<div class='stat-content'>";
                $msg .= "<div class='stat-label'>GASTOS TOTAIS</div>";
                $msg .= "<div class='stat-value'>".$row["TotalGastos"]."€</div>";
                $msg .= "<div class='stat-change'><i class='fas fa-arrow-down'></i> ".$novos."€ Gastos rencentes</div>";
                $msg .= "</div>";

            }
        }
        else
        {
                $msg  = "<div class='stat-icon'><i class='fas fa-credit-card'></i></div>";
                $msg .= "<div class='stat-content'>";
                $msg .= "<div class='stat-label'>GASTOS TOTAIS</div>";
                $msg .= "<div class='stat-value'>0€</div>";
                $msg .= "</div>";
        }

        $stmt->close();

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
    function getNovosGastos(){
        try {

        $msg = "";
        $row = "";
        $novos = 0;

        $sql = "SELECT sum(gastos.valor) NovoGastos from Gastos where data_registo >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $novos = $row["NovoGastos"];
        }

        $stmt->close();

        return $novos;

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
function getVendasGrafico() {
        try {

    $dados1 = [];
    $dados2 = [];
    $dados3 = [];
    $msg = "";
    $flag = false;

    $meses = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];


    $sqlRendimentos = "SELECT YEAR(data_registo) as ano, MONTH(data_registo) as mes, SUM(valor) as total
                       FROM rendimento
                       WHERE data_registo >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                       GROUP BY YEAR(data_registo), MONTH(data_registo)
                       ORDER BY ano, mes";

    $stmtRendimentos = $this->conn->prepare($sqlRendimentos);
    $stmtRendimentos->execute();
    $resultRendimentos = $stmtRendimentos->get_result();
    $rendimentosPorMes = [];

    if ($resultRendimentos && $resultRendimentos->num_rows > 0) {
        while ($row = $resultRendimentos->fetch_assoc()) {
            $chave = $row['ano'] . '-' . $row['mes'];
            $rendimentosPorMes[$chave] = $row['total'];
        }
        $flag = true;
    }


    $sqlGastos = "SELECT YEAR(data_registo) as ano, MONTH(data_registo) as mes, SUM(valor) as total
                  FROM gastos
                  WHERE data_registo >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                  GROUP BY YEAR(data_registo), MONTH(data_registo)
                  ORDER BY ano, mes";

    $stmtGastos = $this->conn->prepare($sqlGastos);
    $stmtGastos->execute();
    $resultGastos = $stmtGastos->get_result();
    $gastosPorMes = [];

    if ($resultGastos && $resultGastos->num_rows > 0) {
        while ($row = $resultGastos->fetch_assoc()) {
            $chave = $row['ano'] . '-' . $row['mes'];
            $gastosPorMes[$chave] = $row['total'];
        }
        $flag = true;
    }


    for ($i = 11; $i >= 0; $i--) {
        $data = strtotime("-$i months");
        $mes = date('n', $data);
        $ano = date('Y', $data);
        $chave = $ano . '-' . $mes;

        $dados1[] = $meses[$mes - 1];
        $dados2[] = isset($rendimentosPorMes[$chave]) ? floatval($rendimentosPorMes[$chave]) : 0;
        $dados3[] = isset($gastosPorMes[$chave]) ? floatval($gastosPorMes[$chave]) : 0;
    }

    if (!$flag) {
        $msg = "Nenhum dado encontrado.";
    }

    $stmtRendimentos->close();
    $stmtGastos->close();

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg,
        "dados1" => $dados1,
        "dados2" => $dados2,
        "dados3" => $dados3
    ), JSON_UNESCAPED_UNICODE);

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
    function getProdutosInvativo(){
        try {

        $msg = "";
        $sql = "SELECT p.*, tp.descricao AS ProdutosNome, u.nome AS NomeAnunciante
            FROM produtos p
            INNER JOIN tipo_produtos tp ON p.tipo_produto_id = tp.id
            INNER JOIN utilizadores u ON u.id = p.anunciante_id
            WHERE p.ativo = 0
            ORDER BY p.Produto_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $text = "";
        $text2 = "";

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $fotoProduto = $this->resolverCaminhoImagemProduto($row['foto'] ?? '');
                $msg .= "<tr>";
                $msg .= "<th scope='row'>".$row['Produto_id']."</th>";
                $msg .= "<td><img src='".htmlspecialchars($fotoProduto, ENT_QUOTES, 'UTF-8')."' class='rounded-circle profile-img-small me-1' width='100px' onerror=\"this.onerror=null;this.src='".htmlspecialchars($this->imagemProdutoFallback, ENT_QUOTES, 'UTF-8')."';\"></td>";
                $msg .= "<td>".$row['nome']."</td>";
                $msg .= "<td>".$row['ProdutosNome']."</td>";
                $msg .= "<td>".$row['preco']."€</td>";
                $msg .= "<td>".$row['stock']."</td>";
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
            $msg .= "</tr>";
        }

        $stmt->close();

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
function getTopTipoGrafico() {
        try {

    $dados1 = [];
    $dados2 = [];
    $msg = "";
    $flag = false;

    $sql = "SELECT tipo_produtos.descricao As Tipo_Produto,count(*) As Vendido from vendas,tipo_produtos,produtos where produtos.produto_id = tipo_produtos.id AND produtos.produto_id = vendas.produto_id group BY tipo_produtos.descricao;";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dados1[] = $row['Tipo_Produto'];
            $dados2[] = $row['Vendido'];
        }
        $flag = true;
    } else {
        $msg = "Nenhum Serviço encontrado.";
    }

    $stmt->close();

    $resp = json_encode(array(
        "flag" => $flag,
        "msg" => $msg,
        "dados1" => $dados1,
        "dados2" => $dados2
    ), JSON_UNESCAPED_UNICODE);

    return $resp;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}
}
?>
