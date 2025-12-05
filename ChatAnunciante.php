<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat com Anunciante - WeGreen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --wegreen: #adff2f;
            --wegreen-dark: #94e126;
            --dark-bg: #414429;
            --bg: #ffffff;
            --text: #111;
            --muted: #6c6c6c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #414429 0%, #2a2b1c 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .chat-container {
            width: 100vw;
            height: 100vh;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Header */
        .chat-header {
            background: linear-gradient(135deg, #adff2f 0%, #9acd32 100%);
            padding: 24px 32px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            min-height: 90px;
        }

        .btn-back {
            background: rgba(0,0,0,0.1);
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.2rem;
        }

        .btn-back:hover {
            background: rgba(0,0,0,0.2);
            transform: scale(1.05);
        }

        .seller-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #414429;
            object-fit: cover;
        }

        .seller-info {
            flex: 1;
        }

        .seller-name {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: #000;
        }

        .seller-status {
            margin: 0;
            font-size: 0.95rem;
            color: #414429;
            opacity: 0.8;
            margin-top: 4px;
        }

        .seller-status.online::before {
            content: '●';
            color: #00c853;
            margin-right: 5px;
            font-size: 1.2rem;
        }

        .btn-menu {
            background: rgba(0,0,0,0.1);
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.2rem;
        }

        .btn-menu:hover {
            background: rgba(0,0,0,0.2);
            transform: scale(1.05);
        }

        /* Messages Area */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 32px 40px;
            background: #f8f9fa;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(173,255,47,0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(65,68,41,0.03) 0%, transparent 50%);
        }

        .chat-messages::-webkit-scrollbar {
            width: 10px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #adff2f;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #9acd32;
        }

        .message-wrapper {
            display: flex;
            margin-bottom: 20px;
            gap: 14px;
        }

        .message-wrapper.sent {
            justify-content: flex-end;
        }

        .message-wrapper.received {
            justify-content: flex-start;
        }

        .message-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
        }

        .message-avatar.seller-avatar-msg {
            border: 2px solid #adff2f;
        }

        .message-avatar.user-avatar-msg {
            border: 2px solid #414429;
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
            padding: 14px 20px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            font-size: 1rem;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .message-wrapper.sent .message-bubble {
            background: linear-gradient(135deg, #adff2f 0%, #9acd32 100%);
            color: #000;
            font-weight: 600;
            border-radius: 18px 18px 4px 18px;
        }

        .message-wrapper.received .message-bubble {
            background: #fff;
            color: #2b2b2b;
            border-radius: 18px 18px 18px 4px;
        }

        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 4px;
            padding: 0 4px;
        }

        /* Date Divider */
        .date-divider {
            text-align: center;
            margin: 20px 0;
        }

        .date-divider span {
            background: rgba(0,0,0,0.05);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 600;
        }

        /* Input Area */
        .chat-input-area {
            padding: 24px 40px;
            background: #fff;
            border-top: 2px solid #e9ecef;
            display: flex;
            gap: 14px;
            align-items: flex-end;
            min-height: 100px;
        }

        .btn-attachment {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 10px;
            border-radius: 10px;
            transition: all 0.3s;
            color: #6c757d;
            font-size: 1.2rem;
        }

        .btn-attachment:hover {
            background: #f8f9fa;
            color: #adff2f;
            transform: scale(1.1);
        }

        .input-wrapper {
            flex: 1;
            position: relative;
        }

        .message-input {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e9ecef;
            border-radius: 14px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            resize: none;
            outline: none;
            transition: all 0.3s;
            min-height: 56px;
            max-height: 140px;
        }

        .message-input:focus {
            border-color: #adff2f;
            box-shadow: 0 0 0 3px rgba(173, 255, 47, 0.1);
        }

        .btn-send {
            background: linear-gradient(135deg, #adff2f 0%, #9acd32 100%);
            border: none;
            border-radius: 14px;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #000;
            font-size: 1.2rem;
        }

        .btn-send:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(173,255,47,0.4);
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-send:disabled:hover {
            transform: scale(1);
            box-shadow: none;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: none;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            background: #fff;
            border-radius: 18px 18px 18px 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            width: fit-content;
        }

        .typing-indicator.active {
            display: flex;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #6c757d;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
                opacity: 0.7;
            }
            30% {
                transform: translateY(-10px);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chat-container {
                height: 100vh;
                max-height: 100vh;
                border-radius: 0;
            }

            body {
                padding: 0;
            }

            .message-content {
                max-width: 80%;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header" id="InfoAnunciante">
            <button class="btn-back" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            
            <img src="https://ui-avatars.com/api/?name=Vendedor&background=414429&color=adff2f&size=128" 
                 alt="Vendedor" 
                 class="seller-avatar"
                 id="sellerAvatar">
            
            <div class="seller-info">
                <h3 class="seller-name" id="sellerName">João Silva</h3>
            </div>
            
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" id="chatMessages">
            <!-- Date Divider -->
            <div class="date-divider">
                <span>Hoje</span>
            </div>

            <!-- Received Message -->
            <div class="message-wrapper received">
                <img src="https://ui-avatars.com/api/?name=Vendedor&background=adff2f&color=000&size=128" 
                     alt="Vendedor" 
                     class="message-avatar seller-avatar-msg">
                <div class="message-content">
                    <div class="message-bubble">
                        Olá! Como posso ajudar?
                    </div>
                    <span class="message-time">14:23</span>
                </div>
            </div>

            <!-- Sent Message -->
            <div class="message-wrapper sent">
                <div class="message-content">
                    <div class="message-bubble">
                        Boa tarde! O produto ainda está disponível?
                    </div>
                    <span class="message-time">14:25</span>
                </div>
                <img src="https://ui-avatars.com/api/?name=Usuario&background=414429&color=adff2f&size=128" 
                     alt="Você" 
                     class="message-avatar user-avatar-msg">
            </div>

            <!-- Received Message -->
            <div class="message-wrapper received">
                <img src="https://ui-avatars.com/api/?name=Vendedor&background=adff2f&color=000&size=128" 
                     alt="Vendedor" 
                     class="message-avatar seller-avatar-msg">
                <div class="message-content">
                    <div class="message-bubble">
                        Sim, ainda tenho em stock! Está interessado?
                    </div>
                    <span class="message-time">14:26</span>
                </div>
            </div>

            <!-- Typing Indicator -->
            <div class="message-wrapper received">
                <img src="https://ui-avatars.com/api/?name=Vendedor&background=adff2f&color=000&size=128" 
                     alt="Vendedor" 
                     class="message-avatar seller-avatar-msg">
                <div class="typing-indicator" id="typingIndicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="chat-input-area">
            <button class="btn-attachment" title="Anexar imagem">
                <i class="fas fa-image fa-lg"></i>
            </button>
            
            <button class="btn-attachment" title="Anexar ficheiro">
                <i class="fas fa-paperclip fa-lg"></i>
            </button>

            <div class="input-wrapper">
                <textarea 
                    class="message-input" 
                    id="messageInput" 
                    placeholder="Escreva a sua mensagem..."
                    rows="1"></textarea>
            </div>

            <button class="btn-send" id="sendBtn" disabled>
                <i class="fas fa-paper-plane fa-lg"></i>
            </button>
        </div>
    </div>

    <script>
        // Configurações
        const USER_ID = 1; // Substituir pelo ID real do utilizador logado
        const SELLER_ID = 2; // Substituir pelo ID real do vendedor
        const CHAT_ID = 1; // ID da conversa

        // Elementos DOM
        const chatMessages = document.getElementById('chatMessages');
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        const typingIndicator = document.getElementById('typingIndicator');

        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            
            // Enable/disable send button
            sendBtn.disabled = this.value.trim() === '';
        });

        // Enviar mensagem com Enter
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Enviar mensagem com botão
        sendBtn.addEventListener('click', sendMessage);

        // Função para enviar mensagem
        function sendMessage() {
            const message = messageInput.value.trim();
            
            if (message === '') return;

            // Adicionar mensagem ao chat
            addMessage(message, 'sent');
            
            // Limpar input
            messageInput.value = '';
            messageInput.style.height = 'auto';
            sendBtn.disabled = true;

            // Scroll para o fim
            scrollToBottom();

            // Enviar para o servidor (PHP)
            sendMessageToServer(message);

            // Simular resposta do vendedor (remover em produção)
            simulateSellerResponse();
        }

        // Adicionar mensagem ao chat
        function addMessage(text, type) {
            const time = new Date().toLocaleTimeString('pt-PT', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });

            const messageWrapper = document.createElement('div');
            messageWrapper.className = `message-wrapper ${type}`;

            const avatar = type === 'sent' 
                ? 'https://ui-avatars.com/api/?name=Usuario&background=414429&color=adff2f&size=128'
                : 'https://ui-avatars.com/api/?name=Vendedor&background=adff2f&color=000&size=128';

            const avatarClass = type === 'sent' ? 'user-avatar-msg' : 'seller-avatar-msg';

            if (type === 'sent') {
                messageWrapper.innerHTML = `
                    <div class="message-content">
                        <div class="message-bubble">${escapeHtml(text)}</div>
                        <span class="message-time">${time}</span>
                    </div>
                    <img src="${avatar}" alt="Avatar" class="message-avatar ${avatarClass}">
                `;
            } else {
                messageWrapper.innerHTML = `
                    <img src="${avatar}" alt="Avatar" class="message-avatar ${avatarClass}">
                    <div class="message-content">
                        <div class="message-bubble">${escapeHtml(text)}</div>
                        <span class="message-time">${time}</span>
                    </div>
                `;
            }

            // Inserir antes do typing indicator
            const typingWrapper = typingIndicator.closest('.message-wrapper');
            chatMessages.insertBefore(messageWrapper, typingWrapper);
        }

        // Enviar mensagem para o servidor PHP
        function sendMessageToServer(message) {
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `chat_id=${CHAT_ID}&user_id=${USER_ID}&message=${encodeURIComponent(message)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Mensagem enviada com sucesso');
                } else {
                    console.error('Erro ao enviar mensagem:', data.error);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
            });
        }

        // Simular resposta do vendedor (remover em produção)
        function simulateSellerResponse() {
            // Mostrar typing indicator
            typingIndicator.classList.add('active');
            scrollToBottom();

            setTimeout(() => {
                typingIndicator.classList.remove('active');
                addMessage('Obrigado pela mensagem! Vou responder em breve.', 'received');
                scrollToBottom();
            }, 2000);
        }

        // Carregar mensagens do servidor
        function loadMessages() {
            fetch(`get_messages.php?chat_id=${CHAT_ID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Processar mensagens
                        // Implementar lógica para adicionar mensagens ao chat
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar mensagens:', error);
                });
        }

        // Verificar novas mensagens periodicamente
        function checkNewMessages() {
            // Implementar polling ou WebSocket
            setInterval(() => {
                loadMessages();
            }, 5000); // Verificar a cada 5 segundos
        }

        // Scroll para o fim
        function scrollToBottom() {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Escapar HTML para prevenir XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
            // loadMessages();
            // checkNewMessages();
        });
    </script>
</body>
</html>