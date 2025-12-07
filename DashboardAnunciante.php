<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Anunciante - Fashion Store</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/Anunciante.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <style>
        .modal-content label {
            display: block;
            margin-bottom: 10px;
            color: #fff;
        }
        .modal-content input, .modal-content select, .modal-content textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            color: #000;
        }
        .filters select, .filters input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            color: #000;
        }
        .modal-content {
            height: auto;
            overflow-y: visible;
        }
    </style>

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
                        <p>Painel do Anunciante</p>
                    </div>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <button class="nav-link active" onclick="showPage('dashboard')">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('products')">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('sales')">
                            <span class="nav-icon">🛒</span>
                            <span class="nav-text">Pedidos</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('analytics')">
                            <span class="nav-icon">📈</span>
                            <span class="nav-text">Relatórios</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('settings')">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Configurações</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div id="dashboard" class="page active">
                <div class="page-header">
                    <h2>Dashboard</h2>
                    <p>Visão geral do seu negócio</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card" id="PontosConfianca"></div>
                    <div class="stat-card" id="GastosCard"></div>
                    <div class="stat-card" id="ProdutoStock"></div>
                    <div class="stat-card" id="PlanosAtual"></div>
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
                <div class="page-header">
                    <h2>Relatórios</h2>
                    <p>Insights detalhados do seu negócio</p>
                    <div class="filters" style="margin-top: 10px;">
                        <select id="reportPeriod" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #000; margin-right: 10px;">
                            <option value="month">Último Mês</option>
                            <option value="year">Último Ano</option>
                            <option value="all">Todo o Período</option>
                        </select>
                        <button id="updateReportsBtn" class="btn btn-primary">Atualizar</button>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card" id="totalRevenue">Receita Total: Carregando...</div>
                    <div class="stat-card" id="totalOrders">Número de Pedidos: Carregando...</div>
                    <div class="stat-card" id="avgTicket">Ticket Médio: Carregando...</div>
                    <div class="stat-card" id="profitMargin">Margem de Lucro: Carregando...</div>
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Vendas por Categoria</h3>
                            <p>Distribuição de vendas por tipo de produto</p>
                        </div>
                        <canvas id="categorySalesChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Receita Diária</h3>
                            <p>Evolução da receita nos últimos dias</p>
                        </div>
                        <canvas id="dailyRevenueChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Lucro por Produto</h3>
                            <p>Lucro gerado por cada produto</p>
                        </div>
                        <canvas id="profitChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Margem de Lucro</h3>
                            <p>Margem percentual de lucro</p>
                        </div>
                        <canvas id="marginChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Dados Detalhados</h3>
                        <p>Tabela com vendas por produto</p>
                    </div>
                    <table id="reportsTable" class="display" style="width:100%;">
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
            <div id="products" class="page">
    <div class="page-header">
        <h2>Produtos</h2>
        <p>Gerencie o seu inventário</p>
        <button id="addProductBtn" class="btn btn-primary">Adicionar Produto</button>
        <span id="productLimit" style="margin-left: 20px; color: #666;"></span>
    </div>

    <div class="filters" style="margin-bottom: 20px;">
        <select id="filterTipo" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #000; margin-right: 10px;">
            <option value="">Todos os Tipos</option>
        </select>
        <input type="text" id="searchProduct" placeholder="Pesquisar produto..." style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; background: #fff; color: #000;">
    </div>

    <table id="productsTable" class="display" style="width:100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Preço (€)</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Ativo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" style="float:right; cursor:pointer; font-size:28px;">&times;</span>
        <h3 id="modalTitle">Adicionar Produto</h3>
        <form id="productForm">
            <input type="hidden" id="productId">
            <label>Nome: <input type="text" id="nome" required></label>
            <label>Tipo: <select id="tipo_produto_id" required></select></label>
            <label>Preço: <input type="number" step="0.01" id="preco" required></label>
            <label>Stock: <input type="number" id="stock" min="0"></label>
            <label>Marca: <input type="text" id="marca"></label>
            <label>Tamanho: <input type="text" id="tamanho"></label>
            <label>Estado: <select id="estado"><option>Excelente</option><option>Como Novo</option><option>Novo</option></select></label>
            <label>Género: <select id="genero"><option>Mulher</option><option>Homem</option><option>Criança</option></select></label>
            <label>Descrição: <textarea id="descricao"></textarea></label>
            <label>Foto: <input type="file" id="foto" accept="image/*"></label>
            <button type="submit" class="btn-primary">Salvar</button>
        </form>
    </div>
