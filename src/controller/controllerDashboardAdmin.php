<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelDashboardAdmin.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new DashboardAdmin($conn);

if ($op == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'], $_SESSION['plano']);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getUtilizadores($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getRendimentos();
    echo $resp;
}

if ($op == 4) {
    $resp = $func->getGastos();
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getVendasGrafico();
    echo $resp;
}

if ($op == 6) {
    $resp = $func->getTopTipoGrafico();
    echo $resp;
}

if ($op == 7) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 8) {
    $resp = $func->getProdutosInvativo();
    echo $resp;
}

if ($op == 9) {
    $resp = $func->getInfoUserDropdown($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 10) {
    $resp = $func->logout();
    echo json_encode(['flag' => true, 'msg' => 'Logout realizado com sucesso'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($op == 21) {
    $resp = $func->getAdminPerfil($_SESSION["utilizador"]);
    echo $resp;
}
?>
