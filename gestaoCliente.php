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
          <link rel="stylesheet" href="src/css/notifications-dropdown.css">
          <script src="src/js/lib/jquery.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
          </script>
          <script src="src/js/lib/datatables.js"></script>
          <script src="src/js/lib/select2.js"></script>
          <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
          <script src="src/js/notifications.js"></script>
          <link rel="stylesheet" href="src/css/gestaoClienteAdmin.css">
              <link rel="stylesheet" href="src/css/gestaoProdutosAdmin.css">
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
          <script src="src/js/gestaoCliente.js"></script>
        </body>

        </html>
