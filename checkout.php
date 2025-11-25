<?php 
require 'src/vendor/autoload.php';  
session_start(); 
\Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');  
 
$preco = isset($_GET['preco']) ? floatval($_GET['preco']) : 0; 
$plano = isset($_GET['plano']) ? $_GET['plano'] : ''; 
$valorCents = intval(round($preco * 100)); 
 
if($valorCents <= 0){ 
    die("Erro: preço inválido. Valor recebido = " . htmlspecialchars($_GET['preco'])); 
} 
 
$planoId = ($plano === 'enterprise') ? 3 : 2; 
$nomeProduto = ($plano === 'enterprise') ? 'Plano Enterprise' : 'Plano Premium'; 
 
$session = \Stripe\Checkout\Session::create([ 
    'payment_method_types' => ['card'],  
    'line_items' => [[ 
        'price_data' => [ 
            'currency' => 'eur', 
            'product_data' => [ 
                'name' => $nomeProduto, 
                'description' => 'Subscrição ' . $nomeProduto, 
            ], 
            'unit_amount' => $valorCents, 
        ], 
        'quantity' => 1, 
    ]], 
    'mode' => 'payment', 
    'success_url' => 'http://localhost/wegreen-main/sucess.php?session_id={CHECKOUT_SESSION_ID}&plano_id=' . $planoId, 
    'cancel_url' => 'http://localhost/wegreen-main/cancelado.php', 
    'metadata' => [ 
        'utilizador' => $_SESSION["utilizador"], 
        'plano_id' => $planoId 
    ] 
]); 
 
header("Location: " . $session->url); 
exit;