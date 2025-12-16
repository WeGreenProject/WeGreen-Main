<?php
include_once '../model/modelCheckout.php';
session_start();

$func = new Checkout();

if ($_POST['op'] == 1) {
    $resp = $func->getPlanosComprar($_SESSION["utilizador"],$_SESSION["plano"]);
    echo $resp;
}

if 
?>

