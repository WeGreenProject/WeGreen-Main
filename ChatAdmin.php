<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats - WeGreen Admin</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <style>
    /* ========================================
           WEGREEN ADMIN CHAT - DARK THEME
           ======================================== */

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --wegreen-primary: #A6D90C;
        --wegreen-dark: #90c207;
        --wegreen-darker: #7fbf00;
        --gold: #ffd700;
        --gold-light: #ffed4e;
        --bg-main: #0a0a0a;
        --bg-dark: #1c1c1c;
        --bg-darker: #121212;
        --bg-card: #1a1a1a;
        --text-primary: #ffffff;
        --text-secondary: #9a9a9a;
        --text-muted: #6b6b6b;
        --border-color: #2a2a2a;
        --border-light: #333;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.5);
        --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.6);
        --shadow-lg: 0 10px 30px rgba(166, 217, 12, 0.2);
        --accent-gradient: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
        --gold-gradient: linear-gradient(90deg, #ffd700 0%, #ffed4e 100%);
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--bg-main);
        color: var(--text-primary);
        overflow-x: hidden;
        line-height: 1.6;
    }

    .container {
        display: flex;
        min-height: 100vh;
    }

    /* SIDEBAR */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, var(--bg-dark) 0%, var(--bg-darker) 100%);
        border-right: 1px solid var(--border-color);
        padding: 0;
        position: fixed;
        height: 100vh;
        z-index: 100;
        box-shadow: 2px 0 20px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 35px 25px;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-card);
        text-decoration: none;
    }

    .logo-icon {
        width: 48px;
        height: 48px;
        background: var(--accent-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--bg-main);
        box-shadow: 0 4px 12px rgba(166, 217, 12, 0.3);
    }

    .logo-text h1 {
        font-size: 20px;
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 2px;
    }

    .logo-text p {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .nav-menu {
        list-style: none;
        padding: 15px 0;
        flex: 1;
        overflow-y: auto;
    }

    .nav-item {
        margin-bottom: 2px;
        padding: 0 15px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        background: transparent;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        border-radius: 10px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        text-align: left;
        font-size: 15px;
        font-weight: 500;
        text-decoration: none;
        position: relative;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: var(--wegreen-primary);
        border-radius: 0 3px 3px 0;
        transition: height 0.25s;
    }

    .nav-link:hover {
        background: rgba(166, 217, 12, 0.1);
        color: var(--wegreen-primary);
    }

    .nav-link:hover::before {
        height: 24px;
    }

    .nav-link.active {
        background: rgba(166, 217, 12, 0.15);
        color: var(--wegreen-primary);
        font-weight: 600;
    }

    .nav-link.active::before {
        height: 32px;
    }

    .nav-icon {
        font-size: 18px;
        width: 20px;
    }

    /* MAIN CONTENT */
    .main-content {
        flex: 1;
        margin-left: 280px;
        padding: 40px;
        background: var(--bg-main);
        min-height: 100vh;
    }

    .page-header {
        margin-bottom: 40px;
    }

    .page-header h2 {
        font-size: 36px;
        color: var(--wegreen-primary);
        margin-bottom: 10px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .page-header p {
        color: var(--text-secondary);
        font-size: 16px;
    }

    /* CHAT TYPE SELECTOR */
    .chat-type-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .chat-type-card {
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--border-color) 100%);
        border: 2px solid var(--border-light);
        border-radius: 20px;
        padding: 40px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .chat-type-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--accent-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .chat-type-card:hover {
        transform: translateY(-5px);
        border-color: var(--wegreen-primary);
        box-shadow: var(--shadow-lg);
    }

    .chat-type-card:hover::before {
        opacity: 0.1;
    }

    .chat-type-icon {
        font-size: 64px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .chat-type-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }

    .chat-type-desc {
        color: var(--text-secondary);
        font-size: 14px;
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }

    .chat-type-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--gold-gradient);
        color: #000;
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        z-index: 2;
    }

    /* CHAT INTERFACE */
    .chat-interface {
        display: none;
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--border-color) 100%);
        border: 2px solid var(--border-light);
        border-radius: 20px;
        overflow: hidden;
        height: calc(100vh - 200px);
    }

    .chat-interface.active {
        display: grid;
        grid-template-columns: 350px 1fr;
    }

    /* CONVERSATIONS LIST */
    .conversations-panel {
        background: var(--bg-darker);
        border-right: 2px solid var(--border-color);
        display: flex;
        flex-direction: column;
    }

    .conversations-header {
        padding: 25px;
        border-bottom: 2px solid var(--border-color);
        background: var(--bg-card);
    }

    .conversations-header h3 {
        color: var(--wegreen-primary);
        font-size: 20px;
        margin-bottom: 15px;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 12px 15px 12px 45px;
        background: var(--bg-main);
        border: 2px solid var(--border-light);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--wegreen-primary);
    }

    .search-box::before {
        content: '🔍';
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
    }

    .conversations-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: transparent;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 5px;
        border: 2px solid transparent;
    }

    .conversation-item:hover {
        background: rgba(166, 217, 12, 0.1);
    }

    .conversation-item.active {
        background: rgba(166, 217, 12, 0.15);
        border-color: var(--wegreen-primary);
    }

    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--accent-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        border: 2px solid var(--border-light);
    }

    .conversation-info {
        flex: 1;
        min-width: 0;
    }

    .conversation-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 15px;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conversation-preview {
        color: var(--text-muted);
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conversation-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
    }

    .conversation-time {
        font-size: 11px;
        color: var(--text-muted);
    }

    .unread-badge {
        background: var(--gold-gradient);
        color: #000;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
    }

    /* CHAT AREA */
    .chat-area {
        display: flex;
        flex-direction: column;
        background: var(--bg-main);
    }

    .chat-header {
        padding: 25px;
        background: var(--bg-card);
        border-bottom: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-user-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .chat-user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--accent-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        border: 2px solid var(--wegreen-primary);
    }

    .chat-user-details h4 {
        color: var(--text-primary);
        font-size: 18px;
        margin-bottom: 4px;
    }

    .chat-user-status {
        color: var(--text-secondary);
        font-size: 13px;
    }

    .chat-user-status.online::before {
        content: '●';
        color: #00c851;
        margin-right: 5px;
    }

    .chat-actions {
        display: flex;
        gap: 10px;
    }

    .chat-action-btn {
        width: 40px;
        height: 40px;
        background: rgba(166, 217, 12, 0.1);
        border: 1px solid var(--border-light);
        border-radius: 10px;
        color: var(--wegreen-primary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .chat-action-btn:hover {
        background: rgba(166, 217, 12, 0.2);
        transform: scale(1.05);
    }

    .back-btn {
        background: transparent;
        border: 2px solid var(--wegreen-primary);
        color: var(--wegreen-primary);
        padding: 10px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: none;
    }

    .back-btn:hover {
        background: rgba(166, 217, 12, 0.1);
    }

    /* MESSAGES AREA */
    .messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 30px;
        background: var(--bg-darker);
    }

    .messages-area::-webkit-scrollbar {
        width: 8px;
    }

    .messages-area::-webkit-scrollbar-track {
        background: var(--bg-main);
    }

    .messages-area::-webkit-scrollbar-thumb {
        background: var(--wegreen-primary);
        border-radius: 10px;
    }

    .date-divider {
        text-align: center;
        margin: 25px 0;
    }

    .date-divider span {
        background: var(--bg-card);
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 12px;
        color: var(--text-secondary);
        font-weight: 600;
        border: 1px solid var(--border-light);
    }

    .message-wrapper {
        display: flex;
        margin-bottom: 20px;
        gap: 12px;
    }

    .message-wrapper.sent {
        justify-content: flex-end;
    }

    .message-wrapper.received {
        justify-content: flex-start;
    }

    .message-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--accent-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .message-content {
        max-width: 60%;
        display: flex;
        flex-direction: column;
    }

    .message-wrapper.sent .message-content {
        align-items: flex-end;
    }

    .message-wrapper.received .message-content {
        align-items: flex-start;
    }

    .message-bubble {
        padding: 12px 18px;
        border-radius: 16px;
        font-size: 15px;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .message-wrapper.sent .message-bubble {
        background: var(--accent-gradient);
        color: #000;
        font-weight: 500;
        border-radius: 16px 16px 4px 16px;
    }

    .message-wrapper.received .message-bubble {
        background: var(--bg-card);
        color: var(--text-primary);
        border: 1px solid var(--border-light);
        border-radius: 16px 16px 16px 4px;
    }

    .message-time {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 5px;
        padding: 0 5px;
    }

    /* INPUT AREA */
    .input-area {
        padding: 25px;
        background: var(--bg-card);
        border-top: 2px solid var(--border-color);
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .input-actions {
        display: flex;
        gap: 10px;
    }

    .input-action-btn {
        width: 44px;
        height: 44px;
        background: rgba(166, 217, 12, 0.1);
        border: none;
        border-radius: 12px;
        color: var(--text-secondary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
    }

    .input-action-btn:hover {
        background: rgba(166, 217, 12, 0.2);
        color: var(--wegreen-primary);
    }

    .input-wrapper {
        flex: 1;
    }

    .message-input {
        width: 100%;
        padding: 14px 18px;
        background: var(--bg-main);
        border: 2px solid var(--border-light);
        border-radius: 12px;
        color: var(--text-primary);
        font-size: 15px;
        font-family: 'Segoe UI', sans-serif;
        resize: none;
        outline: none;
        transition: all 0.3s;
        min-height: 48px;
        max-height: 120px;
    }

    .message-input:focus {
        border-color: var(--wegreen-primary);
    }

    .send-btn {
        width: 48px;
        height: 48px;
        background: var(--accent-gradient);
        border: none;
        border-radius: 12px;
        color: #000;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
    }

    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(166, 217, 12, 0.4);
    }

    .send-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* EMPTY STATE */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
        text-align: center;
        padding: 40px;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: var(--text-secondary);
        font-size: 20px;
        margin-bottom: 10px;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .chat-interface.active {
            grid-template-columns: 300px 1fr;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
        }

        .logo-text,
        .nav-text {
            display: none;
        }

        .main-content {
            margin-left: 80px;
            padding: 20px;
        }

        .chat-interface.active {
            grid-template-columns: 1fr;
        }

        .conversations-panel {
            display: none;
        }

        .conversations-panel.mobile-show {
            display: flex;
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 10;
        }

        .back-btn {
            display: block;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <a href="index.html" style="text-decoration: none;">
                <div class="logo">
                    <span class="logo-icon">👔</span>
                    <div class="logo-text">
                        <h1>Wegreen</h1>
                        <p>Painel do Administrador</p>
                    </div>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="DashboardAdmin.php">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoProdutosAdmin.php">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vendas.php">
                            <span class="nav-icon">🛍️</span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ProdutosAdmin.php">
                            <span class="nav-icon">🛒</span>
                            <span class="nav-text">Aprovar Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon">📈</span>
                            <span class="nav-text">Análises</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="chats.php">
                            <span class="nav-icon">💬</span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fornecedores.php">
                            <span class="nav-icon">🚚</span>
                            <span class="nav-text">Gestão de Fornecedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfilAdmin.php">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="page-header">
                <h2>💬 Sistema de Chats</h2>
                <p>Gerencie conversas de suporte e vendas</p>
            </div>

            <!-- CHAT TYPE SELECTOR -->
            <div class="chat-type-selector" id="chatTypeSelector">
                <div class="chat-type-card" onclick="selectChatType('support')">
                    <div class="chat-type-badge">Suporte</div>
                    <div class="chat-type-icon">🎧</div>
                    <h3 class="chat-type-title">Suporte aos Clientes</h3>
                    <p class="chat-type-desc">Responda dúvidas, resolva problemas e ajude os clientes com suporte
                        profissional</p>
                </div>

                <div class="chat-type-card" onclick="selectChatType('sales')">
                    <div class="chat-type-badge">Vendas</div>
                    <div class="chat-type-icon">🛍️</div>
                    <h3 class="chat-type-title">Minhas Vendas</h3>
                    <p class="chat-type-desc">Converse com compradores interessados nos seus produtos e feche negócios
                    </p>
                </div>
            </div>

            <!-- CHAT INTERFACE -->
            <div class="chat-interface" id="chatInterface">
                <!-- CONVERSATIONS PANEL -->
                <div class="conversations-panel" id="conversationsPanel">
                    <div class="conversations-header">
                        <h3 id="chatTypeTitle">Conversas de Suporte</h3>
                        <div class="search-box">
                            <input type="text" placeholder="Pesquisar conversas..." id="searchConversations">
                        </div>
                    </div>
                    <div class="conversations-list" id="conversationsList">
                        <!-- Conversations will be loaded here -->
                    </div>
                </div>

                <!-- CHAT AREA -->
                <div class="chat-area">
                    <div class="chat-header">
                        <button class="back-btn" onclick="backToChatTypes()">← Voltar</button>
                        <div class="chat-user-info" id="chatUserInfo">
                            <div class="empty-state">
                                <div class="empty-state-icon">💬</div>
                                <h3>Selecione uma conversa</h3>
                                <p>Escolha uma conversa da lista para começar</p>
                            </div>
                        </div>
                        <div class="chat-actions" id="chatActions" style="display: none;">
                            <button class="chat-action-btn" title="Informações">ℹ️</button>
                            <button class="chat-action-btn" title="Arquivar">📁</button>
                            <button class="chat-action-btn" title="Mais opções">⋮</button>
                        </div>
                    </div>

                    <div class="messages-area" id="messagesArea">
                        <div class="empty-state">
                            <div class="empty-state-icon">💬</div>
                            <h3>Nenhuma conversa selecionada</h3>
                            <p>Escolha uma conversa da lista à esquerda para ver as mensagens</p>
                        </div>
                    </div>

                    <div class="input-area" id="inputArea" style="display: none;">
                        <div class="input-actions">
                            <button class="input-action-btn" title="Anexar imagem">📷</button>
                            <button class="input-action-btn" title="Anexar ficheiro">📎</button>
                        </div>
                        <div class="input-wrapper">
                            <textarea class="message-input" id="messageInput" placeholder="Escreva a sua mensagem..."
                                rows="1"></textarea>
                        </div>
                        <button class="send-btn" id="sendBtn" disabled>
                            ➤
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    const mockConversations = {
        support: [{
                id: 1,
                name: "João Silva",
                avatar: "👤",
                preview: "Tenho uma dúvida sobre o envio...",
                time: "10:30",
                unread: 2,
                status: "online"
            },
            {
                id: 2,
                name: "Maria Santos",
                avatar: "👩",
                preview: "O produto chegou danificado",
                time: "09:15",
                unread: 1,
                status: "offline"
            }
        ]
    };
    </script>