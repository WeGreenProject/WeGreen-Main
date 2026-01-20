<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat com Anunciante - WeGreen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&family=Inter:wght@400;600;700&display=swap"
        rel="stylesheet">
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

    <style>
    :root {
        --wegreen: #adff2f;
        --wegreen-dark: #94e126;
        --wegreen-accent: #A6D90C;
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
        background: #f8f9fa;
        padding-top: 120px;
    }

    /* Top Bar */
    .top-bar {
        background-color: var(--wegreen-accent);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1031;
    }

    .slogan-text {
        font-size: 0.9rem;
    }

    /* Header */
    .header-main {
        background: #000;
        position: fixed;
        top: 0px;
        left: 0;
        right: 0;
        z-index: 1030;
    }

    .logo-img {
        height: 80px;
    }

    .navbar .nav-link {
        color: #fff !important;
        font-weight: 700;
    }

    .search-bar .form-control {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: none;
    }

    .search-bar .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    /* Main Content */
    .chat-container-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Product Card */
    .product-sidebar {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 140px;
    }

    .product-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .product-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark-bg);
        margin-bottom: 0.5rem;
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--wegreen-accent);
        margin-bottom: 1rem;
    }

    .product-status {
        display: inline-block;
        padding: 0.4rem 1rem;
        background: #e8f5e9;
        color: #2e7d32;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    /* Chat Area */
    .chat-area {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        height: 700px;
    }

    /* Chat Header */
    .chat-header {
        background: linear-gradient(135deg, var(--wegreen) 0%, var(--wegreen-dark) 100%);
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .seller-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 3px solid var(--dark-bg);
        object-fit: cover;
    }

    .seller-info h5 {
        margin: 0;
        font-weight: 700;
        color: #000;
    }

    .seller-status {
        font-size: 0.85rem;
        color: var(--dark-bg);
        margin: 0;
    }

    .seller-status.online::before {
        content: '●';
        color: #00c853;
        margin-right: 5px;
    }

    /* Messages Area */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 2rem;
        background: #f8f9fa;
    }

    .chat-messages::-webkit-scrollbar {
        width: 8px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: var(--wegreen);
        border-radius: 10px;
    }

    .date-divider {
        text-align: center;
        margin: 1.5rem 0;
    }

    .date-divider span {
        background: rgba(0, 0, 0, 0.05);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        color: #6c757d;
        font-weight: 600;
    }

    .message-wrapper {
        display: flex;
        margin-bottom: 1rem;
        gap: 0.75rem;
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
        object-fit: cover;
    }

    .message-content {
        max-width: 65%;
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
        padding: 0.75rem 1rem;
        border-radius: 16px;
        font-size: 0.95rem;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .message-wrapper.sent .message-bubble {
        background: linear-gradient(135deg, var(--wegreen) 0%, var(--wegreen-dark) 100%);
        color: #000;
        font-weight: 600;
        border-radius: 16px 16px 4px 16px;
    }

    .message-wrapper.received .message-bubble {
        background: #fff;
        color: #2b2b2b;
        border: 1px solid #e9ecef;
        border-radius: 16px 16px 16px 4px;
    }

    .message-time {
        font-size: 0.7rem;
        color: #6c757d;
        margin-top: 0.25rem;
        padding: 0 0.25rem;
    }

    /* Input Area */
    .chat-input-area {
        padding: 1.5rem;
        background: #fff;
        border-top: 2px solid #e9ecef;
        border-radius: 0 0 20px 20px;
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
    }

    .btn-attachment {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 10px;
        transition: all 0.3s;
        color: #6c757d;
        font-size: 1.2rem;
    }

    .btn-attachment:hover {
        background: #f8f9fa;
        color: var(--wegreen);
    }

    .input-wrapper {
        flex: 1;
    }

    .profile-img-small {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border: 2px solid var(--wegreen-accent);
    }

    .profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #198754;
    }

    .message-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 0.95rem;
        font-family: 'Inter', sans-serif;
        resize: none;
        outline: none;
        transition: all 0.3s;
        min-height: 48px;
        max-height: 120px;
    }

    .message-input:focus {
        border-color: var(--wegreen);
        box-shadow: 0 0 0 3px rgba(173, 255, 47, 0.1);
    }

    .btn-send {
        background: linear-gradient(135deg, var(--wegreen) 0%, var(--wegreen-dark) 100%);
        border: none;
        border-radius: 12px;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        color: #000;
        font-size: 1.1rem;
    }

    .btn-send:hover:not(:disabled) {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(173, 255, 47, 0.3);
    }

    .btn-send:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* Typing Indicator */
    .typing-indicator {
        display: none;
        align-items: center;
        gap: 6px;
        padding: 0.75rem 1rem;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 16px 16px 16px 4px;
        width: fit-content;
    }

    .typing-indicator.active {
        display: flex;
    }

    .typing-dot {
        width: 7px;
        height: 7px;
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

        0%,
        60%,
        100% {
            transform: translateY(0);
            opacity: 0.7;
        }

        30% {
            transform: translateY(-8px);
            opacity: 1;
        }
    }

    /* Responsive */
    @media (max-width: 991px) {
        .product-sidebar {
            position: relative;
            top: 0;
            margin-bottom: 2rem;
        }

        .chat-area {
            height: 600px;
        }
    }

    @media (max-width: 768px) {
        body {
            padding-top: 100px;
        }

        .logo-img {
            height: 60px;
        }

        .chat-area {
            height: 500px;
        }

        .message-content {
            max-width: 80%;
        }
    }
    </style>
</head>

