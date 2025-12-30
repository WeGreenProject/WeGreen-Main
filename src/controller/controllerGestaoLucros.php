<?php
include_once '../model/modelGestaoLucros.php';
session_start();

$func = new Lucros();

if ($_POST['op'] == 1) {
    $resp = $func->getCards();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->GraficoReceita();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->getTransicoes();
    echo $resp;
}
?>