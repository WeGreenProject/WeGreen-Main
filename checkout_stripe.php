<?php 
require 'src/vendor/autoload.php';  
session_start(); 
require_once 'src/model/modelCheckout.php';

\Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');  

$func = new Checkout();
$utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : 1;

// Buscar produtos do carrinho
$produtos = $func->getProdutosCarrinho($utilizador_id);

if (empty($produtos)) {
    header("Location: carrinho.html");
    exit;
}

// Construir line_items para o Stripe
$line_items = [];
foreach ($produtos as $produto) {
    $valorCents = intval(round($produto['preco'] * 100));
    
    $line_items[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $produto['nome'],
                'images' => [$produto['foto']], // Stripe aceita URLs de imagens
            ],
            'unit_amount' => $valorCents,
        ],
        'quantity' => $produto['quantidade'],
    ];
}

// Adicionar taxa de envio
$line_items[] = [
    'price_data' => [
        'currency' => 'eur',
        'product_data' => [
            'name' => 'Envio',
            'description' => 'Taxa de envio',
        ],
        'unit_amount' => 500, // €5.00
    ],
    'quantity' => 1,
];

// Aplicar desconto se houver cupão
$discounts = [];
if (isset($_SESSION['cupao_desconto']) && $_SESSION['cupao_desconto'] > 0) {
    // Criar cupom no Stripe
    $coupon = \Stripe\Coupon::create([
        'percent_off' => $_SESSION['cupao_desconto'],
        'duration' => 'once',
        'name' => 'WEGREEN10'
    ]);
    
    $discounts[] = ['coupon' => $coupon->id];
}

$sessionData = [
    'payment_method_types' => ['card'],  
    'line_items' => $line_items,
    'mode' => 'payment', 
    'success_url' => 'http://localhost/wegreen-main/sucess_carrinho.php?session_id={CHECKOUT_SESSION_ID}', 
    'cancel_url' => 'http://localhost/wegreen-main/carrinho.html', 
    'metadata' => [ 
        'utilizador' => $utilizador_id,
        'tipo' => 'carrinho'
    ]
];

if (!empty($discounts)) {
    $sessionData['discounts'] = $discounts;
}

$session = \Stripe\Checkout\Session::create($sessionData);
 
header("Location: " . $session->url); 
exit;