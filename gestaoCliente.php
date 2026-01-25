        <!DOCTYPE html>
        <html lang="pt">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestão de Clientes - WeGreen Admin</title>
            <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
            <link rel="stylesheet" href="src/css/lib/datatables.css">
            <link rel="stylesheet" href="src/css/gestaoClienteAdmin.css">
            <link rel="stylesheet" href="src/css/lib/select2.css">

            <script src="src/js/lib/jquery.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
                crossorigin="anonymous">
            </script>
            <script src="src/js/lib/datatables.js"></script>
            <script src="src/js/lib/select2.js"></script>
            <script src="src/js/lib/sweatalert.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
            <style>

            </style>
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
                                <a class="nav-link active" href="gestaoCliente.php">
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
                                <a class="nav-link" href="logAdmin.php">
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
                            <i class="navbar-icon fas fa-shopping-bag"></i>
                            <h2 class="navbar-title">Gestão de Utilizadores </h2>
                        </div>
                        <div class="navbar-right">
                            <?php include 'src/views/notifications-widget.php'; ?>
                            <div class="navbar-user">
                                <div id="AdminPerfilInfo" style="display:flex;"></div>
                                <i class=" fas fa-chevron-down user-trigger"
                                    style="font-size: 12px; color: #4a5568;"></i>

                                <div class="user-dropdown" id="userDropdown"></div>
                            </div>
                        </div>
                    </nav>

                    <div class="page">
                        <div class="page-header">
                            <h2>Gestão de Clientes</h2>
                            <p>Administre todos os utilizadores da plataforma</p>
                        </div>

                        <div class="page-actions">
                            <button class="btn btn-primary" onclick="showModal()">
                                <i class="fas fa-plus"></i> Adicionar Cliente
                            </button>
                            <button class="btn btn-secondary" onclick="exportClients()">
                                <i class="fas fa-file-export"></i> Exportar Lista
                            </button>
                        </div>

                        <div class="stats-grid" id="CardTipoutilizadores">

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
                </main>
            </div>

            <div id="clientModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="modalTitle">Adicionar Cliente</h3>
                        <button class="close-btn" onclick="closeModal()">&times;</button>
                    </div>
                    <form id="clientForm" onsubmit="saveClient(event)">
                        <input type="hidden" id="clientId">
                        <div class="form-row">
                            <div class="form-col">
                                <label>Nome Completo </label>
                                <input type="text" id="clientNome" required>
                            </div>
                            <div class="form-col">
                                <label>Email</label>
                                <input type="email" id="clientEmail" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label>Telefone</label>
                                <input type="tel" id="clientTelefone" placeholder="+351 912 345 678">
                            </div>
                            <div class="form-col">
                                <label>Tipo de Utilizador</label>
                                <select id="clientTipo" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">Administrador</option>
                                    <option value="2">Cliente</option>
                                    <option value="3">Anunciante</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row-full">
                            <div class="form-col">
                                <label>NIF</label>
                                <input type="text" id="clientNif" placeholder="123456789">
                            </div>
                        </div>
                        <div class="form-row-full">
                            <label>Morada</label>
                            <input type="text" id="clientMorada" placeholder="Rua, número, andar">
                        </div>
                        <div class="form-row-full">
                            <label>Imagem</label>
                            <input type="file" id="imagemClient" placeholder="Rua, número, andar">
                        </div>
                        <div class="form-row">
                        </div>
                        <div class="form-row" id="passwordSection">
                            <div class="form-col">
                                <label>Senha</label>
                                <input type="password" id="clientPassword">
                            </div>
                            <div class="form-col">
                                <label>Confirmar Senha</label>
                                <input type="password" id="clientPasswordConfirm">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" style="width: 100%; margin-top: 15px;"
                            onclick="registaClientes()">
                            <i class="fas fa-save"></i> Salvar Cliente
                        </button>

                    </form>
                </div>
            </div>

            <div id="viewModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Detalhes do Cliente</h3>
                        <button class="close-btn" onclick="closeModal2()">&times;</button>
                    </div>
                    <div class="client-view-grid">
                        <div class="info-group">
                            <label>ID Utilizador</label>
                            <input id="viewIDedit"></input disable>
                        </div>
                        <div class="info-group">
                            <label>Nome Completo</label>
                            <input id="viewNome"></input>
                        </div>
                        <div class="info-group">
                            <label>Email</label>
                            <input id="viewEmail"></input>
                        </div>
                        <div class="info-group">
                            <label>Telefone</label>
                            <input id="viewTelefone"></input>
                        </div>
                        <div class="info-group">
                            <label>Tipo</label>
                            <input id="viewTipo"></input>
                        </div>
                        <div class="info-group">
                            <label>NIF</label>
                            <input id="viewNif"></input>
                        </div>
                        <div class="info-group">
                            <label>Plano</label>
                            <input id="viewPlano"></input>
                        </div>
                        <div class="info-group" style="grid-column: 1 / -1;">
                            <label>Ranking</label>
                            <input id="viewRanking"></input>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Fechar
                        </button>
                        <button type="button" class="btn btn-primary" id="btnGuardar">
                            <i class="bi bi-check-circle"></i> Guardar Alterações
                        </button>
                    </div>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
            <script src="src/js/ClientesAdmin.js"></script>
            <script src="src/js/Adminstrador.js"></script>
            <script>
            function toggleUserDropdown() {
                document.getElementById('userDropdown').classList.toggle('active');
            }

            function closeUserDropdown() {
                document.getElementById('userDropdown').classList.remove('active');
            }

            // Fecha ao clicar fora
            document.addEventListener('click', function(e) {
                const user = document.querySelector('.navbar-user');
                const dropdown = document.getElementById('userDropdown');

                if (!user.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
            </script>
        </body>

        </html>