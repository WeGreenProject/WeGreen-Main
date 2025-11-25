<?php
include_once '../model/modelCriança.php';
session_start();

$func = new Criança();

if ($_POST['op'] == 1) {
    $resp = $func->getProdutosCriança($_POST["categoria"],$_POST["tamanho"],$_POST["estado"]);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutoCriançaMostrar($_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getFiltrosCriancaCategoria();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->getFiltrosCriancaTamanho();
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getFiltrosCriancaEstado();
    echo $resp;
}

?>