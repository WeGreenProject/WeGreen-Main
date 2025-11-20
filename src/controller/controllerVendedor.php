<?php
include_once '../model/modelVendedor.php';

$func = new Vendedor();
$utilizador_id = 1;

if ($_POST['op'] == 1) {
    echo $func->getProdutosVendedora($utilizador_id);
}

if ($_POST['op'] == 2) {
    echo $func->getPerfilVendedora($utilizador_id);
}
?>
