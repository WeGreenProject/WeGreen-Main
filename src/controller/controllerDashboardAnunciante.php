<?php
include_once '../model/modelDashboardAnunciante.php';
session_start();

$func = new DashboardAnunciante();

// op 1 - Dados do Plano
if ($_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'], $_SESSION['plano']);
    echo $resp;
}

// op 2 - Carrega Produtos
if ($_POST['op'] == 2) {
    $resp = $func->CarregaProdutos($_SESSION['utilizador']);
    echo $resp;
}

// op 3 - Carrega Pontos
if ($_POST['op'] == 3) {
    $resp = $func->CarregaPontos($_SESSION['utilizador']);
    echo $resp;
}

// op 4 - Lucro Total
if ($_POST['op'] == 4) {
        $resp = $func->getLucroTotal($_SESSION['utilizador']);
    echo $resp;
}

// op 5 - Vendas Mensais (JSON para gráfico)
if ($_POST['op'] == 5) {
    $resp = $func->getVendasMensais($_SESSION['utilizador']);
    echo json_encode($resp);
}

// op 6 - Top Produtos (JSON para gráfico)
if ($_POST['op'] == 6) {
    $resp = $func->getTopProdutos($_SESSION['utilizador']);
    echo json_encode($resp);
}

// op 7 - Produtos Recentes (HTML)
if ($_POST['op'] == 7) {
    $resp = $func->getProdutosRecentes($_SESSION['utilizador']);
    echo $resp;
}

// op 8 - Todos os Produtos (HTML para grid)
if ($_POST['op'] == 8) {
    $resp = $func->getTodosProdutos($_SESSION['utilizador']);
    echo $resp;
}

// op 9 - Evolução de Vendas (JSON para gráfico)
if ($_POST['op'] == 9) {
    $resp = $func->getEvolucaoVendas($_SESSION['utilizador']);
    echo json_encode($resp);
}

// op 10 - Lucro por Produto (JSON para gráfico)
if ($_POST['op'] == 10) {
    $resp = $func->getLucroPorProduto($_SESSION['utilizador']);
    echo json_encode($resp);
}

// op 11 - Margem de Lucro (JSON para gráfico)
if ($_POST['op'] == 11) {
    $resp = $func->getMargemLucro($_SESSION['utilizador']);
    echo json_encode($resp);
}
?>