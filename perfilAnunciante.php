<?php
session_start();

if(!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])){
    header("Location: login.html");
    exit();
}

if($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 3){
  header("Location: forbiddenerror.html");
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
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css">
  <link rel="stylesheet" href="src/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
  <script src="src/js/notifications.js"></script>
  <link rel="stylesheet" href="src/css/perfilAnunciante.css">
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
          <a href="gestaoDevolucoesAnunciante.php" class="menu-item">
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

    <!-- Conteúdo Principal -->
    <main class="main-content">
      <nav class="top-navbar">
        <div class="navbar-left">
          <h1 class="page-title"><i class="fas fa-user"></i> Meu Perfil</h1>
        </div>
        <div class="navbar-right">
          <?php include 'src/views/notifications-widget.php'; ?>
          <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'" style="<?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'display: none;' : 'display: inline-flex;'; ?>">
            <i class="fas fa-crown"></i> Upgrade
          </button>
          <div class="navbar-user" id="userMenuBtn">
            <img
              src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Anunciante'); ?>&background=3cb371&color=fff"
              alt="User" class="user-avatar">
            <div class="user-info">
              <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Anunciante'; ?></span>
              <span class="user-role">Anunciante</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
          </div>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Anunciante'); ?>&background=3cb371&color=fff"
                alt="User" class="dropdown-avatar">
              <div>
                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Anunciante'; ?></div>
                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? ''; ?></div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="perfilAnunciante.php" style="background: #f8f9fa;">
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

      <div class="page-content">
        <div class="content-area">
          <!-- Header do Perfil -->
          <div class="profile-header-card" id="profileHeader">
            <div class="profile-avatar-large">
              <img id="avatarImg"
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Anunciante'); ?>&background=3cb371&color=fff"
                alt="Perfil">
              <button class="avatar-edit-btn" onclick="document.getElementById('avatarInput').click()">
                <i class="fas fa-camera"></i>
              </button>
              <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="uploadAvatar(this)">
            </div>
            <div class="profile-header-info">
              <div class="profile-header-left">
                <h1 id="profileName"><?php echo $_SESSION['nome'] ?? 'Anunciante'; ?></h1>
                <span class="role-badge"><i class="fas fa-store"></i> Anunciante</span>
                <div class="profile-plano-badge" id="planoBadge">
                  <i class="fas fa-crown"></i>
                  <span id="planoNome">Carregando...</span>
                </div>
                <div class="profile-ranking-badge" id="rankingBadge" style="display: none;">
                  <i class="fas fa-leaf"></i>
                  <span id="rankingNome">--</span>
                </div>
                <!-- Contador de dias restantes do plano -->
                <div class="profile-plan-expiration" id="planExpirationContainer" style="display: none;">
                  <i class="fas fa-clock"></i>
                  <span id="diasRestantes">--</span>
                </div>
              </div>
              <div class="trust-points-card">
                <div class="trust-icon">
                  <i class="fas fa-star"></i>
                </div>
                <div class="trust-info">
                  <h4>Pontos de Confiança</h4>
                  <div class="trust-score" id="pontosConfianca">0</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabs -->
          <div class="profile-tabs" style="display: none;">
            <button class="profile-tab active" onclick="switchTab('personal')">
              <i class="fas fa-user"></i> Informações Pessoais
            </button>
          </div>

          <!-- Tab Content -->
          <div class="profile-tab-content">
            <!-- Tab Informações Pessoais -->
            <div id="tab-personal" class="tab-pane active">
              <div class="profile-section">
                <div class="section-header">
                  <i class="fas fa-user"></i><h3>Informações Pessoais</h3>
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
        </div>
      </div>
    </main>
  </div>

  <script src="src/js/perfilAnunciante.js"></script>
  <script src="src/js/alternancia.js"></script>
</body>

</html>
