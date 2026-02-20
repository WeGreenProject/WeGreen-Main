<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelgestaoComentarios.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new GestaoComentarios($conn);

if ($op == 1) {
    $resp = $func->getCards();
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getProdutos();
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getButaoNav();
    echo $resp;
}

if ($op == 4) {
    $idProduto = $_POST['idProduto'];
    $resp = $func->getComentariosProduto($idProduto);
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getButaoReports();
    echo $resp;
}

if ($op == 6) {
    $resp = $func->getReports();
    echo $resp;
}

if ($op == 7) {
    $idReport = isset($_POST['idReport']) ? (int)$_POST['idReport'] : 0;
    $resp = $func->getReportDetalhes($idReport);
    echo $resp;
}

if ($op == 8) {
    $idReport = isset($_POST['idReport']) ? (int)$_POST['idReport'] : 0;
    $estado = trim((string)($_POST['estado'] ?? ''));
    $resp = $func->atualizarEstadoReport($idReport, $estado);
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
}

if ($op == 9) {
    $idReport = isset($_POST['idReport']) ? (int)$_POST['idReport'] : 0;
    $resp = $func->eliminarComentarioDoReport($idReport);
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
}
?>
