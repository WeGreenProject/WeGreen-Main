<?php
include_once '../model/modelDashboardAdmin.php';
session_start();

$func = new DashboardAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'],$_SESSION['plano']);
    echo $resp;

}
?>