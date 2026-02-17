<?php
session_start();

if($_SESSION['tipo'] == 1){
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Planos - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="src/css/admin.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>

    <link rel="stylesheet" href="src/css/AdminProdutos.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" class="logo">
                <span class="logo-icon"><i class="fas fa-leaf"></i></span>
                <div class="logo-text">
                    <h1>WeGreen</h1>
                    <p>Painel do Administrador</p>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="DashboardAdmin.php">
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
                        <a class="nav-link" href="vendas.php">
                            <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ProdutosAdmin.php">
                            <span class="nav-icon"><i class="fas fa-crown"></i></span>
                            <span class="nav-text">Gestão de Planos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                            <span class="nav-text">Análises</span>
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
                    <li class="nav-item">
                        <a class="nav-link" href="perfilAdmin.php">
                            <span class="nav-icon"><i class="fas fa-cog"></i></span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <nav class="top-navbar">
                <div class="navbar-left">
                    <i class="navbar-icon fas fa-crown"></i>
                    <h2 class="navbar-title">Gestão de Planos</h2>
                </div>
                <div class="navbar-right">
                    <?php include 'src/views/notifications-widget.php'; ?>
                    <div class="navbar-user" onclick="toggleUserDropdown()">
                        <div class="user-avatar">A</div>
                        <div class="user-info">
                            <span class="user-name">Administrador</span>
                            <span class="user-role">Admin</span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 12px; color: #4a5568;"></i>
                        <div class="user-dropdown" id="userDropdown"></div>
                    </div>
                </div>
            </nav>

            <div class="page active" style="padding: 30px 40px;">
                <div class="action-bar">
                    <div class="page-header">
                        <h2>Planos de Subscrição</h2>
                        <p>Gerir todos os planos disponíveis para os utilizadores</p>
                    </div>
                    <button class="btn-primary" onclick="openPlanModal()">
                        <i class="fas fa-plus"></i>
                        Adicionar Plano
                    </button>
                </div>

                <div class="plans-grid" id="plansGrid">
                    <!-- Os planos serão carregados aqui -->
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Adicionar/Editar Plano -->
    <div class="modal" id="planModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-crown"></i>
                    <span id="modalTitle">Adicionar Novo Plano</span>
                </h3>
                <button class="modal-close" onclick="closePlanModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="planForm">
                    <input type="hidden" id="planId" name="planId">

                    <div class="form-group">
                        <label class="form-label">Nome do Plano</label>
                        <input type="text" class="form-input" id="planName" name="planName"
                            placeholder="Ex: Plano Premium" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-textarea" id="planDescription" name="planDescription"
                            placeholder="Descreva os benefícios deste plano..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Preço (€)</label>
                        <input type="number" class="form-input" id="planPrice" name="planPrice" placeholder="0.00"
                            step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Período</label>
                        <select class="form-select" id="planPeriod" name="planPeriod" required>
                            <option value="mês">Por mês</option>
                            <option value="ano">Por ano</option>
                            <option value="semana">Por semana</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ícone (Font Awesome)</label>
                        <input type="text" class="form-input" id="planIcon" name="planIcon"
                            placeholder="Ex: fa-star, fa-rocket, fa-crown" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Características do Plano</label>
                        <div id="featuresList" class="features-list">
                            <!-- Features serão adicionadas aqui -->
                        </div>
                        <button type="button" class="btn-add-feature" onclick="addFeatureField()">
                            <i class="fas fa-plus"></i> Adicionar Característica
                        </button>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="planFeatured" name="planFeatured">
                            <label for="planFeatured" class="form-label" style="margin: 0;">Marcar como Plano em
                                Destaque</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="planActive" name="planActive" checked>
                            <label for="planActive" class="form-label" style="margin: 0;">Plano Ativo</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closePlanModal()">Cancelar</button>
                <button class="btn-save" onclick="savePlan()">
                    <i class="fas fa-save"></i> Guardar Plano
                </button>
            </div>
        </div>
    </div>

    <script src="src/js/ProdutosAdmin.js"></script>
    <?php
}else{
header("Location: forbiddenerror.html");
}
?>
