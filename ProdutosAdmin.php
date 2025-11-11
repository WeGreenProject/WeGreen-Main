<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprovação de Produtos - Fashion Store</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/AdminProdutos.css">
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
                        <h1>Fashion Store</h1>
                        <p>Painel do Administrador</p>
                    </div>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="DashboardAdmin.php">
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
                        <a class="nav-link" href="vendas.php">
                            <span class="nav-icon">🛍️</span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="aprovar.php">
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
            <div class="page-header">
                <h2>Aprovar Produtos</h2>
                <p>Revise e aprove produtos pendentes de fornecedores</p>
            </div>

            <!-- Estatísticas de Aprovação -->
            <div class="approval-stats">
                <div class="approval-stat-card" id="Pendentes">
                </div>
                <div class="approval-stat-card" id="Aprovados">
                </div>
                <div class="approval-stat-card" id="Rejeitados">
                </div>
            </div>

            <!-- Filtros -->
            <div class="approval-filters" id="">
                <button class="filter-btn active">
                    Todos <span class="filter-badge" id="allBadge">0</span>
                </button>
                <button class="filter-btn">
                    Pendentes <span class="filter-badge" id="pendingBadge">0</span>
                </button>
                <button class="filter-btn">
                    Aprovados <span class="filter-badge" id="approvedBadge">0</span>
                </button>
                <button class="filter-btn">
                    Rejeitados <span class="filter-badge" id="rejectedBadge">0</span>
                </button>
            </div>
            <div class="table-container" id="tableAprovar">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Anunciante</th>
                            <th>Tipo_Produto</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Aceitar</th>
                            <th>Recusar</th>
                        </tr>
                    </thead>
                    <tbody id="productsAprovarTable">
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
<script src="src/js/ProdutosAdmin.js"></script>

</html>