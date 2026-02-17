<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelAdminPerfil.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new AdminPerfil($conn);

if ($op == 1) {
    $resp = $func->getDadosTipoPerfil($_SESSION["utilizador"]);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getDadosTipoPerfilAdminInical($_SESSION["utilizador"]);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getDadosTipoPerfilAdminInfo($_SESSION["utilizador"]);
    echo $resp;
}

if ($op == 5) {
    $resp = $func->guardaDadosEditProduto(
        $_POST['nomeAdmin'],
        $_POST['emailAdmin'],
        $_POST['NIFadmin'],
        $_POST['telAdmin'],
        $_SESSION["utilizador"]
    );
    echo $resp;
}

if ($op == 6) {
    $resp = $func->adicionarFotoPerfil($_SESSION["utilizador"], $_FILES['foto']);
    echo $resp;
}

if ($op == 7) {
    $resp = $func->ProfileDropCard($_SESSION["utilizador"]);
    echo $resp;
}

if ($op == 8) {
    $resp = $func->ProfileDropCard2($_SESSION["utilizador"]);
    echo $resp;
}

if ($op == 11) {
    $resp = $func->guardarDadosPerfil(
        $_POST['nomeAdminEdit'] ?? '',
        $_POST['emailAdminEdit'],
        $_POST['nifAdminEdit'],
        $_POST['telefoneAdminEdit'],
        $_POST['moradaAdminEdit'],
        $_SESSION["utilizador"]
    );
    echo $resp;
}
?>
