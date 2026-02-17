<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

include_once '../model/modelVendedor.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Vendedor($conn);

if ($op == 1) {
    $resp = $func->getPerfilVendedora($_POST["utilizadorID"]);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getProdutosVendedora($_POST["utilizadorID"]);
    echo $resp;
}
?>
