<?php
session_start();

if(!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 2){
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/DashboardCliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>

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
                    <a href="DashboardCliente.php" class="menu-item active" data-page="dashboard">
                        <i class="fas fa-home"></i>
                        <span>Início</span>
                    </a>
                    <a href="minhasEncomendas.php" class="menu-item" data-page="orders">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Minhas Encomendas</span>
                    </a>
                    <a href="meusFavoritos.php" class="menu-item" data-page="favorites">
                        <i class="fas fa-heart"></i>
                        <span>Meus Favoritos</span>
                        <span class="badge" id="favoritosBadge" style="display:none; background:#3cb371; color:white; padding:2px 8px; border-radius:10px; font-size:11px; margin-left:auto;"></span>
                    </a>
                    <a href="ChatCliente.php" class="menu-item" data-page="chat">
                        <i class="fas fa-comments"></i>
                        <span>Chat</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <h1 class="page-title"><i class="fas fa-home"></i> Dashboard</h1>
                </div>
                <div class="navbar-right">
                    <button class="btn-upgrade-navbar" onclick="window.location.href='planos.php'">
                        <i class="fas fa-crown"></i> Upgrade
                    </button>
                    <div class="navbar-user" id="userMenuBtn">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff" alt="User" class="user-avatar">
                        <div class="user-info">
                            <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></span>
                            <span class="user-role">Cliente</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff" alt="User" class="dropdown-avatar">
                            <div>
                                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></div>
                                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? ''; ?></div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="perfilCliente.php">
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
            <!-- Página Dashboard -->
            <div id="page-dashboard" class="page-content">
                <div class="content-area">
                    <!-- Page Greeting -->
                    <div class="page-greeting">
                        <h1>Olá, <?php echo isset($_SESSION['nome']) ? explode(' ', $_SESSION['nome'])[0] : 'Cliente'; ?> <span class="wave">👋</span></h1>
                        <p>Descobre os nossos produtos sustentáveis</p>
                    </div>

                    <!-- Produtos Adquiridos Recentemente -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-history"></i> Produtos Adquiridos Recentemente
                            </h2>
                            <a href="minhasEncomendas.php" class="btn-ver-todas">
                                Ver Todas as Compras <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div id="recomendacoesContainer">
                            <!-- Produtos carregados via AJAX -->
                        </div>
                    </div>

                    <!-- Encomendas Recentes -->
                    <div class="section-card">
                        <div class="section-header">
                            <h2 class="section-title">
                                <i class="fas fa-shipping-fast"></i> Encomendas Recentes
                            </h2>
                            <a href="minhasEncomendas.php" class="btn-ver-todas">
                                Ver Todas <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div id="encomendasContainer">
                            <!-- Encomendas carregadas via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Navegação entre páginas
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if(this.getAttribute('href') && this.getAttribute('href') !== '#') {
                    return;
                }
                e.preventDefault();
                document.querySelectorAll('.menu-item').forEach(mi => mi.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // ========== Dashboard Data Loading ==========
        $(document).ready(function() {
            carregarDadosDashboard();
            atualizarContadorFavoritos();
        });

        function carregarDadosDashboard() {
            // Carregar encomendas recentes
            $.ajax({
                url: 'src/controller/controllerDashboardCliente.php',
                method: 'GET',
                data: { op: 2 },
                dataType: 'json',
                success: function(response) {
                    console.log('Encomendas response:', response);
                    if (response.success && response.data.length > 0) {
                        renderEncomendasRecentes(response.data);
                    } else {
                        emptyStateEncomendas();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro encomendas:', status, error, xhr.responseText);
                    emptyStateEncomendas();
                }
            });

            // Carregar produtos recomendados
            $.ajax({
                url: 'src/controller/controllerDashboardCliente.php',
                method: 'GET',
                data: { op: 3 },
                dataType: 'json',
                success: function(response) {
                    console.log('Produtos response:', response);
                    if (response.success && response.data && response.data.length > 0) {
                        renderRecomendacoes(response.data);
                    } else {
                        emptyStateRecomendacoes();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro produtos:', status, error, xhr.responseText);
                    emptyStateRecomendacoes();
                }
            });
        }

        function renderEncomendasRecentes(encomendas) {
            let html = '';

            encomendas.slice(0, 5).forEach(function(enc) {
                const statusInfo = getStatusInfo(enc.estado);
                const data = formatarData(enc.data_envio);

                html += `
                    <div class="encomenda-card">
                        <div class="encomenda-header">
                            <div class="encomenda-info">
                                <div class="encomenda-numero">#${enc.codigo_encomenda || enc.id}</div>
                                <div class="encomenda-data"><i class="far fa-calendar"></i> ${data}</div>
                            </div>
                            <span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>
                        </div>
                        <div class="encomenda-body">
                            <div class="encomenda-detalhes">
                                <span><i class="fas fa-box"></i> ${enc.total_produtos || 0} produto(s)</span>
                            </div>
                            <div class="encomenda-valor">
                                <div class="valor-label">Total</div>
                                <div class="valor-amount">€${parseFloat(enc.valor_total || 0).toFixed(2)}</div>
                            </div>
                        </div>
                        <div class="encomenda-actions">
                            <button class="btn-action btn-primary" onclick="window.location.href='minhasEncomendas.php'">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </button>
                        </div>
                    </div>
                `;
            });

            $('#encomendasContainer').html(html);
        }

function renderRecomendacoes(produtos) {
            let html = '<div class="produtos-grid">';

            produtos.slice(0, 6).forEach(function(produto) {
                const imagem = produto.foto || produto.Imagem1 || 'assets/media/products/default.jpg';
                const preco = parseFloat(produto.preco || produto.Preco || 0).toFixed(2);
                const nome = produto.nome || produto.Nome || 'Produto';
                const id = produto.Produto_id || produto.produto_id;

                html += `
                    <div class="produto-item">
                        <div class="produto-image">
                            <img src="${imagem}" alt="${nome}">
                            <div class="produto-overlay">
                                <a href="produto.php?id=${id}" class="btn-ver-produto">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <div class="produto-info">
                            <h4 class="produto-nome">${nome}</h4>
                            <div class="produto-preco">€${preco}</div>
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            $('#recomendacoesContainer').html(html);
        }

        function emptyStateEncomendas() {
            const html = `
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4>Ainda não tens encomendas</h4>
                    <p>Explora os nossos produtos sustentáveis e faz a tua primeira compra!</p>
                    <a href="ecommerce.html" class="btn-primary">
                        <i class="fas fa-shopping-bag"></i> Explorar Produtos
                    </a>
                </div>
            `;
            $('#encomendasContainer').html(html);
        }

        function emptyStateRecomendacoes() {
            const html = `
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h4>Ainda não compraste nada</h4>
                    <p>Explora a nossa coleção de moda sustentável e faz a tua primeira compra!</p>
                    <a href="marketplace.html" class="btn-primary">
                        <i class="fas fa-search"></i> Explorar Produtos
                    </a>
                </div>
            `;
            $('#recomendacoesContainer').html(html);
        }

        function getStatusClass(estado) {
            return estado?.toLowerCase() || 'pendente';
        }

        function getStatusLabel(estado) {
            const labelMap = {
                'pendente': 'Pendente',
                'processando': 'Processando',
                'enviado': 'Enviado',
                'entregue': 'Entregue',
                'cancelado': 'Cancelado'
            };
            return labelMap[estado?.toLowerCase()] || estado;
        }

        function getStatusInfo(estado) {
            const statusMap = {
                'pendente': { class: 'status-pendente', text: 'Pendente' },
                'processando': { class: 'status-processando', text: 'Processando' },
                'enviado': { class: 'status-enviado', text: 'Enviado' },
                'entregue': { class: 'status-entregue', text: 'Entregue' },
                'devolvido': { class: 'status-devolvido', text: 'Devolvido' },
                'cancelado': { class: 'status-cancelado', text: 'Cancelado' }
            };
            return statusMap[estado?.toLowerCase()] || { class: 'status-pendente', text: estado };
        }

        function formatarData(data) {
            const d = new Date(data);
            return d.toLocaleDateString('pt-PT', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        // Atualizar contador de favoritos
        function atualizarContadorFavoritos() {
            $.ajax({
                url: 'src/controller/controllerFavoritos.php',
                method: 'GET',
                data: { op: 5 },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.total > 0) {
                        $('#favoritosBadge').text(response.total).show();
                    } else {
                        $('#favoritosBadge').hide();
                    }
                }
            });
        }

        // Dropdown do usuário
        $("#userMenuBtn").on("click", function(e) {
            e.stopPropagation();
            $("#userDropdown").toggleClass("active");
        });

        // Fechar dropdown ao clicar fora
        $(document).on("click", function(e) {
            if (!$(e.target).closest(".navbar-user").length) {
                $("#userDropdown").removeClass("active");
            }
        });

        // Evitar que cliques dentro do dropdown o fechem
        $("#userDropdown").on("click", function(e) {
            e.stopPropagation();
        });

        function showPasswordModal() {
            Swal.fire({
                title: 'Alterar Senha',
                html: `
                    <input type="password" id="currentPassword" class="swal2-input" placeholder="Senha Atual">
                    <input type="password" id="newPassword" class="swal2-input" placeholder="Nova Senha">
                    <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirmar Nova Senha">
                `,
                showCancelButton: true,
                confirmButtonText: 'Alterar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3cb371',
                preConfirm: () => {
                    const currentPassword = document.getElementById('currentPassword').value;
                    const newPassword = document.getElementById('newPassword').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;

                    if (!currentPassword || !newPassword || !confirmPassword) {
                        Swal.showValidationMessage('Preencha todos os campos');
                        return false;
                    }

                    if (newPassword !== confirmPassword) {
                        Swal.showValidationMessage('As senhas não coincidem');
                        return false;
                    }

                    if (newPassword.length < 6) {
                        Swal.showValidationMessage('A senha deve ter pelo menos 6 caracteres');
                        return false;
                    }

                    return { currentPassword, newPassword };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Sucesso', 'Senha alterada com sucesso!', 'success');
                    $("#userDropdown").removeClass("active");
                }
            });
        }

        function logout() {
            Swal.fire({
                title: 'Terminar Sessão?',
                text: 'Tem a certeza que pretende sair?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, sair',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'src/controller/controllerPerfil.php?op=2';
                }
            });
        }
    </script>
    <script src="src/js/alternancia.js"></script>
</body>

</html>
