<?php
session_start();

if(!isset($_SESSION['utilizador']) || ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 3)){
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
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css">
  <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="src/js/notifications.js"></script>
  <style>
  .profile-header-card {
    background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
    border-radius: 16px;
    padding: 40px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  }

  .profile-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid #3cb371;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(60, 179, 113, 0.3);
    position: relative;
  }

  .profile-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .avatar-edit-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 36px;
    height: 36px;
    background: #3cb371;
    border: 3px solid #2d3748;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    font-size: 14px;
    transition: all 0.3s;
  }

  .avatar-edit-btn:hover {
    background: #2e8b57;
    transform: scale(1.1);
  }

  .profile-header-info {
    flex: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .profile-header-left h1 {
    font-size: 32px;
    color: #ffffff;
    margin-bottom: 8px;
  }

  .role-badge {
    background: rgba(60, 179, 113, 0.2);
    color: #3cb371;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: inline-block;
    border: 1px solid #3cb371;
  }



  .profile-plano-badge {
    background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
    color: white;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);
  }

  .profile-plan-expiration {
    background: rgba(60, 179, 113, 0.15);
    color: #2e8b57;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    border: 1px solid rgba(60, 179, 113, 0.3);
  }

  .profile-plan-expiration.warning {
    background: rgba(251, 191, 36, 0.15);
    color: #d97706;
    border-color: rgba(251, 191, 36, 0.3);
  }

  .profile-plan-expiration.critical {
    background: rgba(220, 38, 38, 0.15);
    color: #dc2626;
    border-color: rgba(220, 38, 38, 0.3);
  }

  .profile-plan-expiration i {
    font-size: 14px;
  }

  .trust-points-card {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(60, 179, 113, 0.3);
    border-radius: 12px;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 200px;
  }

  .trust-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3cb371, #2e8b57);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 22px;
  }

  .trust-info h4 {
    margin: 0;
    color: #ffffff;
    font-size: 14px;
    font-weight: 500;
    opacity: 0.9;
  }

  .trust-score {
    font-size: 28px;
    font-weight: 700;
    color: #3cb371;
    margin: 0;
  }

  .profile-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid #e2e8f0;
  }

  .profile-tab {
    background: transparent;
    border: none;
    padding: 14px 24px;
    font-size: 15px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .profile-tab:hover {
    color: #3cb371;
  }

  .profile-tab.active {
    color: #3cb371;
    border-bottom-color: #3cb371;
  }

  .profile-tab-content {
    min-height: 400px;
  }

  .tab-pane {
    display: none;
  }

  .tab-pane.active {
    display: block;
  }

  .profile-section {
    background: #ffffff;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  }

  .section-header {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 8px !important;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e2e8f0;
  }

  .section-header h3 {
    font-size: 20px;
    color: #1a1a1a;
    font-weight: 600;
    margin: 0 !important;
    padding: 0 !important;
  }

  .section-header i {
    color: #3cb371;
    font-size: 20px;
    margin: 0 !important;
    padding: 0 !important;
  }

  .info-item {
    margin-bottom: 24px;
  }

  .info-item label {
    display: block;
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }

  .info-item input,
  .info-item select,
  .info-item textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
    background-color: white;
    font-family: inherit;
  }

  .info-item textarea {
    resize: vertical;
    min-height: 100px;
  }

  .info-item input:focus,
  .info-item select:focus,
  .info-item textarea:focus {
    outline: none;
    border-color: #3cb371;
    box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.1);
  }

  .btn-save {
    background: linear-gradient(135deg, #3cb371, #2e8b57);
    color: white;
    border: none;
    padding: 16px 32px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 20px;
    width: 100%;
  }

  .btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);
  }

  .plano-card {
    background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
    border-radius: 16px;
    padding: 30px;
    color: white;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
  }

  .plano-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
  }

  .plano-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
  }

  .plano-name {
    font-size: 24px;
    font-weight: 700;
  }

  .plano-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
  }

  .plano-features {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    position: relative;
    z-index: 1;
  }

  .plano-features li {
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .plano-features i {
    color: #fff;
  }

  .btn-upgrade {
    background: white;
    color: #3cb371;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    z-index: 1;
  }

  .btn-upgrade:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }

  .security-item {
    display: flex;
    gap: 20px;
    padding: 24px;
    background: #f8f9fa;
    border-radius: 12px;
    margin-bottom: 20px;
  }

  .security-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #3cb371, #2e8b57);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
  }

  .security-info {
    flex: 1;
  }

  .security-info h4 {
    font-size: 16px;
    color: #1a1a1a;
    margin-bottom: 6px;
  }

  .security-info p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
  }

  .btn-secondary {
    background: #64748b;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-left: auto;
  }

  .btn-secondary:hover {
    background: #475569;
    transform: translateY(-2px);
  }

  .page-content {
    overflow-x: hidden;
  }

  .profile-header-card {
    padding: 25px 35px !important;
    margin-bottom: 20px !important;
  }

  .profile-avatar-large {
    width: 90px !important;
    height: 90px !important;
  }

  .profile-header-left h1 {
    font-size: 26px !important;
    margin-bottom: 6px !important;
  }

  .profile-section {
    padding: 30px !important;
  }

  .section-header {
    margin-bottom: 24px !important;
    padding-bottom: 16px !important;
  }

  .info-item {
    margin-bottom: 18px !important;
  }

  .info-item input,
  .info-item select,
  .info-item textarea {
    padding: 12px 16px !important;
  }

  .btn-save {
    padding: 14px 28px !important;
    margin-top: 18px !important;
  }
  </style>
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
          <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'" style="display: none;" <?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
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
                  <div class="info-item">
                    <label>Morada Completa</label>
                    <input type="text" id="morada" placeholder="Rua, Número, Código Postal" required>
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

  <script>
  // Dropdown toggle
  $("#userMenuBtn").click(function(e) {
    e.stopPropagation();
    $("#userDropdown").toggleClass("active");
  });

  $(document).click(function(e) {
    if (!$(e.target).closest('.navbar-user').length) {
      $("#userDropdown").removeClass("active");
    }
  });

  // Switch tabs
  function switchTab(tabName) {
    $('.profile-tab').removeClass('active');
    $('.tab-pane').removeClass('active');

    event.target.closest('.profile-tab').classList.add('active');
    $(`#tab-${tabName}`).addClass('active');
  }

  // Carregar dados do perfil ao iniciar
  $(document).ready(function() {
    carregarPerfilAnunciante();
    verificarPlanoUpgrade();
    carregarInfoExpiracaoPlano();
  });

  function carregarInfoExpiracaoPlano() {
    $.ajax({
      url: 'src/controller/controllerDashboardAnunciante.php',
      type: 'POST',
      data: { op: 38 },
      success: function(response) {
        try {
          const data = typeof response === 'string' ? JSON.parse(response) : response;

          if (data.success && data.dias_restantes !== null) {
            const container = $('#planExpirationContainer');
            const diasSpan = $('#diasRestantes');
            const dias = data.dias_restantes;

            if (dias <= 0) {
              diasSpan.text('Plano expirado');
              container.removeClass('warning').addClass('critical');
              container.show();
            } else if (dias <= 3) {
              diasSpan.text(`${dias} dias restantes - Renove agora!`);
              container.removeClass('warning').addClass('critical');
              container.show();
            } else if (dias <= 7) {
              diasSpan.text(`${dias} dias restantes`);
              container.addClass('warning').removeClass('critical');
              container.show();
            } else {
              diasSpan.text(`${dias} dias restantes`);
              container.removeClass('warning critical');
              container.show();
            }
          }
        } catch (e) {
          console.error('Erro ao carregar info de expiracao:', e);
        }
      },
      error: function() {
        console.error('Erro ao buscar info de expiracao do plano');
      }
    });
  }

  function carregarPerfilAnunciante() {
    $.ajax({
      url: 'src/controller/controllerDashboardAnunciante.php',
      type: 'POST',
      data: { op: 27 },
      success: function(response) {
        console.log('Resposta bruta:', response);

        try {
          const dados = typeof response === 'string' ? JSON.parse(response) : response;
          console.log('Dados parseados:', dados);

          if (dados.error) {
            console.error('Erro:', dados.error);
            return;
          }

          // Preencher os campos com os dados do usuário
          if (dados.nome_completo || dados.nome) {
            $('#nome').val(dados.nome_completo || dados.nome);
            $('#profileName').text(dados.nome_completo || dados.nome);
          }
          if (dados.email) {
            $('#email').val(dados.email);
            $('#emailRecuperacao').text(dados.email);
          }
          if (dados.telefone) {
            $('#telefone').val(dados.telefone);
          }
          if (dados.nif) {
            $('#nif').val(dados.nif);
          }
          if (dados.morada) {
            $('#morada').val(dados.morada);
          }
          if (dados.distrito) {
            $('#distrito').val(dados.distrito);
          }
          if (dados.localidade) {
            $('#localidade').val(dados.localidade);
          }

          // Pontos de confiança
          if (dados.pontos_conf !== undefined) {
            $('#pontosConfianca').text(dados.pontos_conf);
          }

          // Plano
          if (dados.plano_nome) {
            $('#planoNome').text(dados.plano_nome);
          }

          // Foto
          if (dados.foto && dados.foto !== 'src/img/default_user.png') {
            $('#avatarImg').attr('src', dados.foto);
          }

          console.log('Perfil carregado com sucesso');
        } catch (e) {
          console.error('Erro ao processar resposta:', e);
          console.error('Resposta recebida:', response);
        }
      },
      error: function(xhr, status, error) {
        console.error('Erro AJAX:', error);
      }
    });
  }



  function verificarPlanoUpgrade() {
    $.post('src/controller/controllerDashboardAnunciante.php', { op: 27 }, function(resp) {
      const dados = JSON.parse(resp);
      if (dados && dados.plano_nome !== "Plano Profissional Eco+") {
        $("#upgradeBtn").show();
      } else {
        $("#upgradeBtn").hide();
      }
    });
  }

  // Form submission - Informações Pessoais
  $("#profileForm").submit(function(e) {
    e.preventDefault();

    const dados = {
      op: 28, // Operação para atualizar perfil anunciante
      nome: $('#nome').val().trim(),
      email: $('#email').val().trim(),
      telefone: $('#telefone').val().trim(),
      nif: $('#nif').val().trim(),
      morada: $('#morada').val().trim(),
      distrito: $('#distrito').val().trim(),
      localidade: $('#localidade').val().trim()
    };

    // Validação básica
    if (!dados.nome || dados.nome.length < 3) {
      return Swal.fire('Atenção', 'Nome deve ter no mínimo 3 caracteres', 'warning');
    }

    if (!dados.email || !dados.email.includes('@')) {
      return Swal.fire('Atenção', 'Email inválido', 'warning');
    }

    if (!dados.morada || dados.morada.trim().length < 10) {
      return Swal.fire('Atenção', 'Morada completa é obrigatória (mínimo 10 caracteres)', 'warning');
    }

    Swal.fire({
      title: 'Guardar alterações?',
      text: 'As suas informações de perfil serão atualizadas',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3cb371',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Sim, guardar!',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('src/controller/controllerDashboardAnunciante.php', dados, function(resp) {
          const resultado = JSON.parse(resp);
          if (resultado.success) {
            Swal.fire({
              icon: 'success',
              title: 'Perfil atualizado!',
              text: resultado.message,
              confirmButtonColor: '#3cb371',
              timer: 2000
            }).then(() => {
              carregarPerfilAnunciante();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Erro',
              text: resultado.message,
              confirmButtonColor: '#3cb371'
            });
          }
        }).fail(function() {
          Swal.fire('Erro', 'Não foi possível atualizar o perfil', 'error');
        });
      }
    });
  });



  function uploadAvatar(input) {
    if (input.files && input.files[0]) {
      const file = input.files[0];

      // Validar tipo de arquivo
      if (!file.type.match('image.*')) {
        return Swal.fire('Erro', 'Por favor selecione uma imagem válida', 'error');
      }

      // Validar tamanho (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        return Swal.fire('Erro', 'A imagem deve ter no máximo 5MB', 'error');
      }

      const formData = new FormData();
      formData.append('avatar', file);
      formData.append('op', 30); // Operação para upload de avatar

      $.ajax({
        url: 'src/controller/controllerDashboardAnunciante.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(resp) {
          const resultado = JSON.parse(resp);
          if (resultado.success) {
            $('#avatarImg').attr('src', resultado.url + '?' + new Date().getTime());
            Swal.fire({
              icon: 'success',
              title: 'Avatar atualizado!',
              confirmButtonColor: '#3cb371',
              timer: 1500
            });
          } else {
            Swal.fire('Erro', resultado.message, 'error');
          }
        },
        error: function() {
          Swal.fire('Erro', 'Não foi possível fazer upload da imagem', 'error');
        }
      });
    }
  }

  function logout() {
    $.ajax({
      url: 'src/controller/controllerPerfil.php?op=2',
      method: 'GET'
    }).always(function() {
      window.location.href = 'index.html';
    });
  }
  </script>
  <script src="src/js/alternancia.js"></script>
</body>

</html>
