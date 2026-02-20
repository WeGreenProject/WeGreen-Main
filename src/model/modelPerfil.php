<?php

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../services/ProfileAddressFieldsService.php';
require_once __DIR__ . '/../services/EmailService.php';

class Perfil{

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function garantirCamposEnderecoPerfilCliente() {
        ProfileAddressFieldsService::garantirCamposEnderecoPerfil($this->conn);
    }

    private function validarPasswordConta($passwordDigitada, $passwordGuardada) {
        if (!is_string($passwordGuardada) || $passwordGuardada === '') {
            return false;
        }

        if (hash_equals($passwordGuardada, $passwordDigitada)) {
            return true;
        }

        if (strlen($passwordGuardada) === 32 && ctype_xdigit($passwordGuardada)) {
            return hash_equals(strtolower($passwordGuardada), md5($passwordDigitada));
        }

        if (strpos($passwordGuardada, '$2y$') === 0 || strpos($passwordGuardada, '$argon2') === 0) {
            return password_verify($passwordDigitada, $passwordGuardada);
        }

        return false;
    }

    function getDadosTipoPerfil($ID_User,$tpUser){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id =  ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $tipoAtual = isset($row['tipo_utilizador_id']) ? (int)$row['tipo_utilizador_id'] : (int)$tpUser;

                                if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['tipo']) && (int)$_SESSION['tipo'] !== $tipoAtual) {
                                        $_SESSION['tipo'] = $tipoAtual;
                                }

                                if($tipoAtual == 1)
                {
                    $msg  = "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='DashboardAdmin.php'><i class='fas fa-chart-line me-2'></i>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='perfilAdmin.php'><i class='fas fa-user-cog me-2'></i>Perfil</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='#' class='dropdown-item text-danger' onclick='event.preventDefault(); logout();'><i class='fas fa-sign-out-alt me-2'></i>Sair</a></li>";
                }
                else if($tipoAtual == 2)
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='DashboardCliente.php'><i class='fas fa-home me-2'></i>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='minhasEncomendas.php'><i class='fas fa-shopping-bag me-2'></i>As Minhas Encomendas</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='meusFavoritos.php'><i class='fas fa-heart me-2'></i>Meus Favoritos</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='perfilCliente.php'><i class='fas fa-user-cog me-2'></i>Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='#' id='btnAlternarConta' onclick='verificarEAlternarConta()' style='display:none;'>";
                    $msg .= "<i class='fas fa-exchange-alt me-2'></i> <span id='textoAlternar'>Alternar Conta</span></a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='#' class='dropdown-item text-danger' onclick='event.preventDefault(); logout();'><i class='fas fa-sign-out-alt me-2'></i>Sair</a></li>";
                }
                else if($tipoAtual == 3)
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='DashboardAnunciante.php'><i class='fas fa-store me-2'></i>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='perfilAnunciante.php'><i class='fas fa-user-cog me-2'></i>Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='#' id='btnAlternarConta' onclick='verificarEAlternarConta()' style='display:none;'>";
                    $msg .= "<i class='fas fa-exchange-alt me-2'></i> <span id='textoAlternar'>Alternar Conta</span></a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='#' class='dropdown-item text-danger' onclick='event.preventDefault(); logout();'><i class='fas fa-sign-out-alt me-2'></i>Sair</a></li>";
                }
                else
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Detectamos um erro na sua conta!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
                }

            }

        }
        else
        {
            $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
            $msg .= "<h6 class='mb-0 text-wegreen-accent'>Detectamos um erro na sua conta!</h6>";
            $msg .= "</div></li>";
            $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
        }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function getDadosTipoPerfilCompleto($ID_User, $tpUser) {
        try {
        if ($ID_User) {
            return $this->getDadosTipoPerfil($ID_User, $tpUser);
        } else {
            return "<li><a class='dropdown-item' href='login.html'><i class='fas fa-sign-in-alt me-2'></i>Entrar na sua conta</a></li>";
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function PerfilDoUtilizador($ID_User){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stmt->close();
                return $row['foto'];
            }
        }

        $stmt->close();
        return "src/img/pexels-beccacorreiaph-31095884.jpg";
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function verificarContaAlternativa($email, $tipoAtual) {
        try {


        $tipoAlternativo = ($tipoAtual == 2) ? 3 : 2;

        $stmt = $this->conn->prepare("SELECT id, tipo_utilizador_id FROM Utilizadores WHERE email = ? AND tipo_utilizador_id = ?");
        $stmt->bind_param("si", $email, $tipoAlternativo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return json_encode([
                'existe' => true,
                'id' => $row['id'],
                'tipo' => $row['tipo_utilizador_id'],
                'nome_tipo' => ($row['tipo_utilizador_id'] == 3) ? 'Anunciante' : 'Cliente'
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();
        return json_encode(['existe' => false], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function alternarConta($email, $tipoAlvo) {
        try {

        $stmt = $this->conn->prepare("SELECT * FROM Utilizadores WHERE email = ? AND tipo_utilizador_id = ?");
        $stmt->bind_param("si", $email, $tipoAlvo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();


            $_SESSION['utilizador'] = $row['id'];
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['tipo'] = $row['tipo_utilizador_id'];
            $_SESSION['PontosConf'] = $row['pontos_conf'];
            $_SESSION['foto'] = $row['foto'];
            $_SESSION['raking'] = $row['ranking_id'];
            $_SESSION['plano'] = $row['plano_id'];
            $_SESSION['data'] = $row['data_criacao'];
            $_SESSION['email'] = $row['email'];


            unset($_SESSION['perfil_duplo']);


            if ($row['tipo_utilizador_id'] == 3) {
                require_once 'modelCarrinho.php';
                $carrinho = new Carrinho($this->conn);
                $stmtLimpar = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ?");
                $userId = $row['id'];
                $stmtLimpar->bind_param("i", $userId);
                $stmtLimpar->execute();
                $stmtLimpar->close();
            }

            return json_encode([
                'success' => true,
                'tipo' => $row['tipo_utilizador_id'],
                'redirect' => ($row['tipo_utilizador_id'] == 3) ? 'DashboardAnunciante.php' : 'DashboardCliente.php'
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();
        return json_encode(['success' => false, 'msg' => 'Conta não encontrada'], JSON_UNESCAPED_UNICODE);
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
    function getContactForm($ID_User){
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

                    $msg .= "<div class='col-md-6'>";
                    $msg .= "<label class='form-label fw-semibold'>Nome Completo *</label>";
                    $msg .= "<input type='text' class='form-control' id='nomeUser' value='".$row["nome"]."' readonly>";
                    $msg .= "</div>";

                    $msg .= "<div class='col-md-6'>";
                    $msg .= "<label class='form-label fw-semibold'>Email *</label>";
                    $msg .= "<input type='email' class='form-control' id='emailUser' value='".$row["email"]."' readonly>";
                    $msg .= "</div>";

                    $msg .= "<div class='col-12'>";
                    $msg .= "<label class='form-label fw-semibold'>Assunto *</label>";
                    $msg .= "<input type='text' class='form-control' id='assuntoContato' required>";
                    $msg .= "</div>";

            }
        }

        return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
function AdicionarMensagemContacto($ID_Anunciante, $mensagem, $nome = null, $email = null, $assunto = null){
    try {

    $flag = false;
    $msg = "";

    $assuntoTexto = trim((string)$assunto);
    $mensagemTexto = trim((string)$mensagem);

    if ($mensagemTexto === '') {
        return json_encode([
            "flag" => false,
            "msg" => "Mensagem vazia."
        ], JSON_UNESCAPED_UNICODE);
    }

    $nomeRemetente = '';
    $emailRemetente = '';
    $idRemetente = null;
    $mensagemFinal = $mensagemTexto;
    $criarChat = false;

    $admins = $this->obterAdministradoresSuporte();
    $adminIds = [];
    $adminEmails = [];

    foreach ($admins as $admin) {
        if (!empty($admin['id'])) {
            $adminIds[] = (int)$admin['id'];
        }
        if (!empty($admin['email'])) {
            $adminEmails[] = trim((string)$admin['email']);
        }
    }

    $adminIds = array_values(array_unique($adminIds));
    $adminEmails = array_values(array_unique($adminEmails));

    if ($ID_Anunciante !== null) {
        $idRemetente = (int)$ID_Anunciante;

        $stmtDados = $this->conn->prepare("SELECT nome, email FROM Utilizadores WHERE id = ? LIMIT 1");
        $stmtDados->bind_param("i", $idRemetente);
        $stmtDados->execute();
        $resDados = $stmtDados->get_result();

        if ($resDados && $resDados->num_rows > 0) {
            $rowDados = $resDados->fetch_assoc();
            $nomeRemetente = trim((string)($rowDados['nome'] ?? 'Utilizador WeGreen'));
            $emailRemetente = trim((string)($rowDados['email'] ?? ''));
        }
        $stmtDados->close();

        if (!empty($assuntoTexto)) {
            $mensagemFinal = "Assunto: {$assuntoTexto}\nMensagem: {$mensagemTexto}";
        }

        $criarChat = true;
    } else {
        $nomeRemetente = trim((string)$nome);
        $emailRemetente = trim((string)$email);
        $mensagemFinal = "Nome: {$nomeRemetente}\nEmail: {$emailRemetente}\n";
        if (!empty($assuntoTexto)) {
            $mensagemFinal .= "Assunto: {$assuntoTexto}\n";
        }
        $mensagemFinal .= "Mensagem: {$mensagemTexto}";

        if (filter_var($emailRemetente, FILTER_VALIDATE_EMAIL)) {
            $stmtUser = $this->conn->prepare("SELECT id, nome, email FROM Utilizadores WHERE LOWER(email) = LOWER(?) LIMIT 1");
            $stmtUser->bind_param("s", $emailRemetente);
            $stmtUser->execute();
            $resUser = $stmtUser->get_result();

            if ($resUser && $resUser->num_rows > 0) {
                $userPlataforma = $resUser->fetch_assoc();
                $idRemetente = (int)$userPlataforma['id'];
                if ($nomeRemetente === '') {
                    $nomeRemetente = trim((string)($userPlataforma['nome'] ?? 'Utilizador WeGreen'));
                }
                $emailRemetente = trim((string)($userPlataforma['email'] ?? $emailRemetente));
                $criarChat = true;
            }

            $stmtUser->close();
        }
    }

    $ticketId = 0;

    if ($criarChat) {
        if (empty($adminIds)) {
            return json_encode([
                "flag" => false,
                "msg" => "Não existem administradores disponíveis para receber a mensagem no chat."
            ], JSON_UNESCAPED_UNICODE);
        }

        $stmt = $this->conn->prepare("INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)");
        foreach ($adminIds as $adminId) {
            $stmt->bind_param("iis", $idRemetente, $adminId, $mensagemFinal);
            if ($stmt->execute()) {
                if ($ticketId === 0) {
                    $ticketId = (int)$stmt->insert_id;
                }
                $flag = true;
            }
        }
        $stmt->close();

        if (!$flag) {
            return json_encode([
                "flag" => false,
                "msg" => "Erro ao enviar mensagem para o chat de suporte."
            ], JSON_UNESCAPED_UNICODE);
        }

        $msg = "Mensagem enviada com sucesso!";
    } else {
        $flag = true;
        $msg = "Mensagem enviada por email para o suporte com sucesso!";
    }

    $this->enviarEmailSuporteAdmin($ticketId, $nomeRemetente, $emailRemetente, $assuntoTexto, $mensagemTexto, (int)($idRemetente ?? 0), $adminEmails);

    if (filter_var($emailRemetente, FILTER_VALIDATE_EMAIL)) {
        $this->enviarConfirmacaoSuporteRemetente($ticketId, $nomeRemetente, $emailRemetente, $assuntoTexto, $mensagemTexto);
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ], JSON_UNESCAPED_UNICODE);

    return $resp;
    } catch (Exception $e) {
        return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
    }
}

private function obterAdministradoresSuporte() {
    $admins = [];

    try {
        $stmt = $this->conn->prepare("SELECT id, email FROM Utilizadores WHERE tipo_utilizador_id = 1");
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();

            while ($result && ($row = $result->fetch_assoc())) {
                $email = trim((string)($row['email'] ?? ''));
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $admins[] = [
                        'id' => (int)$row['id'],
                        'email' => $email
                    ];
                }
            }

            $stmt->close();
        }
    } catch (Exception $e) {
    }

    try {
        $config = require __DIR__ . '/../config/email_config.php';
        $configAdminEmail = trim((string)($config['admin']['email'] ?? ''));
        if (filter_var($configAdminEmail, FILTER_VALIDATE_EMAIL)) {
            $existe = false;
            foreach ($admins as $admin) {
                if (strcasecmp((string)$admin['email'], $configAdminEmail) === 0) {
                    $existe = true;
                    break;
                }
            }

            if (!$existe) {
                $admins[] = [
                    'id' => null,
                    'email' => $configAdminEmail
                ];
            }
        }
    } catch (Exception $e) {
    }

    return $admins;
}

private function enviarEmailSuporteAdmin($ticketId, $nomeRemetente, $emailRemetente, $assunto, $mensagem, $remetenteId, $adminEmails = []) {
    try {
        if (empty($adminEmails)) {
            return;
        }

        $adminEmails = array_values(array_unique(array_filter($adminEmails, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        })));

        if (empty($adminEmails)) {
            return;
        }

        $emailService = new EmailService($this->conn);

        $templatePath = __DIR__ . '/../views/email_templates/suporte_admin.php';
        if (!file_exists($templatePath)) {
            return;
        }

        $nome_remetente = $nomeRemetente ?: 'Visitante';
        $email_remetente = $emailRemetente ?: 'N/D';
        $assunto_mensagem = $assunto ?: 'Sem assunto';
        $mensagem_mensagem = $mensagem;
        $ticket_id = (int)$ticketId;
        $remetente_id = (int)$remetenteId;

        ob_start();
        include $templatePath;
        $htmlBody = ob_get_clean();

        $ticketLabel = ($ticketId > 0) ? "#{$ticketId}" : ('#' . date('YmdHis'));
        $subject = "[Suporte WeGreen] Nova mensagem {$ticketLabel}";

        foreach ($adminEmails as $adminEmail) {
            $emailService->send($adminEmail, $subject, $htmlBody);
        }
    } catch (Exception $e) {
    }
}

private function enviarConfirmacaoSuporteRemetente($ticketId, $nomeRemetente, $emailRemetente, $assunto, $mensagem) {
    try {
        $emailService = new EmailService($this->conn);

        $templatePath = __DIR__ . '/../views/email_templates/suporte_confirmacao.php';
        if (!file_exists($templatePath)) {
            return;
        }

        $nome_remetente = $nomeRemetente ?: 'Cliente';
        $assunto_mensagem = $assunto ?: 'Sem assunto';
        $mensagem_mensagem = $mensagem;
        $ticket_id = (int)$ticketId;

        ob_start();
        include $templatePath;
        $htmlBody = ob_get_clean();

        $subject = "Recebemos o seu pedido de suporte #{$ticketId}";
        $emailService->send($emailRemetente, $subject, $htmlBody);
    } catch (Exception $e) {
    }
}
    function getDadosPlanos($ID_User,$plano,$tpUser){
        try {

    $msg = "";
    $row = "";

    $sql = "SELECT * FROM Utilizadores WHERE id =  ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $ID_User);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
            if($tpUser == 3)
            {
                if($plano == 1)
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Essencial Verde</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>5 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos simples</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge “Iniciante Sustentável” no perfil</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Taxas reduzidas em categorias ecológicas certificadas.</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold' disabled style='opacity: 0.6; cursor: not-allowed;'>Plano Atual</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Até 10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável: visível (para comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança: visível</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios básicos de vendas e audiência</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Notificações de desempenho: alertas simples sobre produtos com baixa performance ou vendas atípicas</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=crescimentocircular&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>70€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável (comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança (visual)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios avançados de impacto ambiental</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ferramentas de fidelização (cupões, packs eco, marketing recorrente)</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=70' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                $msg .= "</div>";
                $msg .= "</div>";

                }
                else if($plano == 2)
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Essencial Verde</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>5 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos simples</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-secondary text-white rounded-pill px-4' disabled style='opacity: 0.5; cursor: not-allowed;'>Downgrade não permitido</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Até 10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável: visível (para comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança: visível</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios básicos de vendas e audiência</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Notificações de desempenho: alertas simples sobre produtos com baixa performance ou vendas atípicas</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold' disabled style='opacity: 0.6; cursor: not-allowed;'>Plano Atual</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>70€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável (comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança (visual)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios avançados de impacto ambiental</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ferramentas de fidelização (cupões, packs eco, marketing recorrente)</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=70' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                $msg .= "</div>";
                $msg .= "</div>";

                }
                else
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Essencial Verde</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos simples</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge “Iniciante Sustentável” no perfil</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Taxas reduzidas em categorias ecológicas certificadas.</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-secondary text-white rounded-pill px-4' disabled style='opacity: 0.5; cursor: not-allowed;'>Downgrade não permitido</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Até 10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável: visível (para comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança: visível</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios básicos de vendas e audiência</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Notificações de desempenho: alertas simples sobre produtos com baixa performance ou vendas atípicas</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-secondary text-white rounded-pill px-4' disabled style='opacity: 0.5; cursor: not-allowed;'>Downgrade não permitido</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>70€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável (comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança (visual)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios avançados de impacto ambiental</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ferramentas de fidelização (cupões, packs eco, marketing recorrente)</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold' disabled style='opacity: 0.6; cursor: not-allowed;'>Plano Atual</a>";
                $msg .= "</div></div></div>";

                $msg .= "</div>";
                $msg .= "</div>";
                }
            }
            else if($tpUser == 2)
            {
                $msg .= "";
            }
            else if($tpUser == 1)
            {
                if($plano == 1)
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Essencial Verde</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos simples</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge “Iniciante Sustentável” no perfil</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Taxas reduzidas em categorias ecológicas certificadas.</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Até 10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável: visível (para comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança: visível</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios básicos de vendas e audiência</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Notificações de desempenho</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=premium&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>70€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável (comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança (visual)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios avançados de impacto ambiental</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ferramentas de fidelização (cupões, packs eco, marketing recorrente)</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=70' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                $msg .= "</div>";
                $msg .= "</div>";

                }
                else if($plano == 2)
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Essencial Verde</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos simples</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge “Iniciante Sustentável” no perfil</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Taxas reduzidas em categorias ecológicas certificadas.</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-secondary text-white rounded-pill px-4' disabled style='opacity: 0.5; cursor: not-allowed;'>Downgrade não permitido</a>";
                $msg .= "</div></div></div>";

                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Até 10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável: visível (para comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança: visível</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios básicos de vendas e audiência</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Notificações de desempenho</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold' disabled style='opacity: 0.6; cursor: not-allowed;'>Plano Atual</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>70€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável (comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança (visual)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios avançados de impacto ambiental</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ferramentas de fidelização (cupões, packs eco, marketing recorrente)</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=70' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                $msg .= "</div>";
                $msg .= "</div>";

                }
                else
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Essencial Verde</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos simples</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge “Iniciante Sustentável” no perfil</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Taxas reduzidas em categorias ecológicas certificadas.</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-secondary text-white rounded-pill px-4' disabled style='opacity: 0.5; cursor: not-allowed;'>Downgrade não permitido</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Até 10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável: visível (para comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança: visível</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios básicos de vendas e audiência</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Notificações de desempenho: alertas simples sobre produtos com baixa performance ou vendas atípicas</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-secondary text-white rounded-pill px-4' disabled style='opacity: 0.5; cursor: not-allowed;'>Downgrade não permitido</a>";
                $msg .= "</div></div></div>";


                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>70€<span class='fs-5 text-muted'>/mês (30 dias)</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Sustentável (comissão)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Badge Confiança (visual)</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios avançados de impacto ambiental</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ferramentas de fidelização (cupões, packs eco, marketing recorrente)</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold' disabled style='opacity: 0.6; cursor: not-allowed;'>Plano Atual</a>";
                $msg .= "</div></div></div>";

                $msg .= "</div>";
                $msg .= "</div>";
                }
            }
            else
            {
                $msg .= "";
            }

        }

    }
    else
    {
        $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
        $msg .= "<h6 class='mb-0 text-wegreen-accent'>Detectamos um erro na sua conta!</h6>";
        $msg .= "</div></li>";
        $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
    }

    return ($msg);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
}

    function getDadosPerfilCliente($ID_User) {
        try {

        $this->garantirCamposEnderecoPerfilCliente();

        $sql = "SELECT u.id, u.nome, u.apelido, u.email, u.nif, u.telefone, u.morada, u.distrito, u.localidade, u.codigo_postal, u.foto, u.data_criacao
                FROM Utilizadores u
                WHERE u.id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $nomeCompleto = trim($row['nome'] . ' ' . ($row['apelido'] ?? ''));
            $row['nome_completo'] = $nomeCompleto;

            if (empty($row['codigo_postal']) && !empty($row['morada']) && preg_match('/\b\d{4}-\d{3}\b/', $row['morada'], $matchPostal)) {
                $row['codigo_postal'] = $matchPostal[0];
            }

            $stmt->close();
            return json_encode($row, JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();
        return json_encode(['error' => 'Utilizador não encontrado'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function atualizarPerfilCliente($ID_User, $nome, $email, $telefone = null, $nif = null, $morada = null, $distrito = null, $localidade = null, $codigo_postal = null) {
        try {

        $this->garantirCamposEnderecoPerfilCliente();

        $email = trim((string)$email);
        $distrito = trim((string)($distrito ?? ''));
        $localidade = trim((string)($localidade ?? ''));
        $codigo_postal = trim((string)($codigo_postal ?? ''));

        if (empty($nome) || strlen($nome) < 3) {
            return json_encode(['success' => false, 'message' => 'Nome deve ter no mínimo 3 caracteres'], JSON_UNESCAPED_UNICODE);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['success' => false, 'message' => 'Email inválido'], JSON_UNESCAPED_UNICODE);
        }

        if (empty($morada) || strlen(trim($morada)) < 10) {
            return json_encode(['success' => false, 'message' => 'Morada completa é obrigatória (mínimo 10 caracteres)'], JSON_UNESCAPED_UNICODE);
        }

        if (!empty($nif) && !preg_match('/^[0-9]{9}$/', $nif)) {
            return json_encode(['success' => false, 'message' => 'NIF deve conter exatamente 9 dígitos'], JSON_UNESCAPED_UNICODE);
        }

        if (!empty($telefone) && !preg_match('/^[0-9]{9}$/', $telefone)) {
            return json_encode(['success' => false, 'message' => 'Telefone deve conter exatamente 9 dígitos'], JSON_UNESCAPED_UNICODE);
        }

        if (!empty($codigo_postal) && !preg_match('/^[0-9]{4}-[0-9]{3}$/', $codigo_postal)) {
            return json_encode(['success' => false, 'message' => 'Código postal deve ter o formato XXXX-XXX'], JSON_UNESCAPED_UNICODE);
        }


        $sqlCheck = "SELECT id FROM utilizadores WHERE email = ? AND id != ? AND tipo_utilizador_id = 2";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param("si", $email, $ID_User);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $stmtCheck->close();
            return json_encode(['success' => false, 'message' => 'Email já está em uso'], JSON_UNESCAPED_UNICODE);
        }
        $stmtCheck->close();

        $partesNome = explode(' ', trim($nome), 2);
        $primeiroNome = $partesNome[0];
        $apelido = isset($partesNome[1]) ? $partesNome[1] : null;


        $sql = "UPDATE utilizadores SET nome = ?, apelido = ?, email = ?, nif = ?, telefone = ?, morada = ?, distrito = ?, localidade = ?, codigo_postal = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssssssi", $primeiroNome, $apelido, $email, $nif, $telefone, $morada, $distrito, $localidade, $codigo_postal, $ID_User);

        if ($stmt->execute()) {
            $_SESSION['nome'] = $primeiroNome;
            $_SESSION['email'] = $email;

            $stmt->close();
            return json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso'], JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function atualizarPerfilClienteComPost($utilizador_id, $post_data) {
        try {
        $nome = $post_data['nome'] ?? null;
        $email = $post_data['email'] ?? null;
        $nif = $post_data['nif'] ?? null;
        $telefone = $post_data['telefone'] ?? null;
        $morada = $post_data['morada'] ?? null;
        $distrito = $post_data['distrito'] ?? null;
        $localidade = $post_data['localidade'] ?? null;
        $codigo_postal = $post_data['codigo_postal'] ?? null;

        return $this->atualizarPerfilCliente($utilizador_id, $nome, $email, $telefone, $nif, $morada, $distrito, $localidade, $codigo_postal);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function alterarSenha($idUser, $senhaAtual, $novaSenha) {
        try {

        $transacaoIniciada = false;

        $idUser = (int)$idUser;
        $senhaAtual = (string)$senhaAtual;
        $novaSenha = (string)$novaSenha;

        if ($idUser <= 0 || $senhaAtual === '' || $novaSenha === '') {
            return json_encode(['success' => false, 'message' => 'Dados insuficientes para alterar a senha.'], JSON_UNESCAPED_UNICODE);
        }

        if (strlen($novaSenha) < 6) {
            return json_encode(['success' => false, 'message' => 'A nova senha deve ter no mínimo 6 caracteres.'], JSON_UNESCAPED_UNICODE);
        }

        $sqlUtilizador = "SELECT id, nome, email, tipo_utilizador_id, password FROM Utilizadores WHERE id = ? LIMIT 1";
        $stmtUtilizador = $this->conn->prepare($sqlUtilizador);
        $stmtUtilizador->bind_param("i", $idUser);
        $stmtUtilizador->execute();
        $resultUtilizador = $stmtUtilizador->get_result();

        if ($resultUtilizador->num_rows === 0) {
            $stmtUtilizador->close();
            return json_encode(['success' => false, 'message' => 'Utilizador não encontrado.'], JSON_UNESCAPED_UNICODE);
        }

        $utilizadorAtual = $resultUtilizador->fetch_assoc();
        $stmtUtilizador->close();

        if (!$this->validarPasswordConta($senhaAtual, (string)$utilizadorAtual['password'])) {
            return json_encode(['success' => false, 'message' => 'Senha atual incorreta.'], JSON_UNESCAPED_UNICODE);
        }

        $idsParaAtualizar = [(int)$utilizadorAtual['id']];
        $tipoAtual = (int)$utilizadorAtual['tipo_utilizador_id'];
        $emailAtual = trim((string)$utilizadorAtual['email']);

        if ($emailAtual !== '' && in_array($tipoAtual, [2, 3], true)) {
            $sqlDual = "SELECT id FROM Utilizadores WHERE email = ? AND tipo_utilizador_id IN (2, 3)";
            $stmtDual = $this->conn->prepare($sqlDual);
            $stmtDual->bind_param("s", $emailAtual);
            $stmtDual->execute();
            $resultDual = $stmtDual->get_result();

            while ($rowDual = $resultDual->fetch_assoc()) {
                $idsParaAtualizar[] = (int)$rowDual['id'];
            }

            $stmtDual->close();
            $idsParaAtualizar = array_values(array_unique($idsParaAtualizar));
        }

        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $this->conn->begin_transaction();
        $transacaoIniciada = true;
        $sqlUpdate = "UPDATE Utilizadores SET password = ? WHERE id = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);

        foreach ($idsParaAtualizar as $targetId) {
            $stmtUpdate->bind_param("si", $novaSenhaHash, $targetId);
            if (!$stmtUpdate->execute()) {
                $stmtUpdate->close();
                $this->conn->rollback();
                return json_encode(['success' => false, 'message' => 'Erro ao atualizar a senha.'], JSON_UNESCAPED_UNICODE);
            }
        }

        $stmtUpdate->close();
        $this->conn->commit();
        $transacaoIniciada = false;

        $emailDestino = trim((string)$utilizadorAtual['email']);
        $nomeDestino = trim((string)$utilizadorAtual['nome']);
        if ($emailDestino !== '') {
            try {
                $emailService = new EmailService($this->conn);
                $emailService->enviarPasswordAlterada($emailDestino, $nomeDestino !== '' ? $nomeDestino : 'Utilizador', 'alteracao_conta');
            } catch (\Exception $e) {
            }
        }

        if (count($idsParaAtualizar) > 1) {
            return json_encode(['success' => true, 'message' => 'Senha alterada com sucesso em todas as contas associadas.'], JSON_UNESCAPED_UNICODE);
        }

        return json_encode(['success' => true, 'message' => 'Senha alterada com sucesso!'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            if (!empty($transacaoIniciada)) {
                $this->conn->rollback();
            }
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function getDadosUtilizadorCheckout($utilizador_id) {
        try {

        $sql = "SELECT nome, email, foto FROM Utilizadores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return json_encode([
                'nome' => $row['nome'],
                'email' => $row['email'],
                'foto' => $row['foto'] ?? ''
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode(['error' => 'Utilizador não encontrado'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    function getContactFormCompleto($utilizador_id) {
        try {

        if ($utilizador_id && strpos($utilizador_id, 'temp_') !== 0) {
            return $this->getContactForm($utilizador_id);
        }

        return "
<div class='col-md-6'>
    <div class='mb-3'>
        <label for='nome' class='form-label'>Nome</label>
        <input type='text' class='form-control' id='nome' name='nome' required>
    </div>
</div>
<div class='col-md-6'>
    <div class='mb-3'>
        <label for='email' class='form-label'>Email</label>
        <input type='email' class='form-control' id='email' name='email' required>
    </div>
</div>
<div class='col-12'>
    <div class='mb-3'>
        <label for='assunto' class='form-label'>Assunto</label>
        <input type='text' class='form-control' id='assunto' name='assunto' required>
    </div>
</div>
<div class='col-12'>
    <div class='mb-3'>
        <label for='mensagem' class='form-label'>Mensagem</label>
        <textarea class='form-control' id='mensagem' name='mensagem' rows='5' required></textarea>
    </div>
</div>";
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
