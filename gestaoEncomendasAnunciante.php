<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomendas - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/Anunciante.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="src/js/Anunciante.js"></script>
    <script src="src/js/GuiaEnvio.js"></script>
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
                        <a class="nav-link" href="DashboardAnunciante.php">
                            <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoProdutosAnunciante.php">
                            <span class="nav-icon"><i class="fas fa-tshirt"></i></span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestaoEncomendasAnunciante.php">
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Encomendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="relatoriosAnunciante.php">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                            <span class="nav-text">Relatórios</span>
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
                            <i class="fas fa-shopping-bag"></i> Encomendas
                        </span>
                    </div>
                </div>
                <div class="navbar-right">
                    <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'" style="display: none;">
                        <i class="fas fa-crown"></i> Upgrade
                    </button>
                    <button class="navbar-icon-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=A6D90C&color=fff" alt="Usuário" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></span>
                            <span class="user-role">Anunciante</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #4a5568;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=A6D90C&color=fff" alt="Usuário" class="dropdown-avatar">
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

            <div id="sales" class="page active">
                <div class="page-header">
                    <h2>Gestão de Encomendas</h2>
                </div>

                <div class="stats-grid stats-grid-compact" id="encomendasSummary">
                    <div class="stat-card stat-card-compact" id="totalPendentesCard"></div>
                    <div class="stat-card stat-card-compact" id="totalProcessandoCard"></div>
                    <div class="stat-card stat-card-compact" id="totalEnviadasCard"></div>
                    <div class="stat-card stat-card-compact" id="totalEntreguesCard"></div>
                </div>

                <div class="filters">
                    <select id="filterEncomendaStatus">
                        <option value="">Todos os Status</option>
                        <option value="pendente">Pendente</option>
                        <option value="processando">Processando</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregue">Entregue</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                    <input type="date" id="filterDateFrom" placeholder="Data Inicial">
                    <input type="date" id="filterDateTo" placeholder="Data Final">
                </div>

                <div class="table-container">
                    <table id="encomendasTable" class="display">
                        <thead>
                            <tr>
                                <th>Nº Encomenda</th>
                                <th>Data</th>
                                <th>Cliente</th>
                                <th>Produtos</th>
                                <th>Transportadora</th>
                                <th>Lucro Líquido</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
                        <input type="text" name="username" autocomplete="username" value="<?php echo $_SESSION['email'] ?? ''; ?>" style="display: none;" readonly>
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
            initSalesPage();
        });
    </script>
</body>
<?php
}else{
    echo "sem permissão!";
}
?>

</html>
