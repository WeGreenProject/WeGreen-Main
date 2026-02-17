<?php
session_start();

if(!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minhas Encomendas - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css">
  <link rel="stylesheet" href="src/css/notifications-dropdown.css">
  <link rel="stylesheet" href="src/css/lib/datatables.css">
  <link rel="stylesheet" href="src/css/lib/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/datatables.js"></script>
  <script src="src/js/lib/select2.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
  <script src="src/js/notifications.js"></script>
  <!-- jsPDF para gerar PDFs -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <!-- Sistema de Devoluções -->
  <script src="src/js/custom/devolucoes.js"></script>

  <link rel="stylesheet" href="src/css/minhasEncomendas.css">
</head>

<body data-user-nome="<?php echo $_SESSION['nome'] ?? ''; ?>">
  <div class="dashboard-container">
    <!-- Sidebar -->
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
          <a href="DashboardCliente.php" class="menu-item">
            <i class="fas fa-home"></i>
            <span>Início</span>
          </a>
          <a href="minhasEncomendas.php" class="menu-item active">
            <i class="fas fa-shopping-bag"></i>
            <span>Minhas Encomendas</span>
          </a>
          <a href="meusFavoritos.php" class="menu-item">
            <i class="fas fa-heart"></i>
            <span>Meus Favoritos</span>
            <span class="badge" id="favoritosBadge"
              style="display:none; background:#3cb371; color:white; padding:2px 8px; border-radius:10px; font-size:11px; margin-left:auto;"></span>
          </a>
          <a href="ChatCliente.php" class="menu-item">
            <i class="fas fa-comments"></i>
            <span>Chat</span>
          </a>
        </div>
      </nav>
    </aside>

    <!-- Conteúdo Principal -->
    <main class="main-content">
      <nav class="top-navbar">
        <div class="navbar-left">
          <h1 class="page-title"><i class="fas fa-shopping-bag"></i> Minhas Encomendas</h1>
        </div>
        <div class="navbar-right">
          <?php include 'src/views/notifications-widget.php'; ?>
          <div class="navbar-user" id="userMenuBtn">
            <img
              src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff"
              alt="User" class="user-avatar">
            <div class="user-info">
              <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></span>
              <span class="user-role">Cliente</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
          </div>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff"
                alt="User" class="dropdown-avatar">
              <div>
                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></div>
                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? ''; ?></div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="perfilCliente.php">
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
      <div class="page-content">
        <div class="content-area" style="padding-top: 20px;">
          <!-- Botão Continuar a Comprar -->
          <div style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
            <button class="btn-continue-shopping" onclick="window.location.href='marketplace.html'"
              style="padding: 12px 24px; background: #3cb371; color: white; border: none; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 500; box-shadow: 0 2px 8px rgba(60,179,113,0.3); transition: all 0.3s ease;"
              onmouseover="this.style.background='#2ea05f'" onmouseout="this.style.background='#3cb371'">
              <i class="fas fa-shopping-cart"></i>
              <span>Continuar a Comprar</span>
            </button>
          </div>
          <!-- Barra de Pesquisa e Filtros -->
          <div class="filters-container"
            style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;">
                  <i class="fas fa-search" style="color: #3cb371; margin-right: 4px;"></i> Pesquisar Produto ou Nº Encomenda
                </label>
                <input type="text" id="searchProduct" placeholder="Nome do produto ou código da encomenda..."
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;"><i
                    class="fas fa-toggle-on" style="color: #3cb371; margin-right: 4px;"></i> Status</label>
                <select id="filterStatus" class="filter-select"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todos</option>
                  <option value="pendente">Pendente</option>
                  <option value="processando">Processando</option>
                  <option value="enviado">Enviado</option>
                  <option value="entregue">Entregue</option>
                  <option value="devolvido">Devolvido</option>
                  <option value="cancelado">Cancelado</option>
                </select>
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;"><i
                    class="fas fa-calendar-alt" style="color: #3cb371; margin-right: 4px;"></i> Período</label>
                <select id="filterPeriod"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todo o período</option>
                  <option value="30">Últimos 30 dias</option>
                  <option value="90">Últimos 3 meses</option>
                  <option value="180">Últimos 6 meses</option>
                  <option value="365">Último ano</option>
                </select>
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;"><i
                    class="fas fa-sort" style="color: #3cb371; margin-right: 4px;"></i> Ordenar</label>
                <select id="sortBy" style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="date-desc">Mais recentes</option>
                  <option value="date-asc">Mais antigas</option>
                  <option value="value-desc">Maior valor</option>
                  <option value="value-asc">Menor valor</option>
                </select>
              </div>
              <button class="btn-clear-filters" onclick="limparFiltros()"
                style="padding: 10px 20px; background: #3cb371; border: none; border-radius: 8px; cursor: pointer; color: #ffffff; font-size: 16px; display: flex; align-items: center; justify-content: center; height: 42px; box-shadow: 0 2px 8px rgba(60, 179, 113, 0.3); transition: all 0.3s ease;"
                onmouseover="this.style.background='#2ea05f'" onmouseout="this.style.background='#3cb371'">
                <i class="fas fa-redo"></i>
              </button>
            </div>
          </div>

          <!-- Grid de Encomendas (Cards) -->
          <div id="encomendasGrid" style="display: grid; gap: 20px;">
            <!-- Cards gerados dinamicamente -->
          </div>

          <!-- Mensagem vazia -->
          <div id="emptyState"
            style="display: none; text-align: center; padding: 60px; background: #fff; border-radius: 12px;">
            <i class="fas fa-shopping-bag" style="font-size: 64px; color: #e0e0e0; margin-bottom: 20px;"></i>
            <h3 style="color: #999;">Nenhuma encomenda encontrada</h3>
            <p style="color: #999;">Ainda não realizou nenhuma compra ou não há encomendas com os filtros aplicados
            </p>
            <button class="btn-primary" onclick="window.location.href='marketplace.html'"
              style="margin-top: 20px; padding: 12px 24px; background: #3cb371; color: white; border: none; border-radius: 8px; cursor: pointer;">
              <i class="fas fa-shopping-cart"></i> Começar a Comprar
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Detalhes da Encomenda -->
      <div id="detalhesModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 750px;">
          <div class="modal-header"
            style="background: #3cb371; color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: white; font-size: 20px;"><i class="fas fa-box"
                style="margin-right: 10px;"></i>Detalhes da Encomenda</h3>
            <span class="close" onclick="fecharModal()"
              style="color: white; font-size: 28px; font-weight: bold; cursor: pointer; background: rgba(255,255,255,0.2); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s;"
              onmouseover="this.style.background='rgba(255,255,255,0.3)'"
              onmouseout="this.style.background='rgba(255,255,255,0.2)'">&times;</span>
          </div>
          <div class="modal-body" id="detalhesContent" style="padding: 0;">
            <!-- Conteúdo dinâmico -->
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="src/js/minhasEncomendas.js"></script>

  <script src="src/js/alternancia.js"></script>
</body>

</html>
