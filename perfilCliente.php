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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
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
  }

  .profile-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

  .profile-stats {
    display: flex;
    gap: 30px;
  }

  .profile-stat {
    text-align: center;
  }

  .profile-stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #3cb371;
  }

  .profile-stat-label {
    font-size: 12px;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
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
  .info-item select {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
    background-color: white;
  }

  .info-item input:focus,
  .info-item select:focus {
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

  .ranking-progress {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 12px;
    border-left: 4px solid #3cb371;
    margin-top: 30px;
  }

  .ranking-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  .ranking-label {
    font-size: 14px;
    font-weight: 600;
    color: #1a1a1a;
  }

  .ranking-points {
    font-size: 14px;
    font-weight: 600;
    color: #3cb371;
  }

  .progress-bar {
    height: 12px;
    background: #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 15px;
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3cb371, #2e8b57);
    border-radius: 6px;
    transition: width 0.5s ease;
  }

  .ranking-badges {
    display: flex;
    gap: 10px;
  }

  .badge-current {
    background: #3cb371;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
  }

  .badge-next {
    background: rgba(60, 179, 113, 0.1);
    color: #3cb371;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #3cb371;
  }

  .security-content {
    padding: 20px 0;
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
  }

  .btn-secondary:hover {
    background: #475569;
    transform: translateY(-2px);
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
                  <select id="distrito" class="form-control" required>
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
                  <input type="text" id="localidade" placeholder="Cidade ou Vila" required>
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

  // Carregar dados do perfil ao iniciar
  carregarPerfilCliente();

  function carregarPerfilCliente() {
    $.post('src/controller/controllerPerfil.php', {
      op: 12
    }, function(resp) {
      console.log('Resposta do servidor:', resp);
      const dados = JSON.parse(resp);
      console.log('Dados parseados:', dados);

      if (dados.error) {
        return Swal.fire('Erro', dados.error, 'error');
      }

      // Preencher os campos
      $('#nome').val(dados.nome_completo || dados.nome || '');
      $('#email').val(dados.email || '');
      $('#telefone').val(dados.telefone || '');
      $('#nif').val(dados.nif || '');
      $('#morada').val(dados.morada || '');
      $('#distrito').val(dados.distrito || '');
      $('#localidade').val(dados.localidade || '');

      console.log('Campos preenchidos:', {
        nome: $('#nome').val(),
        email: $('#email').val(),
        telefone: $('#telefone').val(),
        nif: $('#nif').val(),
        morada: $('#morada').val(),
        distrito: $('#distrito').val(),
        localidade: $('#localidade').val()
      });
    }).fail(function(jqXHR, textStatus) {
      console.error('Erro ao carregar perfil:', textStatus);
      console.error('Detalhes:', jqXHR.responseText);
      Swal.fire('Erro', 'Não foi possível carregar os dados do perfil', 'error');
    });
  }

  // Form submission
  $("#profileForm").submit(function(e) {
    e.preventDefault();

    const dados = {
      op: 16,
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

    if (!dados.distrito) {
      return Swal.fire('Atenção', 'Distrito é obrigatório', 'warning');
    }

    if (!dados.localidade || dados.localidade.trim().length < 2) {
      return Swal.fire('Atenção', 'Localidade é obrigatória', 'warning');
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
        $.post('src/controller/controllerPerfil.php', dados, function(resp) {
          const resultado = JSON.parse(resp);
          if (resultado.success) {
            Swal.fire({
              icon: 'success',
              title: 'Perfil atualizado!',
              text: resultado.message,
              confirmButtonColor: '#3cb371',
              timer: 2000
            }).then(() => {
              carregarPerfilCliente();
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

  function logout() {
    window.location.href = 'src/controller/controllerPerfil.php?op=2';
  }
  </script>
</body>

</html>
