<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelMarketplace.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Marketplace($conn);

if ($op == 1) {
    $categoria = $_POST["categoria"] ?? null;
    $tipoVendedor = $_POST["tipoVendedor"] ?? null;
    $tipoProduto = $_POST["tipoProduto"] ?? null;
    $marca = $_POST["marca"] ?? null;
    $precoMin = $_POST["precoMin"] ?? null;
    $precoMax = $_POST["precoMax"] ?? null;
    $tamanho = $_POST["tamanho"] ?? null;
    $estado = $_POST["estado"] ?? null;
    $pesquisa = $_POST["pesquisa"] ?? null;
    $ordenacao = $_POST["ordenacao"] ?? 'relevant';
    $limite = $_POST["limite"] ?? null;
    $limite = $limite ? (int)$limite : null;

    $isCliente = isset($_SESSION['tipo']) && $_SESSION['tipo'] == 2;
    $isLoggedIn = isset($_SESSION['utilizador']);
    $clienteId = ($isCliente && $isLoggedIn) ? (int)$_SESSION['utilizador'] : null;

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
        $ordenacao,
        $limite,
        $isCliente,
        $isLoggedIn,
        $clienteId
    );
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getFiltrosDisponiveis();
    echo $resp;
}
?>
