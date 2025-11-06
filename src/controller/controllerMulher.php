<?php
require_once '../model/modelMulher.php';

$func = new Mulher();

if ($_POST['op'] == 1) {
    $resp = $func->getProdutosMulher(
        $_POST['marca'],
        $_POST['preco'],
        $_POST['tamanho'],
        $_POST['cor'],
        $_POST['estado'],
        $_POST['material']
         1 //
    );
    echo $resp;
}
?>