<?php
session_start();
if($_SESSION['tipo'] != 1){
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logs do Sistema - WeGreen Admin</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/DashboardAdmin.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/lib/datatables.css">
  <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
  <script src="src/js/lib/jquery.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>
  <script src="src/js/lib/datatables.js"></script>
  <script src="src/js/lib/select2.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="src/js/notifications.js"></script>
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
          <a href="gestaoLucros.php" class="menu-item">
            <i class="fas fa-euro-sign"></i>
            <span>Lucros</span>
          </a>
          <a href="logAdmin.php" class="menu-item active">
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
          <h1 class="page-title"><i class="fas fa-history"></i> Logs do Sistema</h1>
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
        <div class="stats-grid-compact" id="CardsLogs">
        </div>

        <div class="table-container">
          <table id="LogAdminTable" class="display">
            <thead>
              <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Email</th>
                <th>Ação</th>
                <th>Hora</th>
              </tr>
            </thead>
            <tbody id="LogAdminBody">
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  <script src="src/js/logAdmin.js"></script>
  <script>
  // User dropdown toggle
  const userMenuBtn = document.getElementById("userMenuBtn");
  const userDropdown = document.getElementById("userDropdown");

  if (userMenuBtn) {
    userMenuBtn.addEventListener("click", function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle("active");
    });
  }

  document.addEventListener("click", function(e) {
    if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
      userDropdown?.classList.remove("active");
    }
  });

  // Função de logout
  function logout() {
    Swal.fire({
      html: `
                    <div style="background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); padding: 20px; margin: -20px -20px 20px -20px; border-radius: 12px 12px 0 0; text-align: center;">
                        <i class="fas fa-sign-out-alt" style="font-size: 48px; color: white; margin-bottom: 15px;"></i>
                        <h2 style="margin: 0; color: white; font-size: 24px; font-weight: 700;">Terminar Sessão</h2>
                    </div>
                    <p style="font-size: 16px; color: #64748b; margin: 20px 0;">Tem a certeza que deseja sair da plataforma?</p>
                `,
      showCancelButton: true,
      confirmButtonText: '<i class="fas fa-check"></i> Sim, sair',
      cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
      confirmButtonColor: '#3cb371',
      cancelButtonColor: '#6b7280',
    }).then((result) => {
      if (result.isConfirmed) {
        let dados = new FormData();
        dados.append("op", 10);

        $.ajax({
          url: "src/controller/controllerDashboardAdmin.php",
          method: "POST",
          data: dados,
          dataType: "json",
          cache: false,
          contentType: false,
          processData: false,
        }).done(function(response) {
          if (response.success) {
            window.location.href = "index.html";
          }
        }).fail(function() {
          window.location.href = "index.html";
        });
      }
    });
  }
  </script>
</body>

</html>
