<?php
header('Content-Type: application/json; charset=utf-8');
mb_internal_encoding('UTF-8');
include_once '../model/modelProduto.php';
session_start();

$func = new Produto();

// op 1 - Buscar dados do produto
if ($_POST['op'] == 1) {
    if(isset($_POST['id'])) {
        $resp = $func->getDadosProduto($_POST['id']);
        echo $resp;
    } else {
        echo json_encode(['error' => 'ID do produto não fornecido']);
    }
}

// op 2 - Buscar produtos relacionados
if ($_POST['op'] == 2) {
    if(isset($_POST['id'])) {
        $limite = isset($_POST['limite']) ? $_POST['limite'] : 4;
        $resp = $func->getProdutosRelacionados($_POST['id'], null, $limite);
        echo $resp;
    } else {
        echo json_encode([]);
    }
}
?>
