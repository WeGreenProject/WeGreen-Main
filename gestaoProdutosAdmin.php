<!DOCTYPE html>
<html lang="pt">

<head>
<<<<<<< Updated upstream
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
                        <a class="nav-link" href="gestaoCliente.php">
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Gestao de Utilizadores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoLucros.php">
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
                        <a class="nav-link" href="logAdmin.php">
                            <span class="nav-icon"><i class="fas fa-history"></i></span>
                            <span class="nav-text">Logs do Sistema</span>
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
                        <h3><i class="fas fa-fire"></i>Produto anunciados</h3>
                    </div>
                    <canvas id="topProductsChart"></canvas>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-star"></i>Produto Vendidos</h3>
                    </div>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <br>
            <br>
            <div class="tabs-container">
                <!-- <button class="tab-btn active" onclick="switchTab('minhas-vendas')">
                    <i class="fas fa-list"></i> Meus Produtos
                </button> -->
                <!-- <button class="tab-btn" onclick="switchTab('adicionar-venda')">
                    <i class="fas fa-plus-circle"></i> Adicionar Produto
                </button> -->
                <button class="tab-btn" onclick="switchTab('todas-vendas')">
                    <i class="fas fa-globe"></i> Todos os Produtos
                </button>
                <button class="tab-btn" onclick="switchTab('Inativos')">
                    <i class="fas fa-search"></i> Verificar Produtos
                </button>
=======
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Produtos - WeGreen Admin</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/lib/datatables.css">
  <link rel="stylesheet" href="src/css/lib/select2.css">
  <link rel="stylesheet" href="assets/css/notifications-dropdown.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/bootstrap.js"></script>
  <script src="src/js/lib/datatables.js"></script>
  <script src="src/js/lib/select2.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <script src="src/js/notifications.js"></script>
</head>

