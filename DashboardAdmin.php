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
    <link rel="stylesheet" href="src/css/admin.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" class="logo">
                <span class="logo-icon"><i class="fas fa-leaf"></i></span>
                <div class="logo-text">
                    <h1>WeGreen</h1>
                    <p>Painel do Administrador</p>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link active" href="DashboardAdmin.php">
                            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoProdutosAdmin.php">
                            <span class="nav-icon"><i class="fas fa-tshirt"></i></span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoCliente.php">
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Gestao de Utilizadores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoLucros.php">
                            <span class="nav-icon"><i class="fas fa-euro-sign"></i></span>
                            <span class="nav-text">Gestão de Lucros</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Chatadmin.php">
                            <span class="nav-icon"><i class="fas fa-comments"></i></span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fornecedores.php">
                            <span class="nav-icon"><i class="fas fa-truck"></i></span>
                            <span class="nav-text">Gestão de Fornecedores</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <i class="navbar-icon fas fa-chart-line"></i>
                    <h2 class="navbar-title">Dashboard</h2>
                </div>
                <div class="navbar-right">
                    <button class="navbar-icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="navbar-user">
                        <div id="AdminPerfilInfo" style="display:flex;"></div>
                        <i class=" fas fa-chevron-down user-trigger" style="font-size: 12px; color: #4a5568;"></i>

                        <div class="user-dropdown" id="userDropdown"></div>
                    </div>
                </div>
            </nav>

            <div id="dashboard" class="page active">
                <div class="page-header">
                    <h2>Dashboard</h2>
                    <p>Visão geral do seu negócio</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card" id="RendimentosCard">

                    </div>
                    <div class="stat-card" id="GastosCard">
                    </div>
                    <div class="stat-card" id="UtilizadoresCard">
                    </div>
                    <div class="stat-card" id="PlanosAtivos">

                    </div>
                </div>

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

                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-clock"></i> Produtos ainda para verificar</h3>
                        <p>Últimos produtos adicionados</p>
                    </div>
                    <div id="ProdutosInativosTable">
                        <table class="ProdutosInativosTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Stock</th>
                                    <th>Ação</th>
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


</body>
<script src="src/js/Adminstrador.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleUserDropdown() {
    document.getElementById('userDropdown').classList.toggle('active');
}

function closeUserDropdown() {
    document.getElementById('userDropdown').classList.remove('active');
}

// Fecha ao clicar fora
document.addEventListener('click', function(e) {
    const user = document.querySelector('.navbar-user');
    const dropdown = document.getElementById('userDropdown');

    if (!user.contains(e.target)) {
        dropdown.classList.remove('active');
    }
});
</script>

</html>
<?php
}else{
header("Location: forbiddenerror.html");
}
?>