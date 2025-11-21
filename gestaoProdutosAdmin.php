<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas - Painel Admin</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/AdminGestao.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/bootstrap.js"></script>
    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" class="logo">
                <span class="logo-icon">👔</span>
                <div class="logo-text">
                    <h1>Wegreen</h1>
                    <p>Painel do Administrador</p>
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
                        <a class="nav-link active" href="gestaoProdutosAdmin.php">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoProdutosAdmin.php">
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
                        <a class="nav-link" href="fornecedores.php">
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
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h2>Gestão de Produtos</h2>
                <p>Gerir todos os produtos na plataforma</p>
            </div>

            <div class="tabs-container">
                <button class="tab-btn active" onclick="switchTab('minhas-vendas')">
                    📋 Meus Produtos
                </button>
                <button class="tab-btn" onclick="switchTab('adicionar-venda')">
                    ➕ Adicionar Produto
                </button>
                <button class="tab-btn" onclick="switchTab('todas-vendas')">
                    🌐 Todos os Produtos
                </button>
                <button class="tab-btn " onclick="switchTab('Inativos')">
                    🔎 Verificar Produtos
                </button>
            </div>


            <div id="Inativos" class="tab-content">
                <div class="table-container">
                    <h3 style="margin-bottom: 20px; color: #ffd700;">🔎 Produtos Aguardando Verificação</h3>
                    <table id="inativosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Genero</th>
                                <th>Total (€)</th>
                                <th>Estado</th>
                                <th>Info</th>
                            </tr>
                        </thead>
                        <tbody id="inativosBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="minhas-vendas" class="tab-content active">
                <div class="table-container">
                    <h3 style="margin-bottom: 20px; color: #ffd700;">📋 Histórico de Meus Produtos</h3>
                    <table id="minhasVendasTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Genero</th>
                                <th>Total (€)</th>
                                <th>Estado</th>
                                <th>Marca</th>
                            </tr>
                        </thead>
                        <tbody id="minhasVendasBody"></tbody>
                    </table>
                </div>
            </div>

            <div id="adicionar-venda" class="tab-content">
                <div class="form-container">
                    <h3 style="margin-bottom: 30px; color: #ffd700;">➕ Novo Produto</h3>
                    <form id="addVendaForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Vendedor</label>
                                <select id="listaVendedor" required>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Categoria</label>
                                <select id="listaCategoria" required>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input type="number" id="quantidade" min="1" value="1" required>
                            </div>
                            <div class="form-group">
                                <label>Preço Unitário (€)</label>
                                <input type="number" id="preco" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Marca</label>
                                <input type="number" id="quantidade" min="1" value="1" required>
                            </div>
                            <div class="form-group">
                                <label>Tamanho</label>
                                <input type="number" id="preco" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Genero</label>
                            <select id="estado" required>
                                <option value="Homem">Masculino</option>
                                <option value="Mulher">Feminino</option>
                                <option value="Crianca">Criança</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Foto</label>
                            <input type="file" id="preco" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea id="observacoes" rows="4"
                                placeholder="Adicionar notas sobre a descrição..."></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%;">
                            <span>✅</span>
                            Registar Venda
                        </button>
                    </form>
                </div>
            </div>

            <div id="todas-vendas" class="tab-content">
                <div class="table-container">
                    <h3 style="margin-bottom: 20px; color: #ffd700;">🌐 Base de Dados - Todas os Produtos</h3>
                    <table id="todasVendasTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Vendedor</th>
                                <th>Total (€)</th>
                                <th>Estado</th>
                                <th>Marca</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody id="todasVendasBody"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Verificação -->
    <div class="modal fade" id="formEditInativo2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <!-- AQUI FALTAVA -->
            <div class="modal-content">

                <div class="modal-header">
                    <h2>Editar Produto</h2>
                    <button class="modal-close" onclick="closeModal()">×</button>
                </div>

                <div class="product-info-grid">
                    <div class="info-item">
                        <label>ID do Produto</label>
                        <input type="text" class="form-control" id="numprodutoEdit" disabled>
                    </div>
                    <div class="info-item">
                        <label>Nome do Produto</label>
                        <input type="text" class="form-control" id="nomeprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Categoria</label>
                        <select name="" id="categoriaprodutoEdit">

                        </select>
                    </div>
                    <div class="info-item">
                        <label>Marca</label>
                        <input type="text" class="form-control" id="marcaprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Tamanho</label>
                        <input type="text" class="form-control" id="tamanhoprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Preço</label>
                        <input type="text" class="form-control" id="precoprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Gênero</label>
                        <input type="text" class="form-control" id="generoprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Vendedor</label>
                        <select name="" id="vendedorprodutoEdit">

                        </select>
                    </div>
                </div>

                <div class="photos-section" id="fotos-section">

                </div>

                <div class="modal-actions">
                    <button class="btn-approve" id="btnGuardar2">✅ Salvar Alterações</button>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="formEditInativo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <!-- AQUI FALTAVA -->
            <div class="modal-content">

                <div class="modal-header">
                    <h2>🔍 Verificação de Produto</h2>
                    <button class="modal-close" onclick="closeModal()">×</button>
                </div>

                <div class="product-info-grid">
                    <div class="info-item">
                        <label>ID do Produto</label>
                        <input type="text" class="form-control" id="numprodutoEdit" disabled>
                    </div>
                    <div class="info-item">
                        <label>Nome do Produto</label>
                        <input type="text" class="form-control" id="nomeprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Categoria</label>
                        <select name="" id="categoriaprodutoEdit">

                        </select>
                    </div>
                    <div class="info-item">
                        <label>Marca</label>
                        <input type="text" class="form-control" id="marcaprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Tamanho</label>
                        <input type="text" class="form-control" id="tamanhoprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Preço</label>
                        <input type="text" class="form-control" id="precoprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Gênero</label>
                        <input type="text" class="form-control" id="generoprodutoEdit">
                    </div>
                    <div class="info-item">
                        <label>Vendedor</label>
                        <select name="" id="vendedorprodutoEdit">

                        </select>
                    </div>
                </div>

                <div class="photos-section" id="fotos-section">

                </div>

                <div class="modal-actions">
                    <button class="btn-approve" id="btnGuardar">✅ Aprovar Produto</button>
                    <button class=" btn-reject" id="btnRejeitar">❌ Rejeitar Produto</button>
                </div>

            </div>
        </div>
    </div>
    <script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
    </script>
    <script src="src/js/gestaoProdutos.js"></script>
</body>

</html>