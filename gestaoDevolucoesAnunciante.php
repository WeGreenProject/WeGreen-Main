<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Devoluções - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/devolucoes.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <!-- Sistema de Devoluções -->
    <script src="assets/js/custom/devolucoes.js"></script>
    <script src="src/js/Anunciante.js"></script>
</head>

<body>
    <div class="dashboard-container">
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
                    <a href="DashboardAnunciante.php" class="menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="gestaoProdutosAnunciante.php" class="menu-item">
                        <i class="fas fa-tshirt"></i>
                        <span>Produtos</span>
                    </a>
                    <a href="gestaoEncomendasAnunciante.php" class="menu-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Encomendas</span>
                    </a>
                    <a href="gestaoDevolucoesAnunciante.php" class="menu-item active">
                        <i class="fas fa-undo"></i>
                        <span>Devoluções</span>
                    </a>
                    <a href="ChatAnunciante.php" class="menu-item">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                </div>
                <div class="navbar-right">
                    <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'" style="display: none;" <?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
                        <i class="fas fa-crown"></i> Upgrade
                    </button>
                    <?php include 'src/views/notifications-widget.php'; ?>
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

            <div id="devolucoes" class="page active">
                <div class="page-header">
                    <h2><i class="fas fa-undo"></i> Gestão de Devoluções</h2>
                    <p>Gerir pedidos de devolução dos clientes</p>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid stats-grid-compact">
                    <div class="stat-card" id="statPendentes">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Pendentes</div>
                            <div class="stat-value">0</div>
                        </div>
                    </div>
                    <div class="stat-card" id="statAprovadas">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Aprovadas</div>
                            <div class="stat-value">0</div>
                        </div>
                    </div>
                    <div class="stat-card" id="statRejeitadas">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Rejeitadas</div>
                            <div class="stat-value">0</div>
                        </div>
                    </div>
                    <div class="stat-card" id="statReembolsadas">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-label">Valor Reembolsado</div>
                            <div class="stat-value">€0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filters">
                    <select id="filterEstadoDevolucao" onchange="filtrarDevolucoes()">
                        <option value="">Todos os Estados</option>
                        <option value="solicitada">Pendentes</option>
                        <option value="aprovada">Aprovadas</option>
                        <option value="rejeitada">Rejeitadas</option>
                        <option value="produto_recebido">Produto Recebido</option>
                        <option value="reembolsada">Reembolsadas</option>
                        <option value="cancelada">Canceladas</option>
                    </select>
                    <button class="btn btn-secondary" onclick="carregarDevolucoesAnunciante()">
                        <i class="fas fa-sync"></i> Atualizar
                    </button>
                    <button id="exportDevolucoesBtn" class="btn btn-secondary" style="margin-left: auto;">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </button>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table class="display" id="tabelaDevolucoes">
                        <thead>
                            <tr>
                                <th>Código Devolução</th>
                                <th>Encomenda</th>
                                <th>Produto</th>
                                <th>Cliente</th>
                                <th>Motivo</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by JavaScript -->
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
        let devolucoesTable;

        $(document).ready(function() {
            // Inicializar DataTable
            devolucoesTable = $('#tabelaDevolucoes').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-PT.json'
                },
                order: [[6, 'desc']], // Ordenar por data (coluna 6)
                pageLength: 25,
                responsive: true
            });

            // Carregar dados
            carregarDevolucoesAnunciante();
            carregarEstatisticas();

            // Atualizar a cada 60 segundos
            setInterval(function() {
                carregarDevolucoesAnunciante();
                carregarEstatisticas();
            }, 60000);
        });

        function carregarEstatisticas() {
            $.ajax({
                url: 'src/controller/controllerDevolucoes.php?op=10',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Estatísticas recebidas:', response);
                    if (response.success) {
                        const stats = response.data;
                        $('#statPendentes .stat-value').text(stats.pendentes || 0);
                        $('#statAprovadas .stat-value').text(stats.aprovadas || 0);
                        $('#statRejeitadas .stat-value').text(stats.rejeitadas || 0);
                        $('#statReembolsadas .stat-value').text('€' + parseFloat(stats.valor_total_reembolsado || 0).toFixed(2));

                        // Atualizar badge de notificação
                        const pendentes = stats.pendentes || 0;
                        if (pendentes > 0) {
                            $('.notification-badge').text(pendentes).show();
                        } else {
                            $('.notification-badge').hide();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar estatísticas:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        }

        function filtrarDevolucoes() {
            const estado = $('#filterEstadoDevolucao').val();
            carregarDevolucoesAnunciante(estado);
        }

        function renderizarDevolucoesTabela(devolucoes) {
            console.log('Renderizando devoluções:', devolucoes);
            console.log('Total de devoluções:', devolucoes.length);

            devolucoesTable.clear();

            if (!devolucoes || devolucoes.length === 0) {
                console.warn('Nenhuma devolução para renderizar');
                devolucoesTable.draw();
                return;
            }

            devolucoes.forEach(function(dev) {
                console.log('Processando devolução:', dev);
                console.log('ID da devolução:', dev.id);

                const motivoTexto = {
                    'defeituoso': 'Produto Defeituoso',
                    'tamanho_errado': 'Tamanho Errado',
                    'nao_como_descrito': 'Não como Descrito',
                    'arrependimento': 'Arrependimento',
                    'outro': 'Outro'
                };

                // Mapear estados para classes CSS e textos amigáveis
                const estadosConfig = {
                    'solicitada': { class: 'status-solicitada', text: 'Solicitada' },
                    'aprovada': { class: 'status-aprovada', text: 'Aprovada' },
                    'enviada': { class: 'status-enviada', text: 'Enviada' },
                    'recebida': { class: 'status-recebida', text: 'Recebida' },
                    'rejeitada': { class: 'status-rejeitada', text: 'Rejeitada' },
                    'reembolsada': { class: 'status-reembolsada', text: 'Reembolsada' },
                    'cancelada': { class: 'status-cancelada', text: 'Cancelada' }
                };

                const estadoConfig = estadosConfig[dev.estado] || { class: 'status-default', text: dev.estado };
                const estadoClass = estadoConfig.class;
                const estadoTexto = estadoConfig.text;

                let acoes = `
                    <div class="table-actions">
                        <button class="btn-icon btn-info" onclick="console.log('Clicou ver detalhes'); verDetalhesDevolucao(${dev.id})" title="Ver Detalhes">
                            <i class="fas fa-eye"></i>
                        </button>
                `;

                // Botões para devolução SOLICITADA
                if (dev.estado === 'solicitada') {
                    acoes += `
                        <button class="btn-icon btn-success" onclick="console.log('Clicou aprovar'); aprovarDevolucaoAnunciante(${dev.id})" title="Aprovar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-icon btn-danger" onclick="console.log('Clicou rejeitar'); rejeitarDevolucaoAnunciante(${dev.id})" title="Rejeitar">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                }

                // Botão para confirmar RECEBIMENTO (quando produto foi ENVIADO)
                if (dev.estado === 'enviada') {
                    acoes += `
                        <button class="btn-icon btn-success" onclick="mostrarModalConfirmarRecebimento(${dev.id}, '${dev.codigo_devolucao}')" title="Confirmar Recebimento">
                            <i class="fas fa-box-open"></i>
                        </button>
                    `;
                }

                // Botão para processar REEMBOLSO (só após confirmar recebimento)
                if (dev.estado === 'recebida' && dev.reembolso_status !== 'succeeded') {
                    acoes += `
                        <button class="btn-icon btn-success" onclick="console.log('Clicou reembolso'); processarReembolsoAnunciante(${dev.id})" title="Processar Reembolso">
                            <i class="fas fa-euro-sign"></i>
                        </button>
                    `;
                }

                acoes += `</div>`;

                devolucoesTable.row.add([
                    dev.codigo_devolucao,
                    dev.codigo_encomenda || 'N/A',
                    dev.produto_nome || 'Produto',
                    dev.cliente_nome || 'Cliente',
                    motivoTexto[dev.motivo] || dev.motivo,
                    '€' + parseFloat(dev.valor_reembolso).toFixed(2),
                    new Date(dev.data_solicitacao).toLocaleDateString('pt-PT'),
                    `<span class="status-badge ${estadoClass}">${estadoTexto}</span>`,
                    acoes
                ]);
            });

            console.log('Desenhando tabela...');
            devolucoesTable.draw();
            console.log('Tabela desenhada com sucesso');
        }
    </script>
    <script src="src/js/alternancia.js"></script>
</body>

</html>

<?php
}else{
    echo "Sem permissão!";
}
?>
