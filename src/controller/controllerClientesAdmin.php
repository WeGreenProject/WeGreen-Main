<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
include_once '../model/modelClientesAdmin.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['flag' => false, 'msg' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['flag' => false, 'msg' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new ClientesAdmin($conn);

if ($op == 1) {
    $resp = $func->getClientes($_SESSION["utilizador"]);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getCardUtilizadores();
    echo $resp;
}

if ($op == 3) {
    $nome = trim((string)($_POST['clientNome'] ?? ''));
    $email = trim((string)($_POST['clientEmail'] ?? ''));
    $telefone = trim((string)($_POST['clientTelefone'] ?? ''));
    $tipo = (int)($_POST['clientTipo'] ?? 0);
    $nif = trim((string)($_POST['clientNif'] ?? ''));
    $password = (string)($_POST['clientPassword'] ?? '');
    $morada = trim((string)($_POST['clientMorada'] ?? ''));
    $foto = $_FILES['foto'] ?? null;

    $resp = $func->registaClientes(
        $nome,
        $email,
        $telefone,
        $tipo,
        $nif,
        $password,
        $morada,
        $foto
    );
    echo $resp;
}

if ($op == 4) {
    $resp = $func->removerClientes($_POST["ID_Cliente"]);
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getDadosCliente($_POST["id"]);
    echo $resp;
}

if ($op == 6) {
    $resp = $func->guardaEditCliente(
        $_POST["viewNome"],
        $_POST["viewEmail"],
        $_POST["viewTelefone"],
        $_POST["viewTipo"],
        $_POST["viewNif"],
        $_POST["viewPlano"],
        $_POST["viewRanking"],
        $_POST["ID_Utilizador"]
    );
    echo $resp;
}
?>
