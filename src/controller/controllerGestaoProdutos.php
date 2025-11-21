<?php
include_once '../model/modelGestaoProdutos.php';
session_start();
$func = new Vendas();

if ($_POST['op'] == 1) {
    $resp = $func->getProdutos();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getMeusProdutos($_SESSION['utilizador']);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getListaVendedores();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->getListaCategoria();
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getInativos();
    echo $resp;
}
if ($_POST['op'] == 6) {
    $resp = $func->getDadosInativos($_POST['Produto_id']);
    echo $resp;
}
if ($_POST['op'] == 7) {
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
if ($_POST['op'] == 8) {
    $resp = $func->getFotosSection($_POST['Produto_id']);
    echo $resp;
}
if ($_POST['op'] == 9) {
    $resp = $func->rejeitaEditProduto($_POST['Produto_id']);
    echo $resp;
}
if ($_POST['op'] == 10) {
    $resp = $func->getDadosProduto($_POST['Produto_id']);
    echo $resp;
}
if ($_POST['op'] == 11) {
    $resp = $func->guardaDadosEditProduto(
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
?>