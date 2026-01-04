<?php
require 'src/vendor/autoload.php';
session_start();

// Verificar se está autenticado
if(!isset($_SESSION['tipo']) || ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 3)){
    header('Location: login.html');
    exit;
}

// Verificar se o plano foi especificado
if(!isset($_GET['plan']) || empty($_GET['plan'])){
    header('Location: planos.php');
    exit;
}

\Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

$planId = intval($_GET['plan']);
$utilizador_id = $_SESSION['utilizador']; // ID do utilizador na sessão
$userEmail = $_SESSION['email'] ?? '';

// Definir os planos (mesmo array do planos.php)
$plans = [
    1 => [
        'id' => 1,
        'name' => 'Plano Básico',
        'price' => 0,
        'price_id' => null // Plano gratuito não precisa de Stripe
    ],
    2 => [
        'id' => 2,
        'name' => 'Plano Crescimento',
        'price' => 19.99,
        'price_id' => 'price_crescimento' // Você precisará criar este Price ID no Stripe Dashboard
    ],
    3 => [
        'id' => 3,
        'name' => 'Plano Premium',
        'price' => 49.99,
        'price_id' => 'price_premium' // Você precisará criar este Price ID no Stripe Dashboard
    ]
];

// Verificar se o plano existe
if(!isset($plans[$planId])){
    header('Location: planos.php');
    exit;
}

$selectedPlan = $plans[$planId];

// Se for plano gratuito, redirecionar de volta
if($selectedPlan['price'] == 0){
    header('Location: planos.php');
    exit;
}

// Criar sessão de checkout do Stripe para subscrição
try {
    $sessionData = [
        'payment_method_types' => ['card'],
        'mode' => 'subscription', // Modo subscrição ao invés de payment
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $selectedPlan['name'],
                    'description' => 'Subscrição mensal - WeGreen',
                ],
                'unit_amount' => intval(round($selectedPlan['price'] * 100)), // Converter para centavos
                'recurring' => [
                    'interval' => 'month',
                ],
            ],
            'quantity' => 1,
        ]],
        'success_url' => 'http://localhost/wegreen-main/sucess_plano.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/wegreen-main/planos.php',
        'customer_email' => $userEmail,
        'metadata' => [
            'utilizador_id' => $utilizador_id,
            'plano_id' => $planId,
            'plano_nome' => $selectedPlan['name'],
            'tipo_transacao' => 'subscricao_plano',
            'user_type' => $_SESSION['tipo']
        ],
        'subscription_data' => [
            'metadata' => [
                'utilizador_id' => $utilizador_id,
                'plano_id' => $planId,
            ],
        ],
    ];

    $session = \Stripe\Checkout\Session::create($sessionData);

    // Redirecionar para o Stripe Checkout
    header("Location: " . $session->url);
    exit;

} catch(\Stripe\Exception\ApiErrorException $e) {
    // Erro na criação da sessão Stripe
    $_SESSION['erro_stripe'] = $e->getMessage();
    header('Location: planos.php?erro=stripe');
    exit;
}
?>