</div>
        </main>
    </div>

    <script>
    function showPage(pageId) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.getElementById(pageId).classList.add('active');

        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        event.target.closest('.nav-link').classList.add('active');

        // Carregar dados específicos da página
        if (pageId === 'analytics') {
            loadReportStats();
            loadCategorySalesChart();
            loadDailyRevenueChart();
            loadReportsTable();
        }
    }

    // Funções globais para onclick
    function editProduct(id) {
        console.log('Editando produto ID:', id);
        $.post('src/controller/controllerDashboardAnunciante.php', { op: 15, id: id }, function(data) {
            console.log('Dados recebidos:', data);
            if (data && data.Produto_id) {
                $('#productId').val(data.Produto_id);
                $('#nome').val(data.nome);
                $('#tipo_produto_id').val(data.tipo_produto_id);
                $('#preco').val(data.preco);
                $('#stock').val(data.stock);
                $('#marca').val(data.marca);
                $('#tamanho').val(data.tamanho);
                $('#estado').val(data.estado);
                $('#genero').val(data.genero);
                $('#descricao').val(data.descricao);
                $('#modalTitle').text('Editar Produto');
                $('#productModal').addClass('active');
            } else {
                alert('Erro ao carregar dados do produto');
            }
        }, 'json').fail(function() {
            alert('Erro na requisição');
        });
    }

    function deleteProduct(id) {
        if (confirm('Remover produto?')) {
            $.post('src/controller/controllerDashboardAnunciante.php', { op: 16, id: id }, function() {
                loadProducts();
            });
        }
    }

    $(document).ready(function() {

        // Carregar estatísticas principais
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
            $('#totalProfit').text(res);
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
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set',
                        'Out', 'Nov', 'Dez'
                    ],
                    datasets: [{
                        label: 'Vendas (€)',
                        data: res,
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
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
                        backgroundColor: ['#ffd700', '#ffed4e', '#ffe066', '#ffc107',
                            '#ffda8f'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
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
                type: 'polarArea',
                data: {
                    labels: res.map(p => p.nome),
                    datasets: [{
                        data: res.map(p => p.lucro),
                        backgroundColor: ['#ffd700', '#ffed4e', '#ffe066', '#ffc107',
                            '#ffda8f'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
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
                        backgroundColor: '#ffd700'
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

        // Carregar estatísticas de relatórios
        function loadReportStats() {
            // TODO: Carregar via AJAX op:19 para receita total, op:20 para pedidos, etc.
            $('#totalRevenue').text('Receita Total: Carregando...');
            $('#totalOrders').text('Número de Pedidos: Carregando...');
            $('#avgTicket').text('Ticket Médio: Carregando...');
            $('#profitMargin').text('Margem de Lucro: Carregando...');
        }

        // Vendas por Categoria (placeholder)
        function loadCategorySalesChart() {
            const ctx = document.getElementById('categorySalesChart');
            // TODO: Carregar via AJAX op:21
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Vendas (€)',
                        data: [],
                        backgroundColor: '#ffd700'
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
        }

        // Receita Diária (placeholder)
        function loadDailyRevenueChart() {
            const ctx = document.getElementById('dailyRevenueChart');
            // TODO: Carregar via AJAX op:22
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Receita (€)',
                        data: [],
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            });
        }

        // Tabela de Relatórios (placeholder)
        function loadReportsTable() {
            // TODO: Carregar via AJAX op:23
            $('#reportsTable').DataTable({
                data: [],
                columns: [
                    { data: 'produto' },
                    { data: 'vendas' },
                    { data: 'receita', render: v => '€' + v },
                    { data: 'lucro', render: v => '€' + v }
                ],
                destroy: true,
                pageLength: 5,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json'
                }
            });
        }

        // Atualizar Relatórios
        $('#updateReportsBtn').click(function() {
            loadReportStats();
            loadCategorySalesChart();
            loadDailyRevenueChart();
            loadReportsTable();
        });

        // Carregar Tipos de Produto
        $.post('src/controller/controllerDashboardAnunciante.php', { op: 13 }, function(data) {
            console.log('Tipos recebidos:', data);
            if (Array.isArray(data)) {
                data.forEach(t => $('#filterTipo, #tipo_produto_id').append(`<option value="${t.id}">${t.descricao}</option>`));
            } else {
                console.error('Tipos não é array:', data);
            }
        }, 'json').fail(function() {
            console.error('Erro ao carregar tipos');
        });

        // Integrar busca com DataTables
        $('#searchProduct').on('keyup', function() {
            $('#productsTable').DataTable().search($(this).val()).draw();
        });

        // Filtro por Tipo
        $('#filterTipo').on('change', function() {
            $('#productsTable').DataTable().column(2).search($(this).val()).draw();
        });

        // Carregar Produtos
        function loadProducts() {
            $.ajax({
                url: 'src/controller/controllerDashboardAnunciante.php',
                method: 'POST',
                data: { op: 8 },
                dataType: 'json'
            }).done(function(data) {
                $('#productsTable').DataTable({
                    data: data,
                    columns: [
                        { data: 'Produto_id' },
                        { data: 'nome' },
                        { data: 'tipo_descricao' },
                        { data: 'preco', render: v => '€' + parseFloat(v).toFixed(2) },
                        { data: 'stock' },
                        { data: 'estado' },
                        { data: 'ativo', render: v => v ? '<span style="color:green;">Sim</span>' : '<span style="color:red;">Não</span>' },
                        { data: null, orderable: false, render: (data) => `
                            <button onclick="editProduct(${data.Produto_id})" style="background: #ffc107; color: #000; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Editar</button>
                            <button onclick="deleteProduct(${data.Produto_id})" style="background: #dc3545; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Remover</button>
                        ` }
                    ],
                    destroy: true,
                    pageLength: 10,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json'
                    }
                });
            });
        }

        // Verificar Limite de Produtos
        $.post('src/controller/controllerDashboardAnunciante.php', { op: 14 }, function(limit) {
            console.log('Limite recebido:', limit);
            $('#productLimit').text(`Produtos: ${limit.current}/${limit.max}`);
            if (limit.current >= limit.max) {
                console.log('Desabilitando botão');
                $('#addProductBtn').prop('disabled', true).css({'background-color': '#ccc', 'cursor': 'not-allowed', 'opacity': '0.6'});
            }
        }, 'json');

        // Adicionar Produto
        $('#addProductBtn').click(function() {
            if ($(this).prop('disabled')) return alert('Limite de produtos atingido!');
            $('#modalTitle').text('Adicionar Produto');
            $('#productForm')[0].reset();
            $('#productModal').addClass('active');
        });

        // Salvar Produto
        $('#productForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            formData.append('op', $('#productId').val() ? 17 : 18);
            $.ajax({
                url: 'src/controller/controllerDashboardAnunciante.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false
            }).done(function() {
                $('#productModal').removeClass('active');
                loadProducts();
            });
        });

        // Fechar Modal
        $('.close').click(() => $('#productModal').removeClass('active'));

        // Carregar ao abrir página
        if (window.location.hash === '#products') showPage('products');
        loadProducts();

    });
    </script>
</body>
<?php
}else{
    echo "sem permissão!";
}
?>

</html>
