<?php

require_once 'connection.php';

class Perfil{

    function getDadosTipoPerfil($ID_User,$tpUser){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = " . $ID_User;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                if($tpUser == 1)
                {
                    $msg  = "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='DashboardAdmin.php'><i class='fas fa-chart-line me-2'></i>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='perfilAdmin.php'><i class='fas fa-user-cog me-2'></i>Perfil</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'><i class='fas fa-sign-out-alt me-2'></i>Sair</a></li>";
                }
                else if($tpUser == 2)
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
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'><i class='fas fa-sign-out-alt me-2'></i>Sair</li>";
                }
                else if($tpUser == 3)
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='DashboardAnunciante.php'><i class='fas fa-store me-2'></i>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='perfilAnunciante.php'><i class='fas fa-user-cog me-2'></i>Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='#' id='btnAlternarConta' onclick='verificarEAlternarConta()' style='display:none;'>";
                    $msg .= "<i class='fas fa-exchange-alt me-2'></i> <span id='textoAlternar'>Alternar Conta</span></a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'><i class='fas fa-sign-out-alt me-2'></i>Sair</a></li>";
                }
                else
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Dectetamos um erro na sua conta!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
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
        function PerfilDoUtilizador($ID_User){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = " . $ID_User;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                    return $row['foto'];

            }

        }


        $conn->close();

         return "src/img/pexels-beccacorreiaph-31095884.jpg";

    }

    function verificarContaAlternativa($email, $tipoAtual) {
        global $conn;

        // Se é anunciante (2), procura cliente (3) e vice-versa
        $tipoAlternativo = ($tipoAtual == 2) ? 3 : 2;

        $stmt = $conn->prepare("SELECT id, tipo_utilizador_id FROM Utilizadores WHERE email = ? AND tipo_utilizador_id = ?");
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
            ]);
        }

        $stmt->close();
        return json_encode(['existe' => false]);
    }

    function alternarConta($email, $tipoAlvo) {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM Utilizadores WHERE email = ? AND tipo_utilizador_id = ?");
        $stmt->bind_param("si", $email, $tipoAlvo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();

            // Atualizar sessão (session_start já foi chamado no controller)
            $_SESSION['utilizador'] = $row['id'];
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['tipo'] = $row['tipo_utilizador_id'];
            $_SESSION['PontosConf'] = $row['pontos_conf'];
            $_SESSION['foto'] = $row['foto'];
            $_SESSION['raking'] = $row['ranking_id'];
            $_SESSION['plano'] = $row['plano_id'];
            $_SESSION['data'] = $row['data_criacao'];
            $_SESSION['email'] = $row['email'];

            // Limpar flag de perfil duplo para não redirecionar novamente para escolherConta.php
            unset($_SESSION['perfil_duplo']);

            return json_encode([
                'success' => true,
                'tipo' => $row['tipo_utilizador_id'],
                'redirect' => ($row['tipo_utilizador_id'] == 3) ? 'DashboardAnunciante.php' : 'DashboardCliente.php'
            ]);
        }

        $stmt->close();
        return json_encode(['success' => false, 'msg' => 'Conta não encontrada']);
    }

    function logout(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();


       if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }

        session_destroy();

        return("Obrigado!");
    }
    function getContactForm($ID_User){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * from utilizadores where id =".$ID_User;

        $result = $conn->query($sql);

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
        $conn->close();

        return ($msg);

    }
