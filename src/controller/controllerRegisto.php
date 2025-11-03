<?php
include_once '../model/modelLogin.php';

$func = new Registo();

if ($_POST['op'] == 1) {
    $resp = $func->registaUser(
        $_POST['username'], 
        $_POST['email'],
        $_POST['nif'],
        $_POST['foto'],
        $_POST['password'],
        $_FILES['foto']
    );
    echo $resp;
}
?>