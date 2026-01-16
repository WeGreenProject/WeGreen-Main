<?php
include_once '../model/modelLogin.php';

$func = new Login();

if ($_POST['op'] == 1) {
    $resp = $func->login1($_POST['email'], $_POST['password']);
    echo $resp;

}
?>
