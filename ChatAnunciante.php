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
    <title>Chat - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/ChatCliente.css">
    <link rel="stylesheet" href="src/css/ChatAnunciante.css">
    <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/notifications.js"></script>
</head>
<body class="page-chat-cliente">
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
                    <a href="ChatAnunciante.php" class="menu-item active">
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
                    <h1 class="page-title"><i class="fas fa-comments"></i> Chat</h1>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'" style="display: none;" <?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
                        <i class="fas fa-crown"></i> Upgrade
                    </button>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff" alt="Usuário" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></span>
                            <span class="user-role">Anunciante</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff" alt="Usuário" class="dropdown-avatar">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'user@email.com'; ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="perfilAnunciante.php">
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

            <div class="content-area">
                <div class="page-content">
                    <div class="chat-container">
                        <div class="conversations-panel">
                            <div class="conversations-header">
                                <h3>Conversas</h3>
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" placeholder="Pesquisar conversas..." id="searchInput">
                                </div>
                            </div>
                            <div class="conversations-list" id="ListaClientes">
                                <!-- Conversas carregadas via AJAX -->
                            </div>
                        </div>

                        <div class="chat-panel" id="FaixaPessoa">
                            <div class="chat-header" id="tituloChat">
                                <div class="chat-header-info">
                                    <div class="chat-user-avatar" id="chatUserAvatar">C</div>
                                    <div class="chat-user-details">
                                        <h4 id="chatUserName">Selecione uma conversa</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="chat-messages" id="chatMessages">
                                <div class="empty-chat">
                                    <i class="fas fa-comments" style="font-size: 64px; color: #e0e0e0; margin-bottom: 16px;"></i>
                                    <h3>Nenhuma conversa selecionada</h3>
                                    <p>Escolha uma conversa à esquerda para começar</p>
                                </div>
                            </div>

                            <div class="chat-input-container" id="BotaoEscrever">
                                <input type="file" id="fileInput" accept="image/*" style="display: none;">
                                <button class="chat-attach-btn" id="attachBtn" title="Anexar imagem">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <div class="input-wrapper">
                                    <input type="text" class="chat-input" id="messageInput" placeholder="Escreva uma mensagem ou cole uma imagem (Ctrl+V)...">
                                    <div id="imagePreview" class="image-preview" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview">
                                        <button id="removePreview" class="remove-preview">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <button class="chat-send-btn" id="sendButton">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="src/js/ChatAnunciante.js"></script>
    <script src="src/js/alternancia.js"></script>
    <script>
        function logout() {
            $.ajax({
                url: 'src/controller/controllerPerfil.php?op=2',
                method: 'GET'
            }).always(function() {
                window.location.href = 'index.html';
            });
        }

        // Toggle do menu de usuário
        document.getElementById('userMenuBtn')?.addEventListener('click', function() {
            document.getElementById('userDropdown').classList.toggle('show');
        });

        window.addEventListener('click', function(e) {
            if (!e.target.closest('#userMenuBtn') && !e.target.closest('#userDropdown')) {
                document.getElementById('userDropdown')?.classList.remove('show');
            }
        });

        // Verificar plano para mostrar botão Upgrade
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

        // Chamar verificação ao carregar
        $(document).ready(function() {
            verificarPlanoUpgrade();
        });
    </script>
</body>
</html>
