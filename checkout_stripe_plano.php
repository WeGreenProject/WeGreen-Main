<?php
require 'src/vendor/autoload.php';
session_start();

if(!isset($_SESSION['tipo']) || ($_SESSION['tipo'] != 1 && $_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 3)){
    header('Location: login.html');
    exit;
}

if(!isset($_GET['plan']) || empty($_GET['plan'])){
    header('Location: planos.php');
    exit;
}

\Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

$planId = intval($_GET['plan']);
$utilizador_id = $_SESSION['utilizador']; 
$userEmail = $_SESSION['email'] ?? '';

$plans = [
    1 => [
        'id' => 1,
        'name' => 'Plano Essencial Verde',
        'price' => 0,
        'price_id' => null 
    ],
    2 => [
        'id' => 2,
        'name' => 'Plano Crescimento Circular',
        'price' => 25.00,
        'price_id' => 'price_crescimento' 
    ],
    3 => [
        'id' => 3,
        'name' => 'Plano Profissional Eco+',
        'price' => 70.00,
        'price_id' => 'price_premium' 
    ]
];

if(!isset($plans[$planId])){
    header('Location: planos.php');
    exit;
}

$selectedPlan = $plans[$planId];

if($selectedPlan['price'] == 0){
    header('Location: planos.php');
    exit;
}

try {
    $sessionData = [
        'payment_method_types' => ['card'],
        'mode' => 'subscription', 
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $selectedPlan['name'],
                    'description' => 'Subscrição mensal - WeGreen',
                ],
                'unit_amount' => intval(round($selectedPlan['price'] * 100)), 
                'recurring' => [
                    'interval' => 'month',
                    'interval_count' => 1, 
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

    
    header("Location: " . $session->url);
    exit;

} catch(\Stripe\Exception\ApiErrorException $e) {
    
    $_SESSION['erro_stripe'] = $e->getMessage();
    header('Location: planos.php?erro=stripe');
    exit;
}
?>
