<?php
include_once '../model/modelLogin.php';

$func = new Login();

if ($_POST['op'] == 1) {
    $resp = $func->login1($_POST['username'], $_POST['password']);
    echo $resp;

}
?>