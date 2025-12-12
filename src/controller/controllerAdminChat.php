<?php
include_once '../model/modelChatAdmin.php';
session_start();

$func = new ChatAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getConversas();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getFaixa();
    echo $resp;
}
?>