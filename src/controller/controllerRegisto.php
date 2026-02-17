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
    
    if (!isset($_POST['nome']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['morada']) || !isset($_POST['codigo_postal']) || !isset($_POST['distrito']) || !isset($_POST['localidade'])) {
        echo json_encode(['success' => false, 'message' => 'Dados obrigatórios em falta'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->registaUser(
        $_POST['nome'],
        $_POST['apelido'],
        $_POST['email'],
        $_POST['nif'],
        $_POST['morada'],
        $_POST['password'],
        $_POST['tipoUtilizador'],
        $_POST['codigo_postal'],
        $_POST['distrito'],
        $_POST['localidade']
    );
    echo $resp;
}
?>