<body>

    <!-- Top Bar -->
    <header class="header-main text-white position-absolute w-100 z-3">
        <div class="container-fluid px-5">
            <nav class="navbar navbar-expand-lg py-1">

                <a class="navbar-brand me-4" href="index.html">
                    <img src="src/img/2-removebg-preview.png" alt="Wegreen Logo" class="logo-img">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
                    <ul class="navbar-nav mb-2 mb-lg-0">

                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Mulher</a>
                            <ul class="dropdown-menu dropdown-menu-dark rounded-3">
                                <li><a class="dropdown-item" href="produtosmulher.html">Ver Tudo</a></li>
                                <li><a class="dropdown-item" href="#">Vestidos</a></li>
                                <li><a class="dropdown-item" href="#">Tops e T-shirts</a></li>
                                <li><a class="dropdown-item" href="#">Calças</a></li>
                                <li><a class="dropdown-item" href="#">Acessórios</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Homem</a>
                            <ul class="dropdown-menu dropdown-menu-dark rounded-3">
                                <li><a class="dropdown-item" href="produtoshomem.html">Ver Tudo</a></li>
                                <li><a class="dropdown-item" href="#">Blusas</a></li>
                                <li><a class="dropdown-item" href="#">Calças</a></li>
                                <li><a class="dropdown-item" href="#">Calçado</a></li>
                            </ul>
                        </li>

                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Crianças</a>
                            <ul class="dropdown-menu dropdown-menu-dark rounded-3">
                                <li><a class="dropdown-item" href="produtoscriança.html">Ver Tudo</a></li>
                                <li><a class="dropdown-item" href="#">Blusas</a></li>
                                <li><a class="dropdown-item" href="#">Casacos</a></li>
                                <li><a class="dropdown-item" href="#">T-shirts</a></li>
                                <li><a class="dropdown-item" href="#">Calças</a></li>
                                <li><a class="dropdown-item" href="#">Vestidos</a></li>
                                <li><a class="dropdown-item" href="#">Calçado</a></li>
                            </ul>
                        </li>

                        <li class="nav-item"><a class="nav-link text-white mx-2 fw-bold"
                                href="produtosDesigner.html">Designers</a></li>
                        <li class="nav-item"><a class="nav-link text-white mx-2 fw-bold"
                                href="produtoartesao.html">Artesãos</a></li>

                        <li class="nav-item dropdown mx-2">
                            <a class="nav-link dropdown-toggle text-white fw-bold" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">Marcas</a>
                            <ul class="dropdown-menu dropdown-menu-dark rounded-3">
                                <li><a class="dropdown-item" href="#">Naz</a></li>
                                <li><a class="dropdown-item" href="#">Nea Vegan Shoes</a></li>
                                <li><a class="dropdown-item" href="#">PlayUp®</a></li>
                                <li><a class="dropdown-item" href="#">Vintage for a Cause</a></li>
                                <li><a class="dropdown-item" href="#">Isto®</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a class="nav-link text-white mx-2 fw-bold" href="suporte.html">Suporte</a>
                        </li>
                    </ul>
                    </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center ms-auto">

                    <form class="d-flex search-bar me-3" role="search">
                        <input class="form-control rounded-pill me-2 border-0 bg-secondary bg-opacity-25 text-white"
                            type="search" placeholder="Pesquisar..." aria-label="Pesquisar">
                        <button class="btn btn-outline-light rounded-pill" type="submit"><i
                                class="bi bi-search"></i></button>
                    </form>

                    <a class="nav-link text-white mx-2 fs-5" href="#"><i class="bi bi-bag"></i></a>

                    <div class="nav-item dropdown" id="Perfil_do_Utilizador">
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="chat-container-wrapper">
        <div class="row g-4">
            <div class="col-lg-4" id="ProdutoChat">
                <div class="product-sidebar">
                    <img src="https://via.placeholder.com/400x400/adff2f/000000?text=Produto" alt="Produto"
                        class="product-image">

                    <h4 class="product-title">Casaco Verde Pistácio</h4>
                    <p class="text-muted mb-2">Por <strong>Naz</strong></p>
                    <div class="product-status">● Disponível</div>
                    <p class="product-price">€139,90</p>

                    <p class="text-muted small mb-3">
                        Casaco em lã alpaca 100% sustentável. Peça única feita à mão por artesãos locais.
                    </p>

                    <button class="btn btn-dark w-100 rounded-pill mb-2">
                        <i class="bi bi-eye me-2"></i>Ver Produto Completo
                    </button>

                    <button class="btn btn-outline-dark w-100 rounded-pill">
                        <i class="bi bi-heart me-2"></i>Adicionar aos Favoritos
                    </button>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-lg-8">
                <div class="chat-area">
                    <!-- Chat Header -->
                    <div class="chat-header" id="tituloChat">

                    </div>

                    <!-- Messages Area -->
                    <div class="chat-messages" id="chatMessages">
                        <div class="date-divider">
                            <span>Hoje</span>
                        </div>



                        <!-- Typing Indicator -->
                        <div class="message-wrapper received">
                            <img src="https://ui-avatars.com/api/?name=Vendedor&background=adff2f&color=000&size=128"
                                alt="Vendedor" class="message-avatar">
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
                            <i class="bi bi-image"></i>
                        </button>

                        <button class="btn-attachment" title="Anexar ficheiro">
                            <i class="bi bi-paperclip"></i>
                        </button>

                        <div class="input-wrapper">
                            <textarea class="message-input" id="messageInput" placeholder="Escreva a sua mensagem..."
                                rows="1"></textarea>
                        </div>

                        <button class="btn-send" id="sendBtn" onclick="ConsumidorRes()" disabled>
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/ChatAnunciante.js"></script>
</body>

</html>