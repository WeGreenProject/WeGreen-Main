<?php
include_once '../model/modelDashboard.php';

$func = new Dashboard();

if ($_POST['op'] == 1) {
    $resp = $func->GraficoRendimentos();
    echo $resp;
}
?>