<?php
include_once '../model/modelProdutosAdmin.php';
session_start();

$func = new ProdutosAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutosAprovar();
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getProdutosPendentes();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->getProdutosAprovado();
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getProdutosRejeitado();
    echo $resp;
}
?>