<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelDesigner.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Designer($conn);

if ($op == 1) {
    $resp = $func->getFiltrosDesignerCategoria();
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getProdutosDesigner($_POST["categoria"], $_POST["tamanho"], $_POST["estado"]);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getProdutoDesignerMostrar($_POST["id"]);
    echo $resp;
}

if ($op == 7) {
    $resp = $func->getFiltrosDesignerTamanho();
    echo $resp;
}

if ($op == 8) {
    $resp = $func->getFiltrosDesignerEstado();
    echo $resp;
}
?>
