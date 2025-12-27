<?php
session_start();

if($_SESSION['tipo'] == 1){
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Lucros - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/admin.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    /* Estilos específicos para Gestão de Lucros */
    .lucros-filters {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-group select,
    .filter-group input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        color: #1a202c;
        transition: all 0.3s;
    }

    .filter-group select:focus,
    .filter-group input:focus {
        outline: none;
        border-color: #A6D90C;
        box-shadow: 0 0 0 3px rgba(166, 217, 12, 0.1);
    }

    .btn-filter {
        padding: 12px 30px;
        background: linear-gradient(90deg, #A6D90C 0%, #90c207 100%);
        color: #1a202c;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(166, 217, 12, 0.4);
    }

    .lucros-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .summary-card {
        background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .summary-card:hover {
        border-color: #A6D90C;
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(166, 217, 12, 0.15);
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        opacity: 0.1;
    }

    .summary-card.receitas::before {
        background: #48bb78;
    }

    .summary-card.despesas::before {
        background: #f56565;
    }

    .summary-card.lucro::before {
        background: #A6D90C;
    }

    .summary-card.margem::before {
        background: #4299e1;
    }

    .summary-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .summary-card.receitas .summary-icon {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: #ffffff;
    }

    .summary-card.despesas .summary-icon {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: #ffffff;
    }

    .summary-card.lucro .summary-icon {
        background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
        color: #1a202c;
    }

    .summary-card.margem .summary-icon {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: #ffffff;
    }

    .summary-label {
        font-size: 13px;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .summary-value {
        font-size: 36px;
        font-weight: 800;
        color: #1a202c;
        line-height: 1;
        margin-bottom: 10px;
    }

    .summary-change {
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 8px;
    }

    .summary-change.positive {
        background: #c6f6d5;
        color: #22543d;
    }

    .summary-change.negative {
        background: #fed7d7;
        color: #742a2a;
    }

    .lucros-tables {
        display: grid;
        grid-template-columns: 1fr;
        gap: 25px;
    }

    .lucros-table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 25px;
        transition: all 0.3s ease;
    }

    .lucros-table-card:hover {
        border-color: #A6D90C;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-header h3 {
        font-size: 18px;
        color: #1a202c;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-header h3 i {
        color: #A6D90C;
    }

    .btn-export {
        padding: 10px 20px;
        background: #2d3748;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-export:hover {
        background: #1a202c;
        transform: translateY(-2px);
    }

    .lucros-table {
        width: 100%;
        border-collapse: collapse;
    }

    .lucros-table thead {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    }

    .lucros-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }

    .lucros-table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #1a202c;
        font-size: 14px;
    }

    .lucros-table tbody tr {
        transition: all 0.2s;
    }

    .lucros-table tbody tr:hover {
        background: #f7fafc;
    }

    .valor-positivo {
        color: #48bb78;
        font-weight: 600;
    }

    .valor-negativo {
        color: #f56565;
        font-weight: 600;
    }

    .valor-neutro {
        color: #A6D90C;
        font-weight: 600;
    }

    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: #c6f6d5;
        color: #22543d;
    }

    .badge-warning {
        background: #feebc8;
        color: #7c2d12;
    }

    .badge-danger {
        background: #fed7d7;
        color: #742a2a;
    }

    .badge-info {
        background: #bee3f8;
        color: #1e4e8c;
    }

    /* Gráficos */
    .charts-lucros {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    @media (max-width: 1024px) {
        .charts-lucros {
            grid-template-columns: 1fr;
        }
    }
    </style>
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
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Gestão de Utilizadores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestaoLucros.php">
                            <span class="nav-icon"><i class="fas fa-euro-sign"></i></span>
                            <span class="nav-text">Gestão de Lucros</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                            <span class="nav-text">Análises</span>
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
                    <li class="nav-item">
                        <a class="nav-link" href="perfilAdmin.php">
                            <span class="nav-icon"><i class="fas fa-cog"></i></span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <i class="navbar-icon fas fa-euro-sign"></i>
                    <h2 class="navbar-title">Gestão de Lucros</h2>
                </div>
                <div class="navbar-right">
                    <button class="navbar-icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="navbar-user">
                        <div id="AdminPerfilInfo" style="display:flex;"></div>
                        <i class="fas fa-chevron-down user-trigger" style="font-size: 12px; color: #4a5568;"></i>
                        <div class="user-dropdown" id="userDropdown"></div>
                    </div>
                </div>
            </nav>

            <div class="page active">
                <div class="page-header">
                    <h2>Gestão de Lucros</h2>
                    <p>Análise detalhada de receitas, despesas e margem de lucro</p>
                </div>

                <!-- Filtros -->
                <div class="lucros-filters">
                    <div class="filter-group">
                        <label>Período</label>
                        <select id="periodoFilter">
                            <option value="hoje">Hoje</option>
                            <option value="semana">Esta Semana</option>
                            <option value="mes" selected>Este Mês</option>
                            <option value="trimestre">Este Trimestre</option>
                            <option value="ano">Este Ano</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Categoria</label>
                        <select id="categoriaFilter">
                            <option value="todas">Todas as Categorias</option>
                            <option value="vendas">Vendas de Produtos</option>
                            <option value="assinaturas">Assinaturas</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button class="btn-filter">
                            <i class="fas fa-filter"></i>
                            Aplicar Filtros
                        </button>
                    </div>
                </div>

                <!-- Resumo Financeiro -->
                <div class="lucros-summary">

                </div>


                <div class="charts-lucros">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-line"></i> Evolução Da Receita</h3>
                            <p>Comparação de receitas, despesas e lucros</p>
                        </div>
                        <canvas id="evolucaoChart"></canvas>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie"></i> Distribuição de Receitas</h3>
                            <p>Por categoria de produto</p>
                        </div>
                        <canvas id="distribuicaoChart"></canvas>
                    </div>
                </div>

                <!-- Tabela de Transações -->
                <div class="lucros-tables">
                    <div class="lucros-table-card">
                        <div class="table-header">
                            <h3><i class="fas fa-receipt"></i> Transações Recentes</h3>
                            <button class="btn-export">
                                <i class="fas fa-download"></i>
                                Exportar Excel
                            </button>
                        </div>
                        <table class="lucros-table" id="transacoesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th>Receita</th>
                                    <th>Despesa</th>
                                    <th>Lucro</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="transacoesBody">
                                <tr>
                                    <td>#1234</td>
                                    <td>23/12/2025</td>
                                    <td><span class="badge badge-success">Venda</span></td>
                                    <td>Camisa Sustentável - Pedido #5678</td>
                                    <td>Vestuário</td>
                                    <td class="valor-positivo">€89.90</td>
                                    <td class="valor-negativo">€45.50</td>
                                    <td class="valor-neutro">€44.40</td>
                                    <td><span class="badge badge-success">Concluído</span></td>
                                </tr>
                                <tr>
                                    <td>#1233</td>
                                    <td>23/12/2025</td>
                                    <td><span class="badge badge-info">Assinatura</span></td>
                                    <td>Plano Premium - João Silva</td>
                                    <td>Assinaturas</td>
                                    <td class="valor-positivo">€29.90</td>
                                    <td class="valor-negativo">€5.00</td>
                                    <td class="valor-neutro">€24.90</td>
                                    <td><span class="badge badge-success">Ativo</span></td>
                                </tr>
                                <tr>
                                    <td>#1232</td>
                                    <td>22/12/2025</td>
                                    <td><span class="badge badge-warning">Despesa</span></td>
                                    <td>Fornecedor - Matéria Prima</td>
                                    <td>Operacional</td>
                                    <td>-</td>
                                    <td class="valor-negativo">€1,250.00</td>
                                    <td class="valor-negativo">-€1,250.00</td>
                                    <td><span class="badge badge-success">Pago</span></td>
                                </tr>
                                <tr>
                                    <td>#1231</td>
                                    <td>22/12/2025</td>
                                    <td><span class="badge badge-success">Venda</span></td>
                                    <td>Calças Eco-Friendly - Pedido #5677</td>
                                    <td>Vestuário</td>
                                    <td class="valor-positivo">€129.90</td>
                                    <td class="valor-negativo">€68.00</td>
                                    <td class="valor-neutro">€61.90</td>
                                    <td><span class="badge badge-success">Concluído</span></td>
                                </tr>
                                <tr>
                                    <td>#1230</td>
                                    <td>21/12/2025</td>
                                    <td><span class="badge badge-success">Venda</span></td>
                                    <td>Conjunto Sustentável - Pedido #5676</td>
                                    <td>Vestuário</td>
                                    <td class="valor-positivo">€199.90</td>
                                    <td class="valor-negativo">€95.00</td>
                                    <td class="valor-neutro">€104.90</td>
                                    <td><span class="badge badge-warning">Processando</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabela de Produtos Mais Lucrativos -->
                    <div class="lucros-table-card">
                        <div class="table-header">
                            <h3><i class="fas fa-trophy"></i> Produtos Mais Lucrativos</h3>
                            <button class="btn-export">
                                <i class="fas fa-download"></i>
                                Exportar Excel
                            </button>
                        </div>
                        <table class="lucros-table">
                            <thead>
                                <tr>
                                    <th>Posição</th>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Vendas</th>
                                    <th>Receita Total</th>
                                    <th>Custo Total</th>
                                    <th>Lucro Total</th>
                                    <th>Margem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-warning">🥇 1º</span></td>
                                    <td>Camisa Sustentável</td>
                                    <td>Vestuário</td>
                                    <td>156 unidades</td>
                                    <td class="valor-positivo">€14,024.40</td>
                                    <td class="valor-negativo">€7,098.00</td>
                                    <td class="valor-neutro">€6,926.40</td>
                                    <td><span class="badge badge-success">49.4%</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-info">🥈 2º</span></td>
                                    <td>Calças Eco-Friendly</td>
                                    <td>Vestuário</td>
                                    <td>98 unidades</td>
                                    <td class="valor-positivo">€12,730.20</td>
                                    <td class="valor-negativo">€6,664.00</td>
                                    <td class="valor-neutro">€6,066.20</td>
                                    <td><span class="badge badge-success">47.6%</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-danger">🥉 3º</span></td>
                                    <td>Conjunto Sustentável</td>
                                    <td>Vestuário</td>
                                    <td>72 unidades</td>
                                    <td class="valor-positivo">€14,392.80</td>
                                    <td class="valor-negativo">€6,840.00</td>
                                    <td class="valor-neutro">€7,552.80</td>
                                    <td><span class="badge badge-success">52.5%</span></td>
                                </tr>
                                <tr>
                                    <td>4º</td>
                                    <td>Mochila Reciclada</td>
                                    <td>Acessórios</td>
                                    <td>134 unidades</td>
                                    <td class="valor-positivo">€8,040.00</td>
                                    <td class="valor-negativo">€4,824.00</td>
                                    <td class="valor-neutro">€3,216.00</td>
                                    <td><span class="badge badge-success">40.0%</span></td>
                                </tr>
                                <tr>
                                    <td>5º</td>
                                    <td>Ténis Vegano</td>
                                    <td>Calçado</td>
                                    <td>89 unidades</td>
                                    <td class="valor-positivo">€8,010.00</td>
                                    <td class="valor-negativo">€4,450.00</td>
                                    <td class="valor-neutro">€3,560.00</td>
                                    <td><span class="badge badge-success">44.4%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="src/js/GestaoLucros.js"></script>
    <script>
    $(document).ready(function() {
        $('#transacoesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
            },
            order: [
                [0, 'desc']
            ],
            pageLength: 10
        });
    });
    </script>
    <?php
}else{
header("Location: forbiddenerror.html");
}
?>
</body>

</html>