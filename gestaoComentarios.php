<?php
session_start();

if($_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Comentários - WeGreen Admin</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAdmin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/gestaoProdutos.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/gestaoComentariosAdmin.css?v=<?php echo time(); ?>">
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
                    <a href="gestaoProdutosAdmin.php" class="menu-item">
                        <i class="fas fa-tshirt"></i>
                        <span>Produtos</span>
                    </a>
                    <a href="gestaoCliente.php" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Utilizadores</span>
                    </a>
                    <a href="gestaoComentarios.php" class="menu-item active">
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
                    <h1 class="page-title"><i class="fas fa-comment-dots"></i> Gestão de Comentários</h1>
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
                <!-- KPIs/Stats Cards -->
                <div class="stats-grid-compact">

                </div>

                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    <button class="tab-button active" data-tab="comentarios">
                        <i class="fas fa-comments"></i>
                        <span>Comentários</span>
                        <span class="tab-badge" id="badgeComentarios">0</span>
                    </button>
                    <button class="tab-button" data-tab="reports">
                        <i class="fas fa-flag"></i>
                        <span>Reports</span>
                        <span class="tab-badge alert" id="badgeReports">0</span>
                    </button>
                </div>

                <!-- Tab Content: Comentários -->
                <div class="tab-content active" id="tab-comentarios">
                    <!-- Filtros -->
                    <div class="filters-container">
                        <div class="filters-grid">
                            <div class="filter-item">
                                <label>
                                    <i class="fas fa-search"></i> Pesquisar
                                </label>
                                <input type="text" id="filterSearchComentarios" class="filter-input"
                                    placeholder="Procurar por produto, utilizador...">
                            </div>
                            <div class="filter-item">
                                <label>
                                    <i class="fas fa-star"></i> Avaliação
                                </label>
                                <select id="filterAvaliacaoComentarios" class="filter-select">
                                    <option value="">Todas</option>
                                    <option value="5">5 estrelas</option>
                                    <option value="4">4+ estrelas</option>
                                    <option value="3">3+ estrelas</option>
                                    <option value="2">2+ estrelas</option>
                                    <option value="1">1+ estrela</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label>
                                    <i class="fas fa-calendar"></i> Período
                                </label>
                                <select id="filterPeriodoComentarios" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="hoje">Hoje</option>
                                    <option value="semana">Esta Semana</option>
                                    <option value="mes">Este Mês</option>
                                    <option value="ano">Este Ano</option>
                                </select>
                            </div>
                            <div class="filter-item-button">
                                <button class="btn-clear-filters" id="clearFiltersComentarios">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bulk-actions-bar" id="bulkActionsComentarios" style="display: none;">
                        <span class="bulk-count">
                            <i class="fas fa-check-square"></i>
                            <span id="selectedCountComentarios">0</span> comentário(s) selecionado(s)
                        </span>
                        <div class="bulk-buttons">
                            <button class="btn-bulk btn-approve" onclick="aprovarSelecionadosComentarios()">
                                <i class="fas fa-check"></i> Aprovar
                            </button>
                            <button class="btn-bulk btn-reject" onclick="rejeitarSelecionadosComentarios()">
                                <i class="fas fa-times"></i> Rejeitar
                            </button>
                            <button class="btn-bulk btn-delete" onclick="eliminarSelecionadosComentarios()">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table id="comentariosTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-image"></i> Foto Produto</th>
                                    <th><i class="fas fa-box"></i> Nome Produto</th>
                                    <th><i class="fas fa-euro-sign"></i> Preço</th>
                                    <th><i class="fas fa-star"></i> Avaliação</th>
                                    <th><i class="fas fa-calendar-alt"></i> Data</th>
                                    <th><i class="fas fa-tools"></i> Ações</th>
                                </tr>
                            </thead>
                            <tbody id="comentariosTableBody">

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Content: Reports -->
                <div class="tab-content" id="tab-reports">
                    <!-- Filtros Reports -->
                    <div class="filters-container">
                        <div class="filters-grid">
                            <div class="filter-item">
                                <label>
                                    <i class="fas fa-search"></i> Pesquisar
                                </label>
                                <input type="text" id="filterSearchReports" class="filter-input"
                                    placeholder="Procurar por denunciante, comentário...">
                            </div>
                            <div class="filter-item">
                                <label>
                                    <i class="fas fa-list"></i> Estado
                                </label>
                                <select id="filterEstadoReports" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="analisado">Analisado</option>
                                    <option value="resolvido">Resolvido</option>
                                    <option value="rejeitado">Rejeitado</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label>
                                    <i class="fas fa-exclamation-triangle"></i> Tipo
                                </label>
                                <select id="filterTipoReports" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="spam">Spam</option>
                                    <option value="ofensivo">Conteúdo Ofensivo</option>
                                    <option value="falso">Informação Falsa</option>
                                    <option value="inapropriado">Inapropriado</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label>
                                    <i class="fas fa-calendar"></i> Período
                                </label>
                                <select id="filterPeriodoReports" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="hoje">Hoje</option>
                                    <option value="semana">Esta Semana</option>
                                    <option value="mes">Este Mês</option>
                                </select>
                            </div>
                            <div class="filter-item-button">
                                <button class="btn-clear-filters" id="clearFiltersReports">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Ações em Massa Reports -->
                    <div class="bulk-actions-bar" id="bulkActionsReports" style="display: none;">
                        <span class="bulk-count">
                            <i class="fas fa-check-square"></i>
                            <span id="selectedCountReports">0</span> report(s) selecionado(s)
                        </span>
                        <div class="bulk-buttons">
                            <button class="btn-bulk btn-approve" onclick="resolverSelecionadosReports()">
                                <i class="fas fa-check"></i> Resolver
                            </button>
                            <button class="btn-bulk btn-reject" onclick="rejeitarSelecionadosReports()">
                                <i class="fas fa-ban"></i> Rejeitar
                            </button>
                            <button class="btn-bulk btn-delete" onclick="eliminarComentariosReportados()">
                                <i class="fas fa-trash"></i> Eliminar Comentários
                            </button>
                        </div>
                    </div>

                    <!-- Tabela de Reports -->
                    <div class="table-container">
                        <table id="reportsTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-image"></i> Foto</th>
                                    <th><i class="fas fa-user"></i>Denunciante</th>
                                    <th><i class="fas fa-envelope"></i> Email Denunciante</th>
                                    <th><i class="fas fa-align-left"></i> Motivo</th>
                                    <th><i class="fas fa-exclamation-triangle"></i> Denunciado</th>
                                    <th><i class="fas fa-info-circle"></i> Estado</th>
                                    <th><i class="fas fa-calendar"></i> Data</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTableBody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Detalhes do Comentário -->
    <div id="comentarioModal" class="modal-overlay" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2><i class="fas fa-comment-dots"></i> Detalhes do Comentário</h2>
                <button class="modal-close" onclick="fecharModalComentario()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="comentarioModalBody">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes do Report -->
    <div id="reportModal" class="modal-overlay" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2><i class="fas fa-flag"></i> Detalhes do Report</h2>
                <button class="modal-close" onclick="fecharModalReport()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="reportModalBody">
                <!-- Populated by JavaScript -->
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="src/js/Adminstrador.js"></script>
<script src="src/js/gestaoComentarios.js?v=<?php echo time(); ?>"></script>

</html>

<?php
}else{
    header("Location: forbiddenerror.html");
}
?>
