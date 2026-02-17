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
  <title>Chat de Suporte - Admin</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css">
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css">
  <link rel="stylesheet" href="src/css/DashboardAdmin.css">
  <link rel="stylesheet" href="src/css/ChatCliente.css">
  <link rel="stylesheet" href="src/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>
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
          <a href="Chatadmin.php" class="menu-item active">
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
          <h1 class="page-title"><i class="fas fa-comments"></i> Chat de Suporte</h1>
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

      <div class="page-content">
        <div class="content-area">
          <div class="chat-container">
            <div class="conversations-panel">
              <div class="conversations-header">
                <h3>Conversas</h3>
                <div class="search-box">
                  <i class="fas fa-search"></i>
                  <input type="text" placeholder="Pesquisar conversas..." id="searchInput" onkeyup="pesquisarChat()">
                </div>
              </div>
              <div class="conversations-list" id="ListaCliente">
                <!-- Conversas carregadas via AJAX -->
              </div>
            </div>

            <div class="chat-panel" id="FaixaPessoa">
              <div class="chat-header">
                <div class="chat-header-info">
                  <div class="chat-user-avatar" id="chatUserAvatar">U</div>
                  <div class="chat-user-details">
                    <h4 id="chatUserName">Selecione uma conversa</h4>
                  </div>
                </div>
              </div>

              <div class="chat-messages" id="chatMessages">
                <div class="empty-chat">
                  <i class="fas fa-comments" style="font-size: 64px; color: #3cb371; margin-bottom: 16px;"></i>
                  <h3>Nenhuma conversa selecionada</h3>
                  <p>Escolha uma conversa à esquerda para começar</p>
                </div>
              </div>

              <div class="chat-input-container" id="BotaoEscrever">
                <input type="file" id="fileInput" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" style="display: none;">
                <button class="chat-attach-btn" id="attachBtn" title="Anexar imagem">
                  <i class="fas fa-paperclip"></i>
                </button>
                <div class="input-wrapper">
                  <input type="text" class="chat-input" id="messageInput"
                    placeholder="Escreva uma mensagem ou cole uma imagem (Ctrl+V)...">
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

  <script src="src/js/ChatAdmin.js"></script>
</body>

</html>
