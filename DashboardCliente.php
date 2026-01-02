<?php
session_start();

if(!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 2){
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css">
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

</head>
<body>
  <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-leaf"></i>
                <div class="logo-text">
                    <h2>WeGreen</h2>
                    <p>Moda Sustentável</p>
                </div>
            </div>

            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">Menu</div>
                    <a href="DashboardCliente.php" class="menu-item active" data-page="dashboard">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="minhasEncomendas.php" class="menu-item" data-page="orders">
                        <i class="fas fa-shopping-bag"></i>
                        <span>As Minhas Encomendas</span>
                    </a>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">Conta</div>
                    <a href="#" class="menu-item" data-page="settings">
                        <i class="fas fa-cog"></i>
                        <span>Definições</span>
                    </a>
                    <a href="suporte.html" class="menu-item" data-page="support">
                        <i class="fas fa-headset"></i>
                        <span>Suporte</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php 
                        $nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Cliente';
                        $iniciais = '';
                        $palavras = explode(' ', $nome);
                        foreach($palavras as $palavra) {
                            if(!empty($palavra)) {
                                $iniciais .= strtoupper(substr($palavra, 0, 1));
                                if(strlen($iniciais) >= 2) break;
                            }
                        }
                        echo $iniciais;
                        ?>
                    </div>
                    <div class="user-info">
                        <h4><?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Cliente'; ?></h4>
                        <p><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?></p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Página Dashboard -->
            <div id="page-dashboard" class="page-content">
                <header class="dashboard-header">
                    <div class="header-title">
                        <h1>Dashboard</h1>
                        <p>Visão geral das suas compras e atividades</p>
                    </div>
                    <div class="header-actions">
                        <div class="date-filter">
                            <i class="far fa-calendar"></i>
                            <span>Janeiro 2024 - Dezembro 2024</span>
                        </div>
                    </div>
                </header>

                <div class="content-area">
                    <!-- Cards de Estatísticas -->
                    <div class="stats-grid">
                        <div class="stat-card highlight">
                            <div class="stat-header">
                                <span class="stat-title">Gastos Totais</span>
                                <div class="stat-icon">
                                    <i class="fas fa-euro-sign"></i>
                                </div>
                            </div>
                            <div class="stat-value">€565</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+23% desde o último mês</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Produtos Comprados</span>
                                <div class="stat-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="stat-value">24</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+15% desde o último mês</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Pedidos Entregues</span>
                                <div class="stat-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                            <div class="stat-value">3</div>
                            <div class="stat-change">
                                <i class="fas fa-minus"></i>
                                <span>Sem alterações</span>
                            </div>
                        </div>

                        </div>
                    </div>

                    <div class="charts-section">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Gastos Mensais</h3>
                                <div class="chart-legend">
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #3cb371;"></div>
                                        <span>Gastos (€)</span>
                                    </div>
                                </div>
                            </div>
                            <canvas id="monthlyExpensesChart" height="80"></canvas>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Produtos por Categoria</h3>
                            </div>
                            <canvas id="categoryChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Pedidos Recentes -->
                    <div class="orders-table-section">
                        <div class="table-header">
                            <h3 class="table-title">Pedidos Recentes</h3>
                            <div class="table-actions">
                                <button class="btn-filter">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nº Pedido</th>
                                        <th>Data</th>
                                        <th>Produtos</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>#WG001</strong></td>
                                        <td>15/12/2024</td>
                                        <td>T-shirt Orgânica, Calças Recicladas</td>
                                        <td>€89.90</td>
                                        <td><span class="status-badge status-entregue">Entregue</span></td>
                                        <td>
                                            <button class="action-btn">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#WG002</strong></td>
                                        <td>10/12/2024</td>
                                        <td>Vestido Sustentável</td>
                                        <td>€125.00</td>
                                        <td><span class="status-badge status-enviado">Enviado</span></td>
                                        <td>
                                            <button class="action-btn">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#WG003</strong></td>
                                        <td>05/12/2024</td>
                                        <td>Casaco Ecológico, Lenço</td>
                                        <td>€156.50</td>
                                        <td><span class="status-badge status-processando">Processando</span></td>
                                        <td>
                                            <button class="action-btn">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>#WG004</strong></td>
                                        <td>01/12/2024</td>
                                        <td>Sapatos Veganos</td>
                                        <td>€98.00</td>
                                        <td><span class="status-badge status-pendente">Pendente</span></td>
                                        <td>
                                            <button class="action-btn">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Navegação entre páginas
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if(this.getAttribute('href') && this.getAttribute('href') !== '#') {
                    return;
                }
                e.preventDefault();
                document.querySelectorAll('.menu-item').forEach(mi => mi.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Gráfico de Gastos Mensais
        const ctx1 = document.getElementById('monthlyExpensesChart');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Gastos (€)',
                    data: [45, 52, 38, 65, 48, 72, 55, 61, 58, 69, 73, 85],
                    backgroundColor: '#3cb371',
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            callback: function(value) {
                                return '€' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Gráfico de Categorias
        const ctx2 = document.getElementById('categoryChart');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Vestuário', 'Calçado', 'Acessórios', 'Outros'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: [
                        '#3cb371',
                        '#2e8b57',
                        '#90ee90',
                        '#e0e0e0'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>