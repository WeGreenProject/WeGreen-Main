<?php
session_start();

if(!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])){
    header("Location: login.html");
    exit();
}

if(!in_array((int)$_SESSION['tipo'], [1, 2, 3], true)){
    header("Location: forbiddenerror.html");
    exit();
}

$tipo_user = $_SESSION['tipo'];
$is_anunciante = ($tipo_user == 3 || $tipo_user == 1);
$is_cliente = ($tipo_user == 2);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferências de Notificações - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>

    <link rel="stylesheet" href="src/css/preferenciasNotificacoes.css">
</head>
<body>
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
                    <?php if ($is_cliente): ?>
                    <a href="DashboardCliente.php" class="menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="minhasEncomendas.php" class="menu-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Minhas Encomendas</span>
                    </a>
                    <?php elseif ($is_anunciante): ?>
                    <a href="DashboardAnunciante.php" class="menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="gestaoEncomendasAnunciante.php" class="menu-item">
                        <i class="fas fa-boxes"></i>
                        <span>Gestão de Encomendas</span>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">Configurações</div>
                    <a href="preferenciasNotificacoes.php" class="menu-item active">
                        <i class="fas fa-bell"></i>
                        <span>Notificações</span>
                    </a>
                    <a href="logout.php" class="menu-item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="preferences-container">
                <div class="preferences-card">
                    <div class="preferences-header">
                        <i class="fas fa-bell"></i>
                        <div>
                            <h2>Preferências de Notificações</h2>
                            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
                                Gerencie as notificações que deseja receber por email
                            </p>
                        </div>
                    </div>

                    <!-- Preferências de Cliente -->
                    <?php if ($is_cliente): ?>
                    <div class="preferences-section">
                        <div class="section-title">
                            <i class="fas fa-user"></i>
                            <h3>Notificações de Encomendas</h3>
                        </div>
                        <div class="section-description">
                            Receba atualizações sobre o status das suas encomendas
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <div class="preference-label">📧 Confirmação de Encomenda</div>
                                <div class="preference-description">
                                    Receber email quando uma nova encomenda é criada e paga
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="email_confirmacao" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <div class="preference-label">📋 Encomenda em Processamento</div>
                                <div class="preference-description">
                                    Ser notificado quando o vendedor começa a preparar a encomenda
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="email_processando" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <div class="preference-label">🚚 Encomenda Enviada</div>
                                <div class="preference-description">
                                    Receber código de rastreio quando a encomenda é enviada
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="email_enviado" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <div class="preference-label">✅ Encomenda Entregue</div>
                                <div class="preference-description">
                                    Ser notificado quando a encomenda é entregue com sucesso
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="email_entregue" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <div class="preference-label">❌ Encomenda Cancelada</div>
                                <div class="preference-description">
                                    Receber email se uma encomenda for cancelada
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="email_cancelamento" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Preferências de Anunciante -->
                    <?php if ($is_anunciante): ?>
                    <div class="preferences-section">
                        <div class="section-title">
                            <i class="fas fa-store"></i>
                            <h3>Notificações de Vendas</h3>
                        </div>
                        <div class="section-description">
                            Receba alertas sobre novas encomendas e atualizações importantes
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <div class="preference-label">🎉 Nova Encomenda Recebida</div>
                                <div class="preference-description">
                                    Ser notificado imediatamente quando recebe uma nova encomenda
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="email_novas_encomendas_anunciante" checked>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="preference-item">
                            <div class="preference-info">
                                <div class="preference-label">⚠️ Encomendas Pendentes Urgentes</div>
                                <div class="preference-description">
                                    Receber alertas sobre encomendas pendentes há mais de 3 dias
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="email_encomendas_urgentes" checked>
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Botões de Ação -->
                    <div class="action-buttons">
                        <button class="btn btn-primary" id="btnSalvar">
                            <i class="fas fa-save"></i> Salvar Preferências
                        </button>
                        <button class="btn btn-secondary" id="btnAtivarTodas">
                            <i class="fas fa-check-circle"></i> Ativar Todas
                        </button>
                        <button class="btn btn-danger" id="btnDesativarTodas">
                            <i class="fas fa-times-circle"></i> Desativar Todas
                        </button>
                    </div>

                    <!-- Info Box -->
                    <div class="info-box">
                        <i class="fas fa-info-circle"></i>
                        <p>
                            <strong>Sobre as notificações:</strong> Utilizamos o serviço Brevo (SendinBlue)
                            para enviar emails seguros e confiáveis. As suas preferências são salvas
                            automaticamente e pode alterá-las a qualquer momento.
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="src/js/preferenciasNotificacoes.js"></script>
    <script src="src/js/alternancia.js"></script>
</body>
</html>
