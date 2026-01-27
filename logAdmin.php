<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs do Sistema - WeGreen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/admin.css">
    <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
    <link rel="stylesheet" href="src/css/logAdmin.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>

</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" class="logo">
                <span class="logo-icon"><i class="fas fa-leaf"></i></span>
                <div class="logo-text">
                    <h1>WeGreen</h1>
                    <p>Painel do Administrador</p>
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
                        <a class="nav-link" href="gestaoProdutosAdmin.php">
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
                        <a class="nav-link active" href="logAdmin.php">
                            <span class="nav-icon"><i class="fas fa-history"></i></span>
                            <span class="nav-text">Logs do Sistema</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <i class="navbar-icon fas fa-history"></i>
                    <h2 class="navbar-title">Logs do Sistema</h2>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user">
                        <div id="AdminPerfilInfo" style="display:flex;"></div>
                        <i class="fas fa-chevron-down user-trigger" style="font-size: 12px; color: #4a5568;"></i>
                        <div class="user-dropdown" id="userDropdown"></div>
                    </div>
                </div>
            </nav>

            <div class="page-content">
                <div class="page-header">
                    <h2>Logs de Atividade</h2>
                </div>

                <div class="stats-row" id="CardsLogs">

                </div>

            <div class="logs-container">
                <div class="logs-header">
                    <h3>
                        <i class="fas fa-list"></i>
                        Histórico de Atividades
                    </h3>
                </div>

    <div id="logsContent">
        <div class="chart-card">

            <table id="LogAdminTable" class="table table-striped table-bordered">
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
</div>

        </main>
    </div>
    <script src="src/js/logAdmin.js"></script>
    <script>
        // Carregar informações do perfil do admin
        function getAdminPerfil() {
          let dados = new FormData();
          dados.append("op", 21);

          $.ajax({
            url: "src/controller/controllerDashboardAdmin.php",
            method: "POST",
            data: dados,
            dataType: "html",
            cache: false,
            contentType: false,
            processData: false,
          }).done(function (msg) {
            $("#AdminPerfilInfo").html(msg);
          }).fail(function (jqXHR, textStatus) {
            console.error("Erro ao carregar perfil: " + textStatus);
          });
        }

        // Carregar dropdown do usuário
        function getInfoUserDropdown() {
          let dados = new FormData();
          dados.append("op", 9);

          $.ajax({
            url: "src/controller/controllerDashboardAdmin.php",
            method: "POST",
            data: dados,
            dataType: "html",
            cache: false,
            contentType: false,
            processData: false,
          }).done(function (msg) {
            $("#userDropdown").html(msg);
          }).fail(function (jqXHR, textStatus) {
            console.error("Erro ao carregar dropdown: " + textStatus);
          });
        }

        // Toggle dropdown ao clicar no navbar-user
        document.querySelector('.navbar-user').addEventListener('click', function(e) {
          e.stopPropagation();
          document.getElementById('userDropdown').classList.toggle('active');
        });

        // Fecha ao clicar fora
        document.addEventListener('click', function(e) {
          const user = document.querySelector('.navbar-user');
          const dropdown = document.getElementById('userDropdown');

          if (!user.contains(e.target)) {
            dropdown.classList.remove('active');
          }
        });

        // Função de logout
        function logout() {
          Swal.fire({
            html: `
              <div style="padding: 20px 0;">
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                  <i class="fas fa-sign-out-alt" style="font-size: 32px; color: #dc2626;"></i>
                </div>
                <h2 style="margin: 0 0 12px 0; color: #1e293b; font-size: 24px; font-weight: 700;">Terminar Sessao?</h2>
                <p style="margin: 0; color: #64748b; font-size: 15px; line-height: 1.6;">Tem a certeza que pretende sair da sua conta?</p>
              </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sim, sair',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            confirmButtonColor: "#dc2626",
            cancelButtonColor: "#64748b",
            customClass: {
              confirmButton: "swal2-confirm-modern",
              cancelButton: "swal2-cancel-modern",
              popup: "swal2-logout-popup",
            },
            buttonsStyling: false,
            reverseButtons: true,
            didOpen: () => {
              const popup = Swal.getPopup();
              popup.style.borderRadius = "16px";
              popup.style.padding = "25px";

              const confirmBtn = popup.querySelector(".swal2-confirm");
              const cancelBtn = popup.querySelector(".swal2-cancel");

              if (confirmBtn) {
                confirmBtn.style.padding = "12px 28px";
                confirmBtn.style.borderRadius = "10px";
                confirmBtn.style.fontSize = "15px";
                confirmBtn.style.fontWeight = "600";
                confirmBtn.style.border = "none";
                confirmBtn.style.cursor = "pointer";
                confirmBtn.style.transition = "all 0.3s ease";
                confirmBtn.style.backgroundColor = "#dc2626";
                confirmBtn.style.color = "#ffffff";
                confirmBtn.style.marginLeft = "10px";
              }

              if (cancelBtn) {
                cancelBtn.style.padding = "12px 28px";
                cancelBtn.style.borderRadius = "10px";
                cancelBtn.style.fontSize = "15px";
                cancelBtn.style.fontWeight = "600";
                cancelBtn.style.border = "2px solid #e2e8f0";
                cancelBtn.style.cursor = "pointer";
                cancelBtn.style.transition = "all 0.3s ease";
                cancelBtn.style.backgroundColor = "#ffffff";
                cancelBtn.style.color = "#64748b";
              }
            },
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                html: `
                  <div style="padding: 20px;">
                    <div class="swal2-loading-spinner" style="margin: 0 auto 20px;">
                      <div style="width: 50px; height: 50px; border: 4px solid #f3f4f6; border-top: 4px solid #3cb371; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    </div>
                    <p style="margin: 0; color: #64748b; font-size: 15px;">A terminar sessao...</p>
                  </div>
                  <style>
                    @keyframes spin {
                      0% { transform: rotate(0deg); }
                      100% { transform: rotate(360deg); }
                    }
                  </style>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                  const popup = Swal.getPopup();
                  popup.style.borderRadius = "16px";
                },
              });

              let dados = new FormData();
              dados.append("op", 10);

              $.ajax({
                url: "src/controller/controllerDashboardAdmin.php",
                method: "POST",
                data: dados,
                dataType: "html",
                cache: false,
                contentType: false,
                processData: false,
              })
                .done(function (msg) {
                  window.location.href = "index.html";
                })
                .fail(function (jqXHR, textStatus) {
                  alert("Request failed: " + textStatus);
                });
            }
          });
        }

        // Carregar ao iniciar
        $(document).ready(function() {
          getAdminPerfil();
          getInfoUserDropdown();
        });
    </script>
</body>
</html>
