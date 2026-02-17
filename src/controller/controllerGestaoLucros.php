<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelGestaoLucros.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new GestaoLucros($conn);

if ($op == 1) {
    $resp = $func->getCardsReceitas();
    echo $resp;
}

if ($op == 2) {
    $resp = $func->GraficoReceita();
    echo $resp;
}

if ($op == 4) {
    $resp = $func->getTransicoes();
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getRendimentos();
    echo $resp;
}

if ($op == 6) {
    $resp = $func->getGastos();
    echo $resp;
}

if ($op == 7) {
    $resp = $func->removerGastos($_POST['ID_Gasto']);
    echo $resp;
}

if ($op == 8) {
    $resp = $func->removerRendimentos($_POST['ID_Rendimento']);
    echo $resp;
}

if ($op == 9) {
    $resp = $func->registaRendimentos(
        $_SESSION['utilizador'],
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
    );
    echo $resp;
}

if ($op == 10) {
    $resp = $func->registaGastos(
        $_SESSION['utilizador'],
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
    );
    echo $resp;
}

if ($op == 11) {
    $resp = $func->getCardsDespesas();
    echo $resp;
}

if ($op == 12) {
    $resp = $func->getCardsLucro();
    echo $resp;
}

if ($op == 13) {
    $resp = $func->getCardsMargem();
    echo $resp;
}

if ($op == 14) {
    $ids = is_array($_POST['ids']) ? $_POST['ids'] : [$_POST['ids']];
    $resp = $func->removerGastosEmMassa($ids);
    echo $resp;
}

if ($op == 15) {
    $ids = is_array($_POST['ids']) ? $_POST['ids'] : [$_POST['ids']];
    $resp = $func->removerRendimentosEmMassa($ids);
    echo $resp;
}

if ($op == 16) {
    $resp = $func->editarGasto(
        $_POST['id'],
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
    );
    echo $resp;
}

if ($op == 17) {
    $resp = $func->editarRendimento(
        $_POST['id'],
        $_POST['descricao'],
        $_POST['valor'],
        $_POST['data']
    );
    echo $resp;
}
?>
