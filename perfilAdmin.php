<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/Anunciante.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/PerfilAdmin.js"></script>
    <script src="src/js/Adminstrador.js"></script>
</head>


<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <span class="logo-icon"><i class="fas fa-leaf"></i></span>
                <div class="logo-text">
                    <h1>WeGreen</h1>
                    <p>Moda Sustentável</p>
                </div>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link active" href="DashboardAdmin.php">
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
                            <span class="nav-text">Gestão de Utilizadores</span>
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
                        <a class="nav-link" href="fornecedores.php">
                            <span class="nav-icon"><i class="fas fa-truck"></i></span>
                            <span class="nav-text">Gestão de Fornecedores</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <div class="breadcrumb">
                        <span class="breadcrumb-item">
                            <i class="fas fa-home"></i> WeGreen
                        </span>
                        <i class="fas fa-chevron-right breadcrumb-separator"></i>
                        <span class="breadcrumb-item active">
                            <i class="fas fa-user"></i> Perfil
                        </span>
                    </div>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user" id="userMenuBtn">

                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item active" href="perfilAdmin.php">
                            <i class="fas fa-user"></i>
                            <span>Meu Perfil</span>
                        </a>
                        <button class="dropdown-item" onclick="showPasswordModal()">
                            <i class="fas fa-key"></i>
                            <span>Alterar Senha</span>
                        </button>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item dropdown-item-danger" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </button>
                    </div>
                </div>
            </nav>

            <div id="profile" class="page active">
                <div class="profile-container" id="profileCard">
                </div>

                <div class="profile-tabs">
                    <button class="profile-tab active" onclick="switchProfileTab('personal', this)">
                        <i class="fas fa-user"></i> Informações Pessoais
                    </button>
                    <button class="profile-tab" onclick="switchProfileTab('plan', this)">
                        <i class="fas fa-crown"></i> Plano & Ranking
                    </button>
                    <button class="profile-tab" onclick="switchProfileTab('security', this)">
                        <i class="fas fa-shield-alt"></i> Segurança
                    </button>
                </div>

                <div class="profile-tab-content">
                    <div id="tab-personal" class="tab-pane active">
                        <div class="profile-section" id="profileInfo">
                        </div>
                    </div>
                    <div id="tab-plan" class="tab-pane">
                        <div class="profile-section" id="profilePlan">
                        </div>
                    </div>
                    <div id="tab-security" class="tab-pane">
                        <div class="profile-section" id="profileSecurity">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Alterar Senha -->
            <div id="passwordModal" class="modal">
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header">
                        <h3>Alterar Senha</h3>
                        <span class="close close-btn" onclick="closePasswordModal()">&times;</span>
                    </div>
                    <form id="passwordForm" class="profile-form" style="margin-top: 20px;">
                        <input type="text" name="username" autocomplete="username"
                            value="<?php echo $_SESSION['email'] ?? ''; ?>" style="display: none;" readonly>
                        <div class="form-group">
                            <label>Senha Atual</label>
                            <input type="password" id="currentPassword" autocomplete="current-password" required>
                        </div>
                        <div class="form-group">
                            <label>Nova Senha</label>
                            <input type="password" id="newPassword" autocomplete="new-password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmar Nova Senha</label>
                            <input type="password" id="confirmPassword" autocomplete="new-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                            <i class="fas fa-key"></i> Alterar Senha
                        </button>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
    $(document).ready(function() {
        initProfilePage();
    });
    </script>
    <script src="src/js/PerfilAdmin.js"></script>
</body>
<?php
}else{
    echo "sem permissão!";
}
?>

</html>
