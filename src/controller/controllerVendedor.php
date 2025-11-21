<?php
include_once '../model/modelVendedor.php';

$func = new Vendedor();

if ($_POST['op'] == 1) {
    $resp = $func->getPerfilVendedora($_POST["utilizadorID"]);
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutosVendedora($_POST["produto_id"]);
    echo $resp;
}
?>