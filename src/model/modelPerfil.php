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
                    $msg .= "<li><a class='dropdown-item' href='DashboardAdmin.php'>Dashboard Administrador</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='#'>Definições de Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Checkout</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'>Sair</a></li>";
                }
                else if($tpUser == 2)
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='DashboardCliente.php'>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href='minhasEncomendas.php'>As Minhas Encomendas</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Definições de Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Checkout</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'>Sair</li>";
                }
                else if($tpUser == 3)
                {
                    $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
                    $msg .= "<h6 class='mb-0 text-wegreen-accent'>Olá, " . $row['nome'] . "!</h6>";
                    $msg .= "</div></li>";
                    $msg .= "<li><a class='dropdown-item' href='DashboardAnunciante.php'>Dashboard</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Definições de Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Checkout</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'>Sair</a></li>";
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
    function logout(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
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

                    $msg .= "<div class='row'>";

                    $msg .= "<div class='col-md-6'>";
                    $msg .= "<label class='form-label fw-semibold'>Nome Completo *</label>";
                    $msg .= "<input type='text' class='form-control form-control-lg' id='nomeUser' value='".$row["nome"]."' disabled>";
                    $msg .= "</div>";

                    $msg .= "<div class='col-md-6'>";
                    $msg .= "<label class='form-label fw-semibold'>Email *</label>";
                    $msg .= "<input type='email' class='form-control form-control-lg' id='emailUser' value='".$row["email"]."' disabled>";
                    $msg .= "</div>";

                    $msg .= "</div>";

                    $msg .= "<div class='col-12'>";
                    $msg .= "<label class='form-label fw-semibold'>Mensagem *</label>";
                    $msg .= "<textarea class='form-control form-control-lg' rows='5' id='mensagemUser' placeholder='Descreva a sua questão ou sugestão...' required></textarea>";
                    $msg .= "</div>";

                    $msg .= "<div class='col-12 text-center mt-4'>";
                    $msg .= "<button type='button' class='btn btn-submit-wegreen btn-lg' onclick='AdicionarMensagemContacto()'>";
                    $msg .= "<i class='bi bi-send me-2'></i> Enviar Mensagem";
                    $msg .= "</button>";
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
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido </li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Taxas reduzidas em categorias ecológicas certificadas.</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=crescimentocircular&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Taxas reduzidas em categorias ecológicas certificadas.</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido </li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido </li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=crescimentocircular&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
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
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido </li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=premium&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido </li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=eco&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Crescimento Circular</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido </li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=crescimentocircular&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='ffw-bold mb-3 text-white'>Plano Profissional Eco+ </h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5  fw-bold mb-3 text-whitetext-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking sustentabilidade</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Ranking Confiança de Vendas: visualização das métricas individuais de vendas, atendimento e entrega.</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat direto com clientes para suporte rápido</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Histórico completo de vendas, com gráficos avançados</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
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
}
?>