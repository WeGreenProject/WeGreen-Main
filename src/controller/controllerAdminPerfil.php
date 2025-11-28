<?php
include_once '../model/modelAdminPerfil.php';
session_start();

$func = new PerfilAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosTipoPerfil($_SESSION["utilizador"]);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getDadosTipoPerfilAdminInical($_SESSION["utilizador"]);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getDadosTipoPerfilAdminInfo($_SESSION["utilizador"]);
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->guardaDadosEditProduto(
        $_POST['nomeAdmin'],
        $_POST['emailAdmin'],
        $_POST['NIFadmin'],
        $_POST['telAdmin'],
        $_SESSION["utilizador"]
    );
    echo $resp;
}

if ($_POST['op'] == 6) {
    $resp = $func->adicionarFotoPerfil($_SESSION["utilizador"], $_FILES['foto']);
    echo $resp;
}
 
?>