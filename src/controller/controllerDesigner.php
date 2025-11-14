<?php
include_once '../model/modelDesigner.php';
session_start();

$func = new Designer();

if ($_POST['op'] == 1) {
    $resp = $func->getProdutosDesigner();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutoDesignerMostrar($_POST["id"]);
    echo $resp;
}
?>