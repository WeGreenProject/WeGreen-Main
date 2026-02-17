<?php
session_start();

if(!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 2){
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css">
    <link rel="stylesheet" href="src/css/notifications-dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
    <script src="src/js/notifications.js"></script>

</head>
<body>
  <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="index.html" class="sidebar-logo" style="text-decoration: none; color: inherit; cursor: pointer;">
                <i class="fas fa-leaf"></i>
                <div class="logo-text">
                    <h2>WeGreen</h2>
                    <p>Moda Sustentável</p>
                </div>
            </a>

            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">Menu</div>
                    <a href="DashboardCliente.php" class="menu-item active" data-page="dashboard">
                        <i class="fas fa-home"></i>
                        <span>Início</span>
                    </a>
                    <a href="minhasEncomendas.php" class="menu-item" data-page="orders">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Minhas Encomendas</span>
                    </a>
                    <a href="meusFavoritos.php" class="menu-item" data-page="favorites">
                        <i class="fas fa-heart"></i>
                        <span>Meus Favoritos</span>
                        <span class="badge" id="favoritosBadge" style="display:none; background:#3cb371; color:white; padding:2px 8px; border-radius:10px; font-size:11px; margin-left:auto;"></span>
                    </a>
                    <a href="ChatCliente.php" class="menu-item" data-page="chat">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <h1 class="page-title"><i class="fas fa-home"></i> Dashboard</h1>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff" alt="User" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></span>
                            <span class="user-role">Cliente</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff" alt="User" class="dropdown-avatar">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? ''; ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="perfilCliente.php">
                            <i class="fas fa-user"></i>
                            <span>Meu Perfil</span>
                        </a>
                        <a class="dropdown-item" href="alterarSenha.php">
                            <i class="fas fa-key"></i>
                            <span>Alterar Senha</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item dropdown-item-danger" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </button>
                    </div>
                </div>
            </nav>
            <!-- Página Dashboard -->
            <div id="page-dashboard" class="page-content">
                <div class="content-area">
                    <!-- Page Greeting -->
                    <div class="page-greeting">
                        <h1>Olá, <?php echo isset($_SESSION['nome']) ? explode(' ', $_SESSION['nome'])[0] : 'Cliente'; ?> <span class="wave">👋</span></h1>
                        <p>Descobre os nossos produtos sustentáveis</p>
                    </div>

                    <!-- Produtos Adquiridos Recentemente -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-history"></i> Produtos Adquiridos Recentemente
                            </h2>
                        </div>
                        <div id="recomendacoesContainer">
                            <!-- Produtos carregados via AJAX -->
                        </div>
                    </div>

                    <!-- Encomendas Recentes -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-shipping-fast"></i> Encomendas Recentes
                            </h2>
                            <a href="minhasEncomendas.php" class="btn-ver-todas">
                                Ver Todas <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div id="encomendasContainer">
                            <!-- Encomendas carregadas via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="src/js/DashboardCliente.js"></script>
    <script src="src/js/alternancia.js"></script>
</body>

</html>
