<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelProduto.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Produto($conn);

if ($op == 1) {
    if (isset($_POST['id'])) {
        $resp = $func->getDadosProduto($_POST['id']);
        echo $resp;
    } else {
        echo json_encode(['error' => 'ID do produto não fornecido'], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 2) {
    if (isset($_POST['id'])) {
        $limite = $_POST['limite'] ?? 4;
        $resp = $func->getProdutosRelacionados($_POST['id'], null, $limite);
        echo $resp;
    } else {
        echo json_encode([], JSON_UNESCAPED_UNICODE);
    }
}
?>
