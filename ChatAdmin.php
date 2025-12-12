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
                        <a class="nav-link" href="vendas.php">
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ProdutosAdmin.php">
                            <span class="nav-icon"><i class="fas fa-check-circle"></i></span>
                            <span class="nav-text">Aprovar Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                            <span class="nav-text">Análises</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="chats.php">
                            <span class="nav-icon"><i class="fas fa-comments"></i></span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fornecedores.php">
                            <span class="nav-icon"><i class="fas fa-truck"></i></span>
                            <span class="nav-text">Gestão de Fornecedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfilAdmin.php">
                            <span class="nav-icon"><i class="fas fa-cog"></i></span>
                            <span class="nav-text">Configurações</span>
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
                            <input type="text" placeholder="Pesquisar conversas..." id="searchInput">
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

                </div>

                <div class="chat-messages" id="chatMessages">
                    <div class="message">
                        <div class="message-avatar">M</div>
                        <div class="message-content">
                            <div class="message-bubble">
                                Olá, preciso de ajuda com um pedido
                            </div>
                            <div class="message-time">10:28</div>
                        </div>
                    </div>

                    <div class="message">
                        <div class="message-avatar">M</div>
                        <div class="message-content">
                            <div class="message-bubble">
                                Fiz uma encomenda há 3 dias e ainda não recebi informações sobre o envio
                            </div>
                            <div class="message-time">10:30</div>
                        </div>
                    </div>
                </div>

                <div class="chat-input-container">
                    <div class="chat-input-wrapper">
                        <div class="chat-input-tools">
                            <button class="input-tool-btn" title="Anexar ficheiro">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button class="input-tool-btn" title="Emoji">
                                <i class="fas fa-smile"></i>
                            </button>
                        </div>
                        <textarea class="chat-input" id="messageInput" placeholder="Escreva sua mensagem..."
                            rows="1"></textarea>
                        <button class="send-btn" id="sendBtn">
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
    <script>
    // Auto-resize textarea
    const messageInput = document.getElementById('messageInput');
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Send message
    const sendBtn = document.getElementById('sendBtn');
    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;

        const chatMessages = document.getElementById('chatMessages');

        const now = new Date();
        const timeString = now.getHours().toString().padStart(2, '0') + ':' +
            now.getMinutes().toString().padStart(2, '0');

        const messageDiv = document.createElement('div');
        messageDiv.className = 'message sent';
        messageDiv.innerHTML = `
                <div class="message-avatar">A</div>
                <div class="message-content">
                    <div class="message-bubble">${message}</div>
                    <div class="message-time">${timeString}</div>
                </div>
            `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        messageInput.value = '';
        messageInput.style.height = 'auto';
    }

    // Search conversations
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const conversations = document.querySelectorAll('.conversation-item');

        conversations.forEach(conv => {
            const name = conv.querySelector('.conversation-name').textContent.toLowerCase();
            const preview = conv.querySelector('.conversation-preview').textContent.toLowerCase();

            if (name.includes(searchTerm) || preview.includes(searchTerm)) {
                conv.style.display = 'flex';
            } else {
                conv.style.display = 'none';
            }
        });
    });

    // Switch conversations
    const conversationItems = document.querySelectorAll('.conversation-item');
    conversationItems.forEach(item => {
        item.addEventListener('click', function() {
            conversationItems.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const userName = this.querySelector('.conversation-name').textContent;
            const userInitial = userName.charAt(0);

            document.getElementById('chatUserName').textContent = userName;
            document.getElementById('chatUserAvatar').textContent = userInitial;

            const unreadBadge = this.querySelector('.conversation-unread');
            if (unreadBadge) {
                unreadBadge.remove();
            }
        });
    });
    </script>