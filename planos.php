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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 50px;
            color: #1a202c;
        }

        .back-button {
            position: absolute;
            top: 30px;
            left: 30px;
            background: #ffffff;
            color: #475569;
            border: 2px solid #e2e8f0;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #f8f9fa;
            border-color: #A6D90C;
            color: #A6D90C;
            transform: translateX(-5px);
        }

        .page-header h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .page-header p {
            font-size: 20px;
            color: #64748b;
            font-weight: 500;
        }

        .current-plan-banner {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 20px 30px;
            margin-bottom: 40px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
            color: #1a202c;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .current-plan-banner i {
            font-size: 24px;
            color: #A6D90C;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .plan-card {
            background: #ffffff;
            border: 3px solid #e2e8f0;
            border-radius: 24px;
            padding: 40px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .plan-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #A6D90C 0%, #90c207 100%);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .plan-card:hover::before {
            transform: scaleX(1);
        }

        .plan-card.featured {
            border-color: #A6D90C;
            box-shadow: 0 20px 60px rgba(166, 217, 12, 0.25);
            transform: scale(1.05);
        }

        .plan-card.current-plan {
            border-color: #667eea;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .plan-card.featured:hover {
            transform: scale(1.05) translateY(-10px);
        }

        .plan-badge {
            position: absolute;
            top: 25px;
            right: 25px;
            background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
            color: #1a202c;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(166, 217, 12, 0.4);
        }

        .current-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
        }

        .plan-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #1a202c;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(166, 217, 12, 0.3);
        }

        .plan-name {
            font-size: 28px;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 12px;
        }

        .plan-description {
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 30px;
            min-height: 70px;
        }

        .plan-price {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f1f5f9;
        }

        .plan-price-currency {
            font-size: 28px;
            font-weight: 700;
            color: #A6D90C;
        }

        .plan-price-amount {
            font-size: 52px;
            font-weight: 900;
            color: #1a202c;
            line-height: 1;
        }

        .plan-price-period {
            font-size: 16px;
            color: #94a3b8;
            font-weight: 600;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 35px;
        }

        .plan-features li {
            padding: 14px 0;
            color: #475569;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .plan-features li i {
            color: #A6D90C;
            font-size: 18px;
            flex-shrink: 0;
        }

        .plan-button {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-button-primary {
            background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
            color: #1a202c;
            box-shadow: 0 8px 20px rgba(166, 217, 12, 0.3);
        }

        .plan-button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(166, 217, 12, 0.4);
        }

        .plan-button-secondary {
            background: #f1f5f9;
            color: #475569;
        }

        .plan-button-secondary:hover {
            background: #e2e8f0;
        }

        .plan-button-current {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            cursor: default;
        }

        .plan-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .features-comparison {
            background: #ffffff;
            border-radius: 24px;
            padding: 50px;
            margin-top: 60px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .features-comparison h2 {
            font-size: 32px;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 30px;
            text-align: center;
        }


    </style>
</head>

<body>
    <a href="<?php echo $backUrl; ?>" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Voltar ao Dashboard
    </a>

    <div class="container">
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
                        "Até 5 produtos",
                        "Rastreio básico",
                        "Suporte por email",
                        "Acesso à comunidade",
                        "Perfil público"
                    ],
                    featured: false
                },
                {
                    id: 2,
                    name: "Plano Crescimento Circular",
                    description: "Para empreendedores que querem expandir o seu negócio sustentável",
                    price: 25.00,
                    period: "mês",
                    icon: "fa-chart-line",
                    features: [
                        "Até 10 produtos",
                        "Rastreio básico",
                        "Suporte prioritário",
                        "Relatórios PDF",
                        "Badge verificado",
                        "Destaque em pesquisas",
                        "Análises detalhadas"
                    ],
                    featured: false
                },
                {
                    id: 3,
                    name: "Plano Profissional Eco+",
                    description: "Para quem leva a sustentabilidade a sério e quer resultados profissionais",
                    price: 100.00,
                    period: "mês",
                    icon: "fa-crown",
                    features: [
                        "Produtos ilimitados",
                        "Rastreio avançado",
                        "Suporte VIP 24/7",
                        "Relatórios PDF personalizados",
                        "Badge premium dourado",
                        "Prioridade máxima",
                        "Consultoria mensal",
                        "API de integração",
                        "Gestor dedicado"
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
                const isCurrentPlan = currentUserPlan && plan.name.includes(currentUserPlan);

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

                        ${isCurrentPlan ? `
                            <button class="plan-button plan-button-current" disabled>
                                <i class="fas fa-check"></i> Plano Atual
                            </button>
                        ` : `
                            <button class="plan-button ${plan.featured ? 'plan-button-primary' : 'plan-button-secondary'}"
                                    onclick="selectPlan(${plan.id}, '${plan.name}', ${plan.price})">
                                <i class="fas fa-bolt"></i> ${plan.price > 0 ? 'Escolher Plano' : 'Começar Grátis'}
                            </button>
                        `}
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
                    ${price > 0 ? `<p style="font-size: 24px; color: #A6D90C; font-weight: 700;">€${price.toFixed(2)}/mês</p>` : ''}
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#A6D90C',
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
                confirmButtonColor: '#A6D90C'
            }).then(() => {
                location.reload();
            });
        }
    </script>
</body>

</html>
