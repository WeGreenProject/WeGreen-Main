<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Produtos - WeGreen Admin</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/AdminGestao.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/bootstrap.js"></script>
    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" class="logo">
                <span class="logo-icon"><i class="fas fa-leaf"></i></span>
                <div class="logo-text">
                    <h1>WeGreen</h1>
                    <p>Painel Admin</p>
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
                        <a class="nav-link active" href="gestaoProdutosAdmin.php">
                            <span class="nav-icon"><i class="fas fa-tshirt"></i></span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoVendasAdmin.php">
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ProdutosAdmin.php">
                            <span class="nav-icon"><i class="fas fa-check-circle"></i></span>
                            <span class="nav-text">Aprovar Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                            <span class="nav-text">Análises</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chats.php">
                            <span class="nav-icon"><i class="fas fa-comments"></i></span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fornecedores.php">
                            <span class="nav-icon"><i class="fas fa-truck"></i></span>
                            <span class="nav-text">Fornecedores</span>
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
            <div class="page-header">
                <h2>Gestão de Produtos</h2>
                <p>Gerir todos os produtos na plataforma</p>
            </div>
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-fire"></i>Produto anunciados (por Categoria)</h3>
                    </div>
                    <canvas id="topProductsChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-star"></i>Produto Vendidos (por Categoria)</h3>
                    </div>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <br>
            <br>
            <div class="tabs-container">
                <button class="tab-btn active" onclick="switchTab('minhas-vendas')">
                    <i class="fas fa-list"></i> Meus Produtos
                </button>
                <!-- <button class="tab-btn" onclick="switchTab('adicionar-venda')">
                    <i class="fas fa-plus-circle"></i> Adicionar Produto
                </button> -->
                <button class="tab-btn" onclick="switchTab('todas-vendas')">
                    <i class="fas fa-globe"></i> Todos os Produtos
                </button>
                <button class="tab-btn" onclick="switchTab('Inativos')">
                    <i class="fas fa-search"></i> Verificar Produtos
                </button>
            </div>

            <div id="Inativos" class="tab-content">
                <div class="table-container">
                    <h3><i class="fas fa-clock" style="color: #f59e0b;"></i> Produtos Aguardando Verificação</h3>
                    <table id="inativosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Género</th>
                                <th>Total (€)</th>
                                <th>Estado</th>
                                <th>Info</th>
                            </tr>
                        </thead>
                        <tbody id="inativosBody">
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-box"
                                        style="font-size: 48px; color: #cbd5e0; margin-bottom: 15px;"></i>
                                    <p style="color: #718096;"></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="adicionar-venda" class="tab-content">
                <div class="form-container">
                    <h3 style="margin-bottom: 30px; color: #3657c5ff;">➕ Novo Produto</h3>
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
                                <label>Nome Produto</label>
                                <input type="text" id="nomeprod" required>
                            </div>
                            <div class="form-group">
                                <label>Estado</label>
                                <input type="text" id="estadoprod" required>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Quantidade</label>
                                <input type="number" id="quantidade" required>
                            </div>
                            <div class="form-group">
                                <label>Preço Unitário (€)</label>
                                <input type="number" id="preco" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Marca</label>
                                <input type="text" id="marca" required>
                            </div>
                            <div class="form-group">
                                <label>Tamanho</label>
                                <input type="text" id="tamanho" step="0.01" required>
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
                            <input type="file" id="fotoProduto" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea id="observacoes" rows="4"
                                placeholder="Adicionar notas sobre a descrição..."></textarea>
                        </div>
                        <button type="button" class="btn-primary" onclick="adicionarProdutos()" style="width: 100%;">
                            <span>✅</span>
                            Registar Produto
                        </button>
                    </form>
                </div>
            </div>
            <div id="todas-vendas" class="tab-content">
                <div class="table-container">
                    <h3><i class="fa-solid fa-globe" style="color: #007bff;"></i> Todos os Produtos</h3>
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
                        <tbody id="todasVendasBody">
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-box"
                                        style="font-size: 48px; color: #cbd5e0; margin-bottom: 15px;"></i>
                                    <p style="color: #718096;">Nenhum produto aguardando verificação</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="minhas-vendas" class="tab-content active">
                <div class="table-container">
                    <h3><i class="fas fa-box-open" style="color: #A6D90C;"></i> Histórico de Meus Produtos</h3>
                    <table id="minhasVendasTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Género</th>
                                <th>Total (€)</th>
                                <th>Estado</th>
                                <th>Marca</th>
                            </tr>
                        </thead>
                        <tbody id="minhasVendasBody">
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-tshirt"
                                        style="font-size: 48px; color: #cbd5e0; margin-bottom: 15px;"></i>
                                    <p style="color: #718096;">Nenhum produto cadastrado ainda</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal fade" id="formEditInativo2" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h2>Editar Produto</h2>
                            <button type="button" class="modal-close" data-bs-dismiss="modal">×</button>
                        </div>
                        <br>
                        <div class=" product-info-grid">
                            <div class="info-item">
                                <label>ID do Produto</label>
                                <input type="text" class="form-control" id="numprodutoEdit2" disabled>
                            </div>
                            <div class="info-item">
                                <label>Nome do Produto</label>
                                <input type="text" class="form-control" id="nomeprodutoEdit2">
                            </div>
                            <div class="info-item">
                                <label>Categoria</label>
                                <select name="" id="categoriaprodutoEdit2">

                                </select>
                            </div>
                            <div class="info-item">
                                <label>Marca</label>
                                <input type="text" class="form-control" id="marcaprodutoEdit2">
                            </div>
                            <div class="info-item">
                                <label>Tamanho</label>
                                <input type="text" class="form-control" id="tamanhoprodutoEdit2">
                            </div>
                            <div class="info-item">
                                <label>Preço</label>
                                <input type="text" class="form-control" id="precoprodutoEdit2">
                            </div>
                            <div class="info-item">
                                <label>Gênero</label>
                                <input type="text" class="form-control" id="generoprodutoEdit2">
                            </div>
                            <div class="info-item">
                                <label>Vendedor</label>
                                <select name="" id="vendedorprodutoEdit2">

                                </select>
                            </div>
                        </div>

                        <div class="photos-section" id="fotos-section2">

                        </div>

                        <div class="modal-actions">
                            <button class="btn-approve" id="btnGuardar2">✅ Salvar Alterações</button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade" id="formEditInativo" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h2>🔍 Verificação de Produto</h2>
                            <button class="modal-close" type="button" class="modal-close"
                                data-bs-dismiss="modal">×</button>
                        </div>
                        <br>

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
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>