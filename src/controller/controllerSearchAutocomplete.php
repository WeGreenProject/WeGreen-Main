<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelSearchAutocomplete.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new SearchAutocomplete($conn);

if ($op == 1) {
    $query = trim($_GET['q'] ?? '');
    $resp = $func->searchProdutos($query);
    echo $resp;
}
?>
