<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
  <script src="src/js/notifications.js"></script>
  <link rel="stylesheet" href="src/css/perfilAdmin.css?v=<?php echo time(); ?>">
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
          <a href="DashboardAdmin.php" class="menu-item">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
          </a>
          <a href="gestaoProdutosAdmin.php" class="menu-item">
            <i class="fas fa-tshirt"></i>
            <span>Produtos</span>
          </a>
          <a href="gestaoCliente.php" class="menu-item">
            <i class="fas fa-users"></i>
            <span>Utilizadores</span>
          </a>
          <a href="gestaoComentarios.php" class="menu-item">
            <i class="fas fa-comment-dots"></i>
            <span>Comentários</span>
          </a>
          <a href="gestaoLucros.php" class="menu-item">
            <i class="fas fa-euro-sign"></i>
            <span>Lucros</span>
          </a>
          <a href="logAdmin.php" class="menu-item">
            <i class="fas fa-history"></i>
            <span>Logs do Sistema</span>
          </a>
          <a href="Chatadmin.php" class="menu-item">
            <i class="fas fa-comments"></i>
            <span>Chat</span>
          </a>
        </div>
      </nav>
    </aside>

    <main class="main-content">
      <nav class="top-navbar">
        <div class="navbar-left">
          <h1 class="page-title"><i class="fas fa-user"></i> Meu Perfil</h1>
        </div>
        <div class="navbar-right">
          <?php include 'src/views/notifications-widget.php'; ?>
          <div class="navbar-user" id="userMenuBtn">
            <img
              src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff"
              alt="Administrador" class="user-avatar">
            <div class="user-info">
              <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></span>
              <span class="user-role">Administrador</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
          </div>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff"
                alt="Administrador" class="dropdown-avatar">
              <div>
                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></div>
                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'admin@wegreen.com'; ?></div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="perfilAdmin.php" style="background: #f8f9fa;">
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
        <div class="content-area">
          <!-- Header do Perfil -->
          <div class="profile-header-card">
            <div class="profile-avatar-large">
              <img id="avatarPreview"
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff"
                alt="Perfil">
            </div>
            <div class="profile-header-info">
              <div class="profile-header-left">
                <h1><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></h1>
                <span class="role-badge"><i class="fas fa-shield-alt"></i> Administrador</span>
              </div>
            </div>
          </div>

          <!-- Informações Pessoais -->
          <div class="profile-section">
            <div class="section-header">
              <i class="fas fa-user"></i><h3>Informações Pessoais</h3>
            </div>
            <form id="profileForm">
              <div class="info-grid">
                <div class="info-item">
                  <label>Nome Completo</label>
                  <input type="text" id="nome" placeholder="Nome Completo" required>
                </div>

                <div class="info-item">
                  <label>Email</label>
                  <input type="email" id="email" placeholder="exemplo@email.com" required>
                </div>

                <div class="info-item">
                  <label>NIF</label>
                  <input type="text" id="nif" placeholder="000000000" maxlength="9">
                </div>

                <div class="info-item">
                  <label>Telefone</label>
                  <input type="text" id="telefone" placeholder="900000000" maxlength="9">
                </div>
              </div>

              <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Guardar Alterações
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="src/js/perfilAdmin.js"></script>
</body>
</html>
<?php
}else{
    echo "sem permissão!";
}
?>
