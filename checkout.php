<?php
require 'vendor/autoload.php'; // Certifica-te que o Stripe foi instalado via Composer

// ⚙️ Configura a tua chave secreta Stripe (modo teste primeiro)
\Stripe\Stripe::setApiKey('sk_test_TUACHAVE_SECRETA_AQUI');

// Permitir apenas pedidos POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

// 🧾 Capturar dados do pedido vindos do JavaScript/Fetch
$data = json_decode(file_get_contents("php://input"), true);
$itens = $data["itens"] ?? [];
$total = $data["total"] ?? 0;

if (empty($itens)) {
    http_response_code(400);
    echo json_encode(["error" => "Nenhum item no carrinho"]);
    exit;
}

try {
    // 🧮 Criar sessão de pagamento Stripe
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'], // podes adicionar 'paypal' via Stripe Connect se quiseres
        'mode' => 'payment',
        'line_items' => array_map(function($item) {
            return [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['nome'],
                        'images' => [$item['imagem']]
                    ],
                    'unit_amount' => intval($item['preco'] * 100),
                ],
                'quantity' => 1,
            ];
        }, $itens),
        'success_url' => 'https://teusite.com/sucesso.html',
        'cancel_url' => 'https://teusite.com/cancelado.html',
    ]);

    // 🪙 Devolver o ID da sessão ao JavaScript
    header('Content-Type: application/json');
    echo json_encode(['id' => $session->id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
