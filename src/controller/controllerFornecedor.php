<?php
include_once '../model/modelFornecedor.php';
session_start();

$func = new Fornecedor();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getFornecedores();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->guardaAdicionarFornecedor(
        $_POST['fornecedorNome'],
        $_POST['fornecedorCategoria'],
        $_POST['fornecedorEmail'],
        $_POST['fornecedortelefone'],
        $_POST['fornecedorSede'],
        $_POST['observacoes'],
    );
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getListaCategoria();
    echo $resp;
}
if ($_POST['op'] == 6) {
    $resp = $func->removerFornecedores($_POST['id']);
    echo $resp;
}
if ($_POST['op'] == 9) {
    $resp = $func->getDadosFornecedores($_POST['id']);
    echo $resp;
}
?>