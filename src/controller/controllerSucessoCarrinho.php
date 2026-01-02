<?php
include_once '../model/modelSucessoCarrinho.php';
session_start();

$func = new SucessoCarrinho();

// Verificar se está autenticado
if(!isset($_SESSION['utilizador'])){
    header('Location: ../../login.html');
    exit();
}

if (isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];
    $utilizador_id = $_SESSION['utilizador'];

    $resultado = $func->processarPagamentoStripe($session_id, $utilizador_id);

    if ($resultado && $resultado['sucesso']) {
        $_SESSION['resultado_pagamento'] = $resultado;
        header('Location: ../../sucess_carrinho.php');
        exit();
    } else {
        header('Location: ../../Carrinho.html?erro=processamento');
        exit();
    }
}

// Se não houver session_id, redirecionar para carrinho
header('Location: ../../Carrinho.html');
exit();
?>
