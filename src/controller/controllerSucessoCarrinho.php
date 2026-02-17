<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelSucessoCarrinho.php';

if (!isset($_SESSION['utilizador'])) {
    header('Location: /wegreen-main/login.html');
    exit;
}

$func = new SucessoCarrinho($conn);

$session_id = $_GET['session_id'] ?? null;
if (!$session_id) {
    header('Location: /wegreen-main/Carrinho.html');
    exit;
}

$resp = $func->processarPagamentoStripe($session_id, $_SESSION['utilizador']);

$redirectSucesso = '/wegreen-main/sucess_carrinho.php';
$redirectErro = '/wegreen-main/Carrinho.html';

if (is_array($resp) && isset($resp['redirect']) && !empty($resp['redirect'])) {
    $redirect = $resp['redirect'];
} else {
    $temSucesso = is_array($resp) && !empty($resp['sucesso']);
    $redirect = $temSucesso ? $redirectSucesso : $redirectErro;
}

if (is_array($resp) && !empty($resp['sucesso'])) {
    $_SESSION['resultado_pagamento'] = $resp;
    header('Location: ' . $redirect);
} else {
    header('Location: ' . $redirect);
}
exit;
?>
