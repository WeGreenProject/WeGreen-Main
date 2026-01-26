<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Suporte - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/Chatadmin.css">
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
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
                        <a class="nav-link active" href="Chatadmin.php">
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
                    <i class="fas fa-comments navbar-icon"></i>
                    <h2 class="navbar-title">Chat de Suporte</h2>
                </div>
                <div class="navbar-right">
                    <button class="navbar-icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <button class="navbar-icon-btn">
                        <i class="fas fa-envelope"></i>
                    </button>
                    <div class="navbar-user">
                        <div class="user-avatar">A</div>
                        <div class="user-info">
                            <span class="user-name">Admin User</span>
                            <span class="user-role">Administrador</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #718096;"></i>
                    </div>
                </div>
            </nav>

            <div class="chat-container">
                <div class="conversations-panel">
                    <div class="conversations-header">
                        <h3>Conversas</h3>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Pesquisar conversas..." id="searchInput"
                                onkeyup="pesquisarChat()">
                        </div>
                    </div>
                    <div class="conversations-list" id="ListaCliente">

                        <div class="conversation-item" data-user="pedro">
                            <div class="conversation-avatar">P</div>
                            <div class="conversation-info">
                                <div class="conversation-header">
                                    <span class="conversation-name">Pedro Lima</span>
                                    <span class="conversation-time">Segunda</span>
                                </div>
                                <div class="conversation-preview">
                                    Preciso alterar o endereço de entrega
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chat-panel" id="FaixaPessoa">
                    <div id="chatContent" style="display: flex; flex: 1; flex-direction: column;">
                        <div class="chat-header">
                            <div class="chat-header-info">

                                <div class="chat-user-details">
                                    <h4 id="chatUserName">Não Encontrado - Selecione uma conversa</h4>
                                </div>
                            </div>
                            <div class="chat-actions">
                                <button class="chat-action-btn" title="Arquivar">
                                    <i class="fas fa-archive"></i>
                                </button>
                                <button class="chat-action-btn" title="Informações">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                <button class="chat-action-btn" title="Mais opções">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </div>

                        <div class="chat-messages" id="chatMessages">

                        </div>
                        <div class="chat-input-container" id="BotaoEscrever">
                        </div>
                    </div>
                </div>
        </main>
    </div>
    <script src="src/js/ChatAdmin.js"></script>