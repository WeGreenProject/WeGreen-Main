<?php
session_start();
include_once '../model/modelCarrinho.php';

$func = new Carrinho();


if ($_POST['op'] == 1) {
    $resp = $func->getCarrinho($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->getResumoPedido(); 
    echo $resp;
}

if ($_POST['op'] == 3) {
    $produto_id = $_POST['produto_id'];
    $mudanca = $_POST['mudanca'];
    $resp = $func->atualizarQuantidade($produto_id, $mudanca);
    echo $resp;
}

if ($_POST['op'] == 4) {
    $produto_id = $_POST['produto_id'];
    $resp = $func->removerDoCarrinho($produto_id);
    echo $resp;
}

if ($_POST['op'] == 5) {
    $resp = $func->limparCarrinho();
    echo $resp;
}

if ($_POST['op'] == 6) {
    $codigo = $_POST['codigo'];
    if ($codigo == 'WEGREEN10') {
        echo "Cupão aplicado com sucesso! Desconto de 10%.";
    } else {
        echo "Cupão inválido ou expirado.";
    }
}
?>