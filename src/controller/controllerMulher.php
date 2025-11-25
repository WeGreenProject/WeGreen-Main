<?php
include_once '../model/modelMulher.php';
session_start();

$func = new Mulher();
if ($_POST['op'] == 1) {
    $resp = $func->getFiltrosMulherCategoria();
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->getProdutosMulher($_POST["categoria"],$_POST["tamanho"],$_POST["estado"]);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getProdutoMulherMostrar($_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 7) {
    $resp = $func->getFiltrosMulherTamanho();
    echo $resp;
}
if ($_POST['op'] == 8) {
    $resp = $func->getFiltrosMulherEstado();
    echo $resp;
}
?>