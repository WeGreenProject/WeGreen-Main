<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelNotifications.php';

if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Notifications($conn);

if ($op == 1) {
    $resp = $func->contarNotificacoesPorTipoJson($_SESSION['utilizador'], $_SESSION['tipo']);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->listarNotificacoesPorTipoJson($_SESSION['utilizador'], $_SESSION['tipo']);
    echo $resp;
}

if ($op == 3) {
    $tipo_notificacao = $_POST['tipo'] ?? '';
    $referencia_id = $_POST['id'] ?? 0;

    if ($referencia_id > 0 && !empty($tipo_notificacao)) {
        $resp = $func->marcarComoLidaJson($_SESSION['utilizador'], $tipo_notificacao, $referencia_id);
        echo $resp;
    } else {
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos'], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 4) {
    $resp = $func->marcarTodasComoLidasJson($_SESSION['utilizador'], $_SESSION['tipo']);
    echo $resp;
}

if ($op == 5) {
    $resp = $func->listarTodasNotificacoesPorTipoJson($_SESSION['utilizador'], $_SESSION['tipo']);
    echo $resp;
}
?>
