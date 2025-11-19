<?php
include_once '../model/modelVendedor.php';

$func = new Vendedor();

if ($_POST['op'] == 1) {
    $resp = $func->getPerfilVendedora();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getProdutosVendedora($_POST["produtos.id"]);
    echo $resp;
}
?>
