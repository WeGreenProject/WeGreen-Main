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
?>