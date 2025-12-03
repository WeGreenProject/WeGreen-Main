<?php
include_once '../model/modelChatAnunciante.php';
session_start();

$func = new ChatAnunciante();

if ($_POST['op'] == 1) {
    $resp = $func->InfoAnunciante();
    echo $resp;
}
?>

