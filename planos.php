<?php
session_start();

// Verificar se está autenticado
if(!isset($_SESSION['tipo']) || ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 3)){
    header('Location: login.html');
    exit;
}

$userType = $_SESSION['tipo'];
$userName = $_SESSION['nome'] ?? 'Utilizador';
$userEmail = $_SESSION['email'] ?? '';

// Determinar para onde voltar
$backUrl = '';
if($userType == 1){
    $backUrl = 'DashboardAdmin.php';
} elseif($userType == 2){
    $backUrl = 'DashboardCliente.php';
} elseif($userType == 3){
    $backUrl = 'DashboardAnunciante.php';
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos de Subscrição - WeGreen</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        .container {
            max-width: 1400px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-button:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateX(-5px);
        }

        .logo-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo-header img {
            width: auto;
            height: 28px;
            filter: brightness(0) invert(1);
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 5px;
            color: #ffffff;
        }

        .page-header p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .current-plan-banner {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            padding: 10px 20px;
            margin-bottom: 25px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #1a202c;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            font-size: 14px;
        }

        .current-plan-banner i {
            font-size: 16px;
            color: #3cb371;
        }

        .current-plan-banner i {
            font-size: 24px;
            color: #3cb371;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .plan-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.5);
            border-radius: 14px;
            padding: 20px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .plan-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #3cb371 0%, #2e8b57 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .plan-card:hover::before {
            transform: scaleX(1);
        }

        .plan-card.featured {
            border-color: #3cb371;
            box-shadow: 0 20px 50px rgba(60, 179, 113, 0.4);
            transform: scale(1.03);
        }

        .plan-card.current-plan {
            border-color: #3cb371;
            background: linear-gradient(135deg, rgba(60, 179, 113, 0.1) 0%, rgba(255, 255, 255, 0.98) 100%);
        }

        .plan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .plan-card.featured:hover {
            transform: scale(1.03) translateY(-8px);
        }

        .plan-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(60, 179, 113, 0.5);
        }

        .current-badge {
            background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
            color: #ffffff;
        }

        .plan-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #ffffff;
            margin-bottom: 12px;
            box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);
        }

        .plan-name {
            font-size: 18px;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 6px;
        }

        .plan-description {
            color: #64748b;
            font-size: 12px;
            line-height: 1.4;
            margin-bottom: 14px;
            min-height: 40px;
        }

        .plan-price {
            display: flex;
            align-items: baseline;
            gap: 5px;
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 2px solid #f1f5f9;
        }

        .plan-price-currency {
            font-size: 18px;
            font-weight: 700;
            color: #3cb371;
        }

        .plan-price-amount {
            font-size: 32px;
            font-weight: 900;
            color: #1a202c;
            line-height: 1;
        }

        .plan-price-period {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 600;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .plan-features li {
            padding: 6px 0;
            color: #475569;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .plan-features li i {
            color: #3cb371;
            font-size: 13px;
            flex-shrink: 0;
        }

        .plan-button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: auto;
        }

        .plan-button-primary {
            background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);
        }

        .plan-button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(60, 179, 113, 0.4);
        }

        .plan-button-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .plan-button-secondary:hover {
            background: #e2e8f0;
        }

        .plan-button-current {
            background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
            color: #ffffff;
            cursor: default;
        }

        .plan-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Scroll personalizado */
        .container::-webkit-scrollbar {
            width: 8px;
        }

        .container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 768px) {
            body {
                overflow-y: auto;
                height: auto;
            }

            .container {
                height: auto;
                overflow-y: visible;
            }

            .plans-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 32px;
            }

            .back-button {
                top: 15px;
                left: 15px;
                padding: 10px 16px;
                font-size: 14px;
            }
        }


    </style>
</head>

