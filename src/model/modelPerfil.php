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
                    $msg .= "<li><a class='dropdown-item' href=''>Definições de Perfil</a></li>";
                    $msg .= "<li><a class='dropdown-item' href=''>Checkout</a></li>";
                    $msg .= "<li><hr class='dropdown-divider'></li>";
                    $msg .= "<li><a href='index.html' class='dropdown-item text-danger' onclick='logout()'>Sair</a></li>";
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
    function logout(){

        session_start();
        session_destroy();

        return("Obrigado!");
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
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Free</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas básico</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Premium</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=premium&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Enterprise</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=enterprise&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Free</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas básico</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Premium</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Enterprise</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=enterprise&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Free</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas básico</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Premium</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=premium&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Enterprise</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
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
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Free</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas básico</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Premium</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=premium&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Enterprise</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=enterprise&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Free</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas básico</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Premium</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-dark text-wegreen-dark rounded-pill px-4 fw-bold'>Selecionado</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Enterprise</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=enterprise&preco=100' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
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
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Free</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>0€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>3 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas básico</li>";
                $msg .= "</ul>";
                $msg .= "<a href='#' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO PREMIUM ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold text-wegreen-accent mb-3'>Plano Premium</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>25€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>10 produtos ativos</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
                $msg .= "</ul>";
                $msg .= "<a href='checkout.php?plano=premium&preco=25' class='btn btn-wegreen-accent text-black rounded-pill px-4'>Selecionar</a>";
                $msg .= "</div></div></div>";

                // === PLANO ENTERPRISE ===
                $msg .= "<div class='col-md-4'>";
                $msg .= "<div class='card h-100 border-0 shadow-lg rounded-4 bg-wegreen-accent text-white'>";
                $msg .= "<div class='card-body py-5'>";
                $msg .= "<h4 class='fw-bold mb-3 text-white'>Plano Enterprise</h4>";
                $msg .= "<h2 class='display-5 fw-bold mb-4'>100€<span class='fs-5 text-muted'>/mês</span></h2>";
                $msg .= "<ul class='list-unstyled mb-4 text-start px-4'>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Acesso ao ranking</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Suporte com administrador</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Produtos Anunciados ilimitados</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Chat com clientes</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Rastreio de encomendas avançado</li>";
                $msg .= "<li><i class='bi bi-check-circle text-success me-2'></i>Relatórios por PDF</li>";
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