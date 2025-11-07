<?php
include_once '../model/modelDashboardAnunciante.php';
session_start();

$func = new DashboardAnunciante();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'],$_SESSION['plano']);
    echo $resp;

}
if ($_POST['op'] == 2) {
    $resp = $func->CarregaProdutos($_SESSION['utilizador']);
    echo $resp;

}
if ($_POST['op'] == 3) {
    $resp = $func->CarregaPontos($_SESSION['utilizador']);
    echo $resp;

}
?>