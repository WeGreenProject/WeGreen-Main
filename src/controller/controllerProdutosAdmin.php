<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelProdutosAdmin.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new ProdutosAdmin($conn);

if ($op == 1) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getProdutosAprovar($_POST['estado']);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getProdutosPendentes();
    echo $resp;
}

if ($op == 4) {
    $resp = $func->getProdutosAprovado();
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getProdutosRejeitado();
    echo $resp;
}

if ($op == 6) {
    $resp = $func->getFiltro();
    echo $resp;
}
?>
