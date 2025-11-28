<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Admin - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/perfilAdmin.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="index.html" style="text-decoration: none;">
                <div class="logo">
                    <span class="logo-icon">👔</span>
                    <div class="logo-text">
                        <h1>Wegreen</h1>
                        <p>Painel do Adminstrador</p>
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
                        <a class="nav-link" href="chats.php">
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
                        <a class="nav-link active" href="perfilAdmin.php">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="user-profile">
                <div class="profile-info" id="ProfileUser">
                </div>
                <button class="profile-settings-btn" onclick="window.location.href='perfilAdmin.php'"
                    title="Configurações">
                    <span>⚙️</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>Meu Perfil</h2>
                <p>Gerencie suas informações pessoais e configurações de conta</p>
            </div>

            <div class="profile-container" id="PerfilAdminInicial">

            </div>

            <!-- Profile Content Grid -->
            <div class="profile-content-grid">
                <!-- Personal Information -->
                <div class="profile-section" id="PerfilInfo">

                </div>
            </div>
        </main>
    </div>
    <script src="src/js/PerfilAdmin.js"></script>
</body>

</html>