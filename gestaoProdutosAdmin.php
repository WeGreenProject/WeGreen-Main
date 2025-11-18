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
                        <a class="nav-link" href="produtos.php">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestaoProdutosAdmin.php">
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
                <p>Gerir todos as produtos na plataforma</p>
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
            </div>

            <!-- Tab: Minhas Vendas -->
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
                        <tbody id="minhasVendasBody">
                            
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Adicionar Venda -->
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

                        <div class="form-group">
                            <label>Genero</label>
                            <select id="estado" required>
                                <option value="Homem">Masculino</option>
                                <option value="Mulher">Feminino</option>
                                <option value="Crianca">Criança</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Observações</label>
                            <textarea id="observacoes" rows="4" placeholder="Adicionar notas sobre a venda..."></textarea>
                        </div>

                        <button type="submit" class="btn-primary" style="width: 100%;">
                            <span>✅</span>
                            Registar Venda
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab: Todas as Vendas -->
            <div id="todas-vendas" class="tab-content">
                <div class="table-container">
                    <h3 style="margin-bottom: 20px; color: #ffd700;">🌐 Base de Dados - Todas as Vendas</h3>
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
                            </tr>
                        </thead>
                        <tbody id="todasVendasBody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>

        // Função para mudar de tab
        function switchTab(tabId) {
            // Remover active de todos os botões e conteúdos
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Adicionar active ao botão e conteúdo selecionado
            event.target.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        // Função para obter badge de estado
        function getStatusBadge(status) {
            const statusMap = {
                paid: { class: 'status-paid', text: 'Pago' },
                pending: { class: 'status-pending', text: 'Pendente' },
                cancelled: { class: 'status-cancelled', text: 'Cancelado' }
            };
            const s = statusMap[status];
            return `<span class="status-badge ${s.class}">${s.text}</span>`;
        }
    </script>
    <script src="src/js/gestaoProdutos.js"></script>
</body>
</html>