<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <a href="index.html" class="sidebar-logo" style="text-decoration: none; color: inherit; cursor: pointer;">
        <i class="fas fa-leaf"></i>
        <div class="logo-text">
          <h2>WeGreen</h2>
          <p>Painel Administrador</p>
        </div>
      </a>

      <nav class="sidebar-menu">
        <div class="menu-section">
          <div class="menu-section-title">Menu</div>
          <a href="DashboardAdmin.php" class="menu-item">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
          </a>
          <a href="gestaoProdutosAdmin.php" class="menu-item active">
            <i class="fas fa-tshirt"></i>
            <span>Produtos</span>
          </a>
          <a href="gestaoCliente.php" class="menu-item">
            <i class="fas fa-users"></i>
            <span>Utilizadores</span>
          </a>
          <a href="gestaoLucros.php" class="menu-item">
            <i class="fas fa-euro-sign"></i>
            <span>Lucros</span>
          </a>
          <a href="Chatadmin.php" class="menu-item">
            <i class="fas fa-comments"></i>
            <span>Chats</span>
          </a>
          <a href="fornecedores.php" class="menu-item">
            <i class="fas fa-truck"></i>
            <span>Fornecedores</span>
          </a>
          <a href="logAdmin.php" class="menu-item">
            <i class="fas fa-history"></i>
            <span>Logs</span>
          </a>
        </div>
      </nav>
    </aside>

    <main class="main-content">
      <nav class="top-navbar">
        <div class="navbar-left">
          <h1 class="page-title"><i class="fas fa-tshirt"></i> Gestão de Produtos</h1>
        </div>
        <div class="navbar-right">
          <?php include 'src/views/notifications-widget.php'; ?>
          <div class="navbar-user" id="userMenuBtn">
            <img src="https://ui-avatars.com/api/?name=Admin&background=3cb371&color=fff" alt="Admin" class="user-avatar">
            <div class="user-info">
              <span class="user-name">Administrador</span>
              <span class="user-role">Admin</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
          </div>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <img src="https://ui-avatars.com/api/?name=Admin&background=3cb371&color=fff" alt="Admin" class="dropdown-avatar">
              <div>
                <div class="dropdown-name">Administrador</div>
                <div class="dropdown-email">admin@wegreen.com</div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="perfilAdmin.php">
              <i class="fas fa-user"></i>
              <span>Meu Perfil</span>
            </a>
            <a class="dropdown-item" href="alterarSenha.php">
              <i class="fas fa-key"></i>
              <span>Alterar Senha</span>
            </a>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item dropdown-item-danger" onclick="logout()">
              <i class="fas fa-sign-out-alt"></i>
              <span>Sair</span>
            </button>
          </div>
        </div>
      </nav>

      <div class="content-area">
      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-header">
            <h3><i class="fas fa-fire"></i>Produto anunciados</h3>
          </div>
          <canvas id="topProductsChart"></canvas>
        </div>
        <div class="chart-card">
          <div class="chart-header">
            <h3><i class="fas fa-star"></i>Produto Vendidos</h3>
          </div>
          <canvas id="salesChart"></canvas>
        </div>
      </div>
      <br>
      <br>
      <!-- Tabs de Navegação -->
      <div class="page-actions" style="margin-top: 25px;">
        <div class="actions-left">
          <button class="btn-add-product active" onclick="switchTab('todas-vendas')">
            <i class="fas fa-globe"></i> Todos os Produtos
          </button>
          <button class="btn-add-product" onclick="switchTab('Inativos')" style="background: #f59e0b;">
            <i class="fas fa-clock"></i> Verificar Produtos
          </button>
        </div>
      </div>

      <div id="Inativos" class="tab-content">
        <div class="table-container">
          <div class="chart-header" style="margin-bottom: 20px;">
            <h3><i class="fas fa-clock" style="color: #f59e0b; margin-right: 8px;"></i> Produtos Aguardando Verificação</h3>
            <p>Produtos que precisam ser aprovados antes de serem publicados</p>
          </div>
          <table id="inativosTable" class="display" style="width: 100%;">
            <thead>
              <tr>
                <th><i class="fas fa-hashtag"></i> ID</th>
                <th><i class="fas fa-image"></i> Foto</th>
                <th><i class="fas fa-tag"></i> Nome</th>
                <th><i class="fas fa-folder"></i> Categoria</th>
                <th><i class="fas fa-venus-mars"></i> Género</th>
                <th><i class="fas fa-euro-sign"></i> Total</th>
                <th><i class="fas fa-info-circle"></i> Estado</th>
                <th><i class="fas fa-cog"></i> Info</th>
              </tr>
            </thead>
            <tbody id="inativosBody">
              <tr>
                <td colspan="8" style="text-align: center; padding: 40px;">
                  <i class="fas fa-box" style="font-size: 48px; color: #cbd5e0; margin-bottom: 15px;"></i>
                  <p style="color: #718096;">Nenhum produto aguardando verificação</p>
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
>>>>>>> Stashed changes
            </div>

            <div id="Inativos" class="tab-content active">
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
            <div id="minhas-vendas" class="tab-content">
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
<<<<<<< Updated upstream
            <div class="modal fade" id="formEditInativo2" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
=======
            <button type="button" class="btn-primary" onclick="adicionarProdutos()" style="width: 100%;">
              <span>✅</span>
              Registar Produto
            </button>
          </form>
        </div>
      </div>
      <div id="todas-vendas" class="tab-content active">
        <div class="table-container">
          <div class="chart-header" style="margin-bottom: 20px;">
            <h3><i class="fas fa-globe" style="color: #3cb371; margin-right: 8px;"></i> Todos os Produtos</h3>
            <p>Visão completa de todos os produtos cadastrados na plataforma</p>
          </div>
          <table id="todasVendasTable" class="display" style="width: 100%;">
            <thead>
              <tr>
                <th><i class="fas fa-hashtag"></i> ID</th>
                <th><i class="fas fa-image"></i> Foto</th>
                <th><i class="fas fa-tag"></i> Nome</th>
                <th><i class="fas fa-folder"></i> Categoria</th>
                <th><i class="fas fa-user"></i> Vendedor</th>
                <th><i class="fas fa-euro-sign"></i> Total</th>
                <th><i class="fas fa-info-circle"></i> Estado</th>
                <th><i class="fas fa-copyright"></i> Marca</th>
                <th><i class="fas fa-cog"></i> Ação</th>
              </tr>
            </thead>
            <tbody id="todasVendasBody">
              <tr>
                <td colspan="9" style="text-align: center; padding: 40px;">
                  <i class="fas fa-box" style="font-size: 48px; color: #cbd5e0; margin-bottom: 15px;"></i>
                  <p style="color: #718096;">Carregando produtos...</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      </div>

      <div class="modal fade" id="formEditInativo2" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
