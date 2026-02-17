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
  <title>Meu Perfil - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css">
  <link rel="stylesheet" href="src/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
  <script src="src/js/notifications.js"></script>
  <link rel="stylesheet" href="src/css/perfilCliente.css">
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
          <a href="meusFavoritos.php" class="menu-item">
            <i class="fas fa-heart"></i>
            <span>Meus Favoritos</span>
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
          <h1 class="page-title"><i class="fas fa-user"></i> Meu Perfil</h1>
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
            <a class="dropdown-item" href="perfilCliente.php" style="background: #f8f9fa;">
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
              <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff"
                alt="Perfil">
            </div>
            <div class="profile-header-info">
              <div class="profile-header-left">
                <h1><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></h1>
                <span class="role-badge"><i class="fas fa-shopping-bag"></i> Cliente</span>
              </div>
            </div>
          </div>

          <!-- Formulário de Informações -->
          <div class="profile-section">
            <div class="section-header">
              <i class="fas fa-user"></i>
              <h3>Informações Pessoais</h3>
            </div>
            <form id="profileForm">
              <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                <div class="info-item">
                  <label>Nome Completo</label>
                  <input type="text" id="nome" placeholder="Nome Completo" required>
                </div>
                <div class="info-item">
                  <label>Email</label>
                  <input type="email" id="email" placeholder="exemplo@email.com" required>
                </div>
                <div class="info-item">
                  <label>Telefone</label>
                  <input type="text" id="telefone" placeholder="900000000" maxlength="9">
                </div>
                <div class="info-item">
                  <label>NIF</label>
                  <input type="text" id="nif" placeholder="000000000" maxlength="9">
                </div>
              </div>
              <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                <div class="info-item">
                  <label>Morada Completa</label>
                  <input type="text" id="morada" placeholder="Rua, Número" required>
                </div>
                <div class="info-item">
                  <label>Código Postal</label>
                  <input type="text" id="codigo_postal" placeholder="XXXX-XXX" maxlength="8">
                </div>
              </div>
              <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                <div class="info-item">
                  <label>Distrito</label>
                  <select id="distrito" class="form-control">
                    <option value="">Selecione o distrito</option>
                    <option value="Aveiro">Aveiro</option>
                    <option value="Beja">Beja</option>
                    <option value="Braga">Braga</option>
                    <option value="Bragança">Bragança</option>
                    <option value="Castelo Branco">Castelo Branco</option>
                    <option value="Coimbra">Coimbra</option>
                    <option value="Évora">Évora</option>
                    <option value="Faro">Faro</option>
                    <option value="Guarda">Guarda</option>
                    <option value="Leiria">Leiria</option>
                    <option value="Lisboa">Lisboa</option>
                    <option value="Portalegre">Portalegre</option>
                    <option value="Porto">Porto</option>
                    <option value="Santarém">Santarém</option>
                    <option value="Setúbal">Setúbal</option>
                    <option value="Viana do Castelo">Viana do Castelo</option>
                    <option value="Vila Real">Vila Real</option>
                    <option value="Viseu">Viseu</option>
                    <option value="Região Autónoma da Madeira">Região Autónoma da Madeira</option>
                    <option value="Região Autónoma dos Açores">Região Autónoma dos Açores</option>
                  </select>
                </div>
                <div class="info-item">
                  <label>Localidade</label>
                  <input type="text" id="localidade" placeholder="Cidade ou Vila">
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

  <script src="src/js/perfilCliente.js"></script>
</body>

</html>
