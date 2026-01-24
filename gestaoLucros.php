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

    /* Substituir o CSS dos summary-card existente por este */

    .lucros-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .summary-card {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        border: 1px solid #4a5568;
        border-radius: 20px;
        padding: 35px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(166, 217, 12, 0.2);
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(166, 217, 12, 0.12) 0%, transparent 70%);
    }

    /* Bordas específicas por tipo */
    .summary-card.receitas {
        border-left: 4px solid #48bb78;
    }

    .summary-card.receitas:hover {
        border-left-color: #38a169;
    }

    .summary-card.despesas {
        border-left: 4px solid #f56565;
    }

    .summary-card.despesas:hover {
        border-left-color: #e53e3e;
    }

    .summary-card.lucro {
        border-left: 4px solid #A6D90C;
    }

    .summary-card.lucro:hover {
        border-left-color: #8fb80a;
    }

    .summary-card.margem {
        border-left: 4px solid #4299e1;
    }

    .summary-card.margem:hover {
        border-left-color: #3182ce;
    }

    .summary-icon {
        font-size: 48px;
        margin-bottom: 18px;
        width: auto;
        height: auto;
        border-radius: 0;
        display: block;
        box-shadow: none;
        background: none;
    }

    /* Ícones coloridos por tipo */
    .summary-card.receitas .summary-icon {
        filter: drop-shadow(0 4px 8px rgba(72, 187, 120, 0.4));
        color: #48bb78;
    }

    .summary-card.despesas .summary-icon {
        filter: drop-shadow(0 4px 8px rgba(245, 101, 101, 0.4));
        color: #f56565;
    }

    .summary-card.lucro .summary-icon {
        filter: drop-shadow(0 4px 8px rgba(166, 217, 12, 0.4));
        color: #A6D90C;
    }

    .summary-card.margem .summary-icon {
        filter: drop-shadow(0 4px 8px rgba(66, 153, 225, 0.4));
        color: #4299e1;
    }

    .summary-label {
        color: #cbd5e0;
        font-size: 12px;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 600;
    }

    .summary-value {
        font-size: 48px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 12px;
    }

    /* Valores coloridos por tipo */
    .summary-card.receitas .summary-value {
        color: #48bb78;
        text-shadow: 0 2px 6px rgba(72, 187, 120, 0.25);
    }

    .summary-card.despesas .summary-value {
        color: #f56565;
        text-shadow: 0 2px 6px rgba(245, 101, 101, 0.25);
    }

    .summary-card.lucro .summary-value {
        color: #A6D90C;
        text-shadow: 0 2px 6px rgba(166, 217, 12, 0.25);
    }

    .summary-card.margem .summary-value {
        color: #4299e1;
        text-shadow: 0 2px 6px rgba(66, 153, 225, 0.25);
    }

    .summary-change {
        font-size: 14px;
        margin-top: 0;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 0;
        border-radius: 0;
        background: none;
    }

    .summary-change.positive {
        color: #4ade80;
        background: none;
    }

    .summary-change.negative {
        color: #f87171;
        background: none;
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

    /* Adicionar este CSS ao arquivo - Seção de Formulários */

    .forms-lucros {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    @media (max-width: 1024px) {
        .forms-lucros {
            grid-template-columns: 1fr;
        }
    }

    .form-section {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    /* Card de Formulário */
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 25px;
        transition: all 0.3s ease;
        min-height: 280px;
    }

    .form-card:hover {
        border-color: #A6D90C;
        box-shadow: 0 4px 12px rgba(166, 217, 12, 0.1);
    }

    .form-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e2e8f0;
    }

    .form-header h3 {
        font-size: 18px;
        color: #1a202c;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .form-header h3 i {
        color: #A6D90C;
        font-size: 20px;
    }

    .form-header p {
        color: #718096;
        font-size: 13px;
        margin: 0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        color: #1a202c;
        transition: all 0.3s;
        background: #ffffff;
    }

    .form-input:focus {
        outline: none;
        border-color: #A6D90C;
        box-shadow: 0 0 0 3px rgba(166, 217, 12, 0.1);
    }

    .form-input::placeholder {
        color: #a0aec0;
    }

    .btn-submit {
        width: 100%;
        padding: 14px 25px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .btn-success {
        background: linear-gradient(90deg, #48bb78 0%, #38a169 100%);
        color: #ffffff;
    }

    .btn-success:hover {
        box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
    }

    .btn-danger {
        background: linear-gradient(90deg, #f56565 0%, #e53e3e 100%);
        color: #ffffff;
    }

    .btn-danger:hover {
        box-shadow: 0 6px 20px rgba(245, 101, 101, 0.4);
    }

    /* Card de Tabela */
    .table-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 25px;
        transition: all 0.3s ease;
        min-height: 450px;
    }

    .table-card:hover {
        border-color: #A6D90C;
    }

    .table-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-card-header h3 {
        font-size: 18px;
        color: #1a202c;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .table-card-header h3 i {
        color: #A6D90C;
    }

    .btn-export-sm {
        padding: 8px 16px;
        background: #2d3748;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-export-sm:hover {
        background: #1a202c;
        transform: translateY(-2px);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    }

    .data-table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #2d3748;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .data-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #1a202c;
        font-size: 14px;
    }

    .data-table tbody tr {
        transition: all 0.2s;
    }

    .data-table tbody tr:hover {
        background: #f7fafc;
    }

    /* Botões de ação na tabela */
    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-edit {
        background: #bee3f8;
        color: #1e4e8c;
    }

    .btn-edit:hover {
        background: #90cdf4;
    }

    .btn-remove {
        background: #fed7d7;
        color: #742a2a;
    }

    .btn-remove:hover {
        background: #fc8181;
        color: #ffffff;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .table-card {
            min-height: auto;
        }

        .data-table {
            font-size: 12px;
        }

        .data-table th,
        .data-table td {
            padding: 8px 10px;
        }
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
                            <span class="nav-text">Gestao de Utilizadores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestaoLucros.php">
                            <span class="nav-icon"><i class="fas fa-euro-sign"></i></span>
                            <span class="nav-text">Gestão de Lucros</span>
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
                        <a class="nav-link" href="logAdmin.php">
                            <span class="nav-icon"><i class="fas fa-history"></i></span>
                            <span class="nav-text">Logs do Sistema</span>
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
                    <?php include 'src/views/notifications-widget.php'; ?>
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

                <!-- Resumo Financeiro -->
                <div class="lucros-summary">

                </div>


                <!-- Substituir a div .charts-lucros por este código -->
                <div class="forms-lucros">
                    <!-- Coluna Gastos -->
                    <div class="form-section">
                        <!-- Card Adicionar Gastos -->
                        <div class="form-card">
                            <div class="form-header">
                                <h3><i class="fas fa-wallet"></i> Adicionar Gastos</h3>
                                <p>Registar novos gastos e despesas</p>
                            </div>
                            <form class="form-grid">
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <input type="text" class="form-input" placeholder="Ex: Fornecedor, Material..."
                                        id="descricaoGasto">
                                </div>
                                <div class="form-group">
                                    <label>Valor (€)</label>
                                    <input type="text" class="form-input" placeholder="0.00" id="valorGasto">
                                </div>
                                <div class="form-group">
                                    <label>Data</label>
                                    <input type="date" class="form-input" id="dataGasto">
                                </div>
                                <div class="form-group full-width">
                                    <button type="button" class="btn-submit btn-danger" onclick="registaGastos()">
                                        <i class="fas fa-plus-circle"></i>
                                        Registar Gasto
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Tabela Gastos -->
                        <div class="table-card">
                            <div class="table-card-header">
                                <h3><i class="fas fa-list"></i> Lista de Gastos</h3>
                                <button class="btn-export-sm">
                                    <i class="fas fa-download"></i>
                                    Exportar
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table" id="tblGastos">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                            <th>Data</th>
                                            <th>Editar</th>
                                            <th>Remover</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listagemGastos">
                                        <!-- Dados dinâmicos via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Coluna Rendimentos -->
                    <div class="form-section">
                        <!-- Card Adicionar Rendimentos -->
                        <div class="form-card">
                            <div class="form-header">
                                <h3><i class="fas fa-hand-holding-usd"></i> Adicionar Rendimentos</h3>
                                <p>Registar novas receitas e rendimentos</p>
                            </div>
                            <form class="form-grid">
                                <div class="form-group">
                                    <label>Descrição</label>
                                    <input type="text" class="form-input" placeholder="Ex: Venda, Serviço..."
                                        id="descricaoRendimento">
                                </div>
                                <div class="form-group">
                                    <label>Valor (€)</label>
                                    <input type="text" class="form-input" placeholder="0.00" id="valorRendimento">
                                </div>
                                <div class="form-group">
                                    <label>Data</label>
                                    <input type="date" class="form-input" id="dataRendimento">
                                </div>
                                <div class="form-group full-width">
                                    <button type="button" class="btn-submit btn-success" onclick="registaRendimentos()">
                                        <i class="fas fa-plus-circle"></i>
                                        Registar Rendimento
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Tabela Rendimentos -->
                        <div class="table-card">
                            <div class="table-card-header">
                                <h3><i class="fas fa-list"></i> Lista de Rendimentos</h3>
                                <button class="btn-export-sm">
                                    <i class="fas fa-download"></i>
                                    Exportar
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="data-table" id="tblRendimentos">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                            <th>Data</th>
                                            <th>Editar</th>
                                            <th>Remover</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listagemRendimentos">
                                        <!-- Dados dinâmicos via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

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
                                    <th>Anunciante</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody id="transacoesBody">
                            </tbody>
                        </table>
                    </div>
                </div>
        </main>
    </div>
    <script src="src/js/GestaoLucros.js"></script>
    <script src="src/js/Adminstrador.js"></script>
    <?php
}else{
header("Location: forbiddenerror.html");
}
?>
</body>

</html>
