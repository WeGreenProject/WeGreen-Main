<?php

include_once '../model/modelPerfil.php';

session_start();

$func = new Perfil();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosTipoPerfil($_SESSION['cliente_id'],$_SESSION['tpUser']);
    echo $resp;
}
?>