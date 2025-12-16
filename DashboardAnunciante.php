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
    <link rel="stylesheet" href="src/css/Anunciante.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="src/js/Anunciante.js"></script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <span class="logo-icon"><i class="fas fa-leaf"></i></span>
                <div class="logo-text">
                    <h1>WeGreen</h1>
                    <p>Moda Sustentável</p>
                </div>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <button class="nav-link active" onclick="showPage('dashboard', this)">
                            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                            <span class="nav-text">Dashboard</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('products', this)">
                            <span class="nav-icon"><i class="fas fa-tshirt"></i></span>
                            <span class="nav-text">Produtos</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('sales', this)">
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Encomendas</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('analytics', this)">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                            <span class="nav-text">Relatórios</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <div class="breadcrumb">
                        <span class="breadcrumb-item">
                            <i class="fas fa-home"></i> WeGreen
                        </span>
                        <i class="fas fa-chevron-right breadcrumb-separator"></i>
                        <span class="breadcrumb-item active" id="pageBreadcrumb">
                            <i class="navbar-icon fas fa-chart-line" id="pageIcon"></i> Dashboard
                        </span>
                    </div>
                </div>
                <div class="navbar-right">
                    <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="mostrarPlanosUpgrade()"
                        style="display: none;">
                        <i class="fas fa-crown"></i> Upgrade
                    </button>
                    <button class="navbar-icon-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="src/img/default-avatar.png" alt="Usuário" class="user-avatar"
                            onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=A6D90C&color=fff'">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></span>
                            <span class="user-role">Anunciante</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #4a5568;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="src/img/default-avatar.png" alt="Usuário" class="dropdown-avatar"
                                onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=A6D90C&color=fff'">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'user@email.com'; ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item" onclick="showPage('profile', null); closeUserDropdown();">
                            <i class="fas fa-user"></i>
                            <span>Meu Perfil</span>
                        </button>
                        <button class="dropdown-item" onclick="showPasswordModal()">
                            <i class="fas fa-key"></i>
                            <span>Alterar Senha</span>
                        </button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item dropdown-item-danger" onclick="window.location.href='login.html'">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </button>
                    </div>
                </div>
            </nav>

            <div id="dashboard" class="page active">
                <div class="stats-grid stats-grid-compact">
                    <div id="PontosConfianca" class="stat-card stat-card-compact"></div>
                    <div id="GastosCard" class="stat-card stat-card-compact"></div>
                    <div id="ProdutoStock" class="stat-card stat-card-compact"></div>
                    <div id="PlanosAtual" class="stat-card stat-card-compact"></div>
                </div>

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

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Produtos Recentes</h3>
                        <p>Últimos produtos adicionados</p>
                    </div>
                    <div id="recentProducts"></div>
                </div>
            </div>


            <div id="analytics" class="page">
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
                        <table id="productReportTable" class="display">
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

            <div id="profile" class="page">
                <div class="profile-container" id="profileCard">
                </div>

                <div class="profile-tabs">
                    <button class="profile-tab active" onclick="switchProfileTab('personal', this)">
                        <i class="fas fa-user"></i> Informações Pessoais
                    </button>
                    <button class="profile-tab" onclick="switchProfileTab('plan', this)">
                        <i class="fas fa-crown"></i> Plano & Ranking
                    </button>
                    <button class="profile-tab" onclick="switchProfileTab('security', this)">
                        <i class="fas fa-shield-alt"></i> Segurança
                    </button>
                </div>

                <div class="profile-tab-content">
                    <div id="tab-personal" class="tab-pane active">
                        <div class="profile-section" id="profileInfo">
                            <!-- Carregado via JS -->
                        </div>
                    </div>
                    <div id="tab-plan" class="tab-pane">
                        <div class="profile-section" id="profilePlan">
                            <!-- Carregado via JS -->
                        </div>
                    </div>
                    <div id="tab-security" class="tab-pane">
                        <div class="profile-section" id="profileSecurity">
                            <!-- Carregado via JS -->
                        </div>
                    </div>
                </div>
            </div>

            <div id="sales" class="page">
                <div class="page-header">
                    <h2>Gestão de Encomendas</h2>
                </div>

                <div class="stats-grid stats-grid-compact" id="encomendasSummary">
                    <div class="stat-card stat-card-compact" id="totalPendentesCard"></div>
                    <div class="stat-card stat-card-compact" id="totalProcessandoCard"></div>
                    <div class="stat-card stat-card-compact" id="totalEnviadasCard"></div>
                    <div class="stat-card stat-card-compact" id="totalEntreguesCard"></div>
                </div>

                <div class="filters">
                    <select id="filterEncomendaStatus">
                        <option value="">Todos os Status</option>
                        <option value="pendente">Pendente</option>
                        <option value="processando">Processando</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregue">Entregue</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                    <input type="date" id="filterDateFrom" placeholder="Data Inicial">
                    <input type="date" id="filterDateTo" placeholder="Data Final">
                </div>

                <div class="table-container">
                    <table id="encomendasTable" class="display">
                        <thead>
                            <tr>
                                <th>Nº Encomenda</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Produtos</th>
                                <th>Transportadora</th>
                                <th>Total (€)</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #718096;">
                                    <i class="fas fa-shopping-bag"
                                        style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                                    <p>Nenhum pedido encontrado</p>
                                    <small>Os pedidos dos seus produtos aparecerão aqui</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="products" class="page">
                <div class="page-actions">
                    <div class="actions-left">
                        <button id="addProductBtn" class="btn btn-primary"><i class="fas fa-plus"></i> Adicionar
                            Produto</button>
                    </div>
                    <div class="actions-right">
                        <button id="exportProductsBtn" class="btn btn-secondary"><i class="fas fa-file-pdf"></i>
                            Exportar PDF</button>
                    </div>
                </div>

                <div class="stats-grid stats-grid-compact" id="productStats">
                    <div class="stat-card stat-card-compact" id="totalProdutosCard"></div>
                    <div class="stat-card stat-card-compact" id="produtosAtivosCard"></div>
                    <div class="stat-card stat-card-compact" id="produtosInativosCard"></div>
                    <div class="stat-card stat-card-compact" id="stockCriticoCard"></div>
                </div>

                <div id="bulkActions" class="bulk-actions" style="display: none;">
                    <span id="selectedCount">0 selecionados</span>
                    <button onclick="ativarEmMassa()" class="btn-bulk"><i class="fas fa-check"></i> Ativar</button>
                    <button onclick="desativarEmMassa()" class="btn-bulk"><i class="fas fa-times"></i>
                        Desativar</button>
                    <button onclick="alterarEstadoEmMassa()" class="btn-bulk"><i class="fas fa-tag"></i> Alterar
                        Estado</button>
                    <button onclick="editarSelecionado()" class="btn-bulk"><i class="fas fa-edit"></i> Editar</button>
                    <button onclick="removerEmMassa()" class="btn-bulk"><i class="fas fa-trash"></i> Remover</button>
                </div>

                <div class="filters">
                    <select id="filterTipo">
                        <option value="">Todos os Tipos</option>
                    </select>
                    <select id="filterEstado">
                        <option value="">Todos os Estados</option>
                        <option value="Novo">Novo</option>
                        <option value="Como Novo">Como Novo</option>
                        <option value="Excelente">Excelente</option>
                    </select>
                    <select id="filterGenero">
                        <option value="">Todos os Géneros</option>
                        <option value="Mulher">Mulher</option>
                        <option value="Homem">Homem</option>
                        <option value="Criança">Criança</option>
                    </select>
                    <select id="filterAtivo">
                        <option value="">Todos (Ativo/Inativo)</option>
                        <option value="1">Apenas Ativos</option>
                        <option value="0">Apenas Inativos</option>
                    </select>
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

            <!-- Modal Alterar Senha -->
            <div id="passwordModal" class="modal">
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header">
                        <h3>Alterar Senha</h3>
                        <span class="close close-btn" onclick="closePasswordModal()">&times;</span>
                    </div>
                    <form id="passwordForm" class="profile-form" style="margin-top: 20px;">
                        <div class="form-group">
                            <label>Senha Atual</label>
                            <input type="password" id="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label>Nova Senha</label>
                            <input type="password" id="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmar Nova Senha</label>
                            <input type="password" id="confirmPassword" required>
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
    // Carregar estatísticas de relatórios
    function loadReportStats() {
        const periodo = $('#reportPeriod').val();
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 19,
            periodo: periodo
        }, function(res) {
            $('#totalRevenue').text('€' + parseFloat(res).toFixed(2));
        });

        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 20,
            periodo: periodo
        }, function(res) {
            $('#totalOrders').text(res);
        });

        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 21,
            periodo: periodo
        }, function(res) {
            $('#avgTicket').text('€' + parseFloat(res).toFixed(2));
        });

        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 22,
            periodo: periodo
        }, function(res) {
            $('#profitMargin').text(parseFloat(res).toFixed(2) + '%');
        });
    } // Vendas por Categoria (placeholder)
    function loadCategorySalesChart() {
        const ctx = document.getElementById('categorySalesChart');
        if (window.categoryChart && typeof window.categoryChart.destroy === 'function') window.categoryChart.destroy();
        const periodo = $('#reportPeriod').val();
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 23,
            periodo: periodo
        }, function(data) {
            window.categoryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.categoria),
                    datasets: [{
                        label: 'Vendas (unidades)',
                        data: data.map(d => d.vendas),
                        backgroundColor: '#A6D90C',
                        borderColor: '#2d3748',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }, 'json');
    }

    // Receita Diária (placeholder)
    function loadDailyRevenueChart() {
        const ctx = document.getElementById('dailyRevenueChart');
        if (window.dailyRevenueChart && typeof window.dailyRevenueChart.destroy === 'function') window.dailyRevenueChart
            .destroy();
        const periodo = $('#reportPeriod').val();

        // Atualizar título e subtítulo baseado no período
        if (periodo === 'month') {
            $('#revenueChartTitle').text('Receita Diária');
        } else if (periodo === 'year') {
            $('#revenueChartTitle').text('Receita Mensal');
            $('#revenueChartSubtitle').text('Evolução da receita nos últimos 12 meses');
        } else {
            $('#revenueChartTitle').text('Receita Mensal');
            $('#revenueChartSubtitle').text('Evolução da receita em todo o período');
        }

        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 24,
            periodo: periodo
        }, function(data) {
            // Para dados mensais (year/all), usar gráfico de barras; para diários (month), usar linha
            const chartType = periodo === 'month' ? 'line' : 'bar';
            const chartLabel = periodo === 'month' ? 'Receita Diária (€)' : 'Receita Mensal (€)';

            // Configuração baseada no tipo
            const chartConfig = {
                type: chartType,
                data: {
                    labels: data.map(d => d.data),
                    datasets: [{
                        label: chartLabel,
                        data: data.map(d => d.receita),
                        borderColor: '#A6D90C',
                        backgroundColor: chartType === 'bar' ? '#A6D90C' :
                            'rgba(166, 217, 12, 0.15)',
                        borderWidth: 2,
                        hoverBackgroundColor: '#90c207'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return chartLabel + ': €' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '€' + value;
                                }
                            }
                        }
                    }
                }
            };

            // Adicionar configurações específicas para linha
            if (chartType === 'line') {
                chartConfig.data.datasets[0].tension = 0.3;
                chartConfig.data.datasets[0].fill = true;
                chartConfig.data.datasets[0].pointRadius = data.length <= 3 ? 6 : 3;
                chartConfig.data.datasets[0].pointHoverRadius = data.length <= 3 ? 8 : 5;
                chartConfig.data.datasets[0].pointBackgroundColor = '#2d3748';
                chartConfig.data.datasets[0].pointBorderColor = '#A6D90C';
                chartConfig.data.datasets[0].pointBorderWidth = 2;
                chartConfig.data.datasets[0].pointHoverBackgroundColor = '#A6D90C';
                chartConfig.data.datasets[0].pointHoverBorderColor = '#2d3748';
            }

            window.dailyRevenueChart = new Chart(ctx, chartConfig);
        }, 'json');
    }

    // Tabela de Relatórios (placeholder)
    function loadReportsTable() {
        const periodo = $('#reportPeriod').val();
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 25,
            periodo: periodo
        }, function(data) {
            $('#reportsTable').DataTable({
                data: data,
                columns: [{
                        data: 'produto'
                    },
                    {
                        data: 'vendas'
                    },
                    {
                        data: 'receita',
                        render: v => '€' + parseFloat(v).toFixed(2)
                    },
                    {
                        data: 'lucro',
                        render: v => '€' + parseFloat(v).toFixed(2)
                    }
                ],
                destroy: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json'
                },
                drawCallback: function() {
                    $('#reportsTable tbody tr').removeClass('even odd').css('background',
                    '#ffffff');
                }
            });
        }, 'json');
    }

    function showPage(pageId, target) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.getElementById(pageId).classList.add('active');

        if (target) {
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            target.closest('.nav-link').classList.add('active');
        }

        // Atualizar breadcrumb e ícone da navbar
        const paginas = {
            'dashboard': {
                titulo: 'Dashboard',
                icone: 'fa-chart-line'
            },
            'products': {
                titulo: 'Gestão de Produtos',
                icone: 'fa-tshirt'
            },
            'sales': {
                titulo: 'Pedidos',
                icone: 'fa-shopping-bag'
            },
            'analytics': {
                titulo: 'Relatórios',
                icone: 'fa-chart-bar'
            },
            'profile': {
                titulo: 'Meu Perfil',
                icone: 'fa-user'
            }
        };
        const pagina = paginas[pageId] || paginas['dashboard'];
        document.getElementById('pageBreadcrumb').innerHTML =
            `<i class="navbar-icon fas ${pagina.icone}" id="pageIcon"></i> ${pagina.titulo}`;
        document.getElementById('pageIcon').className = 'navbar-icon fas ' + pagina.icone;

        // Carregar dados específicos da página
        if (pageId === 'analytics') {
            loadReportStats();
            loadCategorySalesChart();
            loadDailyRevenueChart();
            loadReportsTable();
        }

        if (pageId === 'products') {
            carregarProdutos();
        }

        if (pageId === 'profile') {
            carregarPerfil();
        }
    }

    // Funções globais para onclick
    function visualizarProduto(id) {
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 15,
            id: id
        }, function(dados) {
            if (dados && dados.Produto_id) {
                const foto = dados.foto ? dados.foto : 'src/img/no-image.png';
                const ativo = dados.ativo ? 'Sim' : 'Não';

                // Preparar galeria de fotos (por enquanto só a foto principal, preparado para múltiplas)
                const fotosGaleria = foto.split(',').map(f => f.trim());
                const galeriaHTML = fotosGaleria.length > 0 ? `
                    <div class="modal-gallery">
                        <div class="gallery-main">
                            <img id="mainImage" src="${fotosGaleria[0]}" alt="${dados.nome}" />
                        </div>
                        ${fotosGaleria.length > 1 ? `
                        <div class="gallery-thumbs">
                            ${fotosGaleria.map((f, i) => `<img src="${f}" onclick="document.getElementById('mainImage').src='${f}'" class="${i === 0 ? 'active' : ''}" />`).join('')}
                        </div>
                        ` : ''}
                    </div>
                ` : '';

                Swal.fire({
                    title: dados.nome,
                    html: `
                        <div class="modal-view-container">
                            <div class="modal-view-left">
                                ${galeriaHTML}
                            </div>
                            <div class="modal-view-right">
                                <div class="info-group">
                                    <label>Preço</label>
                                    <span class="price">€${parseFloat(dados.preco).toFixed(2)}</span>
                                </div>
                                <div class="info-group">
                                    <label>Tipo</label>
                                    <span>${dados.tipo_descricao || 'N/A'}</span>
                                </div>
                                <div class="info-group">
                                    <label>Stock</label>
                                    <span>${dados.stock} unidades</span>
                                </div>
                                <div class="info-group">
                                    <label>Estado</label>
                                    <span>${dados.estado}</span>
                                </div>
                                <div class="info-group">
                                    <label>Género</label>
                                    <span>${dados.genero || 'N/A'}</span>
                                </div>
                                <div class="info-group">
                                    <label>Marca</label>
                                    <span>${dados.marca || 'N/A'}</span>
                                </div>
                                <div class="info-group">
                                    <label>Tamanho</label>
                                    <span>${dados.tamanho || 'N/A'}</span>
                                </div>
                                <div class="info-group">
                                    <label>Ativo</label>
                                    <span>${ativo}</span>
                                </div>
                                <div class="info-group full-width">
                                    <label>Descrição</label>
                                    <p>${dados.descricao || 'Sem descrição'}</p>
                                </div>
                            </div>
                        </div>
                    `,
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: 900,
                    customClass: {
                        popup: 'product-modal-view',
                        htmlContainer: 'modal-view-wrapper'
                    }
                });
            }
        }, 'json');
    }

    function editarProduto(id) {
        console.log('Editando produto ID:', id);
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 15,
            id: id
        }, function(dados) {
            console.log('Dados recebidos:', dados);
            if (dados && dados.Produto_id) {
                abrirModalProduto('Editar Produto', dados);
            } else {
                Swal.fire('Erro', 'Erro ao carregar dados do produto', 'error');
            }
        }, 'json').fail(function() {
            Swal.fire('Erro', 'Erro na requisição', 'error');
        });
    }

    function removerProduto(id) {
        Swal.fire({
            title: 'Remover produto?',
            text: 'Esta ação não pode ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                $.post('src/controller/controllerDashboardAnunciante.php', {
                    op: 16,
                    id: id
                }, function() {
                    Swal.fire('Removido!', 'Produto removido com sucesso.', 'success');
                    carregarProdutos();
                });
            }
        });
    }

    // Funções de seleção múltipla
    function obterProdutosSelecionados() {
        return $('.product-checkbox:checked').map(function() {
            return $(this).data('id');
        }).get();
    }

    function atualizarAcoesEmMassa() {
        const selecionados = obterProdutosSelecionados();
        $('#selectedCount').text(selecionados.length + ' selecionados');
        if (selecionados.length > 0) {
            $('#bulkActions').slideDown();
        } else {
            $('#bulkActions').slideUp();
        }
    }

    function editarSelecionado() {
        const ids = obterProdutosSelecionados();
        if (ids.length === 0) {
            Swal.fire('Atenção', 'Selecione um produto para editar.', 'warning');
            return;
        }
        if (ids.length > 1) {
            Swal.fire('Atenção', 'Selecione apenas um produto para editar.', 'warning');
            return;
        }
        editarProduto(ids[0]);
    }

    function ativarEmMassa() {
        const ids = obterProdutosSelecionados();
        if (ids.length === 0) return;
        Swal.fire({
            title: `Ativar ${ids.length} produtos?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                $.post('src/controller/controllerDashboardAnunciante.php', {
                    op: 17,
                    ids: ids,
                    ativo: 1
                }, function() {
                    Swal.fire('Sucesso!', 'Produtos ativados.', 'success');
                    carregarProdutos();
                    carregarEstatisticasProdutos();
                });
            }
        });
    }

    function desativarEmMassa() {
        const ids = obterProdutosSelecionados();
        if (ids.length === 0) return;
        Swal.fire({
            title: `Desativar ${ids.length} produtos?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                $.post('src/controller/controllerDashboardAnunciante.php', {
                    op: 17,
                    ids: ids,
                    ativo: 0
                }, function() {
                    Swal.fire('Sucesso!', 'Produtos desativados.', 'success');
                    carregarProdutos();
                    carregarEstatisticasProdutos();
                });
            }
        });
    }

    function removerEmMassa() {
        const ids = obterProdutosSelecionados();
        if (ids.length === 0) return;
        Swal.fire({
            title: `Remover ${ids.length} produtos?`,
            text: 'Esta ação não pode ser desfeita!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                $.post('src/controller/controllerDashboardAnunciante.php', {
                    op: 18,
                    ids: ids
                }, function() {
                    Swal.fire('Removido!', 'Produtos removidos com sucesso.', 'success');
                    carregarProdutos();
                    carregarEstatisticasProdutos();
                });
            }
        });
    }

    function alterarEstadoEmMassa() {
        const ids = obterProdutosSelecionados();
        if (ids.length === 0) return;
        Swal.fire({
            title: `Alterar estado de ${ids.length} produtos?`,
            input: 'select',
            inputOptions: {
                'Novo': 'Novo',
                'Como Novo': 'Como Novo',
                'Excelente': 'Excelente'
            },
            inputPlaceholder: 'Selecione o estado',
            showCancelButton: true,
            confirmButtonText: 'Alterar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#A6D90C',
            inputValidator: (value) => {
                if (!value) {
                    return 'Precisa selecionar um estado!';
                }
            }
        }).then((resultado) => {
            if (resultado.isConfirmed) {
                $.post('src/controller/controllerDashboardAnunciante.php', {
                    op: 19,
                    ids: ids,
                    estado: resultado.value
                }, function() {
                    Swal.fire('Sucesso!', `Estado alterado para "${resultado.value}".`, 'success');
                    carregarProdutos();
                });
            }
        });
    }

    // Carregar Produtos
    function carregarProdutos() {
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: {
                op: 8
            },
            dataType: 'json'
        }).done(function(dados) {
            // Destruir DataTable existente se houver
            if ($.fn.DataTable.isDataTable('#productsTable')) {
                $('#productsTable').DataTable().destroy();
            }

            window.tabelaProdutos = $('#productsTable').DataTable({
                data: dados,
                columns: [{
                        data: null,
                        orderable: false,
                        render: (dados) =>
                            `<input type="checkbox" class="product-checkbox" data-id="${dados.Produto_id}">`
                    },
                    {
                        data: 'foto',
                        orderable: false,
                        render: (foto) => {
                            const imgSrc = foto ? foto : 'src/img/no-image.png';
                            return `<img src="${imgSrc}" alt="Produto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">`;
                        }
                    },
                    {
                        data: 'nome'
                    },
                    {
                        data: 'tipo_descricao'
                    },
                    {
                        data: 'preco',
                        render: v => '€' + parseFloat(v).toFixed(2)
                    },
                    {
                        data: 'stock',
                        render: (v) => {
                            const stock = parseInt(v);
                            if (stock < 5) {
                                return `<span class="stock-low"><i class="fas fa-exclamation-triangle"></i> ${stock}</span>`;
                            }
                            return stock;
                        }
                    },
                    {
                        data: 'estado'
                    },
                    {
                        data: 'ativo',
                        render: v => v ? '<span class="status-active">Sim</span>' :
                            '<span class="status-inactive"><i class="fas fa-exclamation-circle"></i> Não</span>'
                    }
                ],
                destroy: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json'
                },
                drawCallback: function() {
                    $('#productsTable tbody tr').removeClass('even odd').css('background',
                        '#ffffff');
                }
            });

            // Evento de clique na linha da tabela para visualizar produto
            $('#productsTable tbody').off('click', 'tr').on('click', 'tr', function(e) {
                // Ignora clique em checkbox e botões
                if ($(e.target).closest('.product-checkbox, button').length) return;

                const dados = window.tabelaProdutos.row(this).data();
                if (dados && dados.Produto_id) {
                    visualizarProduto(dados.Produto_id);
                }
            });
        });
    }

    $(document).ready(function() {

        // Carregar estatísticas principais do dashboard
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 3
        }, function(res) {
            $('#PontosConfianca').html(res);
        });
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 1
        }, function(res) {
            $('#PlanosAtual').html(res);
        });
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 2
        }, function(res) {
            $('#ProdutoStock').html(res);
        });
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 4
        }, function(res) {
            $('#GastosCard').html(res);
        });

        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: {
                op: 5
            },
            dataType: 'json'
        }).done(function(res) {
            const ctx = document.getElementById('salesChart');

            // Destruir gráfico anterior se existir
            if (window.salesChartInstance) {
                window.salesChartInstance.destroy();
            }

            // Limpar o canvas completamente
            ctx.getContext('2d').clearRect(0, 0, ctx.width, ctx.height);

            const colors = ['#A6D90C', '#2d3748', '#A6D90C', '#2d3748', '#A6D90C', '#2d3748', '#A6D90C',
                '#2d3748', '#A6D90C', '#2d3748', '#A6D90C', '#2d3748'
            ];
            const hoverColors = ['#90c207', '#1a202c', '#90c207', '#1a202c', '#90c207', '#1a202c',
                '#90c207', '#1a202c', '#90c207', '#1a202c', '#90c207', '#1a202c'
            ];

            window.salesChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set',
                        'Out', 'Nov', 'Dez'
                    ],
                    datasets: [{
                        label: 'Vendas (€)',
                        data: res,
                        backgroundColor: colors,
                        borderColor: '#ffffff',
                        borderWidth: 1,
                        borderRadius: 8,
                        hoverBackgroundColor: hoverColors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#2d3748',
                                font: {
                                    size: 13,
                                    weight: 600
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#2d3748',
                            titleColor: '#A6D90C',
                            bodyColor: '#ffffff',
                            borderColor: '#A6D90C',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return 'Vendas: €' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '€' + value;
                                },
                                color: '#2d3748'
                            },
                            grid: {
                                color: 'rgba(45, 55, 72, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#2d3748'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        // Top Produtos
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: {
                op: 6
            },
            dataType: 'json'
        }).done(function(res) {
            const ctx = document.getElementById('topProductsChart');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: res.map(p => p.nome),
                    datasets: [{
                        data: res.map(p => p.vendidos),
                        backgroundColor: ['#A6D90C', '#2d3748', '#A6D90C', '#2d3748',
                            '#A6D90C'
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#2d3748',
                                font: {
                                    size: 12,
                                    weight: 500
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#2d3748',
                            titleColor: '#A6D90C',
                            bodyColor: '#ffffff',
                            borderColor: '#A6D90C',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a +
                                        b, 0);
                                    const percentage = ((context.parsed / total) * 100)
                                        .toFixed(1);
                                    return context.label + ': ' + context.parsed + ' un (' +
                                        percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });

        // Produtos Recentes
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: {
                op: 7
            },
            dataType: 'html'
        }).done(function(res) {
            $('#recentProducts').html(res);
        });

        // Lucro por Produto
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: {
                op: 10
            },
            dataType: 'json'
        }).done(function(res) {
            const ctx = document.getElementById('profitChart');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: res.map(p => p.nome),
                    datasets: [{
                        data: res.map(p => p.lucro),
                        backgroundColor: ['#A6D90C', '#2d3748', '#A6D90C', '#2d3748',
                            '#A6D90C'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverBorderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff',
                                padding: 10,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': €' + context.parsed.toFixed(
                                    2);
                                }
                            }
                        }
                    }
                }
            });
        });

        // Margem de Lucro
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: {
                op: 11
            },
            dataType: 'json'
        }).done(function(res) {
            const ctx = document.getElementById('marginChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: res.map(p => p.nome),
                    datasets: [{
                        label: 'Margem (%)',
                        data: res.map(p => p.margem),
                        backgroundColor: '#A6D90C',
                        borderColor: '#2d3748',
                        borderWidth: 1,
                        hoverBackgroundColor: '#90c207'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: v => v + '%'
                            }
                        }
                    }
                }
            });
        });

        // Atualizar Relatórios ao mudar período
        $('#reportPeriod').change(function() {
            loadReportStats();
            loadCategorySalesChart();
            loadDailyRevenueChart();
            loadReportsTable();
        });

        // Carregar Tipos de Produto
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 13
        }, function(data) {
            console.log('Tipos recebidos:', data);
            if (Array.isArray(data)) {
                data.forEach(t => $('#filterTipo, #tipo_produto_id').append(
                    `<option value="${t.id}">${t.descricao}</option>`));
            } else {
                console.error('Tipos não é array:', data);
            }
        }, 'json').fail(function() {
            console.error('Erro ao carregar tipos');
        });

        // Filtro por Tipo
        $('#filterTipo').on('change', function() {
            $('#productsTable').DataTable().column(3).search($(this).val()).draw();
        });

        // Filtro por Estado
        $('#filterEstado').on('change', function() {
            $('#productsTable').DataTable().column(6).search($(this).val()).draw();
        });

        // Filtro por Género (busca na coluna de dados originais)
        $('#filterGenero').on('change', function() {
            const valor = $(this).val();
            if (window.tabelaProdutos) {
                window.tabelaProdutos.rows().every(function() {
                    const dadosLinha = this.data();
                    const corresponde = !valor || (dadosLinha.genero && dadosLinha.genero ===
                        valor);
                    $(this.node()).toggle(corresponde);
                });
            }
        });

        // Filtro por Ativo
        $('#filterAtivo').on('change', function() {
            const valor = $(this).val();
            if (valor === '') {
                $('#productsTable').DataTable().column(7).search('').draw();
            } else {
                const termoPesquisa = valor === '1' ? 'Sim' : 'Não';
                $('#productsTable').DataTable().column(7).search(termoPesquisa).draw();
            }
        });

        // Seleção múltipla
        $(document).on('change', '#selectAll', function() {
            $('.product-checkbox').prop('checked', $(this).prop('checked'));
            atualizarAcoesEmMassa();
        });

        $(document).on('change', '.product-checkbox', function() {
            atualizarAcoesEmMassa();
            const total = $('.product-checkbox').length;
            const marcados = $('.product-checkbox:checked').length;
            $('#selectAll').prop('checked', total === marcados);
        });

        // Exportar produtos para PDF
        $('#exportProductsBtn').on('click', function() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            const tabela = $('#productsTable').DataTable();
            const dados = tabela.rows({
                search: 'applied'
            }).data();

            // Cabeçalho do PDF
            doc.setFontSize(18);
            doc.setTextColor(166, 217, 12);
            doc.text('WeGreen - Lista de Produtos', 14, 22);

            doc.setFontSize(10);
            doc.setTextColor(100);
            doc.text('Data: ' + new Date().toLocaleDateString('pt-PT'), 14, 30);

            // Preparar dados para a tabela
            const linhasTabela = [];
            dados.each(function(linha) {
                linhasTabela.push([
                    linha.nome,
                    linha.tipo_descricao,
                    '€' + parseFloat(linha.preco).toFixed(2),
                    linha.stock,
                    linha.estado,
                    linha.ativo ? 'Sim' : 'Não'
                ]);
            });

            // Criar tabela no PDF
            doc.autoTable({
                startY: 35,
                head: [
                    ['Nome', 'Tipo', 'Preço', 'Stock', 'Estado', 'Ativo']
                ],
                body: linhasTabela,
                theme: 'striped',
                headStyles: {
                    fillColor: [166, 217, 12],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold'
                },
                styles: {
                    fontSize: 9,
                    cellPadding: 3
                },
                columnStyles: {
                    0: {
                        cellWidth: 50
                    },
                    1: {
                        cellWidth: 35
                    },
                    2: {
                        cellWidth: 25
                    },
                    3: {
                        cellWidth: 20
                    },
                    4: {
                        cellWidth: 30
                    },
                    5: {
                        cellWidth: 20
                    }
                }
            });

            // Salvar PDF
            doc.save('produtos_' + new Date().toISOString().split('T')[0] + '.pdf');
        });

        // Carregar estatísticas de produtos (inclui limite)
        carregarEstatisticasProdutos();

        // Adicionar Produto
        $('#addProductBtn').click(function() {
            if ($(this).prop('disabled')) return alert('Limite de produtos atingido!');
            abrirModalProduto('Adicionar Produto');
        });

        // Função para abrir modal de produto com SweetAlert2
        function abrirModalProduto(titulo, dados = {}) {
            Swal.fire({
                title: titulo,
                html: `
                    <form id="productFormSwal" style="text-align: left;">
                        <input type="hidden" id="productId" value="${dados.Produto_id || ''}">
                        <div class="form-row">
                            <div class="form-col">
                                <label>Nome</label>
                                <input type="text" id="nome" value="${dados.nome || ''}" required>
                            </div>
                            <div class="form-col">
                                <label>Tipo</label>
                                <select id="tipo_produto_id" required></select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label>Preço</label>
                                <input type="number" step="0.01" id="preco" value="${dados.preco || ''}" required>
                            </div>
                            <div class="form-col">
                                <label>Stock</label>
                                <input type="number" id="stock" value="${dados.stock || ''}" min="0">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label>Marca</label>
                                <input type="text" id="marca" value="${dados.marca || ''}">
                            </div>
                            <div class="form-col">
                                <label>Tamanho</label>
                                <input type="text" id="tamanho" value="${dados.tamanho || ''}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label>Estado</label>
                                <select id="estado">
                                    <option ${dados.estado === 'Excelente' ? 'selected' : ''}>Excelente</option>
                                    <option ${dados.estado === 'Como Novo' ? 'selected' : ''}>Como Novo</option>
                                    <option ${dados.estado === 'Novo' ? 'selected' : ''}>Novo</option>
                                </select>
                            </div>
                            <div class="form-col">
                                <label>Género</label>
                                <select id="genero">
                                    <option ${dados.genero === 'Mulher' ? 'selected' : ''}>Mulher</option>
                                    <option ${dados.genero === 'Homem' ? 'selected' : ''}>Homem</option>
                                    <option ${dados.genero === 'Criança' ? 'selected' : ''}>Criança</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row-full">
                            <label>Descrição</label>
                            <textarea id="descricao" rows="3">${dados.descricao || ''}</textarea>
                        </div>
                        <div class="form-row-full">
                            <label>Fotos (até 5 imagens)</label>
                            <input type="file" id="foto" accept="image/*" multiple>
                            <small style="color: #666; margin-top: 5px; display: block;">Selecione até 5 fotos do produto</small>
                        </div>
                        <div id="photoPreview" class="photo-preview"></div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                width: 700,
                customClass: {
                    popup: 'product-modal-view',
                    htmlContainer: 'modal-view-wrapper',
                    confirmButton: 'btn-primary',
                    cancelButton: 'btn-secondary'
                },
                didOpen: () => {
                    // Carregar tipos de produto no select
                    carregarTiposProduto();
                    if (dados.tipo_produto_id) {
                        setTimeout(() => $('#tipo_produto_id').val(dados.tipo_produto_id), 100);
                    }
                },
                preConfirm: () => {
                    const formData = new FormData();
                    formData.append('op', $('#productId').val() ? 17 : 18);
                    formData.append('id', $('#productId').val());
                    formData.append('nome', $('#nome').val());
                    formData.append('tipo_produto_id', $('#tipo_produto_id').val());
                    formData.append('preco', $('#preco').val());
                    formData.append('stock', $('#stock').val());
                    formData.append('marca', $('#marca').val());
                    formData.append('tamanho', $('#tamanho').val());
                    formData.append('estado', $('#estado').val());
                    formData.append('genero', $('#genero').val());
                    formData.append('descricao', $('#descricao').val());

                    const files = $('#foto')[0].files;
                    for (let i = 0; i < files.length; i++) {
                        formData.append('foto[]', files[i]);
                    }

                    return $.ajax({
                        url: 'src/controller/controllerDashboardAnunciante.php',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false
                    }).then(() => {
                        carregarProdutos();
                        carregarEstatisticasProdutos();
                        return true;
                    }).catch(() => {
                        Swal.showValidationMessage('Erro ao salvar produto');
                        return false;
                    });
                }
            });
        }

        // Salvar Produto - REMOVIDO (agora é tratado no preConfirm do SweetAlert2)

        // Verificar plano do utilizador para mostrar botão upgrade
        verificarPlanoUpgrade();

        // Inicializar página ativa
        const paginaAtiva = window.location.hash.replace('#', '') || 'dashboard';
        const botaoAtivo = document.querySelector(`.nav-link[onclick*="${paginaAtiva}"]`);
        if (botaoAtivo) {
            showPage(paginaAtiva, botaoAtivo);
        } else {
            // Carregar produtos por padrão se estiver na página de produtos
            carregarProdutos();
        }

    });

    // ========================
    // FUNÇÕES DE PERFIL
    // ========================

    function verificarPlanoUpgrade() {
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 27
        }, function(resp) {
            const dados = JSON.parse(resp);
            if (dados && dados.plano_nome !== 'Enterprise') {
                $('#upgradeBtn').show();
            } else {
                $('#upgradeBtn').hide();
            }
        });
    }

    function carregarPerfil() {
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 27
        }, function(resp) {
            const dados = JSON.parse(resp);
            if (dados.error) return Swal.fire('Erro', dados.error, 'error');

            const foto = dados.foto || 'src/img/default_user.png';

            // Mostrar botão upgrade na navbar apenas se não for Enterprise
            if (dados.plano_nome !== 'Enterprise') {
                $('#upgradeBtn').show();
            } else {
                $('#upgradeBtn').hide();
            }

            // Header com avatar e estatísticas (sem botão upgrade)
            $('#profileCard').html(`
                <div class='profile-header-card'>
                    <div class='profile-avatar-large'>
                        <img src='${foto}' alt='Foto de Perfil' id='userPhoto'>
                        <button class='avatar-edit-btn' type='button'>
                            <i class='fas fa-camera'></i>
                            <input type='file' id='avatarUpload' class='avatar-file-input'
                                accept='image/jpeg,image/jpg,image/png,image/gif,image/webp'
                                onchange='adicionarFotoPerfil()' />
                        </button>
                    </div>
                    <div class='profile-header-info'>
                        <div class='profile-header-left'>
                            <h1>${dados.nome}</h1>
                            <span class='role-badge'>📦 ${dados.plano_nome || 'Anunciante'}</span>
                        </div>
                        <div class='profile-stats'>
                            <div class='profile-stat'>
                                <div class='profile-stat-value'>${dados.total_produtos || 0}</div>
                                <div class='profile-stat-label'>Produtos Ativos</div>
                            </div>
                            <div class='profile-stat'>
                                <div class='profile-stat-value'>${dados.ranking_nome || 'N/A'}</div>
                                <div class='profile-stat-label'>Classificação</div>
                            </div>
                            <div class='profile-stat'>
                                <div class='profile-stat-value'>${dados.pontos_conf || 0}</div>
                                <div class='profile-stat-label'>Pontos de Confiança</div>
                            </div>
                        </div>
                    </div>
                </div>
            `); // Tab 1: Informações Pessoais
            $('#profileInfo').html(`
                <div class='section-header'>
                    <h3><i class='fas fa-user'></i> Informações Pessoais</h3>
                </div>
                <div class='info-item'>
                    <label>Nome Completo</label>
                    <input type='text' id='nomeAnunciante' value='${dados.nome}'>
                </div>
                <div class='info-item'>
                    <label>Email</label>
                    <input type='email' id='emailAnunciante' value='${dados.email}'>
                </div>
                <div class='info-item'>
                    <label>NIF</label>
                    <input type='text' id='nifAnunciante' value='${dados.nif || ''}' placeholder='000000000' maxlength='9'>
                </div>
                <div class='info-item'>
                    <label>Telefone</label>
                    <input type='text' id='telefoneAnunciante' value='${dados.telefone || ''}' placeholder='900000000' maxlength='9'>
                </div>
                <div class='info-item'>
                    <label>Morada</label>
                    <input type='text' id='moradaAnunciante' value='${dados.morada || ''}' placeholder='Rua, Número, Código Postal, Cidade'>
                </div>
                <button class='btn btn-primary' onclick='guardarDadosPerfil()' style='margin-top: 20px; width: 100%;'>
                    <i class='fas fa-save'></i> Guardar Alterações
                </button>
            `);

            // Tab 2: Plano & Ranking
            const planoLimite = dados.plano_limite ? dados.plano_limite : 'Ilimitado';
            const progressoPct = dados.ranking_pontos_necessarios ?
                Math.min((dados.pontos_conf / dados.ranking_pontos_necessarios) * 100, 100) : 0;

            $('#profilePlan').html(`
                <div class='section-header'>
                    <h3><i class='fas fa-crown'></i> Plano & Ranking</h3>
                </div>
                <div class='plan-info-card'>
                    <div class='plan-current'>
                        <span class='plan-label'>Plano Atual</span>
                        <span class='plan-name'>${dados.plano_nome || 'Free'}</span>
                        <span class='plan-price'>€${parseFloat(dados.plano_preco || 0).toFixed(2)}/mês</span>
                    </div>
                    <div class='plan-limits'>
                        <div class='limit-item'>
                            <i class='fas fa-box'></i>
                            <span>Produtos: ${dados.total_produtos}/${planoLimite}</span>
                        </div>
                    </div>
                </div>
                <div class='ranking-progress'>
                    <div class='ranking-header'>
                        <span class='ranking-label'>Progresso do Ranking</span>
                        <span class='ranking-points'>${dados.pontos_conf} / ${dados.ranking_pontos_necessarios || '∞'} pontos</span>
                    </div>
                    <div class='progress-bar'>
                        <div class='progress-fill' style='width: ${progressoPct}%'></div>
                    </div>
                    <div class='ranking-badges'>
                        <span class='badge-current'>${dados.ranking_nome || 'Iniciante'}</span>
                    </div>
                </div>
            `);

            // Tab 3: Segurança
            $('#profileSecurity').html(`
                <div class='section-header'>
                    <h3><i class='fas fa-shield-alt'></i> Segurança</h3>
                </div>
                <div class='security-content'>
                    <div class='security-item'>
                        <div class='security-icon'>
                            <i class='fas fa-key'></i>
                        </div>
                        <div class='security-info'>
                            <h4>Alterar Password</h4>
                            <p>Mantenha sua conta segura atualizando sua senha regularmente</p>
                        </div>
                    </div>
                    <button class='btn btn-secondary' onclick='showPasswordModal()' style='width: 100%; margin-top: 15px;'>
                        <i class='fas fa-lock'></i> Alterar Password
                    </button>
                </div>
            `);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Erro ao carregar perfil:', textStatus, errorThrown);
            Swal.fire('Erro', 'Não foi possível carregar o perfil.', 'error');
        });
    }

    function switchProfileTab(tabName, element) {
        // Remover active de todas as tabs e paineis
        document.querySelectorAll('.profile-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

        // Adicionar active na tab clicada e painel correspondente
        element.classList.add('active');
        document.getElementById('tab-' + tabName).classList.add('active');
    }

    function guardarDadosPerfil() {
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 28,
            nome: $('#nomeAnunciante').val(),
            email: $('#emailAnunciante').val(),
            nif: $('#nifAnunciante').val(),
            telefone: $('#telefoneAnunciante').val(),
            morada: $('#moradaAnunciante').val()
        }, function(resp) {
            const dados = JSON.parse(resp);
            if (dados.success) {
                Swal.fire('Sucesso', dados.message, 'success');
                carregarPerfil();
            } else {
                Swal.fire('Erro', dados.message, 'error');
            }
        });
    }

    function mostrarPlanosUpgrade() {
        Swal.fire({
            title: 'Planos Disponíveis',
            html: `
                <div style="text-align: left; padding: 20px;">
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #A6D90C;">
                        <h3 style="margin: 0 0 10px 0; color: #A6D90C;">🌱 Free</h3>
                        <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">€0/mês</p>
                        <ul style="margin: 10px 0; padding-left: 20px;">
                            <li>Até 3 produtos</li>
                            <li>Rastreio básico</li>
                        </ul>
                    </div>
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #ffa500;">
                        <h3 style="margin: 0 0 10px 0; color: #ffa500;">⭐ Premium</h3>
                        <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">€25/mês</p>
                        <ul style="margin: 10px 0; padding-left: 20px;">
                            <li>Até 10 produtos</li>
                            <li>Rastreio básico</li>
                            <li>Relatórios em PDF</li>
                        </ul>
                    </div>
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #6a4c93;">
                        <h3 style="margin: 0 0 10px 0; color: #6a4c93;">💎 Enterprise</h3>
                        <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">€100/mês</p>
                        <ul style="margin: 10px 0; padding-left: 20px;">
                            <li>Produtos ilimitados</li>
                            <li>Rastreio avançado</li>
                            <li>Relatórios em PDF</li>
                            <li>Suporte prioritário</li>
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'Fechar',
            confirmButtonColor: '#A6D90C',
            width: 600
        });
    }

    function adicionarFotoPerfil() {
        const fileInput = document.getElementById('avatarUpload');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('op', 29);
        formData.append('foto', file);

        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false
        }).done(function(resp) {
            const dados = JSON.parse(resp);
            if (dados.success) {
                Swal.fire('Sucesso', dados.message, 'success');
                $('#userPhoto').attr('src', dados.foto);
                carregarPerfil();
            } else {
                Swal.fire('Erro', dados.message, 'error');
            }
        });
    }

    // ========================
    // CONTROLE DO MODAL DE SENHA
    // ========================

    function showPasswordModal() {
        $('#passwordModal').addClass('active');
        closeUserDropdown();
    }

    function closePasswordModal() {
        $('#passwordModal').removeClass('active');
        $('#passwordForm')[0].reset();
    }

    $('#passwordForm').submit(function(e) {
        e.preventDefault();
        const senhaAtual = $('#currentPassword').val();
        const senhaNova = $('#newPassword').val();
        const senhaConfirm = $('#confirmPassword').val();

        if (senhaNova !== senhaConfirm) {
            return Swal.fire('Erro', 'As senhas não correspondem', 'error');
        }

        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 30,
            senha_atual: senhaAtual,
            senha_nova: senhaNova
        }, function(resp) {
            const dados = JSON.parse(resp);
            if (dados.success) {
                Swal.fire('Sucesso', dados.message, 'success');
                $('#passwordForm')[0].reset();
                closePasswordModal();
            } else {
                Swal.fire('Erro', dados.message, 'error');
            }
        });
    });

    // ========================
    // CONTROLE DO DROPDOWN DO USUÁRIO
    // ========================

    $('#userMenuBtn').click(function(e) {
        e.stopPropagation();
        $('#userDropdown').toggleClass('active');
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('.navbar-user, .user-dropdown').length) {
            $('#userDropdown').removeClass('active');
        }
    });

    function closeUserDropdown() {
        $('#userDropdown').removeClass('active');
    }

    // ========================
    // CONTROLE DO MODAL DE SENHA
    // ========================

    function showPasswordModal() {
        $('#passwordModal').addClass('active');
        closeUserDropdown();
    }

    function closePasswordModal() {
        $('#passwordModal').removeClass('active');
        $('#passwordForm')[0].reset();
    }

    // ========================
    // ESTATÍSTICAS DE PRODUTOS
    // ========================

    function carregarEstatisticasProdutos() {
        // Carregar estatísticas
        $.post('src/controller/controllerDashboardAnunciante.php', {
            op: 31
        }, function(stats) {
            console.log('Stats recebidas:', stats);

            // Card Produtos Ativos
            $('#produtosAtivosCard').html(`
                <div class='stat-icon'><i class='fas fa-check-circle' style='color: #A6D90C;'></i></div>
                <div class='stat-content'>
                    <div class='stat-label'>Produtos Ativos</div>
                    <div class='stat-value' style='color: #A6D90C;'>${stats.ativos}</div>
                </div>
            `);

            // Card Produtos Inativos
            $('#produtosInativosCard').html(`
                <div class='stat-icon'><i class='fas fa-exclamation-circle' style='color: #A6D90C;'></i></div>
                <div class='stat-content'>
                    <div class='stat-label'>Produtos Inativos</div>
                    <div class='stat-value' style='color: #fbbf24;'>${stats.inativos}</div>
                </div>
            `);

            // Card Stock Crítico
            $('#stockCriticoCard').html(`
                <div class='stat-icon'><i class='fas fa-exclamation-triangle' style='color: #A6D90C;'></i></div>
                <div class='stat-content'>
                    <div class='stat-label'>Stock Crítico (&lt;5)</div>
                    <div class='stat-value' style='color: #ef4444;'>${stats.stockBaixo}</div>
                </div>
            `);

            // Card Total de Produtos (será completado com limite)
            $('#totalProdutosCard').html(`
                <div class='stat-icon'><i class='fas fa-box' style='color: #A6D90C;'></i></div>
                <div class='stat-content'>
                    <div class='stat-label'>Total de Produtos</div>
                    <div class='stat-value'>${stats.total}</div>
                    <div class='stat-progress' id='totalProgress'></div>
                </div>
            `);

            console.log('Elemento #totalProgress criado:', $('#totalProgress').length > 0);

            // Carregar limite de produtos (depois de criar o elemento #totalProgress)
            $.post('src/controller/controllerDashboardAnunciante.php', {
                op: 14
            }, function(limite) {
                console.log('Limite recebido:', limite);

                const percentagem = (limite.current / limite.max) * 100;
                let corBarra = '#A6D90C'; // Verde
                if (percentagem >= 90) corBarra = '#ef4444'; // Vermelho
                else if (percentagem >= 70) corBarra = '#fbbf24'; // Amarelo

                console.log('Percentagem:', percentagem, 'Cor:', corBarra);

                // Adicionar barra de progresso ao card Total
                const progressHTML = `
                    <div class='stat-progress-bar'>
                        <div class='stat-progress-fill' style='width: ${percentagem}%; background-color: ${corBarra};'></div>
                    </div>
                `;

                console.log('HTML da barra:', progressHTML);
                $('#totalProgress').html(progressHTML);
                console.log('Barra inserida, conteúdo de #totalProgress:', $('#totalProgress').html());

                if (limite.current >= limite.max) {
                    $('#addProductBtn').prop('disabled', true).css({
                        'background-color': '#ccc',
                        'cursor': 'not-allowed',
                        'opacity': '0.6'
                    });
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('Erro ao carregar limite:', error, xhr.responseText);
            });
        }, 'json').fail(function(xhr, status, error) {
            console.error('Erro ao carregar estatísticas:', error, xhr.responseText);
        });
    }
    </script>
</body>
<?php
}else{
header("Location: forbiddenerror.html");
}
?>

</html>