>>>>>>> Stashed changes

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
                                <input type="text" class="form-control" id="nomeprodutoEdit2" >
                            </div>
                            <div class="info-item">
                                <label>Categoria</label>
                                <select name="" id="categoriaprodutoEdit2" >

                                </select>
                            </div>
                            <div class="info-item">
                                <label>Marca</label>
                                <input type="text" class="form-control" id="marcaprodutoEdit2" >
                            </div>
                            <div class="info-item">
                                <label>Tamanho</label>
                                <input type="text" class="form-control" id="tamanhoprodutoEdit2" >
                            </div>
                            <div class="info-item">
                                <label>Preço</label>
                                <input type="text" class="form-control" id="precoprodutoEdit2" >
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
                                <input type="text" class="form-control" id="nomeprodutoEdit" disabled>
                            </div>
                            <div class="info-item">
                                <label>Categoria</label>
                                <select name="" id="categoriaprodutoEdit" disabled>

                                </select>
                            </div>
                            <div class="info-item">
                                <label>Marca</label>
                                <input type="text" class="form-control" id="marcaprodutoEdit" disabled>
                            </div>
                            <div class="info-item">
                                <label>Tamanho</label>
                                <input type="text" class="form-control" id="tamanhoprodutoEdit" disabled>
                            </div>

                            <div class="info-item">
                                <label>Preço</label>
                                <input type="text" class="form-control" id="precoprodutoEdit" disabled>
                            </div>
                            <div class="info-item">
                                <label>Gênero</label>
                                <input type="text" class="form-control" id="generoprodutoEdit" disabled>
                            </div>
                            <div class="info-item">
                                <label>Vendedor</label>
                                <select name="" id="vendedorprodutoEdit" disabled>

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

<<<<<<< Updated upstream
                event.target.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            }
            </script>
            <script src="src/js/gestaoProdutos.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
=======
            <div class="photos-section" id="fotos-section2">

            </div>

            <div class="modal-actions">
              <button class="btn-approve" id="btnGuardar2">✅ Salvar Alterações</button>
            </div>

          </div>
        </div>
      </div>
      <div class="modal fade" id="formEditInativo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h2>🔍 Verificação de Produto</h2>
              <button class="modal-close" type="button" class="modal-close" data-bs-dismiss="modal">×</button>
            </div>
            <br>

            <div class="product-info-grid">
              <div class="info-item">
                <label>ID do Produto</label>
                <input type="text" class="form-control" id="numprodutoEdit" disabled>
              </div>
              <div class="info-item">
                <label>Nome do Produto</label>
                <input type="text" class="form-control" id="nomeprodutoEdit" disabled>
              </div>
              <div class="info-item">
                <label>Categoria</label>
                <select name="" id="categoriaprodutoEdit" disabled>

                </select>
              </div>
              <div class="info-item">
                <label>Marca</label>
                <input type="text" class="form-control" id="marcaprodutoEdit" disabled>
              </div>
              <div class="info-item">
                <label>Tamanho</label>
                <input type="text" class="form-control" id="tamanhoprodutoEdit" disabled>
              </div>

              <div class="info-item">
                <label>Preço</label>
                <input type="text" class="form-control" id="precoprodutoEdit" disabled>
              </div>
              <div class="info-item">
                <label>Gênero</label>
                <input type="text" class="form-control" id="generoprodutoEdit" disabled>
              </div>
              <div class="info-item">
                <label>Vendedor</label>
                <select name="" id="vendedorprodutoEdit" disabled>

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
        document.querySelectorAll('.btn-add-product').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
      }

      // User dropdown toggle
      const userMenuBtn = document.getElementById('userMenuBtn');
      const userDropdown = document.getElementById('userDropdown');

      if (userMenuBtn) {
          userMenuBtn.addEventListener('click', function(e) {
              e.stopPropagation();
              userDropdown.classList.toggle('active');
          });
      }

      // Fecha dropdown ao clicar fora
      document.addEventListener('click', function(e) {
          if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
              userDropdown?.classList.remove('active');
          }
      });

      // Função de logout
      function logout() {
          if (confirm('Tem certeza que deseja sair?')) {
              window.location.href = 'logout.php';
          }
      }
      </script>
      <script src="src/js/gestaoProdutos.js"></script>
>>>>>>> Stashed changes
</body>

</html>