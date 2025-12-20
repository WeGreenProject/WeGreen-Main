<?php
include_once '../model/modelClientesAdmin.php';
session_start();

$func = new ClienteAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getClientes($_SESSION["utilizador"]);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getCardUtilizadores();
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->registaClientes($_POST["clientNome"],$_POST["clientEmail"],$_POST["clientTelefone"],$_POST["clientTipo"],$_POST["clientNif"],$_POST["clientPassword"],$_FILES['foto']);
    echo $resp;
}
if ($_POST['op'] == 4) {
    $resp = $func->removerClientes($_POST["ID_Cliente"]);
    echo $resp;
}
if ($_POST['op'] == 5) {
    $resp = $func->getDadosCliente($_POST["id"]);
    echo $resp;
}
if ($_POST['op'] == 6) {
    $resp = $func->guardaEditCliente(        
        $_POST["viewNome"],
        $_POST["viewEmail"],
        $_POST["viewTelefone"],
        $_POST["viewTipo"],
        $_POST["viewNif"],
        $_POST["viewPlano"],
        $_POST["viewRanking"],
        $_POST["ID_Utilizador"]);
    echo $resp;
}
?>