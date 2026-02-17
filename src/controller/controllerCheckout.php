<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelCheckout.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Checkout($conn);

if ($op == 1) {
    $utilizador = $_SESSION["utilizador"] ?? null;
    $plano = $_SESSION["plano"] ?? 1;

    $resp = $func->getPlanosComprar($utilizador, $plano);
    echo $resp;
}

if ($op == 2) {
    $nome = $_POST['nome'] ?? null;
    $email = $_POST['email'] ?? null;
    $morada = $_POST['morada'] ?? null;
    $codigo_postal = $_POST['codigo_postal'] ?? null;
    $metodo_entrega = $_POST['metodo_entrega'] ?? null;
    $metodo_pagamento = $_POST['metodo_pagamento'] ?? null;
    $dados_pagamento = $_POST['dados_pagamento'] ?? null;

    $resp = $func->guardarDadosCheckout($nome, $email, $morada, $codigo_postal, $metodo_entrega, $metodo_pagamento, $dados_pagamento);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->obterDadosCheckout();
    echo $resp;
}

if ($op == 4) {
    $resp = $func->limparDadosCheckout();
    echo $resp;
}

if ($op == 5) {
    $autenticado = isset($_SESSION['utilizador']) && !empty($_SESSION['utilizador']) && is_numeric($_SESSION['utilizador']) && (int)$_SESSION['utilizador'] > 0;
    echo json_encode(['autenticado' => $autenticado, 'utilizador_id' => $_SESSION['utilizador'] ?? null], JSON_UNESCAPED_UNICODE);
}
?>
