<?php
include_once '../model/modelCriança.php';
session_start();

$func = new Criança();

if ($_POST['op'] == 1) {
    $resp = $func->getProdutosCriança();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutoCriançaMostrar($_POST["id"]);
    echo $resp;
}
?>