<?php
session_start();

if($_SESSION['tipo'] == 1){
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Lucros - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/DashboardAdmin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/gestaoProdutos.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="src/css/gestaoComentariosAdmin.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="src/js/notifications.js"></script>
    <style>
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal.hidden {
            display: none !important;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 560px;
            position: relative;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .modal-header-success {
            background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header-success h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            opacity: 0.8;
        }

        .modal-body {
            padding: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #1f2937;
            font-size: 14px;
        }

        .form-input {
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: inherit;
            resize: vertical;
        }

        .form-input:focus {
            outline: none;
            border-color: #3cb371;
            box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.1);
        }

        .btn-submit {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(60, 179, 113, 0.4);
        }

        .btn-add-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
        }

        .btn-add-primary:active {
            transform: translateY(0);
        }
    </style>
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
                    <a href="DashboardAdmin.php" class="menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="gestaoProdutosAdmin.php" class="menu-item">
                        <i class="fas fa-tshirt"></i>
                        <span>Produtos</span>
                    </a>
                    <a href="gestaoCliente.php" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Utilizadores</span>
                    </a>
                    <a href="gestaoComentarios.php" class="menu-item">
                        <i class="fas fa-comment-dots"></i>
                        <span>Comentários</span>
                    </a>
                    <a href="gestaoLucros.php" class="menu-item active">
                        <i class="fas fa-euro-sign"></i>
                        <span>Lucros</span>
                    </a>
                    <a href="logAdmin.php" class="menu-item">
                        <i class="fas fa-history"></i>
                        <span>Logs do Sistema</span>
                    </a>
                    <a href="Chatadmin.php" class="menu-item">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <h1 class="page-title"><i class="fas fa-euro-sign"></i> Gestão de Lucros</h1>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff" alt="Administrador" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></span>
                            <span class="user-role">Administrador</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Admin'); ?>&background=3cb371&color=fff" alt="Administrador" class="dropdown-avatar">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Administrador'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'admin@wegreen.com'; ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="perfilAdmin.php">
                            <i class="fas fa-user"></i>
                            <span>Meu Perfil</span>
                        </a>
                        <a class="dropdown-item" href="alterarSenha.php">
                            <i class="fas fa-key"></i>
                            <span>Alterar Senha</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item dropdown-item-danger" onclick="logout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Sair</span>
                        </button>
                    </div>
                </div>
            </nav>

            <div class="content-area">
                <div class="stats-grid-compact">
                    <div id="ReceitasCard" class="stat-card-compact"></div>
                    <div id="DespesasCard" class="stat-card-compact"></div>
                    <div id="LucroCard" class="stat-card-compact"></div>
                    <div id="MargemCard" class="stat-card-compact"></div>
                </div>

                <!-- Tab Navigation -->
                <div class="tab-navigation">
                    <button class="tab-button active" data-tab="total">
                        <i class="fas fa-receipt"></i>
                        <span>Total</span>
                    </button>
                    <button class="tab-button" data-tab="gastos">
                        <i class="fas fa-wallet"></i>
                        <span>Gastos</span>
                    </button>
                    <button class="tab-button" data-tab="rendimentos">
                        <i class="fas fa-hand-holding-usd"></i>
                        <span>Rendimentos</span>
                    </button>
                </div>

                <!-- Tab Content: Total -->
                <div class="tab-content active" id="tab-total">
                    <div class="table-container">
                        <table class="display" id="transacoesTable">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-calendar"></i> Data</th>
                                    <th><i class="fas fa-tag"></i> Tipo</th>
                                    <th><i class="fas fa-user"></i> Anunciante</th>
                                    <th><i class="fas fa-align-left"></i> Descrição</th>
                                    <th><i class="fas fa-euro-sign"></i> Valor</th>
                                </tr>
                            </thead>
                            <tbody id="transacoesBody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Content: Gastos -->
                <div class="tab-content" id="tab-gastos">
                    <div id="bulkActionsGastos" class="bulk-actions" style="display: none; width: 100%; margin-bottom: 15px;">
                        <span id="selectedCountGastos">0 selecionados</span>
                        <div style="display: flex; gap: 10px; margin-left: auto;">
                            <button onclick="editarSelecionadoGastos()" class="btn-bulk"><i class="fas fa-edit"></i> Editar</button>
                            <button onclick="removerEmMassaGastos()" class="btn-bulk"><i class="fas fa-trash"></i> Remover</button>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px; display: flex; justify-content: flex-start;">
                        <button class="btn-add-primary" onclick="openModalGasto()" style="background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3); transition: all 0.3s ease;">
                            <i class="fas fa-plus-circle"></i>
                            Adicionar Gasto
                        </button>
                    </div>
                    <div class="table-container">
                        <table id="tblGastos" class="display">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllGastos"></th>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-align-left"></i> Descrição</th>
                                    <th><i class="fas fa-euro-sign"></i> Valor</th>
                                    <th><i class="fas fa-calendar"></i> Data</th>
                                </tr>
                            </thead>
                            <tbody id="listagemGastos">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab Content: Rendimentos -->
                <div class="tab-content" id="tab-rendimentos">
                    <div id="bulkActionsRendimentos" class="bulk-actions" style="display: none; width: 100%; margin-bottom: 15px;">
                        <span id="selectedCountRendimentos">0 selecionados</span>
                        <div style="display: flex; gap: 10px; margin-left: auto;">
                            <button onclick="editarSelecionadoRendimentos()" class="btn-bulk"><i class="fas fa-edit"></i> Editar</button>
                            <button onclick="removerEmMassaRendimentos()" class="btn-bulk"><i class="fas fa-trash"></i> Remover</button>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px; display: flex; justify-content: flex-start;">
                        <button class="btn-add-primary" onclick="openModalRendimento()" style="background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3); transition: all 0.3s ease;">
                            <i class="fas fa-plus-circle"></i>
                            Adicionar Rendimento
                        </button>
                    </div>
                    <div class="table-container">
                        <table id="tblRendimentos" class="display">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAllRendimentos"></th>
                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                    <th><i class="fas fa-align-left"></i> Descrição</th>
                                    <th><i class="fas fa-euro-sign"></i> Valor</th>
                                    <th><i class="fas fa-calendar"></i> Data</th>
                                </tr>
                            </thead>
                            <tbody id="listagemRendimentos">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Adicionar Gasto -->
    <div class="modal hidden" id="modalGasto">
        <div class="modal-content">
            <div class="modal-header-success">
                <h2><i class="fas fa-wallet"></i> Adicionar Gasto</h2>
                <button class="modal-close" onclick="closeModalGasto()">&times;</button>
            </div>
            <div class="modal-body">
                <form class="form-grid">
                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" class="form-input" id="dataGasto">
                    </div>
                    <div class="form-group">
                        <label>Valor (€)</label>
                        <input type="text" class="form-input" placeholder="0.00" id="valorGasto">
                    </div>
                    <div class="form-group full-width">
                        <label>Descrição</label>
                        <textarea class="form-input" rows="3" placeholder="Ex: Fornecedor, Material..." id="descricaoGasto"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <button type="button" class="btn-submit btn-success" onclick="registaGastos()">
                            <i class="fas fa-plus-circle"></i>
                            Registar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Rendimento -->
    <div class="modal hidden" id="modalRendimento">
        <div class="modal-content">
            <div class="modal-header-success">
                <h2><i class="fas fa-hand-holding-usd"></i> Adicionar Rendimento</h2>
                <button class="modal-close" onclick="closeModalRendimento()">&times;</button>
            </div>
            <div class="modal-body">
                <form class="form-grid">
                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" class="form-input" id="dataRendimento">
                    </div>
                    <div class="form-group">
                        <label>Valor (€)</label>
                        <input type="text" class="form-input" placeholder="0.00" id="valorRendimento">
                    </div>
                    <div class="form-group full-width">
                        <label>Descrição</label>
                        <textarea class="form-input" rows="3" placeholder="Ex: Venda, Serviço..." id="descricaoRendimento"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <button type="button" class="btn-submit btn-success" onclick="registaRendimentos()">
                            <i class="fas fa-plus-circle"></i>
                            Registar Rendimento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="src/js/Adminstrador.js"></script>
    <script src="src/js/GestaoLucros.js"></script>
    <script>
        // Override do getInfoUserDropdown para manter o HTML correto da página
        function getInfoUserDropdown() {
            // Não fazer nada - o HTML já está correto na página
            console.log('Dropdown já configurado no HTML');
        }

        // ========== Funcionalidade das Tabs ==========
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    document.getElementById('tab-' + targetTab).classList.add('active');
                });
            });
        });

        // ========== Função Alerta ==========
        function alerta(titulo, msg, icon) {
            let iconClass = 'fa-check';
            let gradient = 'linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%)';
            let buttonColor = '#3cb371';

            if (icon === 'error') {
                iconClass = 'fa-times';
                gradient = 'linear-gradient(135deg, #dc3545 0%, #c92a2a 100%)';
                buttonColor = '#dc3545';
            } else if (icon === 'warning') {
                iconClass = 'fa-exclamation-triangle';
                gradient = 'linear-gradient(135deg, #ff9800 0%, #f57c00 100%)';
                buttonColor = '#ff9800';
            } else if (icon === 'info') {
                iconClass = 'fa-info-circle';
                gradient = 'linear-gradient(135deg, #17a2b8 0%, #138496 100%)';
                buttonColor = '#17a2b8';
            }

            Swal.fire({
                html: `
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: ${gradient}; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);">
                            <i class="fas ${iconClass}" style="font-size: 40px; color: white;"></i>
                        </div>
                        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">${titulo}</h2>
                        <p style="color: #64748b; font-size: 15px; margin: 0;">${msg}</p>
                    </div>
                `,
                confirmButtonColor: buttonColor,
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'swal2-confirm-modern-alert',
                    popup: 'swal2-border-radius'
                },
                buttonsStyling: false,
                didOpen: () => {
                    const style = document.createElement('style');
                    style.textContent = `
                        .swal2-confirm-modern-alert {
                            padding: 12px 30px !important;
                            border-radius: 8px !important;
                            font-weight: 600 !important;
                            font-size: 14px !important;
                            cursor: pointer !important;
                            transition: all 0.3s ease !important;
                            border: none !important;
                            background: ${gradient} !important;
                            color: white !important;
                        }
                        .swal2-confirm-modern-alert:hover {
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 20px rgba(60, 179, 113, 0.4) !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            });
        }

        // ========== Funções Modal Gasto ==========
        function openModalGasto() {
            // Resetar título para "Adicionar"
            document.querySelector('#modalGasto .modal-header-success h2').innerHTML = '<i class="fas fa-wallet"></i> Adicionar Gasto';
            document.querySelector('#modalGasto .btn-submit').innerHTML = '<i class="fas fa-plus-circle"></i> Registar Gasto';
            window.editandoGastoId = null;
            document.getElementById('modalGasto').classList.remove('hidden');
        }

        function closeModalGasto() {
            document.getElementById('modalGasto').classList.add('hidden');
            // Limpar campos
            document.getElementById('descricaoGasto').value = '';
            document.getElementById('valorGasto').value = '';
            document.getElementById('dataGasto').value = '';
            window.editandoGastoId = null;
        }

        // ========== Funções Modal Rendimento ==========
        function openModalRendimento() {
            // Resetar título para "Adicionar"
            document.querySelector('#modalRendimento .modal-header-success h2').innerHTML = '<i class="fas fa-hand-holding-usd"></i> Adicionar Rendimento';
            document.querySelector('#modalRendimento .btn-submit').innerHTML = '<i class="fas fa-plus-circle"></i> Registar Rendimento';
            window.editandoRendimentoId = null;
            document.getElementById('modalRendimento').classList.remove('hidden');
        }

        function closeModalRendimento() {
            document.getElementById('modalRendimento').classList.add('hidden');
            // Limpar campos
            document.getElementById('descricaoRendimento').value = '';
            document.getElementById('valorRendimento').value = '';
            document.getElementById('dataRendimento').value = '';
            window.editandoRendimentoId = null;
        }

        // Fechar modais ao clicar fora
        window.onclick = function(event) {
            const modalGasto = document.getElementById('modalGasto');
            const modalRendimento = document.getElementById('modalRendimento');

            if (event.target == modalGasto) {
                closeModalGasto();
            }
            if (event.target == modalRendimento) {
                closeModalRendimento();
            }
        }

        // ========== Seleção Múltipla Gastos ==========
        $(document).on('change', '#selectAllGastos', function() {
            const isChecked = $(this).prop('checked');
            $('#tblGastos tbody input[type="checkbox"]').prop('checked', isChecked);
            updateBulkActionsGastos();
        });

        $(document).on('change', '#tblGastos tbody input[type="checkbox"]', function() {
            updateBulkActionsGastos();
        });

        function updateBulkActionsGastos() {
            const checkedCount = $('#tblGastos tbody input[type="checkbox"]:checked').length;
            const totalCount = $('#tblGastos tbody input[type="checkbox"]').length;

            $('#selectAllGastos').prop('checked', checkedCount === totalCount && totalCount > 0);
            $('#selectedCountGastos').text(checkedCount + ' selecionados');

            if (checkedCount > 0) {
                $('#bulkActionsGastos').slideDown(200);
            } else {
                $('#bulkActionsGastos').slideUp(200);
            }
        }

        function editarSelecionadoGastos() {
            const selected = [];
            $('#tblGastos tbody input[type="checkbox"]:checked').each(function() {
                const row = $(this).closest('tr');
                selected.push({
                    id: row.find('td:eq(1)').text(),
                    descricao: row.find('td:eq(2)').text(),
                    valor: row.find('td:eq(3)').text().replace('€', ''),
                    data: row.find('td:eq(4)').text()
                });
            });

            if (selected.length === 0) {
                alerta('Atenção', 'Nenhum gasto selecionado', 'warning');
                return;
            }

            if (selected.length > 1) {
                alerta('Atenção', 'Selecione apenas um gasto para editar', 'warning');
                return;
            }

            // Preencher modal com dados
            const gasto = selected[0];
            $('#descricaoGasto').val(gasto.descricao);
            $('#valorGasto').val(gasto.valor);
            $('#dataGasto').val(gasto.data);

            // Armazenar ID para edição
            window.editandoGastoId = gasto.id;

            // Alterar título do modal para "Editar"
            document.querySelector('#modalGasto .modal-header-success h2').innerHTML = '<i class="fas fa-edit"></i> Editar Gasto';
            document.querySelector('#modalGasto .btn-submit').innerHTML = '<i class="fas fa-save"></i> Atualizar Gasto';

            $('#modalGasto').removeClass('hidden');
        }

        function removerEmMassaGastos() {
            const ids = [];
            $('#tblGastos tbody input[type="checkbox"]:checked').each(function() {
                ids.push($(this).closest('tr').find('td:eq(1)').text());
            });

            if (ids.length === 0) {
                Swal.fire({
                    html: `
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(255, 152, 0, 0.3);">
                                <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Atenção</h2>
                            <p style="color: #64748b; font-size: 15px; margin: 0;">Selecione pelo menos um gasto para remover.</p>
                        </div>
                    `,
                    confirmButtonColor: '#ff9800',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal2-confirm-modern-warning',
                        popup: 'swal2-border-radius'
                    },
                    buttonsStyling: false,
                    didOpen: () => {
                        const style = document.createElement('style');
                        style.textContent = `
                            .swal2-confirm-modern-warning {
                                padding: 12px 30px !important;
                                border-radius: 8px !important;
                                font-weight: 600 !important;
                                font-size: 14px !important;
                                cursor: pointer !important;
                                transition: all 0.3s ease !important;
                                border: none !important;
                                background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%) !important;
                                color: white !important;
                            }
                            .swal2-confirm-modern-warning:hover {
                                transform: translateY(-2px) !important;
                                box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4) !important;
                            }
                        `;
                        document.head.appendChild(style);
                    }
                });
                return;
            }

            Swal.fire({
                html: `
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
                            <i class="fas fa-trash-alt" style="font-size: 40px; color: white;"></i>
                        </div>
                        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Remover ${ids.length} gasto${ids.length > 1 ? 's' : ''}?</h2>
                        <p style="color: #64748b; font-size: 15px; margin: 0;">Esta ação não pode ser desfeita!</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sim, remover',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                customClass: {
                    confirmButton: 'swal2-confirm-modern',
                    cancelButton: 'swal2-cancel-modern',
                    popup: 'swal2-border-radius'
                },
                buttonsStyling: false,
                didOpen: () => {
                    const style = document.createElement('style');
                    style.textContent = `
                        .swal2-confirm-modern, .swal2-cancel-modern {
                            padding: 12px 30px !important;
                            border-radius: 8px !important;
                            font-weight: 600 !important;
                            font-size: 14px !important;
                            cursor: pointer !important;
                            transition: all 0.3s ease !important;
                            border: none !important;
                        }
                        .swal2-confirm-modern {
                            background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%) !important;
                            color: white !important;
                        }
                        .swal2-confirm-modern:hover {
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
                        }
                        .swal2-cancel-modern {
                            background: #6c757d !important;
                            color: white !important;
                        }
                        .swal2-cancel-modern:hover {
                            background: #5a6268 !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Removendo gastos com IDs:', ids);
                    $.ajax({
                        url: 'src/controller/controllerGestaoLucros.php',
                        method: 'POST',
                        data: { op: 14, ids: ids },
                        traditional: true,
                        dataType: 'json',
                        success: function(response) {
                            console.log('Resposta do servidor:', response);
                            if (response.flag) {
                                Swal.fire({
                                    html: `
                                        <div style="text-align: center;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);">
                                                <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Sucesso!</h2>
                                            <p style="color: #64748b; font-size: 15px; margin: 0;">${response.msg}</p>
                                        </div>
                                    `,
                                    confirmButtonColor: '#3cb371',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'swal2-confirm-modern-success',
                                        popup: 'swal2-border-radius'
                                    },
                                    buttonsStyling: false,
                                    didOpen: () => {
                                        const style = document.createElement('style');
                                        style.textContent = `
                                            .swal2-confirm-modern-success {
                                                padding: 12px 30px !important;
                                                border-radius: 8px !important;
                                                font-weight: 600 !important;
                                                font-size: 14px !important;
                                                cursor: pointer !important;
                                                transition: all 0.3s ease !important;
                                                border: none !important;
                                                background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%) !important;
                                                color: white !important;
                                            }
                                            .swal2-confirm-modern-success:hover {
                                                transform: translateY(-2px) !important;
                                                box-shadow: 0 6px 20px rgba(60, 179, 113, 0.4) !important;
                                            }
                                        `;
                                        document.head.appendChild(style);
                                    }
                                });
                                getGastos();
                                getCards();
                                getCardsDespesas();
                                getCardsLucro();
                                getCardsMargem();
                                $('#selectAllGastos').prop('checked', false);
                                updateBulkActionsGastos();
                            } else {
                                Swal.fire({
                                    html: `
                                        <div style="text-align: center;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
                                                <i class="fas fa-times" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Erro</h2>
                                            <p style="color: #64748b; font-size: 15px; margin: 0;">${response.msg}</p>
                                        </div>
                                    `,
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro AJAX Gastos:', {xhr, status, error});
                            console.error('Resposta:', xhr.responseText);
                            Swal.fire({
                                html: `
                                    <div style="text-align: center;">
                                        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
                                            <i class="fas fa-times" style="font-size: 40px; color: white;"></i>
                                        </div>
                                        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Erro</h2>
                                        <p style="color: #64748b; font-size: 15px; margin: 0;">Não foi possível remover os gastos</p>
                                    </div>
                                `,
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }

        // ========== Seleção Múltipla Rendimentos ==========
        $(document).on('change', '#selectAllRendimentos', function() {
            const isChecked = $(this).prop('checked');
            $('#tblRendimentos tbody input[type="checkbox"]').prop('checked', isChecked);
            updateBulkActionsRendimentos();
        });

        $(document).on('change', '#tblRendimentos tbody input[type="checkbox"]', function() {
            updateBulkActionsRendimentos();
        });

        function updateBulkActionsRendimentos() {
            const checkedCount = $('#tblRendimentos tbody input[type="checkbox"]:checked').length;
            const totalCount = $('#tblRendimentos tbody input[type="checkbox"]').length;

            $('#selectAllRendimentos').prop('checked', checkedCount === totalCount && totalCount > 0);
            $('#selectedCountRendimentos').text(checkedCount + ' selecionados');

            if (checkedCount > 0) {
                $('#bulkActionsRendimentos').slideDown(200);
            } else {
                $('#bulkActionsRendimentos').slideUp(200);
            }
        }

        function editarSelecionadoRendimentos() {
            const selected = [];
            $('#tblRendimentos tbody input[type="checkbox"]:checked').each(function() {
                const row = $(this).closest('tr');
                selected.push({
                    id: row.find('td:eq(1)').text(),
                    descricao: row.find('td:eq(2)').text(),
                    valor: row.find('td:eq(3)').text().replace('€', ''),
                    data: row.find('td:eq(4)').text()
                });
            });

            if (selected.length === 0) {
                alerta('Atenção', 'Nenhum rendimento selecionado', 'warning');
                return;
            }

            if (selected.length > 1) {
                alerta('Atenção', 'Selecione apenas um rendimento para editar', 'warning');
                return;
            }

            // Preencher modal com dados
            const rendimento = selected[0];
            $('#descricaoRendimento').val(rendimento.descricao);
            $('#valorRendimento').val(rendimento.valor);
            $('#dataRendimento').val(rendimento.data);

            // Armazenar ID para edição
            window.editandoRendimentoId = rendimento.id;

            // Alterar título do modal para "Editar"
            document.querySelector('#modalRendimento .modal-header-success h2').innerHTML = '<i class="fas fa-edit"></i> Editar Rendimento';
            document.querySelector('#modalRendimento .btn-submit').innerHTML = '<i class="fas fa-save"></i> Atualizar Rendimento';

            $('#modalRendimento').removeClass('hidden');
        }

        function removerEmMassaRendimentos() {
            const ids = [];
            $('#tblRendimentos tbody input[type="checkbox"]:checked').each(function() {
                ids.push($(this).closest('tr').find('td:eq(1)').text());
            });

            if (ids.length === 0) {
                Swal.fire({
                    html: `
                        <div style="text-align: center;">
                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(255, 152, 0, 0.3);">
                                <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: white;"></i>
                            </div>
                            <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Atenção</h2>
                            <p style="color: #64748b; font-size: 15px; margin: 0;">Selecione pelo menos um rendimento para remover.</p>
                        </div>
                    `,
                    confirmButtonColor: '#ff9800',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'swal2-confirm-modern-warning',
                        popup: 'swal2-border-radius'
                    },
                    buttonsStyling: false,
                    didOpen: () => {
                        const style = document.createElement('style');
                        style.textContent = `
                            .swal2-confirm-modern-warning {
                                padding: 12px 30px !important;
                                border-radius: 8px !important;
                                font-weight: 600 !important;
                                font-size: 14px !important;
                                cursor: pointer !important;
                                transition: all 0.3s ease !important;
                                border: none !important;
                                background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%) !important;
                                color: white !important;
                            }
                            .swal2-confirm-modern-warning:hover {
                                transform: translateY(-2px) !important;
                                box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4) !important;
                            }
                        `;
                        document.head.appendChild(style);
                    }
                });
                return;
            }

            Swal.fire({
                html: `
                    <div style="text-align: center;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
                            <i class="fas fa-trash-alt" style="font-size: 40px; color: white;"></i>
                        </div>
                        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Remover ${ids.length} rendimento${ids.length > 1 ? 's' : ''}?</h2>
                        <p style="color: #64748b; font-size: 15px; margin: 0;">Esta ação não pode ser desfeita!</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sim, remover',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                customClass: {
                    confirmButton: 'swal2-confirm-modern',
                    cancelButton: 'swal2-cancel-modern',
                    popup: 'swal2-border-radius'
                },
                buttonsStyling: false,
                didOpen: () => {
                    const style = document.createElement('style');
                    style.textContent = `
                        .swal2-confirm-modern, .swal2-cancel-modern {
                            padding: 12px 30px !important;
                            border-radius: 8px !important;
                            font-weight: 600 !important;
                            font-size: 14px !important;
                            cursor: pointer !important;
                            transition: all 0.3s ease !important;
                            border: none !important;
                        }
                        .swal2-confirm-modern {
                            background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%) !important;
                            color: white !important;
                        }
                        .swal2-confirm-modern:hover {
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
                        }
                        .swal2-cancel-modern {
                            background: #6c757d !important;
                            color: white !important;
                        }
                        .swal2-cancel-modern:hover {
                            background: #5a6268 !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Removendo rendimentos com IDs:', ids);
                    $.ajax({
                        url: 'src/controller/controllerGestaoLucros.php',
                        method: 'POST',
                        data: { op: 15, ids: ids },
                        traditional: true,
                        dataType: 'json',
                        success: function(response) {
                            console.log('Resposta do servidor:', response);
                            if (response.flag) {
                                Swal.fire({
                                    html: `
                                        <div style="text-align: center;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);">
                                                <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Sucesso!</h2>
                                            <p style="color: #64748b; font-size: 15px; margin: 0;">${response.msg}</p>
                                        </div>
                                    `,
                                    confirmButtonColor: '#3cb371',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'swal2-confirm-modern-success',
                                        popup: 'swal2-border-radius'
                                    },
                                    buttonsStyling: false,
                                    didOpen: () => {
                                        const style = document.createElement('style');
                                        style.textContent = `
                                            .swal2-confirm-modern-success {
                                                padding: 12px 30px !important;
                                                border-radius: 8px !important;
                                                font-weight: 600 !important;
                                                font-size: 14px !important;
                                                cursor: pointer !important;
                                                transition: all 0.3s ease !important;
                                                border: none !important;
                                                background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%) !important;
                                                color: white !important;
                                            }
                                            .swal2-confirm-modern-success:hover {
                                                transform: translateY(-2px) !important;
                                                box-shadow: 0 6px 20px rgba(60, 179, 113, 0.4) !important;
                                            }
                                        `;
                                        document.head.appendChild(style);
                                    }
                                });
                                getRendimentos();
                                getCards();
                                getCardsDespesas();
                                getCardsLucro();
                                getCardsMargem();
                                $('#selectAllRendimentos').prop('checked', false);
                                updateBulkActionsRendimentos();
                            } else {
                                Swal.fire({
                                    html: `
                                        <div style="text-align: center;">
                                            <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
                                                <i class="fas fa-times" style="font-size: 40px; color: white;"></i>
                                            </div>
                                            <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Erro</h2>
                                            <p style="color: #64748b; font-size: 15px; margin: 0;">${response.msg}</p>
                                        </div>
                                    `,
                                    confirmButtonColor: '#dc3545',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Erro AJAX Rendimentos:', {xhr, status, error});
                            console.error('Resposta:', xhr.responseText);
                            Swal.fire({
                                html: `
                                    <div style="text-align: center;">
                                        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
                                            <i class="fas fa-times" style="font-size: 40px; color: white;"></i>
                                        </div>
                                        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Erro</h2>
                                        <p style="color: #64748b; font-size: 15px; margin: 0;">Não foi possível remover os rendimentos</p>
                                    </div>
                                `,
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        }
    </script>

<?php
}else{
header("Location: forbiddenerror.html");
}
?>
</body>

</html>
