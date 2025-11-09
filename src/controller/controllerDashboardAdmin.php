<?php
include_once '../model/modelDashboardAdmin.php';
session_start();

$func = new DashboardAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'],$_SESSION['plano']);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getUtilizadores($_SESSION['utilizador']);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getRendimentos();
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->getGastos();
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getVendasGrafico();
    echo $resp;
}
if ($_POST['op'] == 6) {
    $resp = $func->getTopTipoGrafico();
    echo $resp;
}
if ($_POST['op'] == 7) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}
?>