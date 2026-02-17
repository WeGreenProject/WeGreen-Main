<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelGestaoProdutos.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new GestaoProdutos($conn);

if ($op == 1) {
    $resp = $func->getProdutos();
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getMeusProdutos($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getListaVendedores();
    echo $resp;
}

if ($op == 4) {
    $resp = $func->getListaCategoria();
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getInativos();
    echo $resp;
}

if ($op == 6) {
    $resp = $func->getDadosInativos($_POST['Produto_id']);
    echo $resp;
}

if ($op == 7) {
    $resp = $func->guardaEditProduto(
        $_POST['nomeprodutoEdit'],
        $_POST['categoriaprodutoEdit'],
        $_POST['marcaprodutoEdit'],
        $_POST['tamanhoprodutoEdit'],
        $_POST['precoprodutoEdit'],
        $_POST['generoprodutoEdit'],
        $_POST['vendedorprodutoEdit'],
        $_POST['Produto_id']
    );
    echo $resp;
}

if ($op == 8) {
    $resp = $func->getFotosSection($_POST['Produto_id']);
    echo $resp;
}

if ($op == 9) {
    $resp = $func->rejeitaEditProduto($_POST['Produto_id'], $_POST['motivo_rejeicao'] ?? '');
    echo $resp;
}

if ($op == 10) {
    $resp = $func->getDadosProduto($_POST['Produto_id']);
    echo $resp;
}

if ($op == 11) {
    $resp = $func->guardaDadosEditProduto(
        $_POST['nomeprodutoEdit2'],
        $_POST['categoriaprodutoEdit2'],
        $_POST['marcaprodutoEdit2'],
        $_POST['tamanhoprodutoEdit2'],
        $_POST['precoprodutoEdit2'],
        $_POST['generoprodutoEdit2'],
        $_POST['vendedorprodutoEdit2'],
        $_POST['Produto_id']
    );
    echo $resp;
}

if ($op == 12) {
    $resp = $func->getDesativacao($_POST['produto_id']);
    echo $resp;
}

if ($op == 13) {
    $resp = $func->adicionarProdutos(
        $_POST['listaVendedor'],
        $_POST['listaCategoria'],
        $_POST['nomeprod'],
        $_POST['estadoprod'],
        $_POST['quantidade'],
        $_POST['preco'],
        $_POST['marca'],
        $_POST['tamanho'],
        $_POST['selectestado'],
        $_FILES['foto']
    );
    echo $resp;
}

if ($op == 14) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 15) {
    $resp = $func->getTopTipoGrafico();
    echo $resp;
}

if ($op == 16) {
    $resp = $func->getProdutoVendidos();
    echo $resp;
}

if ($op == 17) {
    $resp = $func->aprovarProduto($_POST['Produto_id']);
    echo $resp;
}
?>
