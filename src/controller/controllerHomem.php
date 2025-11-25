<?php
include_once '../model/modelHomem.php';
session_start();

$func = new Homem();

if ($_POST['op'] == 1) {
    $resp = $func->getProdutosHomem($_POST["categoria"],$_POST["tamanho"],$_POST["estado"]);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutoHomemMostrar($_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getFiltrosHomemCategoria();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->getFiltrosHomemTamanho();
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getFiltrosHomemEstado();
    echo $resp;
}
?>