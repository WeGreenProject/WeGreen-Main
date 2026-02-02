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
          <title>Gestão de Clientes - WeGreen Admin</title>
          <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
          <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
          <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
          <link rel="stylesheet" href="src/css/DashboardAdmin.css?v=<?php echo time(); ?>">
          <link rel="stylesheet" href="src/css/lib/datatables.css">
          <link rel="stylesheet" href="src/css/lib/select2.css">
          <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
          <script src="src/js/lib/jquery.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
          </script>
          <script src="src/js/lib/datatables.js"></script>
          <script src="src/js/lib/select2.js"></script>
          <script src="src/js/lib/sweatalert.js"></script>
          <script src="src/js/notifications.js"></script>
          <style>
            /* Modal Styles */
            .modal {
              display: none;
              position: fixed;
              z-index: 9999;
              left: 0;
              top: 0;
              width: 100%;
              height: 100%;
              background-color: rgba(0, 0, 0, 0.5);
              backdrop-filter: blur(4px);
            }

            .modal.active {
              display: flex;
              align-items: center;
              justify-content: center;
            }

            .modal-content {
              background: #ffffff;
              border-radius: 16px;
              width: 90%;
              max-width: 800px;
              max-height: 90vh;
              overflow-y: auto;
              box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
              animation: modalSlideIn 0.3s ease-out;
            }

            @keyframes modalSlideIn {
              from {
                opacity: 0;
                transform: translateY(-50px);
              }
              to {
                opacity: 1;
                transform: translateY(0);
              }
            }

            .modal-header {
              display: flex;
              justify-content: space-between;
              align-items: center;
              padding: 24px 30px;
              border-bottom: 1px solid #e2e8f0;
              background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
              border-radius: 16px 16px 0 0;
            }

            .modal-header h3 {
              margin: 0;
              font-size: 20px;
              font-weight: 600;
              color: #ffffff;
            }

            .close-btn {
              background: rgba(255, 255, 255, 0.2);
              border: none;
              color: #ffffff;
              font-size: 28px;
              font-weight: 300;
              cursor: pointer;
              width: 36px;
              height: 36px;
              border-radius: 8px;
              display: flex;
              align-items: center;
              justify-content: center;
              transition: all 0.2s;
            }

            .close-btn:hover {
              background: rgba(255, 255, 255, 0.3);
              transform: rotate(90deg);
            }

            .modal-content form {
              padding: 30px;
            }

            .form-row {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 20px;
              margin-bottom: 20px;
            }

            .form-row-full {
              margin-bottom: 20px;
            }

            .form-col label,
            .form-row-full label {
              display: flex;
              align-items: center;
              gap: 8px;
              margin-bottom: 8px;
              font-weight: 600;
              color: #2d3748;
              font-size: 13px;
            }

            .form-col label i,
            .form-row-full label i {
              color: #3cb371;
              font-size: 14px;
            }

            .form-col input,
            .form-col select,
            .form-row-full input {
              width: 100%;
              padding: 12px 16px;
              border: 1px solid #e2e8f0;
              border-radius: 8px;
              font-size: 14px;
              transition: all 0.2s;
            }

            .form-col .input-with-icon input,
            .form-col .input-with-icon select,
            .form-row-full .input-with-icon input {
              padding-left: 42px;
            }

            .form-col input:focus,
            .form-col select:focus,
            .form-row-full input:focus {
              outline: none;
              border-color: #3cb371;
              box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.1);
            }

            .client-view-grid {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 20px;
              padding: 30px;
            }

            .info-group {
              display: flex;
              flex-direction: column;
              gap: 8px;
            }

            .info-group label {
              font-weight: 600;
              color: #2d3748;
              font-size: 13px;
              display: flex;
              align-items: center;
              gap: 8px;
            }

            .info-group label i {
              color: #3cb371;
              font-size: 14px;
            }

            .input-with-icon {
              position: relative;
              display: flex;
              align-items: center;
            }

            .input-with-icon .input-icon {
              position: absolute;
              left: 14px;
              color: #94a3b8;
              font-size: 14px;
              pointer-events: none;
              transition: color 0.2s;
            }

            .input-with-icon input,
            .input-with-icon select {
              width: 100%;
              padding: 12px 16px 12px 42px;
              border: 1px solid #e2e8f0;
              border-radius: 8px;
              font-size: 14px;
              transition: all 0.2s;
              background: #ffffff;
            }

            .input-with-icon input:focus,
            .input-with-icon select:focus {
              outline: none;
              border-color: #3cb371;
              box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.1);
            }

            .input-with-icon input:focus ~ .input-icon,
            .input-with-icon select:focus ~ .input-icon {
              color: #3cb371;
            }

            .input-with-icon input:disabled {
              background: #f1f5f9;
              color: #64748b;
              cursor: not-allowed;
            }

            .info-group input,
            .info-group select {
              width: 100%;
              padding: 12px 16px;
              border: 1px solid #e2e8f0;
              border-radius: 8px;
              font-size: 14px;
              transition: all 0.2s;
            }

            .info-group input:focus,
            .info-group select:focus {
              outline: none;
              border-color: #3cb371;
              box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.1);
            }

            .modal-footer {
              display: flex;
              justify-content: flex-end;
              gap: 12px;
              padding: 20px 30px;
              border-top: 1px solid #e2e8f0;
              background: #f8f9fa;
              border-radius: 0 0 16px 16px;
            }

            .btn {
              padding: 12px 24px;
              border: none;
              border-radius: 8px;
              font-weight: 500;
              cursor: pointer;
              transition: all 0.2s;
              display: inline-flex;
              align-items: center;
              gap: 8px;
              font-size: 14px;
            }

            .btn-primary {
              background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
              color: white;
            }

            .btn-primary:hover {
              transform: translateY(-2px);
              box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);
            }

            .btn-secondary {
              background: #6b7280;
              color: white;
            }

            .btn-secondary:hover {
              background: #4b5563;
              transform: translateY(-2px);
              box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
            }

            /* Dropdown styles */
            .user-dropdown {
              display: none;
              position: absolute;
              top: 100%;
              right: 0;
              margin-top: 8px;
              background: white;
              border-radius: 12px;
              box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
              min-width: 240px;
              z-index: 1000;
              opacity: 0;
              transform: translateY(-10px);
              transition: all 0.2s ease;
            }

            .user-dropdown.active {
              display: block;
              opacity: 1;
              transform: translateY(0);
            }
          </style>
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
                  <a href="gestaoCliente.php" class="menu-item active">
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
                  <h1 class="page-title"><i class="fas fa-users"></i> Gestão de Utilizadores</h1>
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
                <div id="clients" class="page active">
                  <div class="page-actions">
                    <div class="actions-left">
                      <button class="btn-add-product" onclick="showModal()">
                        <i class="fas fa-plus"></i>
                        <span>Adicionar Utilizador</span>
                      </button>
                    </div>
                  </div>

                  <div class="stats-grid-compact" id="CardTipoutilizadores">
                  </div>

                  <div class="table-container">
                    <table id="clientsTable" class="display">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Cliente</th>
                          <th>Tipo</th>
                          <th>Telefone</th>
                          <th>Data Registo</th>
                          <th>Ações</th>
                        </tr>
                      </thead>
                      <tbody id="clientsTableBody">
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </main>
          </div>

        <div id="clientModal" class="modal">
            <div class="modal-content">
              <div class="modal-header">
                <h3 id="modalTitle"><i class="fas fa-user-plus"></i> Adicionar Cliente</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
              </div>
              <form id="clientForm" onsubmit="saveClient(event)">
                <input type="hidden" id="clientId">
                <div class="form-row">
                  <div class="form-col">
                    <label><i class="fas fa-user"></i> Nome Completo</label>
                    <input type="text" id="clientNome" required>
                  </div>
                  <div class="form-col">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="clientEmail" required>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-col">
                    <label><i class="fas fa-phone"></i> Telefone</label>
                    <input type="tel" id="clientTelefone" placeholder="+351 912 345 678">
                  </div>
                  <div class="form-col">
                    <label><i class="fas fa-user-tag"></i> Tipo de Utilizador</label>
                    <select id="clientTipo" required>
                      <option value="">Selecione...</option>
                      <option value="1">Administrador</option>
                      <option value="2">Cliente</option>
                      <option value="3">Anunciante</option>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-col">
                    <label><i class="fas fa-id-card"></i> NIF</label>
                    <input type="text" id="clientNif" placeholder="123456789">
                  </div>
                  <div class="form-col">
                    <label><i class="fas fa-map-marker-alt"></i> Morada</label>
                    <input type="text" id="clientMorada" placeholder="Rua, número, andar" autocomplete="address-line1">
                  </div>
                </div>
                <div class="form-row-full">
                  <label><i class="fas fa-image"></i> Imagem</label>
                  <input type="file" id="imagemClient">
                </div>
                <div class="form-row" id="passwordSection">
                  <div class="form-col">
                    <label><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" id="clientPassword" autocomplete="new-password">
                  </div>
                  <div class="form-col">
                    <label><i class="fas fa-lock"></i> Confirmar Senha</label>
                    <input type="password" id="clientPasswordConfirm" autocomplete="new-password">
                  </div>
                </div>
              </form>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">
                  <i class="fas fa-times"></i> Fechar
                </button>
                <button type="button" class="btn btn-primary" onclick="registaClientes()">
                  <i class="fas fa-save"></i> Guardar Cliente
                </button>
              </div>
            </div>
          </div>

          <div id="viewModal" class="modal">
            <div class="modal-content">
              <div class="modal-header">
                <h3><i class="fas fa-user-edit"></i> Editar Cliente</h3>
                <button class="close-btn" onclick="closeModal2()">&times;</button>
              </div>
              <div class="client-view-grid">
                <div class="info-group">
                  <label><i class="fas fa-hashtag"></i> ID Utilizador</label>
                  <input type="text" id="viewIDedit" disabled>
                </div>
                <div class="info-group">
                  <label><i class="fas fa-user"></i> Nome Completo</label>
                  <input type="text" id="viewNome">
                </div>
                <div class="info-group">
                  <label><i class="fas fa-envelope"></i> Email</label>
                  <input type="email" id="viewEmail">
                </div>
                <div class="info-group">
                  <label><i class="fas fa-phone"></i> Telefone</label>
                  <input type="tel" id="viewTelefone">
                </div>
                <div class="info-group">
                  <label><i class="fas fa-user-tag"></i> Tipo</label>
                  <select id="viewTipo">
                    <option value="">Selecione...</option>
                    <option value="1">Administrador</option>
                    <option value="2">Cliente</option>
                    <option value="3">Anunciante</option>
                  </select>
                </div>
                <div class="info-group">
                  <label><i class="fas fa-id-card"></i> NIF</label>
                  <input type="text" id="viewNif">
                </div>
                <div class="info-group">
                  <label><i class="fas fa-crown"></i> Plano</label>
                  <select id="viewPlano">
                    <option value="">Selecione...</option>
                    <option value="1">Básico</option>
                    <option value="2">Premium</option>
                    <option value="3">Enterprise</option>
                  </select>
                </div>
                <div class="info-group">
                  <label><i class="fas fa-star"></i> Ranking</label>
                  <select id="viewRanking">
                    <option value="">Selecione...</option>
                    <option value="1">Bronze</option>
                    <option value="2">Prata</option>
                    <option value="3">Ouro</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal2()">
                  <i class="fas fa-times"></i> Fechar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardar">
                  <i class="fas fa-save"></i> Guardar Alterações
                </button>
              </div>
            </div>
          </div>

          <script src="src/js/ClientesAdmin.js"></script>
          <script>
          // User dropdown toggle
          function initializeDropdownEvents() {
            const userMenuBtn = document.getElementById("userMenuBtn");
            const userDropdown = document.getElementById("userDropdown");

            if (userMenuBtn) {
              userMenuBtn.addEventListener("click", function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle("active");
              });
            }

            // Fecha ao clicar fora
            document.addEventListener("click", function(e) {
              if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
                userDropdown?.classList.remove("active");
              }
            });
          }

          // Logout function
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
              customClass: {
                popup: 'logout-modal',
                confirmButton: 'btn-confirm-logout',
                cancelButton: 'btn-cancel-logout'
              },
              didOpen: () => {
                const confirmBtn = Swal.getConfirmButton();
                const cancelBtn = Swal.getCancelButton();

                if (confirmBtn) {
                  confirmBtn.style.background = 'linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)';
                  confirmBtn.style.padding = '12px 30px';
                  confirmBtn.style.borderRadius = '8px';
                  confirmBtn.style.fontWeight = '600';
                  confirmBtn.style.boxShadow = '0 4px 12px rgba(60, 179, 113, 0.3)';
                }
                if (cancelBtn) {
                  cancelBtn.style.background = 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)';
                  cancelBtn.style.padding = '12px 30px';
                  cancelBtn.style.borderRadius = '8px';
                  cancelBtn.style.fontWeight = '600';
                  cancelBtn.style.boxShadow = '0 4px 12px rgba(107, 114, 128, 0.3)';
                }
              }
            }).then((result) => {
              if (result.isConfirmed) {
                Swal.fire({
                  html: '<div style="padding: 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #3cb371;"></i><p style="margin-top: 20px; font-size: 16px; color: #64748b;">A terminar sessão...</p></div>',
                  showConfirmButton: false,
                  allowOutsideClick: false,
                  allowEscapeKey: false
                });

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

          // Initialize on page load
          $(document).ready(function() {
            initializeDropdownEvents();
            getCardUtilizadores();
            getClientes();
          });
          </script>
        </body>

        </html>
