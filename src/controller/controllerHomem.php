<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelHomem.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Homem($conn);

if ($op == 1) {
    $resp = $func->getProdutosHomem($_POST["categoria"], $_POST["tamanho"], $_POST["estado"]);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getProdutoHomemMostrar($_POST["id"]);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getFiltrosHomemCategoria();
    echo $resp;
}

if ($op == 4) {
    $resp = $func->getFiltrosHomemTamanho();
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getFiltrosHomemEstado();
    echo $resp;
}
?>
