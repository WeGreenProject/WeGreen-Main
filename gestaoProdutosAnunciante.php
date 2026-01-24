<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/gestaoProdutos.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/modalProduto.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
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
                    <a href="DashboardAnunciante.php" class="menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="gestaoProdutosAnunciante.php" class="menu-item active">
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
                    <h1 class="page-title"><i class="fas fa-tshirt"></i> Produtos</h1>
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
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item dropdown-item-danger" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </button>
                    </div>
                </div>
            </nav>

            <div class="content-area">
                <div id="products" class="page active">
                    <div class="page-actions">
                        <div class="actions-left">
                            <button id="addProductBtn" class="btn-add-product">
                                <i class="fas fa-plus"></i>
                                <span>Adicionar Produto</span>
                            </button>
                        </div>
                        <div class="actions-right">
                            <button id="exportProductsBtn" class="btn-export-pdf">
                                <i class="fas fa-file-pdf"></i>
                                <span>Exportar PDF</span>
                            </button>
                        </div>
                    </div>

                <div class="stats-grid-compact" id="productStats">
                    <div class="stat-card-compact" id="totalProdutosCard"></div>
                    <div class="stat-card-compact" id="produtosAtivosCard"></div>
                    <div class="stat-card-compact" id="produtosInativosCard"></div>
                    <div class="stat-card-compact" id="stockCriticoCard"></div>
                </div>

                <div id="bulkActions" class="bulk-actions" style="display: none;">
                    <span id="selectedCount">0 selecionados</span>
                    <button onclick="editarSelecionado()" class="btn-bulk"><i class="fas fa-edit"></i> Editar</button>
                    <button onclick="removerEmMassa()" class="btn-bulk"><i class="fas fa-trash"></i> Remover</button>
                </div>

                <!-- Barra de Pesquisa e Filtros -->
                <div class="filters-container">
                    <div class="filters-grid">
                        <div class="filter-item">
                            <label>
                                <i class="fas fa-search"></i> Pesquisar Produto
                            </label>
                            <input type="text" id="searchProduct" placeholder="Nome do produto..." class="filter-input">
                        </div>
                        <div class="filter-item">
                            <label>
                                <i class="fas fa-box"></i> Tipo
                            </label>
                            <select id="filterTipo" class="filter-select">
                                <option value="">Todos os Tipos</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>
                                <i class="fas fa-info-circle"></i> Estado
                            </label>
                            <select id="filterEstado" class="filter-select">
                                <option value="">Todos os Estados</option>
                                <option value="Novo">Novo</option>
                                <option value="Como Novo">Como Novo</option>
                                <option value="Excelente">Excelente</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>
                                <i class="fas fa-venus-mars"></i> Gênero
                            </label>
                            <select id="filterGenero" class="filter-select">
                                <option value="">Todos os Géneros</option>
                                <option value="Mulher">Mulher</option>
                                <option value="Homem">Homem</option>
                                <option value="Criança">Criança</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>
                                <i class="fas fa-toggle-on"></i> Status
                            </label>
                            <select id="filterAtivo" class="filter-select">
                                <option value="">Todos (Ativo/Inativo)</option>
                                <option value="1">Apenas Ativos</option>
                                <option value="0">Apenas Inativos</option>
                            </select>
                        </div>
                        <div class="filter-item-button">
                            <button class="btn-clear-filters" onclick="limparFiltrosProdutos()">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table id="productsTable" class="display">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th><i class="fas fa-image"></i> Foto</th>
                                <th><i class="fas fa-tag"></i> Nome</th>
                                <th><i class="fas fa-box"></i> Tipo</th>
                                <th><i class="fas fa-euro-sign"></i> Preço (€)</th>
                                <th><i class="fas fa-warehouse"></i> Stock</th>
                                <th><i class="fas fa-info-circle"></i> Estado</th>
                                <th><i class="fas fa-toggle-on"></i> Ativo</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            </div>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            initProductsPage();
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
