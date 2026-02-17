<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelPasswordReset.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new PasswordReset($conn);

if ($op == 1) {
    $email = $_POST['email'] ?? null;

    if (!$email || empty($email)) {
        echo json_encode(['flag' => false, 'msg' => 'Por favor, insira o seu email.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->solicitarRecuperacao($email);
    echo $resp;
}

if ($op == 2) {
    $token = $_POST['token'] ?? null;

    if (!$token || empty($token)) {
        echo json_encode(['flag' => false, 'msg' => 'Token não fornecido.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->validarToken($token);
    echo $resp;
}

if ($op == 3) {
    $token = $_POST['token'] ?? null;
    $nova_password = $_POST['nova_password'] ?? null;

    if (!$token || !$nova_password) {
        echo json_encode(['flag' => false, 'msg' => 'Token ou password não fornecidos.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->redefinirPasswordComValidacao($token, $nova_password);
    echo $resp;
}
?>
