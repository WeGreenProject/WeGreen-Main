<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelRegisto.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Registo($conn);

if ($op == 1) {

    if (!isset($_POST['nome']) || !isset($_POST['apelido']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['morada']) || !isset($_POST['tipoUtilizador']) || !isset($_POST['codigo_postal']) || !isset($_POST['distrito']) || !isset($_POST['localidade'])) {
        echo json_encode(['success' => false, 'message' => 'Dados obrigatórios em falta'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $tipoUtilizador = (int)($_POST['tipoUtilizador'] ?? 0);
    if (!in_array($tipoUtilizador, [2, 3], true)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de conta inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $nome = trim($_POST['nome'] ?? '');
    $apelido = trim($_POST['apelido'] ?? '');
    $email = mb_strtolower(trim($_POST['email'] ?? ''), 'UTF-8');
    $nif = trim($_POST['nif'] ?? '');
    $morada = trim($_POST['morada'] ?? '');
    $codigo_postal = trim($_POST['codigo_postal'] ?? '');
    $distrito = trim($_POST['distrito'] ?? '');
    $localidade = trim($_POST['localidade'] ?? '');
    $password = $_POST['password'] ?? '';

    $resp = $func->registaUser(
        $nome,
        $apelido,
        $email,
        $nif,
        $morada,
        $password,
        $tipoUtilizador,
        $codigo_postal,
        $distrito,
        $localidade
    );
    echo $resp;
}
?>
