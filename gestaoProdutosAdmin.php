<?php
session_start();

if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3){
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Produtos - WeGreen Admin</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAdmin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/modalProduto.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="src/css/notifications-dropdown.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="src/js/notifications.js"></script>
    <link rel="stylesheet" href="src/css/gestaoProdutosAdmin.css">
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
                    <a href="DashboardAdmin.php" class="menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="gestaoProdutosAdmin.php" class="menu-item active">
                        <i class="fas fa-tshirt"></i>
                        <span>Produtos</span>
                    </a>
                    <a href="gestaoCliente.php" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Utilizadores</span>
                    </a>
                    <a href="gestaoComentarios.php" class="menu-item">
                        <i class="fas fa-comment-dots"></i>
                        <span>Comentários</span>
                    </a>
                    <a href="gestaoLucros.php" class="menu-item">
                        <i class="fas fa-euro-sign"></i>
                        <span>Lucros</span>
                    </a>
                    <a href="logAdmin.php" class="menu-item">
                        <i class="fas fa-history"></i>
                        <span>Logs do Sistema</span>
                    </a>
                    <a href="Chatadmin.php" class="menu-item">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <h1 class="page-title"><i class="fas fa-tshirt"></i> Gestão de Produtos</h1>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff"
                            alt="Administrador" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></span>
                            <span class="user-role">Administrador</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff"
                                alt="Administrador" class="dropdown-avatar">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'admin@wegreen.com'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="perfilAdmin.php">
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

            <div class="content-area">
                <!-- Charts Section -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-fire"></i> Produtos Anunciados</h3>
                            <p>Evolução de produtos na plataforma</p>
                        </div>
                        <canvas id="topProductsChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-star"></i> Produtos Vendidos</h3>
                            <p>Performance de vendas</p>
                        </div>
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <!-- Tabs and Products Management -->
                <div class="chart-card" style="margin-top: 30px;">
                    <div class="chart-header">
                        <h3><i class="fas fa-globe"></i> Gestão de Produtos</h3>
                        <p>Todos os produtos da plataforma - Ativos, Inativos e Rejeitados</p>
                    </div>
                    <div class="table-container">
                        <table id="produtosTable" class="display">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-image"></i> Foto</th>
                                    <th><i class="fas fa-tag"></i> Nome</th>
                                    <th><i class="fas fa-box"></i> Categoria</th>
                                    <th><i class="fas fa-user"></i> Vendedor</th>
                                    <th><i class="fas fa-euro-sign"></i> Preço (€)</th>
                                    <th><i class="fas fa-info-circle"></i> Estado</th>
                                    <th><i class="fas fa-trademark"></i> Marca</th>
                                    <th><i class="fas fa-cog"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody id="produtosBody">
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-box"
                                            style="font-size: 48px; color: #cbd5e0; margin-bottom: 15px;"></i>
                                        <p style="color: #718096;">Nenhum produto encontrado</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    </div>
    </main>
    </div>

    <script src="src/js/Adminstrador.js"></script>
    <script src="src/js/gestaoProdutos.js"></script>
    <script src="src/js/gestaoProdutosAdmin.js"></script>
</body>

</html>
<?php
}else{
    echo "sem permissão!";
}
?>
