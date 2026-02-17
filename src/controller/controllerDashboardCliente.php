<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelDashboardCliente.php';

if (!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new DashboardCliente($conn);

if ($op == 1) {
    $resp = $func->getEstatisticasCliente($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getEncomendasRecentes($_SESSION['utilizador'], 5);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getProdutosRecomendados($_SESSION['utilizador'], 6);
    echo $resp;
}
?>
