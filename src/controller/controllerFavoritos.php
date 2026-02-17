<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelFavoritos.php';

if (!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Apenas clientes podem gerir favoritos.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Favoritos($conn);

if ($op == 1) {
    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];
    $resp = $func->adicionarFavorito($_SESSION['utilizador'], $produto_id);
    echo $resp;
}

if ($op == 2) {
    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];
    $resp = $func->removerFavorito($_SESSION['utilizador'], $produto_id);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->listarFavoritos($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 4) {
    if (!isset($_GET['produto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $produto_id = (int)$_GET['produto_id'];
    $resp = $func->verificarFavorito($_SESSION['utilizador'], $produto_id);
    echo $resp;
}

if ($op == 5) {
    $resp = $func->contarFavoritos($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 6) {
    $resp = $func->limparFavoritosInativos($_SESSION['utilizador']);
    echo $resp;
}
?>
