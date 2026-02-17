<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelArtesao.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Artesao($conn);

if ($op == 1) {
    $resp = $func->getFiltrosArtesaoCategoria();
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getProdutosArtesao($_POST["categoria"], $_POST["tamanho"], $_POST["estado"]);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getProdutoArtesaoMostrar($_POST["id"]);
    echo $resp;
}

if ($op == 7) {
    $resp = $func->getFiltrosArtesaoTamanho();
    echo $resp;
}

if ($op == 8) {
    $resp = $func->getFiltrosArtesaoEstado();
    echo $resp;
}
?>
