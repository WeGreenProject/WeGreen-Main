<?php
include_once '../model/modelFornecedor.php';
session_start();

$func = new Fornecedor();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getFornecedores();
    echo $resp;
}
?>