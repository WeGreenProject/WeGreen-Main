<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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
                    <a href="DashboardAnunciante.php" class="menu-item">
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
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                </div>
                <div class="navbar-right">
                    <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'" style="display: none;" <?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
                        <i class="fas fa-crown"></i> Upgrade
                    </button>
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=A6D90C&color=fff" alt="Usuário" class="user-avatar">
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

            <div id="analytics" class="page active">
                <div class="filters filters-right">
                    <select id="reportPeriod">
                        <option value="month">Último Mês</option>
                        <option value="year">Último Ano</option>
                        <option value="all">Todo o Período</option>
                    </select>
                </div>

                <div class="stats-grid stats-grid-wide">
                    <div class="stat-card stat-card-compact" id="totalRevenue"></div>
                    <div class="stat-card stat-card-compact" id="averageTicket"></div>
                    <div class="stat-card stat-card-compact" id="profitMargin"></div>
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Receita Diária</h3>
                            <p>Evolução da receita ao longo dos dias</p>
                        </div>
                        <canvas id="dailyRevenueChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Distribuição de Vendas</h3>
                            <p>Vendas por categoria de produto</p>
                        </div>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Relatório de Produtos</h3>
                        <p>Análise detalhada de vendas por produto</p>
                    </div>
                    <div class="table-container">
                        <table id="reportsTable" class="display">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Vendas</th>
                                    <th>Receita (€)</th>
                                    <th>Lucro (€)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Alterar Senha -->
            <div id="passwordModal" class="modal">
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header">
                        <h3>Alterar Senha</h3>
                        <span class="close close-btn" onclick="closePasswordModal()">&times;</span>
                    </div>
                    <form id="passwordForm" class="profile-form" style="margin-top: 20px;">
                        <input type="text" name="username" autocomplete="username" value="<?php echo $_SESSION['email'] ?? ''; ?>" style="display: none;" readonly>
                        <div class="form-group">
                            <label>Senha Atual</label>
                            <input type="password" id="currentPassword" autocomplete="current-password" required>
                        </div>
                        <div class="form-group">
                            <label>Nova Senha</label>
                            <input type="password" id="newPassword" autocomplete="new-password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmar Nova Senha</label>
                            <input type="password" id="confirmPassword" autocomplete="new-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                            <i class="fas fa-key"></i> Alterar Senha
                        </button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        $(document).ready(function() {
            initAnalyticsPage();
        });
    </script>
    <script src="src/js/alternancia.js"></script>
</body>
<?php
}else{
    echo "sem permissão!";
}
?>

</html>
