<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelVerificarEmail.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new VerificarEmail($conn);

if ($op == 1) {
    $token = $_POST['token'] ?? null;

    if (!$token || empty($token)) {
        echo json_encode(['flag' => false, 'msg' => 'Token não fornecido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->verificarToken($token);
    echo $resp;
}

if ($op == 2) {
    $email = $_POST['email'] ?? null;

    if (!$email || empty($email)) {
        echo json_encode(['flag' => false, 'msg' => 'Email não fornecido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->reenviarVerificacao($email);
    echo $resp;
}
?>
