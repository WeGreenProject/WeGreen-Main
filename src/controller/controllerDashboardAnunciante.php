<?php
include_once '../model/modelDashboardAnunciante.php';
session_start();

$func = new DashboardAnunciante();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'],$_SESSION['plano']);
    echo $resp;

}
?>