function AdicionarMensagemContacto($ID_Anunciante, $mensagem, $nome = null, $email = null){
    global $conn;
    $flag = false;
    $msg = "";
    $ID_Consumidor = 1; //ID do Admin

    if($ID_Anunciante !== null){
        $stmt = $conn->prepare("INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $ID_Anunciante, $ID_Consumidor, $mensagem);

        if($stmt->execute()) {
            $flag = true;
            $msg = "Mensagem enviada com sucesso!";
        } else {
            $flag = false;
            $msg = "Erro ao enviar mensagem.";
        }
    } else {
        // Utilizador não autenticado
        $remetente = 0;
        $mensagemFull = "Nome: $nome\nEmail: $email\nMensagem: $mensagem";

        $stmt = $conn->prepare("INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $remetente, $ID_Consumidor, $mensagemFull);

        if($stmt->execute()) {
            $flag = true;
            $msg = "Mensagem enviada com sucesso!";
        } else {
            $flag = false;
            $msg = "Erro ao enviar mensagem.";
        }
    }

    $resp = json_encode([
        "flag" => $flag,
        "msg" => $msg
    ]);

    $stmt->close();
    $conn->close();

    return $resp;
}
    function getDadosPlanos($ID_User,$plano,$tpUser){
    global $conn;
    $msg = "";
    $row = "";

    $sql = "SELECT * FROM Utilizadores WHERE id = " . $ID_User;
    $result = $conn->query($sql);

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

                // === PLANO FREE ===
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

                // === PLANO PREMIUM ===
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

                // === PLANO ENTERPRISE ===
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

                $msg .= "</div>"; // fecha row
                $msg .= "</div>"; // fecha container

                }
                else if($plano == 2)
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";

                // === PLANO FREE ===
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

                // === PLANO PREMIUM ===
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

                // === PLANO ENTERPRISE ===
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

                // === PLANO FREE ===
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

                // === PLANO PREMIUM ===
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

                // === PLANO ENTERPRISE ===
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

                // === PLANO FREE ===
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

                // === PLANO PREMIUM ===
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

                // === PLANO ENTERPRISE ===
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

                $msg .= "</div>"; // fecha row
                $msg .= "</div>"; // fecha container

                }
                else if($plano == 2)
                {
                $msg  = "<div class='container text-center'>";
                $msg .= "<h2 class='fw-bold mb-4 text-dark'>Planos Wegreen</h2>";
                $msg .= "<p class='text-muted mb-5 fs-5'>Escolhe o plano ideal para ti e junta-te à comunidade sustentável da Wegreen.</p>";

                $msg .= "<div class='row g-4 justify-content-center'>";

                // === PLANO FREE ===
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

                // === PLANO ENTERPRISE ===
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

                // === PLANO FREE ===
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

                // === PLANO PREMIUM ===
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

                // === PLANO ENTERPRISE ===
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
        $msg .= "<h6 class='mb-0 text-wegreen-accent'>Dectetamos um erro na sua conta!</h6>";
        $msg .= "</div></li>";
        $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
    }
    $conn->close();

    return ($msg);

}

    // Buscar dados do perfil do cliente
    function getDadosPerfilCliente($ID_User) {
        global $conn;

        $sql = "SELECT u.id, u.nome, u.apelido, u.email, u.nif, u.telefone, u.morada, u.distrito, u.localidade, u.foto, u.data_criacao
                FROM Utilizadores u
                WHERE u.id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Juntar nome e apelido
            $nomeCompleto = trim($row['nome'] . ' ' . ($row['apelido'] ?? ''));
            $row['nome_completo'] = $nomeCompleto;

            $stmt->close();
            return json_encode($row);
        }

        $stmt->close();
        return json_encode(['error' => 'Utilizador não encontrado']);
    }

    // Atualizar dados do perfil do cliente
    function atualizarPerfilCliente($ID_User, $nome, $email, $telefone = null, $nif = null, $morada = null, $distrito = null, $localidade = null) {
        global $conn;

        // Validações
        if (empty($nome) || strlen($nome) < 3) {
            return json_encode(['success' => false, 'message' => 'Nome deve ter no mínimo 3 caracteres']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['success' => false, 'message' => 'Email inválido']);
        }

        if (empty($morada) || strlen(trim($morada)) < 10) {
            return json_encode(['success' => false, 'message' => 'Morada completa é obrigatória (mínimo 10 caracteres)']);
        }

        if (empty($distrito)) {
            return json_encode(['success' => false, 'message' => 'Distrito é obrigatório']);
        }

        if (empty($localidade) || strlen(trim($localidade)) < 2) {
            return json_encode(['success' => false, 'message' => 'Localidade é obrigatória']);
        }

        if (!empty($nif) && !preg_match('/^[0-9]{9}$/', $nif)) {
            return json_encode(['success' => false, 'message' => 'NIF deve conter exatamente 9 dígitos']);
        }

        if (!empty($telefone) && !preg_match('/^[0-9]{9}$/', $telefone)) {
            return json_encode(['success' => false, 'message' => 'Telefone deve conter exatamente 9 dígitos']);
        }

        // Verificar se email já existe (exceto o próprio utilizador)
        $sqlCheck = "SELECT id FROM utilizadores WHERE email = ? AND id != ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("si", $email, $ID_User);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $stmtCheck->close();
            return json_encode(['success' => false, 'message' => 'Email já está em uso']);
        }
        $stmtCheck->close();

        // Separar nome e apelido
        $partesNome = explode(' ', trim($nome), 2);
        $primeiroNome = $partesNome[0];
        $apelido = isset($partesNome[1]) ? $partesNome[1] : null;

        // Atualizar dados
        $sql = "UPDATE utilizadores SET nome = ?, apelido = ?, email = ?, nif = ?, telefone = ?, morada = ?, distrito = ?, localidade = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $primeiroNome, $apelido, $email, $nif, $telefone, $morada, $distrito, $localidade, $ID_User);

        if ($stmt->execute()) {
            // Atualizar sessão
            $_SESSION['nome'] = $primeiroNome;
            $_SESSION['email'] = $email;

            $stmt->close();
            return json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso']);
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil']);
    }

    // Alterar senha do utilizador
    function alterarSenha($idUser, $senhaAtual, $novaSenha) {
        global $conn;

        // Verificar senha atual
        $sql = "SELECT password FROM Utilizadores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idUser);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verificar se a senha atual está correta
            if(password_verify($senhaAtual, $row['password'])) {
                // Hash da nova senha
                $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                // Atualizar senha
                $sqlUpdate = "UPDATE Utilizadores SET password = ? WHERE id = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $novaSenhaHash, $idUser);

                if($stmtUpdate->execute()) {
                    $stmt->close();
                    $stmtUpdate->close();
                    return json_encode(['success' => true, 'message' => 'Senha alterada com sucesso!']);
                } else {
                    $stmt->close();
                    $stmtUpdate->close();
                    return json_encode(['success' => false, 'message' => 'Erro ao atualizar a senha.']);
                }
            } else {
                $stmt->close();
                return json_encode(['success' => false, 'message' => 'Senha atual incorreta.']);
            }
        } else {
            $stmt->close();
            return json_encode(['success' => false, 'message' => 'Utilizador não encontrado.']);
        }
    }
}
?>
