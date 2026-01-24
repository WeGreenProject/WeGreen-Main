<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anunciante - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="src/js/notifications.js"></script>
    <script src="src/js/Anunciante.js"></script>
</head>

<body>
    <div class="dashboard-container">
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
                    <a href="DashboardAnunciante.php" class="menu-item active">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="gestaoProdutosAnunciante.php" class="menu-item">
                        <i class="fas fa-tshirt"></i>
                        <span>Produtos</span>
                    </a>
                    <a href="gestaoEncomendasAnunciante.php" class="menu-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Encomendas</span>
                    </a>
                    <a href="gestaoDevolucoesAnunciante.php" class="menu-item">
                        <i class="fas fa-undo"></i>
                        <span>Devoluções</span>
                    </a>
                    <a href="ChatAnunciante.php" class="menu-item">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <h1 class="page-title"><i class="fas fa-chart-line"></i> Dashboard</h1>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'" style="display: none;" <?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
                        <i class="fas fa-crown"></i> Upgrade
                    </button>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff" alt="Usuário" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></span>
                            <span class="user-role">Anunciante</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff" alt="Usuário" class="dropdown-avatar">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'user@email.com'; ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="perfilAnunciante.php">
                            <i class="fas fa-user"></i>
                            <span>Meu Perfil</span>
                        </a>
                        <a class="dropdown-item" href="alterarSenha.php">
                            <i class="fas fa-key"></i>
                            <span>Alterar Senha</span>
                        </a>
                        <button class="dropdown-item" id="btnAlternarConta" onclick="verificarEAlternarConta()" style="display:none;">
                            <i class="fas fa-exchange-alt"></i>
                            <span id="textoAlternar">Alternar Conta</span>
                        </button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item dropdown-item-danger" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </button>
                    </div>
                </div>
            </nav>

            <div class="content-area">
                <!-- KPIs/Stats Cards -->
                <div class="stats-grid-compact">
                    <div id="PontosConfianca" class="stat-card-compact"></div>
                    <div id="GastosCard" class="stat-card-compact"></div>
                    <div id="ProdutoStock" class="stat-card-compact"></div>
                    <div id="PlanosAtual" class="stat-card-compact"></div>
                </div>

                <!-- Charts Section -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Vendas Mensais</h3>
                            <p>Evolução das vendas nos últimos meses</p>
                        </div>
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Top Produtos</h3>
                            <p>Produtos mais vendidos</p>
                        </div>
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>

                <!-- Recent Products Section -->
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-box"></i> Produtos Recentes
                        </h2>
                        <a href="gestaoProdutosAnunciante.php" class="btn-ver-todas">
                            Ver Todos <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div id="recentProducts"></div>
                </div>
            </div>
        </main>
    </div>
</body>
<?php
}else{
    echo "sem permissão!";
}
?>

</html>

    <script>
        $(document).ready(function() {
            initDashboardPage();
        });
    </script>    <script src="src/js/alternancia.js"></script>
