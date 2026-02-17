<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../model/modelEncomendas.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Encomendas($conn);

if ($op == 'listarEncomendasCliente') {
    $cliente_id = $_SESSION['utilizador'];
    $resp = $func->listarPorCliente($cliente_id);

    if (!is_array($resp)) {
        $resp = [];
    }

    if ($resp) {
        echo json_encode(['success' => true, 'data' => $resp], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => true, 'data' => []], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 'detalhesEncomenda') {
    $codigo = $_POST['codigo'] ?? null;

    if (!$codigo || empty($codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $cliente_id = $_SESSION['utilizador'];
    $resp = $func->obterDetalhes($codigo, $cliente_id);

    if ($resp) {
        echo json_encode(['success' => true, 'data' => $resp], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Encomenda não encontrada'], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 'cancelarEncomenda') {
    $codigo = $_POST['codigo'] ?? null;

    if (!$codigo || empty($codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cliente_id = $_SESSION['utilizador'];
    $resp = $func->cancelar($codigo, $cliente_id);
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
}

if ($op == 'confirmarRececao') {
    $codigo = $_POST['codigo_confirmacao'] ?? null;

    if (!$codigo || empty(trim($codigo))) {
        echo json_encode(['success' => false, 'message' => 'Código inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $cliente_id = $_SESSION['utilizador'];
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $resp = $func->confirmarRececao($codigo, $cliente_id, $ip);
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
}

if ($op == 'gerarFatura') {
    $codigo = $_GET['codigo'] ?? null;

    if (!$codigo || empty($codigo)) {
        die('Código inválido');
    }

    $cliente_id = $_SESSION['utilizador'];
    $resp = $func->gerarFaturaPDF($codigo, $cliente_id);

    if ($resp['success']) {
        header('Content-Type: text/html; charset=UTF-8');
        echo $resp['content'];
    } else {
        die($resp['message']);
    }
}
?>
