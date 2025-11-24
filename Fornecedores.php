<?php
    session_start();

    if($_SESSION['tipo'] == 1){ 
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Dashboard</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/fornecedores.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" style="text-decoration: none;">
                <div class="logo">
                    <span class="logo-icon">👔</span>
                    <div class="logo-text">
                        <h1>Wegreen</h1>
                        <p>Painel do Adminstrador</p>
                    </div>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="DashboardAdmin.php">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoProdutosAdmin.php">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vendas.php">
                            <span class="nav-icon">🛍️</span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ProdutosAdmin.php">
                            <span class="nav-icon">🛒</span>
                            <span class="nav-text">Aprovar Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon">📈</span>
                            <span class="nav-text">Análises</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chats.php">
                            <span class="nav-icon">💬</span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="fornecedores.php">
                            <span class="nav-icon">🚚</span>
                            <span class="nav-text">Gestão de Fornecedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="user-profile">
                <div class="profile-info" id="ProfileUser">
                </div>
                <button class="profile-settings-btn" onclick="showPage('settings')" title="Configurações">
                    <span>⚙️</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h2>🚚 Fornecedores</h2>
                <p>Gerencie seus fornecedores e parceiros comerciais</p>
            </div>

            <div class="action-bar">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Pesquisar fornecedores...">
                    <span class="search-icon">🔍</span>
                </div>
                <button class="btn-primary" type="button" onclick="adicionarFornecedor();">
                    ➕ Adicionar Fornecedor
                </button>

            </div>

            <div class="table-container">
                <table id="suppliersTable">
                    <thead>
                        <tr>
                            <th>Fornecedor</th>
                            <th>Contato</th>
                            <th>Sede</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="suppliersTableBody">

                    </tbody>
                </table>
            </div>
        </main>
    </div>
<div class="modal" id="supplierModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">➕ Novo Fornecedor</h3>
            <button class="close-btn"></button>
        </div>

        <form id="supplierForm">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nome do Fornecedor</label>
                    <input type="text" id="fornecedorNome" required>
                </div>
                <div class="form-group">
                    <label>Categoria</label>
                    <select id="fornecedorCategoria"></select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="fornecedorEmail" required>
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" id="fornecedortelefone" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Sede</label>
                    <input type="text" id="fornecedorSede" placeholder="A morada da Empresa">
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea id="observacoes" rows="4" placeholder="Adicionar o serviço..."></textarea>
                </div>
            </div>

            <button type="button" class="btn-primary" id="btnGuardar2" style="width: 100%; margin-top: 10px;">
                💾 Salvar Fornecedor
            </button>
        </form>
    </div>
</div>
<div class="modal" id="formEditFornecedores" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">➕ Novo Fornecedor</h3>
            <button class="close-btn"></button>
        </div>

        <form id="supplierForm">
            <div class="form-grid">
                <div class="form-group">
                    <label>ID do Fornecedor</label>
                    <input type="text" id="numfornecedorEdit" required>
                </div>
                <div class="form-group">
                    <label>Nome do Fornecedor</label>
                    <input type="text" id="fornecedorNomeEdit" required>
                </div>
                <div class="form-group">
                    <label>Categoria</label>
                    <select id="fornecedorCategoriaEdit"></select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="fornecedorEmailEdit" required>
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" id="fornecedortelefoneEdit" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Sede</label>
                    <input type="text" id="fornecedorSedeEdit" placeholder="A morada da Empresa">
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea id="observacoesEdit" rows="4" placeholder="Adicionar o serviço..."></textarea>
                </div>
            </div>

            <button type="button" class="btn-primary" id="btnGuardar3" style="width: 100%; margin-top: 10px;">
                💾 Salvar Fornecedor
            </button>
        </form>
    </div>
</div>
    <script src="src/js/fornecedor.js"></script>
    <?php 
}else{
    echo "sem permissão!";
}

?>
</body>

</html>