<?php
include_once '../model/modelDashboardAnunciante.php';
session_start();

$func = new DashboardAnunciante();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'], $_SESSION['plano']);
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->CarregaProdutos($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 3) {
    $resp = $func->CarregaPontos($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 4) {
    $resp = $func->getGastos($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 12) {
        $resp = $func->getLucroTotal($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 5) {
    $resp = $func->getVendasMensais($_SESSION['utilizador']);
    echo json_encode($resp);
}

if ($_POST['op'] == 6) {
    $resp = $func->getTopProdutos($_SESSION['utilizador']);
    echo json_encode($resp);
}

if ($_POST['op'] == 7) {
    $resp = $func->getProdutosRecentes($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 8) {
    $resp = $func->getTodosProdutos($_SESSION['utilizador']);
    echo $resp;
}

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

// op 13 - Tipos de Produtos
if ($_POST['op'] == 13) {
    $resp = $func->getTiposProdutos();
    echo $resp;
}

// op 15 - Buscar Produto por ID
if ($_POST['op'] == 15) {
    $resp = $func->getProdutoById($_POST['id']);
    echo $resp;
}

// op 16 - Deletar Produto
if ($_POST['op'] == 16) {
    $resp = $func->deleteProduto($_POST['id']);
    echo $resp;
}

// op 17 - Ativar/Desativar múltiplos produtos
if ($_POST['op'] == 17) {
    $ids = $_POST['ids'];
    $ativo = $_POST['ativo'];
    $resp = $func->atualizarAtivoEmMassa($ids, $ativo);
    echo json_encode(['success' => $resp]);
}

// op 18 - Remover múltiplos produtos
if ($_POST['op'] == 18) {
    $ids = $_POST['ids'];
    $resp = $func->removerProdutosEmMassa($ids);
    echo json_encode(['success' => $resp]);
}

// op 19 - Alterar estado de múltiplos produtos
if ($_POST['op'] == 19) {
    $ids = $_POST['ids'];
    $estado = $_POST['estado'];
    $resp = $func->alterarEstadoEmMassa($ids, $estado);
    echo json_encode(['success' => $resp]);
}

// op 20 - Receita Total
if ($_POST['op'] == 20) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getReceitaTotal($_SESSION['utilizador'], $periodo);
    echo $resp;
}

// op 21 - Total de Pedidos
if ($_POST['op'] == 21) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getTotalPedidos($_SESSION['utilizador'], $periodo);
    echo $resp;
}

// op 22 - Ticket Médio
if ($_POST['op'] == 22) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getTicketMedio($_SESSION['utilizador'], $periodo);
    echo $resp;
}

// op 23 - Vendas por Categoria
if ($_POST['op'] == 23) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getVendasPorCategoria($_SESSION['utilizador'], $periodo);
    echo json_encode($resp);
}

// op 24 - Receita Diária
if ($_POST['op'] == 24) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getReceitaDiaria($_SESSION['utilizador'], $periodo);
    echo json_encode($resp);
}

// op 25 - Relatórios de Produtos
if ($_POST['op'] == 25) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getRelatoriosProdutos($_SESSION['utilizador'], $periodo);
    echo json_encode($resp);
}

// ========================
// OPERAÇÕES DE PERFIL
// ========================

// op 27 - Obter dados do perfil
if ($_POST['op'] == 27) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}

// op 28 - Atualizar dados de perfil
if ($_POST['op'] == 28) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $nif = isset($_POST['nif']) ? $_POST['nif'] : null;
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : null;
    $morada = isset($_POST['morada']) ? $_POST['morada'] : null;
    echo $func->atualizarPerfil($_SESSION['utilizador'], $nome, $email, $telefone, $nif, $morada);
}

// op 29 - Atualizar foto de perfil
if ($_POST['op'] == 29) {
    if (isset($_FILES['foto'])) {
        $resp = $func->atualizarFotoPerfil($_SESSION['utilizador'], $_FILES['foto']);
        echo $resp;
    }
}

// op 30 - Alterar password
if ($_POST['op'] == 30) {
    $resp = $func->alterarPassword(
        $_SESSION['utilizador'],
        $_POST['senha_atual'],
        $_POST['senha_nova']
    );
    echo $resp;
}
?>
