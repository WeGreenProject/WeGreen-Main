<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../model/modelMarketplace.php';
session_start();

$func = new Marketplace();

// op 1 - Obter todos os produtos com filtros
if (isset($_POST['op']) && $_POST['op'] == 1) {
    $categoria = isset($_POST["categoria"]) ? $_POST["categoria"] : null;
    $tipoVendedor = isset($_POST["tipoVendedor"]) ? $_POST["tipoVendedor"] : null;
    $tipoProduto = isset($_POST["tipoProduto"]) ? $_POST["tipoProduto"] : null;
    $marca = isset($_POST["marca"]) ? $_POST["marca"] : null;
    $precoMin = isset($_POST["precoMin"]) ? $_POST["precoMin"] : null;
    $precoMax = isset($_POST["precoMax"]) ? $_POST["precoMax"] : null;
    $tamanho = isset($_POST["tamanho"]) ? $_POST["tamanho"] : null;
    $estado = isset($_POST["estado"]) ? $_POST["estado"] : null;
    $pesquisa = isset($_POST["pesquisa"]) ? $_POST["pesquisa"] : null;
    $ordenacao = isset($_POST["ordenacao"]) ? $_POST["ordenacao"] : 'relevant';

    $resp = $func->getProdutos(
        $categoria,
        $tipoVendedor,
        $tipoProduto,
        $marca,
        $precoMin,
        $precoMax,
        $tamanho,
        $estado,
        $pesquisa,
        $ordenacao
    );
    echo $resp;
}

// op 2 - Obter filtros disponíveis
if (isset($_POST['op']) && $_POST['op'] == 2) {
    $resp = $func->getFiltrosDisponiveis();
    echo $resp;
}
?>
