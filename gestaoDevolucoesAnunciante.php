<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){

$user_plano_id = (int)($_SESSION['plano'] ?? 1);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Devoluções - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/gestaoProdutos.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/modalProduto.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/lib/datatables.css">
  <link rel="stylesheet" href="src/css/lib/select2.css">
  <link rel="stylesheet" href="src/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/datatables.js"></script>
  <script src="src/js/lib/select2.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <script src="src/js/notifications.js"></script>
  <!-- Sistema de Devoluções -->
  <script src="src/js/custom/devolucoes.js"></script>
  <script src="src/js/Anunciante.js"></script>
</head>

<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <a href="index.html" class="sidebar-logo" style="text-decoration: none; color: inherit; cursor: pointer;">
        <i class="fas fa-leaf"></i>
        <div class="logo-text">
          <h2>WeGreen</h2>
          <p>Moda Sustentável</p>
        </div>
      </a>

      <nav class="sidebar-menu">
        <div class="menu-section">
          <div class="menu-section-title">Menu</div>
          <a href="DashboardAnunciante.php" class="menu-item">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
          </a>
          <a href="gestaoProdutosAnunciante.php" class="menu-item">
            <i class="fas fa-tshirt"></i>
            <span>Produtos</span>
          </a>
          <a href="gestaoEncomendasAnunciante.php" class="menu-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Encomendas</span>
          </a>
          <a href="gestaoDevolucoesAnunciante.php" class="menu-item active">
            <i class="fas fa-undo"></i>
            <span>Devoluções</span>
          </a>
          <a href="ChatAnunciante.php" class="menu-item">
            <i class="fas fa-comments"></i>
            <span>Chat</span>
          </a>
        </div>
      </nav>
    </aside>

    <main class="main-content">
      <nav class="top-navbar">
        <div class="navbar-left">
          <h1 class="page-title"><i class="fas fa-undo"></i> Gestão de Devoluções</h1>
        </div>
        <div class="navbar-right">
          <?php include 'src/views/notifications-widget.php'; ?>
          <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'"
            style="<?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'display: none;' : 'display: inline-flex;'; ?>">
            <i class="fas fa-crown"></i> Upgrade
          </button>
          <div class="navbar-user" id="userMenuBtn">
            <img
              src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff"
              alt="Usuário" class="user-avatar">
            <div class="user-info">
              <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></span>
              <span class="user-role">Anunciante</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
          </div>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff"
                alt="Usuário" class="dropdown-avatar">
              <div>
                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></div>
                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'user@email.com'; ?></div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="perfilAnunciante.php">
              <i class="fas fa-user"></i>
              <span>Meu Perfil</span>
            </a>
            <a class="dropdown-item" href="alterarSenha.php">
              <i class="fas fa-key"></i>
              <span>Alterar Senha</span>
            </a>
            <button class="dropdown-item" id="btnAlternarConta" onclick="verificarEAlternarConta()" style="display:none;">
              <i class="fas fa-exchange-alt"></i>
              <span id="textoAlternar">Alternar Conta</span>
            </button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item dropdown-item-danger" onclick="logout()">
              <i class="fas fa-sign-out-alt"></i>
              <span>Sair</span>
            </button>
          </div>
        </div>
      </nav>

      <div class="content-area">
        <div id="devolucoes" class="page active">
          <div class="page-actions">
            <div class="actions-left">
            </div>
            <div class="actions-right">
              <button id="exportDevolucoesBtn" class="btn-export-pdf" style="display:none;">
                <i class="fas fa-file-pdf"></i>
                <span>Exportar PDF</span>
              </button>
            </div>
          </div>

          <div class="stats-grid-compact" id="devolucoesStats">
            <div class="stat-card-compact" id="statPendentes"></div>
            <div class="stat-card-compact" id="statAprovadas"></div>
            <div class="stat-card-compact" id="statRejeitadas"></div>
            <div class="stat-card-compact" id="statReembolsadas"></div>
          </div>

          <div class="filters-container">
            <div class="filters-grid">
              <div class="filter-item">
                <label>
                  <i class="fas fa-search"></i> Pesquisar
                </label>
                <input type="text" id="filterPesquisa" placeholder="Código, cliente ou produto..." class="filter-input"
                  onkeyup="filtrarDevolucoes()">
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-toggle-on"></i> Estado
                </label>
                <select id="filterEstadoDevolucao" onchange="filtrarDevolucoes()" class="filter-select">
                  <option value="">Todos os Estados</option>
                  <option value="solicitada">Pendentes</option>
                  <option value="aprovada">Aprovadas</option>
                  <option value="rejeitada">Rejeitadas</option>
                  <option value="produto_recebido">Produto Recebido</option>
                  <option value="reembolsada">Reembolsadas</option>
                  <option value="cancelada">Canceladas</option>
                </select>
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-comment-alt"></i> Motivo
                </label>
                <select id="filterMotivo" onchange="filtrarDevolucoes()" class="filter-select">
                  <option value="">Todos os Motivos</option>
                  <option value="defeituoso">Defeituoso</option>
                  <option value="tamanho_errado">Tamanho Errado</option>
                  <option value="nao_como_descrito">Não Conforme</option>
                  <option value="arrependimento">Arrependimento</option>
                  <option value="outro">Outro</option>
                </select>
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-calendar-alt"></i> Data Inicial
                </label>
                <input type="date" id="filterDataInicial" class="filter-input" onchange="filtrarDevolucoes()">
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-calendar-alt"></i> Data Final
                </label>
                <input type="date" id="filterDataFinal" class="filter-input" onchange="filtrarDevolucoes()">
              </div>
              <div class="filter-item-button">
                <button class="btn-clear-filters" onclick="limparFiltros()">
                  <i class="fas fa-redo"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="table-container">
            <table id="tabelaDevolucoes" class="display">
              <thead>
                <tr>
                  <th><i class="fas fa-hashtag"></i> Código Devolução</th>
                  <th><i class="fas fa-shopping-bag"></i> Encomenda</th>
                  <th><i class="fas fa-box"></i> Produto</th>
                  <th><i class="fas fa-user"></i> Cliente</th>
                  <th><i class="fas fa-comment-alt"></i> Motivo</th>
                  <th><i class="fas fa-euro-sign"></i> Valor</th>
                  <th><i class="fas fa-calendar-alt"></i> Data</th>
                  <th><i class="fas fa-info-circle"></i> Estado</th>
                  <th><i class="fas fa-cog"></i> Ações</th>
                </tr>
              </thead>
              <tbody>
                <!-- Populated by JavaScript -->
              </tbody>
            </table>
          </div>
        </div>

    </main>
  </div>

  <script src="src/js/gestaoDevolucoesAnunciante.js"></script>
  <script src="src/js/alternancia.js"></script>
</body>

</html>

<?php
}else{
    echo "Sem permissão!";
}
?>
