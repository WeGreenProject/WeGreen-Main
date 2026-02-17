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
  <title>Meus Favoritos - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css">
  <link rel="stylesheet" href="src/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
  <script src="src/js/notifications.js"></script>
  <script src="src/js/custom/favoritos.js"></script>

  <link rel="stylesheet" href="src/css/favoritos.css">
</head>

<body>
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
          <a href="minhasEncomendas.php" class="menu-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Minhas Encomendas</span>
          </a>
          <a href="meusFavoritos.php" class="menu-item active">
            <i class="fas fa-heart"></i>
            <span>Meus Favoritos</span>
            <span class="badge" id="sidebarFavCount"
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
          <h1 class="page-title"><i class="fas fa-heart"></i> Meus Favoritos</h1>
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
          <!-- Botões de Ação -->
          <div style="margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 12px;">
            <button class="btn-limpar-inativos" onclick="limparInativos()">
              <i class="fas fa-broom"></i>
              <span>Limpar Inativos</span>
            </button>
            <button class="btn-continue-shopping" onclick="window.location.href='marketplace.html'">
              <i class="fas fa-shopping-cart"></i>
              <span>Continuar a Comprar</span>
            </button>
          </div>
          <!-- Filtros -->
          <div class="filtros-favoritos"
            style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 15px; align-items: end;">
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;">
                  <i class="fas fa-search" style="color: #3cb371; margin-right: 4px;"></i> Pesquisar
                </label>
                <input type="text" id="searchFavoritos" placeholder="Nome do produto..."
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;">
                  <i class="fas fa-filter" style="color: #3cb371; margin-right: 4px;"></i> Categoria
                </label>
                <select id="filterCategoria"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todas</option>
                  <option value="Roupa">Roupa</option>
                  <option value="Calçado">Calçado</option>
                  <option value="Acessórios">Acessórios</option>
                  <option value="Artesanato">Artesanato</option>
                </select>
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;">
                  <i class="fas fa-tag" style="color: #3cb371; margin-right: 4px;"></i> Disponibilidade
                </label>
                <select id="filterDisponibilidade"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todos</option>
                  <option value="disponivel">Disponíveis</option>
                  <option value="indisponivel">Indisponíveis</option>
                </select>
              </div>
              <button class="btn-clear-filters" onclick="limparFiltrosFavoritos()"
                style="padding: 10px 20px; background: #3cb371; border: none; border-radius: 8px; cursor: pointer; color: #ffffff; font-size: 16px; display: flex; align-items: center; justify-content: center; height: 42px; box-shadow: 0 2px 8px rgba(60, 179, 113, 0.3); transition: all 0.3s ease;"
                onmouseover="this.style.background='#2ea05f'" onmouseout="this.style.background='#3cb371'">
                <i class="fas fa-redo"></i>
              </button>
            </div>
          </div>

          <!-- Grid de Favoritos -->
          <div class="favoritos-grid" id="favoritosGrid">
            <!-- Carregado via JavaScript -->
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="src/js/alternancia.js"></script>
  <script src="src/js/meusFavoritos.js"></script>
</body>

</html>
