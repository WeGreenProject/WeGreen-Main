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
                            <span class="nav-icon">🛍️</span>
                            <span class="nav-text">Vendas</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('analytics')">
                            <span class="nav-icon">📈</span>
                            <span class="nav-text">Análises</span>
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
            <!-- Dashboard -->
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

            <!-- Analytics -->
            <div id="analytics" class="page">
                <div class="page-header">
                    <h2>Análises</h2>
                    <p>Insights detalhados do seu negócio</p>
                </div>
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Lucro por Produto</h3>
                        </div>
                        <canvas id="profitChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Margem de Lucro</h3>
                        </div>
                        <canvas id="marginChart"></canvas>
                    </div>
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
    }

    $(document).ready(function(){

        // Carregar estatísticas principais
        $.post('src/controller/controllerDashboardAnunciante.php', {op:3}, function(res){
            $('#PontosConfianca').html(res);
        });
        $.post('src/controller/controllerDashboardAnunciante.php', {op:1}, function(res){
            $('#PlanosAtual').html(res);
        });
        $.post('src/controller/controllerDashboardAnunciante.php', {op:2}, function(res){
            $('#ProdutoStock').html(res);
        });
        $.post('src/controller/controllerDashboardAnunciante.php', {op:4}, function(res){
            $('#totalProfit').text(res);
        });

        // Vendas Mensais
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: { op: 5 },
            dataType: 'json'
        }).done(function(res){
            const ctx = document.getElementById('salesChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    datasets: [{
                        label: 'Vendas (€)',
                        data: res,
                        borderColor: '#ffd700',
                        backgroundColor: 'rgba(255, 215, 0, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { responsive: true, plugins:{legend:{display:true}} }
            });
        });

        // Top Produtos
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: { op: 6 },
            dataType: 'json'
        }).done(function(res){
            const ctx = document.getElementById('topProductsChart');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: res.map(p => p.nome),
                    datasets: [{
                        data: res.map(p => p.vendidos),
                        backgroundColor: ['#ffd700','#ffed4e','#ffe066','#ffc107','#ffda8f']
                    }]
                },
                options: { responsive: true, plugins:{legend:{position:'bottom'}} }
            });
        });

        // Produtos Recentes
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: { op: 7 },
            dataType: 'html'
        }).done(function(res){
            $('#recentProducts').html(res);
        });

        // Lucro por Produto
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: { op: 10 },
            dataType: 'json'
        }).done(function(res){
            const ctx = document.getElementById('profitChart');
            new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: res.map(p => p.nome),
                    datasets: [{
                        data: res.map(p => p.lucro),
                        backgroundColor: ['#ffd700','#ffed4e','#ffe066','#ffc107','#ffda8f']
                    }]
                },
                options: { responsive: true, plugins:{legend:{position:'bottom'}} }
            });
        });

        // Margem de Lucro
        $.ajax({
            url: 'src/controller/controllerDashboardAnunciante.php',
            method: 'POST',
            data: { op: 11 },
            dataType: 'json'
        }).done(function(res){
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
                    plugins:{legend:{display:false}},
                    scales: {
                        y: {
                            ticks:{
                                callback: v => v + '%'
                            }
                        }
                    }
                }
            });
        });

    });
    </script>
</body>
<?php 
}else{
    echo "sem permissão!";
}
?>
</html>
