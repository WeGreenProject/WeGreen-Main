<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="DashboardCliente.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
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
                    <a href="#" class="menu-item active" data-page="dashboard">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#" class="menu-item" data-page="orders">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Os Meus Pedidos</span>
                    </a>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">Conta</div>
                    <a href="#" class="menu-item" data-page="settings">
                        <i class="fas fa-cog"></i>
                        <span>Definições</span>
                    </a>
                    <a href="#" class="menu-item" data-page="support">
                        <i class="fas fa-headset"></i>
                        <span>Suporte</span>
                    </a>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">MB</div>
                    <div class="user-info">
                        <h4>Mariana Brites</h4>
                        <p><?php echo $_SESSION['email'] ?? 'cliente@wegreen.pt'; ?></p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Página Dashboard -->
            <div id="page-dashboard" class="page-content">
                <header class="dashboard-header">
                    <div class="header-title">
                        <h1>Dashboard</h1>
                        <p>Visão geral das suas compras e atividades</p>
                    </div>
                    <div class="header-actions">
                        <div class="date-filter">
                            <i class="far fa-calendar"></i>
                            <span>Janeiro 2024 - Dezembro 2024</span>
                        </div>
                    </div>
                </header>

                <div class="content-area">
                    <!-- Cards de Estatísticas -->
                    <div class="stats-grid">
                        <div class="stat-card highlight">
                            <div class="stat-header">
                                <span class="stat-title">Gastos Totais</span>
                                <div class="stat-icon">
                                    <i class="fas fa-euro-sign"></i>
                                </div>
                            </div>
                            <div class="stat-value">€565</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+23% desde o último mês</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Produtos Comprados</span>
                                <div class="stat-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="stat-value">24</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+15% desde o último mês</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Pedidos Ativos</span>
                                <div class="stat-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                            </div>
                            <div class="stat-value">3</div>
                            <div class="stat-change">
                                <i class="fas fa-minus"></i>
                                <span>Sem alterações</span>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <span class="stat-title">Economia CO₂</span>
                                <div class="stat-icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                            </div>
                            <div class="stat-value">12kg</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>Impacto positivo</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="charts-section">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Gastos Mensais</h3>
                                <div class="chart-legend">
                                    <div class="legend-item">
                                        <div class="legend-color" style="background-color: #3cb371;"></div>
                                        <span>Gastos (€)</span>
                                    </div>
                                </div>
                            </div>
                            <canvas id="monthlyExpensesChart" height="80"></canvas>
                        </div>

                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Produtos por Categoria</h3>
                            </div>
                            <canvas id="categoryChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Pedidos Recentes -->
                    <div class="orders-table-section">
                        <div class="table-header">
                            <h3 class="table-title">Pedidos Recentes</h3>
                            <div class="table-actions">
                                <button class="btn-filter">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nº Pedido</th>
                                        <th>Data</th>
                                        <th>Produtos</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="recentOrdersTable">
                                    <!-- Dados serão carregados dinamicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Página Os Meus Pedidos -->
            <div id="page-orders" class="page-content hidden">
                <header class="dashboard-header">
                    <div class="header-title">
                        <h1>Gestão de Encomendas</h1>
                        <p>Acompanhe todos os seus pedidos</p>
                    </div>
                </header>

                <div class="content-area">
                    <!-- Cards de Status -->
                    <div class="stats-grid">
                        <div class="stat-card" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                            <div class="stat-header">
                                <span class="stat-title">Pendentes</span>
                                <div class="stat-icon">
                                    <i class="far fa-clock"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="pendingCount">0</div>
                        </div>

                        <div class="stat-card" style="background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);">
                            <div class="stat-header">
                                <span class="stat-title">Processando</span>
                                <div class="stat-icon">
                                    <i class="fas fa-box-open"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="processingCount">0</div>
                        </div>

                        <div class="stat-card" style="background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);">
                            <div class="stat-header">
                                <span class="stat-title">Enviados</span>
                                <div class="stat-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="shippedCount">0</div>
                        </div>

                        <div class="stat-card highlight">
                            <div class="stat-header">
                                <span class="stat-title">Entregues</span>
                                <div class="stat-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="stat-value" id="deliveredCount">0</div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="orders-table-section">
                        <div class="table-header">
                            <h3 class="table-title">Todas as Encomendas</h3>
                            <div class="table-actions">
                                <select id="statusFilter" class="btn-filter">
                                    <option value="">Todos os Status</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="processando">Processando</option>
                                    <option value="enviado">Enviado</option>
                                    <option value="entregue">Entregue</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                                <input type="date" id="dateFilterStart" class="btn-filter">
                                <input type="date" id="dateFilterEnd" class="btn-filter">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table" id="ordersDataTable">
                                <thead>
                                    <tr>
                                        <th>Nº Encomenda</th>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Produtos</th>
                                        <th>Transportadora</th>
                                        <th>Total (€)</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="ordersTableBody">
                                    <!-- Dados serão carregados via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Página Definições -->
            <div id="page-settings" class="page-content hidden">
                <header class="dashboard-header">
                    <div class="header-title">
                        <h1>Definições da Conta</h1>
                        <p>Gerencie suas informações pessoais e preferências</p>
                    </div>
                </header>

                <div class="content-area">
                    <div class="settings-section">
                        <div class="settings-card">
                            <h3>Informações Pessoais</h3>
                            <form id="personalInfoForm">
                                <div class="form-group">
                                    <label>Nome Completo</label>
                                    <input type="text" class="form-control" value="Mariana Brites" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" value="<?php echo $_SESSION['email'] ?? 'cliente@wegreen.pt'; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Telefone</label>
                                    <input type="tel" class="form-control" placeholder="+351 123 456 789">
                                </div>
                                <button type="submit" class="btn-primary">Guardar Alterações</button>
                            </form>
                        </div>

                        <div class="settings-card">
                            <h3>Endereço de Entrega</h3>
                            <form id="addressForm">
                                <div class="form-group">
                                    <label>Morada</label>
                                    <input type="text" class="form-control" placeholder="Rua, número, andar">
                                </div>
                                <div class="form-group">
                                    <label>Código Postal</label>
                                    <input type="text" class="form-control" placeholder="0000-000">
                                </div>
                                <div class="form-group">
                                    <label>Cidade</label>
                                    <input type="text" class="form-control" placeholder="Lisboa">
                                </div>
                                <button type="submit" class="btn-primary">Atualizar Endereço</button>
                            </form>
                        </div>

                        <div class="settings-card">
                            <h3>Alterar Palavra-passe</h3>
                            <form id="passwordForm">
                                <div class="form-group">
                                    <label>Palavra-passe Atual</label>
                                    <input type="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Nova Palavra-passe</label>
                                    <input type="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Confirmar Nova Palavra-passe</label>
                                    <input type="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn-primary">Alterar Palavra-passe</button>
                            </form>
                        </div>

                        <div class="settings-card">
                            <h3>Preferências de Notificações</h3>
                            <form id="notificationsForm">
                                <div class="form-group">
                                    <label style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" checked>
                                        <span>Receber emails sobre novos produtos</span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox" checked>
                                        <span>Notificações sobre status de pedidos</span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label style="display: flex; align-items: center; gap: 10px;">
                                        <input type="checkbox">
                                        <span>Newsletter semanal</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn-primary">Guardar Preferências</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Página Suporte -->
            <div id="page-support" class="page-content hidden">
                <header class="dashboard-header">
                    <div class="header-title">
                        <h1>Suporte ao Cliente</h1>
                        <p>Estamos aqui para ajudar</p>
                    </div>
                </header>

                <div class="content-area">
                    <div class="settings-section">
                        <div class="settings-card">
                            <h3>Contacte-nos</h3>
                            <form id="supportForm">
                                <div class="form-group">
                                    <label>Assunto</label>
                                    <select class="form-control" required>
                                        <option value="">Selecione um assunto</option>
                                        <option value="pedido">Problema com pedido</option>
                                        <option value="produto">Questão sobre produto</option>
                                        <option value="pagamento">Problema de pagamento</option>
                                        <option value="devolucao">Devolução/Reembolso</option>
                                        <option value="outro">Outro</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nº do Pedido (opcional)</label>
                                    <input type="text" class="form-control" placeholder="Ex: #WG001">
                                </div>
                                <div class="form-group">
                                    <label>Mensagem</label>
                                    <textarea class="form-control" rows="6" placeholder="Descreva o seu problema ou questão..." required></textarea>
                                </div>
                                <button type="submit" class="btn-primary">Enviar Mensagem</button>
                            </form>
                        </div>

                        <div class="settings-card">
                            <h3>Informações de Contacto</h3>
                            <div style="line-height: 2;">
                                <p><strong>Email:</strong> suporte@wegreen.pt</p>
                                <p><strong>Telefone:</strong> +351 210 123 456</p>
                                <p><strong>Horário:</strong> Segunda a Sexta, 9h - 18h</p>
                                <p><strong>Morada:</strong> Rua Verde, 123, 1000-001 Lisboa</p>
                            </div>
                        </div>

                        <div class="settings-card">
                            <h3>Perguntas Frequentes</h3>
                            <div style="line-height: 2;">
                                <details style="margin-bottom: 15px;">
                                    <summary style="cursor: pointer; font-weight: 500;">Como posso rastrear o meu pedido?</summary>
                                    <p style="margin-top: 10px; color: #666;">Pode rastrear o seu pedido na página "Os Meus Pedidos". Cada pedido tem um link de rastreamento.</p>
                                </details>
                                <details style="margin-bottom: 15px;">
                                    <summary style="cursor: pointer; font-weight: 500;">Qual é a política de devolução?</summary>
                                    <p style="margin-top: 10px; color: #666;">Aceitamos devoluções até 30 dias após a entrega. O produto deve estar em condições originais.</p>
                                </details>
                                <details style="margin-bottom: 15px;">
                                    <summary style="cursor: pointer; font-weight: 500;">Quanto tempo demora a entrega?</summary>
                                    <p style="margin-top: 10px; color: #666;">Normalmente 3-5 dias úteis para Portugal Continental. Ilhas podem demorar até 7 dias.</p>
                                </details>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="DashboardCliente.js"></script>
</body>

<?php
}else{
    echo "sem permissão!";
}
?>

</html>