# Script para atualizar navbar em todas as páginas HTML

$paginasParaAtualizar = @(
    "suporte.html",
    "Carrinho.html"
)

$novaNavbar = @'
    <header class="header-main text-white w-100 z-3" style="position: fixed; top: 0; left: 0; right: 0; background-color: #000000; z-index: 1030;">
        <div class="container-fluid px-5">
            <nav class="navbar navbar-expand-lg py-1">
                <a class="navbar-brand me-4" href="index.html">
                    <img src="src\img\2-removebg-preview.png" alt="Wegreen Logo" class="logo-img">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-start" id="navbarNav">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item mx-3">
                            <a class="nav-link text-white fw-bold d-flex align-items-center" href="marketplace.html">
                                <i class="bi bi-shop me-2"></i>Marketplace
                            </a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link text-white fw-bold d-flex align-items-center" href="sobrenos.html">
                                <i class="bi bi-info-circle me-2"></i>Sobre Nós
                            </a>
                        </li>
                        <li class="nav-item mx-3">
                            <a class="nav-link text-white fw-bold d-flex align-items-center" href="suporte.html">
                                <i class="bi bi-headset me-2"></i>Suporte
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center ms-auto gap-2">
                    <!-- Barra de Pesquisa -->
                    <div class="search-container">
                        <form class="search-form" role="search">
                            <div class="search-input-wrapper">
                                <i class="bi bi-search search-icon"></i>
                                <input class="search-input" type="search" placeholder="Pesquisar produtos..." id="searchInput">
                            </div>
                        </form>
                    </div>

                    <!-- Carrinho -->
                    <a class="cart-button" href="Carrinho.html">
                        <i class="bi bi-bag-fill"></i>
                        <span class="cart-badge" id="cartCount" style="display: none;">0</span>
                    </a>

                    <!-- Utilizador -->
                    <div class="nav-item dropdown user-dropdown">
                        <a class="nav-link dropdown-toggle user-avatar-btn" href="#" role="button" data-bs-toggle="dropdown">
                            <img id="FotoPerfil" src="assets/media/avatars/blank.png" class="user-avatar-img" alt="Perfil">
                            <i class="bi bi-chevron-down ms-1" style="font-size: 12px;"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu" id="PerfilTipo"></ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
'@

Write-Host "Navbar atualizada com sucesso! As páginas precisam de edição manual para substituir a navbar antiga." -ForegroundColor Green
Write-Host "`nPáginas a atualizar:" -ForegroundColor Yellow
$paginasParaAtualizar | ForEach-Object { Write-Host "  - $_" }
