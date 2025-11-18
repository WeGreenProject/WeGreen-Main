<?php
    session_start();

    if($_SESSION['tipo'] == 1){ 
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Adminstrador - Fashion Store</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/Admin.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" style="text-decoration: none;">
                <div class="logo">
                    <span class="logo-icon">👔</span>
                    <div class="logo-text">
                        <h1>Wegreen</h1>
                        <p>Painel do Adminstrador</p>
                    </div>
                </div>
            </a>
                                    <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link active" href="DashboardAdmin.php">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produtos.php">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoProdutosAdmin.php">
                            <span class="nav-icon">🛍️</span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ProdutosAdmin.php">
                            <span class="nav-icon">🛒</span>
                            <span class="nav-text">Aprovar Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon">📈</span>
                            <span class="nav-text">Análises</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chats.php">
                            <span class="nav-icon">💬</span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fornecedores.php">
                            <span class="nav-icon">🚚</span>
                            <span class="nav-text">Gestão de Fornecedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="user-profile">
                <div class="profile-info" id="ProfileUser">
                </div>
                <button class="profile-settings-btn" onclick="showPage('settings')" title="Configurações">
                    <span>⚙️</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
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
                            <h3>Vendas 🔥</h3>
                            <p>Evolução do lucro nos últimos meses</p>
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

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Produtos Recentes</h3>
                        <p>Últimos produtos adicionados</p>
                    </div>
                    <div id="recentProducts"></div>
                </div>
            </div>

            <div id="products" class="page">
                <div class="action-bar">
                    <div class="page-header">
                        <h2>Produtos</h2>
                        <p>Gerir todos os seus produtos</p>
                    </div>
                    <button class="btn-primary" onclick="openModal()">
                        <span>➕</span>
                        Adicionar Produto
                    </button>
                </div>
                <div class="products-grid" id="productsGrid"></div>
            </div>
        </main>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Adicionar Produto</h3>
                <button class="close-btn" onclick="closeModal()">×</button>
            </div>
            <form id="productForm">
                <div class="form-group">
                    <label>Nome do Produto</label>
                    <input type="text" id="productName" required placeholder="Ex: Camisa Casual">
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea id="productDesc" rows="3" placeholder="Descrição do produto..."></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Preço (€)</label>
                        <input type="number" id="productPrice" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Custo (€)</label>
                        <input type="number" id="productCost" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Stock Inicial</label>
                    <input type="number" id="productStock" required>
                </div>
                <div class="form-group">
                    <label>Ícone (emoji)</label>
                    <input type="text" id="productIcon" value="👔" maxlength="2">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">Adicionar Produto</button>
            </form>
        </div>
    </div>
</body>
<?php 
}else{
    echo "sem permissão!";
}

?>
<script src="src/js/Adminstrador.js"></script>

</html>