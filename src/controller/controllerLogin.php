<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelLogin.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Login($conn);

if ($op == 1) {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$email || !$password) {
        echo json_encode(['flag' => false, 'msg' => 'Email e password são obrigatórios'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->iniciarSessao($email, $password);
    echo $resp;
}
?>
