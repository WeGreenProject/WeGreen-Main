<?php
include_once '../model/modelRegisto.php';

$func = new Registo();

if ($_POST['op'] == 1) {
    $resp = $func->registaUser(
        $_POST['nome'],
        $_POST['apelido'],
        $_POST['email'],
        $_POST['nif'],
        $_POST['password'],
        $_POST['tipoUtilizador']
    );
    echo $resp;
}
?>
