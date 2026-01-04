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
    <link rel="stylesheet" href="src/css/GestaoLucros.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <script src="src/css/lib/bootstrap.css"></script>

    <script src="src/js/lib/bootstrap.js"></script>
    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
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

                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <i class="navbar-icon fas fa-chart-line"></i>
                    <h2 class="navbar-title">Dashboard</h2>
                </div>
                <div class="navbar-right">
                    <button class="navbar-icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="navbar-user">
                        <div id="AdminPerfilInfo" style="display:flex;"></div>
                        <i class=" fas fa-chevron-down user-trigger" style="font-size: 12px; color: #4a5568;"></i>

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
                                    <label>Categoria</label>
                                    <select class="form-select" id="selectGastos">
                                        <option value="-1">Selecione a Opção...</option>
                                        <option value="Plano">Plano</option>
                                        <option value="Comissao">Comissão</option>
                                        <option value="Plataforma">Plataforma</option>
                                        <option value="Outro">Outro</option>
                                    </select>
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
                                            <th>Origem</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                            <th>Editar</th>
                                            <th>Remover</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listagemGastos">

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
                                    <label>Categoria</label>
                                    <select class="form-select" id="selectRendimento">
                                        <option value="-1">Selecione a Opção...</option>
                                        <option value="Plano">Plano</option>
                                        <option value="Comissao">Comissão</option>
                                        <option value="Plataforma">Plataforma</option>
                                        <option value="Outro">Outro</option>
                                    </select>
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
                                            <th>Origem</th>
                                            <th>Descrição</th>
                                            <th>Valor</th>
                                            <th>Editar</th>
                                            <th>Remover</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listagemRendimentos">

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
                                    <th>Origem / Anunciante</th>
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
    <div class="modal fade" id="formEditRendimento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i>
                        Editar Rendimento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-form-grid">
                        <div class="modal-form-group">
                            <label>Número</label>
                            <input type="text" class="form-input" placeholder="Número do rendimento"
                                id="numRendimentoEdit" disabled>
                        </div>
                        <div class="modal-form-group">
                            <label>Descrição</label>
                            <input type="text" class="form-input" placeholder="Ex: Venda, Serviço..."
                                id="descricaoRendimentoEdit">
                        </div>
                        <div class="modal-form-group">
                            <label>Valor (€)</label>
                            <input type="text" class="form-input" placeholder="0.00" id="valorRendimentoEdit">
                        </div>
                        <div class="modal-form-group">
                            <label>Categoria</label>
                            <select class="form-select" id="selectRendimentoEdit">
                                <option value="-1">Selecione a Opção...</option>
                                <option value="Plano">Plano</option>
                                <option value="Comissao">Comissão</option>
                                <option value="Plataforma">Plataforma</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn-modal-save" id="btnGuardar">
                        <i class="fas fa-check"></i>
                        <span class="btn-text">Guardar Alterações</span>
                        <span class="loading-spinner" id="spinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="formEditGastos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i>
                        Editar Rendimento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="modal-form-grid">
                        <div class="modal-form-group">
                            <label>Número</label>
                            <input type="text" class="form-input" placeholder="Número do rendimento" id="numGastosEdit"
                                disabled>
                        </div>
                        <div class="modal-form-group">
                            <label>Descrição</label>
                            <input type="text" class="form-input" placeholder="Ex: Venda, Serviço..."
                                id="descricaoGastosEdit">
                        </div>
                        <div class="modal-form-group">
                            <label>Valor (€)</label>
                            <input type="text" class="form-input" placeholder="0.00" id="valorGastosEdit">
                        </div>
                        <div class="modal-form-group">
                            <label>Categoria</label>
                            <select class="form-select" id="selectGastosEdit">
                                <option value="-1">Selecione a Opção...</option>
                                <option value="Plano">Plano</option>
                                <option value="Comissao">Comissão</option>
                                <option value="Plataforma">Plataforma</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn-modal-save" id="btnGuardar2">
                        <i class="fas fa-check"></i>
                        <span class="btn-text">Guardar Alterações</span>
                        <span class="loading-spinner" id="spinner" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="src/js/GestaoLucros.js"></script>

    <?php
}else{
header("Location: forbiddenerror.html");
}
?>
</body>

</html>