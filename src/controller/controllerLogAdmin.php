<?php
include_once '../model/modelLogAdmin.php';
session_start();

$func = new LogAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getCardLog();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getTabelaLog();
    echo $resp;
}
?>