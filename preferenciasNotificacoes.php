<?php
session_start();

// Permitir acesso a clientes (tipo 2) e anunciantes (tipo 3) e admin (tipo 1)
if(!isset($_SESSION['utilizador']) || !in_array($_SESSION['tipo'], [1, 2, 3])){
    header("Location: login.html");
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

    <style>
        .preferences-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }

        .preferences-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 25px;
        }

        .preferences-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .preferences-header i {
            font-size: 32px;
            color: #22c55e;
        }

        .preferences-header h2 {
            margin: 0;
            color: #1f2937;
            font-size: 24px;
        }

        .preferences-section {
            margin-bottom: 30px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-title i {
            color: #3b82f6;
            font-size: 18px;
        }

        .section-title h3 {
            margin: 0;
            color: #374151;
            font-size: 18px;
            font-weight: 600;
        }

        .section-description {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .preference-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 10px;
            background: #f9fafb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .preference-item:hover {
            background: #f3f4f6;
        }

        .preference-info {
            flex: 1;
        }

        .preference-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
            font-size: 15px;
        }

        .preference-description {
            color: #6b7280;
            font-size: 13px;
        }

        .toggle-switch {
            position: relative;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 30px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #22c55e;
        }

        input:checked + .slider:before {
            transform: translateX(30px);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #22c55e;
            color: white;
        }

        .btn-primary:hover {
            background-color: #16a34a;
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .info-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-left: 4px solid #22c55e;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .info-box i {
            color: #16a34a;
            margin-right: 10px;
        }

        .info-box p {
            margin: 0;
            color: #065f46;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-leaf"></i>
                <div class="logo-text">
                    <h2>WeGreen</h2>
                    <p>Moda Sustentável</p>
                </div>
            </div>

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

    <script>
        $(document).ready(function() {

            // Carregar preferências ao iniciar
            carregarPreferencias();

            // Salvar preferências
            $('#btnSalvar').click(function() {
                salvarPreferencias();
            });

            // Ativar todas
            $('#btnAtivarTodas').click(function() {
                Swal.fire({
                    title: 'Ativar todas as notificações?',
                    text: 'Receberá todos os tipos de notificações por email',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#22c55e',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sim, ativar todas',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'src/controller/controllerNotificacoes.php?op=ativarTodas',
                            type: 'POST',
                            success: function(response) {
                                const data = JSON.parse(response);
                                if (data.success) {
                                    Swal.fire('Sucesso!', data.message, 'success');
                                    carregarPreferencias();
                                } else {
                                    Swal.fire('Erro!', data.message, 'error');
                                }
                            }
                        });
                    }
                });
            });

            // Desativar todas
            $('#btnDesativarTodas').click(function() {
                Swal.fire({
                    title: 'Desativar todas as notificações?',
                    text: 'Não receberá mais emails sobre encomendas',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sim, desativar todas',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'src/controller/controllerNotificacoes.php?op=desativarTodas',
                            type: 'POST',
                            success: function(response) {
                                const data = JSON.parse(response);
                                if (data.success) {
                                    Swal.fire('Sucesso!', data.message, 'success');
                                    carregarPreferencias();
                                } else {
                                    Swal.fire('Erro!', data.message, 'error');
                                }
                            }
                        });
                    }
                });
            });

            function carregarPreferencias() {
                $.ajax({
                    url: 'src/controller/controllerNotificacoes.php?op=getPreferencias',
                    type: 'GET',
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            const pref = data.preferencias;

                            // Atualizar checkboxes
                            $('#email_confirmacao').prop('checked', pref.email_confirmacao == 1);
                            $('#email_processando').prop('checked', pref.email_processando == 1);
                            $('#email_enviado').prop('checked', pref.email_enviado == 1);
                            $('#email_entregue').prop('checked', pref.email_entregue == 1);
                            $('#email_cancelamento').prop('checked', pref.email_cancelamento == 1);
                            $('#email_novas_encomendas_anunciante').prop('checked', pref.email_novas_encomendas_anunciante == 1);
                            $('#email_encomendas_urgentes').prop('checked', pref.email_encomendas_urgentes == 1);
                        }
                    }
                });
            }

            function salvarPreferencias() {
                const preferencias = {
                    email_confirmacao: $('#email_confirmacao').is(':checked') ? 1 : 0,
                    email_processando: $('#email_processando').is(':checked') ? 1 : 0,
                    email_enviado: $('#email_enviado').is(':checked') ? 1 : 0,
                    email_entregue: $('#email_entregue').is(':checked') ? 1 : 0,
                    email_cancelamento: $('#email_cancelamento').is(':checked') ? 1 : 0,
                    email_novas_encomendas_anunciante: $('#email_novas_encomendas_anunciante').is(':checked') ? 1 : 0,
                    email_encomendas_urgentes: $('#email_encomendas_urgentes').is(':checked') ? 1 : 0,
                    op: 'salvarPreferencias'
                };

                $.ajax({
                    url: 'src/controller/controllerNotificacoes.php',
                    type: 'POST',
                    data: preferencias,
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Preferências Salvas!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Erro!', data.message, 'error');
                        }
                    }
                });
            }
        });
    </script>
    <script src="src/js/alternancia.js"></script>
</body>
</html>
