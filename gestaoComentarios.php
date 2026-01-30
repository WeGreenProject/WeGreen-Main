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
    <link rel="stylesheet" href="src/css/admin.css">
    <link rel="stylesheet" href="src/css/gestaoComentariosAdmin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="assets/css/notifications-dropdown.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/notifications.js"></script>
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
                        <a class="nav-link" href="DashboardAdmin.php">
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
                            <span class="nav-icon"><i class="fas fa-users"></i></span>
                            <span class="nav-text">Utilizadores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestaoComentariosAdmin.php">
                            <span class="nav-icon"><i class="fas fa-comments"></i></span>
                            <span class="nav-text">Comentários</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoLucros.php">
                            <span class="nav-icon"><i class="fas fa-euro-sign"></i></span>
                            <span class="nav-text">Lucros</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Chatadmin.php">
                            <span class="nav-icon"><i class="fas fa-message"></i></span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logAdmin.php">
                            <span class="nav-icon"><i class="fas fa-history"></i></span>
                            <span class="nav-text">Logs</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <i class="navbar-icon fas fa-comments"></i>
                    <h2 class="navbar-title">Gestão de Comentários</h2>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user">
                        <div id="AdminPerfilInfo" style="display:flex;"></div>
                        <i class="fas fa-chevron-down user-trigger" style="font-size: 12px; color: #4a5568;"></i>
                        <div class="user-dropdown" id="userDropdown"></div>
                    </div>
                </div>
            </nav>

            <div id="comentarios" class="page active">
                <div class="page-header">
                    <h2>Gestão de Comentários & Reports</h2>
                    <p>Modere comentários e analise denúncias</p>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">

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
                    <div class="filters-card">
                        <div class="filters-header">
                            <h3><i class="fas fa-filter"></i> Filtros</h3>
                            <button class="btn-clear-filters" id="clearFiltersComentarios">
                                <i class="fas fa-times"></i> Limpar
                            </button>
                        </div>
                        <div class="filters-grid">
                            <div class="filter-item">
                                <label><i class="fas fa-search"></i> Pesquisar</label>
                                <input type="text" id="filterSearchComentarios" class="filter-input"
                                    placeholder="Procurar por produto, utilizador...">
                            </div>
                            <div class="filter-item">
                                <label><i class="fas fa-star"></i> Avaliação</label>
                                <select id="filterAvaliacaoComentarios" class="filter-select">
                                    <option value="">Todas</option>
                                    <option value="5">5 Estrelas</option>
                                    <option value="4">4 Estrelas</option>
                                    <option value="3">3 Estrelas</option>
                                    <option value="2">2 Estrelas</option>
                                    <option value="1">1 Estrela</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label><i class="fas fa-calendar"></i> Período</label>
                                <select id="filterPeriodoComentarios" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="hoje">Hoje</option>
                                    <option value="semana">Esta Semana</option>
                                    <option value="mes">Este Mês</option>
                                    <option value="ano">Este Ano</option>
                                </select>
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


                    <div class="table-card">
                        <div class="table-header">
                            <h3><i class="fas fa-list"></i> Lista de Produtos Comentados</h3>
                        </div>
                        <div class="table-responsive">
                            <table id="comentariosTable" class="modern-table" style="width:100%">
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
                </div>

                <!-- Tab Content: Reports -->
                <div class="tab-content" id="tab-reports">
                    <!-- Filtros Reports -->
                    <div class="filters-card">
                        <div class="filters-header">
                            <h3><i class="fas fa-filter"></i> Filtros de Reports</h3>
                            <button class="btn-clear-filters" id="clearFiltersReports">
                                <i class="fas fa-times"></i> Limpar
                            </button>
                        </div>
                        <div class="filters-grid">
                            <div class="filter-item">
                                <label><i class="fas fa-search"></i> Pesquisar</label>
                                <input type="text" id="filterSearchReports" class="filter-input"
                                    placeholder="Procurar por denunciante, comentário...">
                            </div>
                            <div class="filter-item">
                                <label><i class="fas fa-list"></i> Estado</label>
                                <select id="filterEstadoReports" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="analisado">Analisado</option>
                                    <option value="resolvido">Resolvido</option>
                                    <option value="rejeitado">Rejeitado</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label><i class="fas fa-exclamation-triangle"></i> Tipo</label>
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
                                <label><i class="fas fa-calendar"></i> Período</label>
                                <select id="filterPeriodoReports" class="filter-select">
                                    <option value="">Todos</option>
                                    <option value="hoje">Hoje</option>
                                    <option value="semana">Esta Semana</option>
                                    <option value="mes">Este Mês</option>
                                </select>
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
                    <div class="table-card">
                        <div class="table-header">
                            <h3><i class="fas fa-flag"></i> Lista de Reports</h3>
                        </div>
                        <div class="table-responsive">
                            <table id="reportsTable" class="modern-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="selectAllReports">
                                        </th>
                                        <th><i class="fas fa-user"></i> Denunciante</th>
                                        <th><i class="fas fa-comment"></i> Comentário Reportado</th>
                                        <th><i class="fas fa-user-circle"></i> Autor do Comentário</th>
                                        <th><i class="fas fa-exclamation-triangle"></i> Motivo</th>
                                        <th><i class="fas fa-align-left"></i> Descrição</th>
                                        <th><i class="fas fa-calendar"></i> Data</th>
                                        <th><i class="fas fa-info-circle"></i> Estado</th>
                                        <th><i class="fas fa-cog"></i> Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="reportsTableBody">

                                </tbody>
                            </table>
                        </div>
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
<script src="src/js/gestaoComentarios.js"></script>
<script>
// Toggle dropdown ao clicar no navbar-user
document.querySelector('.navbar-user').addEventListener('click', function(e) {
    e.stopPropagation();
    document.getElementById('userDropdown').classList.toggle('active');
});

// Fecha ao clicar fora
document.addEventListener('click', function(e) {
    const user = document.querySelector('.navbar-user');
    const dropdown = document.getElementById('userDropdown');

    if (!user.contains(e.target)) {
        dropdown.classList.remove('active');
    }
});

// Tab Navigation
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
        const tabName = this.dataset.tab;

        // Remove active class from all buttons and contents
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove(
            'active'));

        // Add active class to clicked button and corresponding content
        this.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
    });
});
</script>

</html>

<?php
}else{
    header("Location: forbiddenerror.html");
}
?>