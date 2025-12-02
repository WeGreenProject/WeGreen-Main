<?php
include_once '../model/modelDesigner.php';
session_start();

$func = new Designer();
if ($_POST['op'] == 1) {
    $resp = $func->getFiltrosDesignerCategoria();
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->getProdutosDesigner($_POST["categoria"],$_POST["tamanho"],$_POST["estado"]);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getProdutoDesignerMostrar($_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 7) {
    $resp = $func->getFiltrosDesignerTamanho();
    echo $resp;
}
if ($_POST['op'] == 8) {
    $resp = $func->getFiltrosDesignerEstado();
    echo $resp;
}
?>