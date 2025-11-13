<?php
include_once '../model/modelHomem.php';
session_start();

$func = new Homem();

if ($_POST['op'] == 1) {
    $resp = $func->getProdutosHomem();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutoHomemMostrar($_POST["id"]);
    echo $resp;
}
?>