<body>
    <a href="<?php echo $backUrl; ?>" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Voltar ao Dashboard
    </a>

    <div class="container">
        <div class="logo-header">
            <img src="src/img/2-removebg-preview.png" alt="WeGreen Logo">
        </div>

        <div class="page-header">
            <h1>Escolha o Seu Plano</h1>
            <p>Encontre o plano perfeito para o seu negócio sustentável</p>
        </div>

        <center>
            <div class="current-plan-banner" id="currentPlanBanner" style="display: none;">
                <i class="fas fa-crown"></i>
                <div>
                    <strong>Plano Atual:</strong>
                    <span id="currentPlanName">Carregando...</span>
                </div>
            </div>
        </center>

        <div class="plans-grid" id="plansGrid">
            <!-- Os planos serão carregados aqui via JavaScript -->
        </div>
    </div>

    <script>
        let currentUserPlan = null;
        let currentUserPlanId = null;

        $(document).ready(function() {
            loadPlans();
            loadCurrentPlan();
        });

        function loadCurrentPlan() {
            $.post('src/controller/controllerDashboardAnunciante.php', {
                op: 1
            }, function(response) {
                const data = JSON.parse(response);
                if (data.success && data.plano) {
                    currentUserPlan = data.plano;
                    // Usar o plano_id retornado diretamente do backend
                    currentUserPlanId = data.plano_id || 1;
                    $('#currentPlanName').text(data.plano);
                    $('#currentPlanBanner').show();
                }
            }).fail(function() {
                console.log('Não foi possível carregar plano atual');
            });
        }

        function loadPlans() {
            // Planos correspondentes à tabela 'planos' da database
            const plans = [{
                    id: 1,
                    name: "Plano Essencial Verde",
                    description: "Perfeito para começar a sua jornada sustentável na plataforma WeGreen",
                    price: 0,
                    period: "Gratuito",
                    icon: "fa-seedling",
                    features: [
                        "5 produtos ativos",
                        "Acesso ao ranking sustentabilidade",
                        "Ranking Confiança de Vendas",
                        "Chat direto com clientes",
                        "Histórico de vendas (gráficos simples)",
                        "Badge 'Iniciante Sustentável'",
                        "Taxas reduzidas em categorias ecológicas"
                    ],
                    featured: false
                },
                {
                    id: 2,
                    name: "Plano Crescimento Circular",
                    description: "Para empreendedores que querem expandir o seu negócio sustentável",
                    price: 25.00,
                    period: "30 dias",
                    icon: "fa-chart-line",
                    features: [
                        "Até 10 produtos ativos",
                        "Badge Sustentável: visível (para comissão)",
                        "Badge Confiança: visível",
                        "Relatórios básicos de vendas e audiência",
                        "Notificações de desempenho",
                        "Alertas sobre produtos com baixa performance"
                    ],
                    featured: false
                },
                {
                    id: 3,
                    name: "Plano Profissional Eco+",
                    description: "Para quem leva a sustentabilidade a sério e quer resultados profissionais",
                    price: 70.00,
                    period: "30 dias",
                    icon: "fa-crown",
                    features: [
                        "Produtos ilimitados",
                        "Badge Sustentável (comissão)",
                        "Badge Confiança (visual)",
                        "Relatórios avançados de impacto ambiental",
                        "Ferramentas de fidelização",
                        "Cupões e packs eco",
                        "Marketing recorrente",
                        "Análises detalhadas de performance"
                    ],
                    featured: true
                }
            ];

            renderPlans(plans);
        }

        function renderPlans(plans) {
            const grid = $('#plansGrid');
            grid.empty();

            plans.forEach(plan => {
                const isCurrentPlan = currentUserPlanId === plan.id;
                const isDowngrade = currentUserPlanId && plan.id < currentUserPlanId;
                const isFree = plan.price === 0;

                let buttonHtml = '';
                if (isCurrentPlan) {
                    buttonHtml = `
                        <button class="plan-button plan-button-current" disabled>
                            <i class="fas fa-check"></i> Plano Atual
                        </button>
                    `;
                } else if (isDowngrade) {
                    buttonHtml = `
                        <button class="plan-button plan-button-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fas fa-ban"></i> Downgrade Não Permitido
                        </button>
                    `;
                } else if (isFree) {
                    buttonHtml = `
                        <button class="plan-button plan-button-secondary" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <i class="fas fa-lock"></i> Plano Gratuito
                        </button>
                    `;
                } else {
                    buttonHtml = `
                        <button class="plan-button ${plan.featured ? 'plan-button-primary' : 'plan-button-secondary'}"
                                onclick="selectPlan(${plan.id}, '${plan.name}', ${plan.price})">
                            <i class="fas fa-bolt"></i> ${currentUserPlanId ? 'Fazer Upgrade' : 'Escolher Plano'}
                        </button>
                    `;
                }

                const card = `
                    <div class="plan-card ${plan.featured ? 'featured' : ''} ${isCurrentPlan ? 'current-plan' : ''}">
                        ${plan.featured ? '<div class="plan-badge">Mais Popular</div>' : ''}
                        ${isCurrentPlan ? '<div class="plan-badge current-badge">Plano Atual</div>' : ''}

                        <div class="plan-icon">
                            <i class="fas ${plan.icon}"></i>
                        </div>

                        <div class="plan-name">${plan.name}</div>
                        <div class="plan-description">${plan.description}</div>

                        <div class="plan-price">
                            ${plan.price > 0 ? `
                                <span class="plan-price-currency">€</span>
                                <span class="plan-price-amount">${plan.price.toFixed(2)}</span>
                                <span class="plan-price-period">/${plan.period}</span>
                            ` : `
                                <span class="plan-price-amount" style="font-size: 36px;">Gratuito</span>
                            `}
                        </div>

                        <ul class="plan-features">
                            ${plan.features.map(feature => `
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    ${feature}
                                </li>
                            `).join('')}
                        </ul>

                        ${buttonHtml}
                    </div>
                `;

                grid.append(card);
            });
        }

        function selectPlan(planId, planName, price) {
            Swal.fire({
                title: planName,
                html: `
                    <p style="font-size: 16px; margin-bottom: 20px;">
                        Tem certeza que deseja ${price > 0 ? 'fazer upgrade para' : 'escolher'} o <strong>${planName}</strong>?
                    </p>
                    ${price > 0 ? `<p style="font-size: 24px; color: #3cb371; font-weight: 700;">€${price.toFixed(2)}/mês</p>` : ''}
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3cb371',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: price > 0 ? '<i class="fas fa-credit-card"></i> Prosseguir para Pagamento' : '<i class="fas fa-check"></i> Confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (price > 0) {
                        // Redirecionar para checkout/pagamento
                        Swal.fire({
                            title: 'Processando...',
                            html: 'A redirecionar para o pagamento seguro',
                            timer: 1500,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        }).then(() => {
                            // Redirecionar para checkout Stripe
                            window.location.href = `checkout_stripe_plano.php?plan=${planId}`;
                        });
                    } else {
                        // Ativar plano gratuito diretamente
                        activateFreePlan(planId);
                    }
                }
            });
        }

        function activateFreePlan(planId) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Plano ativado com sucesso!',
                icon: 'success',
                confirmButtonColor: '#3cb371'
            }).then(() => {
                location.reload();
            });
        }
    </script>
</body>

</html>
