<?php
include_once '../model/modelMulher.php';
session_start();

$func = new Mulher();
if ($_POST['op'] == 1) {
    $resp = $func->getFiltrosMulher();
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->getProdutosMulher();
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getProdutoMulherMostrar($_POST["id"]);
    echo $resp;
}
?>