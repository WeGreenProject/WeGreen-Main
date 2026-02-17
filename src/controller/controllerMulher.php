<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelMulher.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Mulher($conn);

if ($op == 1) {
    $resp = $func->getFiltrosMulherCategoria();
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getProdutosMulher($_POST["categoria"], $_POST["tamanho"], $_POST["estado"]);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getProdutoMulherMostrar($_POST["id"]);
    echo $resp;
}

if ($op == 7) {
    $resp = $func->getFiltrosMulherTamanho();
    echo $resp;
}

if ($op == 8) {
    $resp = $func->getFiltrosMulherEstado();
    echo $resp;
}
?>
