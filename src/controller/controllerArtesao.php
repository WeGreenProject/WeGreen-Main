<?php
include_once '../model/modelArtesao.php';
session_start();

$func = new Designer();
if ($_POST['op'] == 1) {
    $resp = $func->getFiltrosArtesaoCategoria();
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->getProdutosArtesao($_POST["categoria"],$_POST["tamanho"],$_POST["estado"]);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getProdutoArtesaoMostrar($_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 7) {
    $resp = $func->getFiltrosArtesaoTamanho();
    echo $resp;
}
if ($_POST['op'] == 8) {
    $resp = $func->getFiltrosArtesaoEstado();
    echo $resp;
}
?>