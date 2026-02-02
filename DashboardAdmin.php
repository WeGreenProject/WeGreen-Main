<?php
session_start();

if($_SESSION['tipo'] == 1){
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAdmin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="assets/css/notifications-dropdown.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="src/js/notifications.js"></script>
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
                    <a href="DashboardAdmin.php" class="menu-item active">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="gestaoProdutosAdmin.php" class="menu-item">
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
                    <h1 class="page-title"><i class="fas fa-chart-line"></i> Dashboard</h1>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff" alt="Administrador" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></span>
                            <span class="user-role">Administrador</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff" alt="Administrador" class="dropdown-avatar">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'admin@wegreen.com'; ?></div>
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
                <!-- KPIs/Stats Cards -->
                <div class="stats-grid-compact">
                    <div id="RendimentosCard" class="stat-card-compact"></div>
                    <div id="GastosCard" class="stat-card-compact"></div>
                    <div id="UtilizadoresCard" class="stat-card-compact"></div>
                    <div id="PlanosAtivos" class="stat-card-compact"></div>
                </div>

                <!-- Charts Section -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-fire"></i> Gastos e Rendimentos</h3>
                            <p>Evolução nos últimos meses</p>
                        </div>
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-star"></i> Top Produtos</h3>
                            <p>Produtos mais vendidos</p>
                        </div>
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>

                <!-- Products Table Section -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-clock"></i> Produtos ainda para verificar</h3>
                        <p>Últimos produtos adicionados</p>
                    </div>
                    <div class="table-container">
                        <table id="ProdutosInativosTable" class="display">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-image"></i> Foto</th>
                                    <th><i class="fas fa-tag"></i> Nome</th>
                                    <th><i class="fas fa-box"></i> Categoria</th>
                                    <th><i class="fas fa-euro-sign"></i> Preço</th>
                                    <th><i class="fas fa-warehouse"></i> Stock</th>
                                    <th><i class="fas fa-cog"></i> Ação</th>
                                </tr>
                            </thead>
                            <tbody id="ProdutosInativosBody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="products" class="page">
                <div class="action-bar">
                    <div class="page-header">
                        <h2>Produtos</h2>
                        <p>Gerir todos os seus produtos</p>
                    </div>
                    <button class="btn-primary" onclick="alert('Adicionar Produto')">
                        <i class="fas fa-plus"></i>
                        Adicionar Produto
                    </button>
                </div>
                <div class="products-grid" id="productsGrid">
                    <div class="product-card">
                        <div class="product-image"><i class="fas fa-tshirt"></i></div>
                        <div class="product-name">Camisa Sustentável</div>
                        <div class="product-desc">Algodão orgânico 100% sustentável</div>
                        <div class="product-info">
                            <div class="product-info-row">
                                <span class="product-info-label">Preço</span>
                                <span class="product-info-value">€45.99</span>
                            </div>
                            <div class="product-info-row">
                                <span class="product-info-label">Stock</span>
                                <span class="product-info-value">23</span>
                            </div>
                            <div class="product-info-row">
                                <span class="product-info-label">Lucro</span>
                                <span class="product-info-value product-profit">€15.50</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="src/js/Adminstrador.js"></script>
    <script>
        // Override do getInfoUserDropdown para manter o HTML correto da página
        function getInfoUserDropdown() {
            // Não fazer nada - o HTML já está correto na página
            console.log('Dropdown já configurado no HTML');
        }
    </script>
</body>
</html>
<?php
}else{
header("Location: forbiddenerror.html");
}
?>
