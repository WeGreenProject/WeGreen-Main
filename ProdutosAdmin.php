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

    <style>
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .plan-card {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .plan-card.featured {
        border-color: #A6D90C;
        box-shadow: 0 10px 40px rgba(166, 217, 12, 0.2);
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    .plan-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
        color: #1a202c;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .plan-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #1a202c;
        margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba(166, 217, 12, 0.3);
    }

    .plan-name {
        font-size: 24px;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 10px;
    }

    .plan-description {
        color: #718096;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 25px;
        min-height: 60px;
    }

    .plan-price {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 25px;
    }

    .price-value {
        font-size: 42px;
        font-weight: 800;
        color: #A6D90C;
        line-height: 1;
    }

    .price-currency {
        font-size: 24px;
        font-weight: 700;
        color: #A6D90C;
    }

    .price-period {
        font-size: 14px;
        color: #718096;
        font-weight: 500;
    }

    .plan-features {
        list-style: none;
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e2e8f0;
    }

    .plan-features li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        color: #2d3748;
        font-size: 14px;
    }

    .plan-features li i {
        color: #A6D90C;
        font-size: 16px;
    }

    .plan-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .plan-stat {
        text-align: center;
        padding: 12px;
        background: #f7fafc;
        border-radius: 10px;
    }

    .plan-stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #1a202c;
        display: block;
    }

    .plan-stat-label {
        font-size: 11px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .plan-actions {
        display: flex;
        gap: 10px;
    }

    .btn-edit,
    .btn-delete,
    .btn-toggle {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-edit {
        background: #A6D90C;
        color: #1a202c;
    }

    .btn-edit:hover {
        background: #90c207;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: #fee;
        color: #e53e3e;
    }

    .btn-delete:hover {
        background: #fdd;
        transform: translateY(-2px);
    }

    .btn-toggle {
        background: #edf2f7;
        color: #4a5568;
    }

    .btn-toggle:hover {
        background: #e2e8f0;
    }

    .btn-toggle.active {
        background: #c6f6d5;
        color: #22543d;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: #ffffff;
        border-radius: 20px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
        padding: 25px 30px;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #ffffff;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .modal-close:hover {
        background: #A6D90C;
        color: #1a202c;
    }

    .modal-body {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s;
        font-family: inherit;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #A6D90C;
        box-shadow: 0 0 0 3px rgba(166, 217, 12, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .features-list {
        margin-bottom: 20px;
    }

    .feature-item {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .feature-item input {
        flex: 1;
    }

    .btn-remove-feature {
        background: #fee;
        color: #e53e3e;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-remove-feature:hover {
        background: #fdd;
    }

    .btn-add-feature {
        width: 100%;
        padding: 12px;
        background: #edf2f7;
        color: #4a5568;
        border: 2px dashed #cbd5e0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-add-feature:hover {
        background: #e2e8f0;
        border-color: #A6D90C;
        color: #A6D90C;
    }

    .modal-footer {
        padding: 20px 30px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-cancel {
        padding: 12px 24px;
        background: #edf2f7;
        color: #4a5568;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: #e2e8f0;
    }

    .btn-save {
        padding: 12px 24px;
        background: linear-gradient(90deg, #A6D90C 0%, #90c207 100%);
        color: #1a202c;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(166, 217, 12, 0.25);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(166, 217, 12, 0.4);
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .checkbox-group input[type="checkbox"] {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #718096;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 20px;
        color: #2d3748;
        margin-bottom: 10px;
    }
    </style>
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

    <script>
    // Dados de exemplo dos planos
    let plans = [{
            id: 1,
            name: "Plano Básico",
            description: "Perfeito para começar a sua jornada sustentável",
            price: 9.99,
            period: "mês",
            icon: "fa-seedling",
            features: [
                "Até 10 produtos por mês",
                "Suporte por email",
                "Acesso à comunidade",
                "Relatórios mensais"
            ],
            featured: false,
            active: true,
            subscribers: 45,
            revenue: 449.55
        },
        {
            id: 2,
            name: "Plano Premium",
            description: "Para quem leva a sustentabilidade a sério",
            price: 24.99,
            period: "mês",
            icon: "fa-star",
            features: [
                "Produtos ilimitados",
                "Suporte prioritário 24/7",
                "Análises avançadas",
                "Badge de vendedor premium",
                "Destaque nos resultados",
                "Relatórios personalizados"
            ],
            featured: true,
            active: true,
            subscribers: 128,
            revenue: 3198.72
        },
        {
            id: 3,
            name: "Plano Empresarial",
            description: "Solução completa para grandes negócios",
            price: 99.99,
            period: "mês",
            icon: "fa-building",
            features: [
                "Tudo do Premium",
                "API de integração",
                "Gestor de conta dedicado",
                "Personalização de marca",
                "Treinamento da equipa",
                "Análises empresariais"
            ],
            featured: false,
            active: true,
            subscribers: 12,
            revenue: 1199.88
        }
    ];

    // Carregar planos ao iniciar
    document.addEventListener('DOMContentLoaded', function() {
        renderPlans();
    });

    function renderPlans() {
        const grid = document.getElementById('plansGrid');

        if (plans.length === 0) {
            grid.innerHTML = `
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fas fa-crown"></i>
                        <h3>Nenhum plano criado</h3>
                        <p>Comece criando o seu primeiro plano de subscrição</p>
                    </div>
                `;
            return;
        }

        grid.innerHTML = plans.map(plan => `
                <div class="plan-card ${plan.featured ? 'featured' : ''}">
                    ${plan.featured ? '<div class="plan-badge">Em Destaque</div>' : ''}

                    <div class="plan-icon">
                        <i class="fas ${plan.icon}"></i>
                    </div>

                    <h3 class="plan-name">${plan.name}</h3>
                    <p class="plan-description">${plan.description}</p>

                    <div class="plan-price">
                        <span class="price-currency">€</span>
                        <span class="price-value">${plan.price.toFixed(2)}</span>
                        <span class="price-period">/${plan.period}</span>
                    </div>

                    <ul class="plan-features">
                        ${plan.features.map(feature => `
                            <li><i class="fas fa-check-circle"></i> ${feature}</li>
                        `).join('')}
                    </ul>

                    <div class="plan-stats">
                        <div class="plan-stat">
                            <span class="plan-stat-value">${plan.subscribers}</span>
                            <span class="plan-stat-label">Subscritores</span>
                        </div>
                        <div class="plan-stat">
                            <span class="plan-stat-value">€${plan.revenue.toFixed(0)}</span>
                            <span class="plan-stat-label">Receita</span>
                        </div>
                    </div>

                    <div class="plan-actions">
                        <button class="btn-edit" onclick="editPlan(${plan.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn-toggle ${plan.active ? 'active' : ''}" onclick="togglePlan(${plan.id})">
                            <i class="fas fa-${plan.active ? 'toggle-on' : 'toggle-off'}"></i>
                        </button>
                        <button class="btn-delete" onclick="deletePlan(${plan.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
    }

    function openPlanModal(planId = null) {
        const modal = document.getElementById('planModal');
        const form = document.getElementById('planForm');
        const modalTitle = document.getElementById('modalTitle');

        form.reset();
        document.getElementById('featuresList').innerHTML = '';

        if (planId) {
            const plan = plans.find(p => p.id === planId);
            if (plan) {
                modalTitle.textContent = 'Editar Plano';
                document.getElementById('planId').value = plan.id;
                document.getElementById('planName').value = plan.name;
                document.getElementById('planDescription').value = plan.description;
                document.getElementById('planPrice').value = plan.price;
                document.getElementById('planPeriod').value = plan.period;
                document.getElementById('planIcon').value = plan.icon;
                document.getElementById('planFeatured').checked = plan.featured;
                document.getElementById('planActive').checked = plan.active;

                plan.features.forEach(feature => {
                    addFeatureField(feature);
                });
            }
        } else {
            modalTitle.textContent = 'Adicionar Novo Plano';
            addFeatureField();
        }

        modal.classList.add('active');
    }

    function closePlanModal() {
        document.getElementById('planModal').classList.remove('active');
    }

    function addFeatureField(value = '') {
        const featuresList = document.getElementById('featuresList');
        const featureItem = document.createElement('div');
        featureItem.className = 'feature-item';
        featureItem.innerHTML = `
                <input type="text" class="form-input feature-input" placeholder="Ex: Suporte 24/7" value="${value}" required>
                <button type="button" class="btn-remove-feature" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
        featuresList.appendChild(featureItem);
    }

    function savePlan() {
        const form = document.getElementById('planForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const planId = document.getElementById('planId').value;
        const features = Array.from(document.querySelectorAll('.feature-input'))
            .map(input => input.value)
            .filter(value => value.trim() !== '');

        const planData = {
            id: planId ? parseInt(planId) : Date.now(),
            name: document.getElementById('planName').value,
            description: document.getElementById('planDescription').value,
            price: parseFloat(document.getElementById('planPrice').value),
            period: document.getElementById('planPeriod').value,
            icon: document.getElementById('planIcon').value,
            features: features,
            featured: document.getElementById('planFeatured').checked,
            active: document.getElementById('planActive').checked,
            subscribers: planId ? plans.find(p => p.id === parseInt(planId)).subscribers : 0,
            revenue: planId ? plans.find(p => p.id === parseInt(planId)).revenue : 0
        };

        if (planId) {
            const index = plans.findIndex(p => p.id === parseInt(planId));
            plans[index] = planData;
            Swal.fire('Sucesso!', 'Plano atualizado com sucesso!', 'success');
        } else {
            plans.push(planData);

        }
    }
    </script>
    <?php
}else{
header("Location: forbiddenerror.html");
}
?>
