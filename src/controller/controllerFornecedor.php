<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelFornecedor.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Fornecedor($conn);

if ($op == 1) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getFornecedores();
    echo $resp;
}

if ($op == 4) {
    $resp = $func->guardaAdicionarFornecedor(
        $_POST['fornecedorNome'],
        $_POST['fornecedorCategoria'],
        $_POST['fornecedorEmail'],
        $_POST['fornecedortelefone'],
        $_POST['fornecedorSede'],
        $_POST['observacoes']
    );
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getListaCategoria();
    echo $resp;
}

if ($op == 6) {
    $resp = $func->removerFornecedores($_POST['id']);
    echo $resp;
}

if ($op == 9) {
    $resp = $func->getDadosFornecedores($_POST['id']);
    echo $resp;
}

if ($op == 67) {
    $resp = $func->guardaEditDadosFornecedores(
        $_POST['fornecedorNomeEdit'],
        $_POST['fornecedorCategoriaEdit'],
        $_POST['fornecedorEmailEdit'],
        $_POST['fornecedorTelefoneEdit'],
        $_POST['fornecedorSedeEdit'],
        $_POST['observacoesEdit'],
        $_POST['id']
    );
    echo $resp;
}
?>
