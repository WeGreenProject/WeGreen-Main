<?php
require 'src/vendor/autoload.php';
session_start();
require_once 'src/model/modelCheckout.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    require_once 'connection.php';
}

\Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

$func = new Checkout($conn);
$utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : 1;

$transportadoraId = isset($_POST['transportadora_id']) ? $_POST['transportadora_id'] : null;
$shippingData = [
    'firstName' => isset($_POST['firstName']) ? $_POST['firstName'] : '',
    'lastName' => isset($_POST['lastName']) ? $_POST['lastName'] : '',
    'address1' => isset($_POST['address1']) ? $_POST['address1'] : '',
    'address2' => isset($_POST['address2']) ? $_POST['address2'] : '',
    'zipCode' => isset($_POST['zipCode']) ? $_POST['zipCode'] : '',
    'city' => isset($_POST['city']) ? $_POST['city'] : '',
    'state' => isset($_POST['state']) ? $_POST['state'] : ''
];

$pickupPointData = [
    'pickup_point_id' => isset($_POST['pickup_point_id']) ? $_POST['pickup_point_id'] : '',
    'pickup_point_name' => isset($_POST['pickup_point_name']) ? $_POST['pickup_point_name'] : '',
    'pickup_point_address' => isset($_POST['pickup_point_address']) ? $_POST['pickup_point_address'] : ''
];

$produtos = $func->getProdutosCarrinho($utilizador_id);

if (empty($produtos)) {
    header("Location: carrinho.html");
    exit;
}

$line_items = [];
foreach ($produtos as $produto) {
    $valorCents = intval(round($produto['preco'] * 100));

    $line_items[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $produto['nome'],
                'images' => [$produto['foto']],
            ],
            'unit_amount' => $valorCents,
        ],
        'quantity' => $produto['quantidade'],
    ];
}

$shippingCost = 0;
$shippingName = '';

switch ($transportadoraId) {
    case '1': 
        $shippingCost = 250; 
        $shippingName = 'Envio CTT Standard';
        break;
    case '2': 
        $shippingCost = 250; 
        $shippingName = 'Envio CTT Pickup - ' . ($pickupPointData['pickup_point_name'] ?? 'Ponto de Recolha');
        break;
    case '3': 
        $shippingCost = 250; 
        $shippingName = 'Envio DPD Standard';
        break;
    case '4': 
        $shippingCost = 250; 
        $shippingName = 'Envio DPD Pickup - ' . ($pickupPointData['pickup_point_name'] ?? 'Ponto de Recolha');
        break;
    case '5': 
        $shippingCost = 500; 
        $shippingName = 'Entrega em Casa';
        break;
    default:
        $shippingCost = 250; 
        $shippingName = 'Envio Standard';
}

if ($shippingCost > 0) {
    $line_items[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $shippingName,
            ],
            'unit_amount' => $shippingCost,
        ],
        'quantity' => 1,
    ];
}

$discounts = [];
if (isset($_SESSION['cupao_desconto']) && $_SESSION['cupao_desconto'] > 0) {
    
    $coupon = \Stripe\Coupon::create([
        'percent_off' => $_SESSION['cupao_desconto'],
        'duration' => 'once',
        'name' => 'WEGREEN10'
    ]);

    $discounts[] = ['coupon' => $coupon->id];
}

$sessionData = [
    'payment_method_types' => ['card', 'paypal', 'klarna'],
    'line_items' => $line_items,
    'mode' => 'payment',
    'success_url' => 'http://localhost/wegreen-main/sucess_carrinho.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/wegreen-main/carrinho.html',
    'customer_creation' => 'always',
    'metadata' => [
        'utilizador' => $utilizador_id,
        'tipo' => 'carrinho',
        'transportadora_id' => $transportadoraId ?? '',
        'shipping_firstName' => $shippingData['firstName'],
        'shipping_lastName' => $shippingData['lastName'],
        'shipping_address1' => $shippingData['address1'],
        'shipping_address2' => $shippingData['address2'],
        'shipping_zipCode' => $shippingData['zipCode'],
        'shipping_city' => $shippingData['city'],
        'shipping_state' => $shippingData['state'],
        
        'pickup_point_id' => $pickupPointData['pickup_point_id'],
        'pickup_point_name' => $pickupPointData['pickup_point_name'],
        'pickup_point_address' => $pickupPointData['pickup_point_address']
    ]
];

if (!empty($discounts)) {
    $sessionData['discounts'] = $discounts;
}

$session = \Stripe\Checkout\Session::create($sessionData);

header("Location: " . $session->url);
exit;
