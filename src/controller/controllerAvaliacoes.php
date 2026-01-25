<?php
// Garantir que não há output antes do JSON
ob_start();

include_once '../model/modelAvaliacoes.php';
session_start();

// Definir header JSON
header('Content-Type: application/json; charset=utf-8');

$func = new Avaliacoes();

// Criar nova avaliação
if (isset($_POST['op']) && $_POST['op'] == 'criarAvaliacao') {
    // Verificar autenticação para criar avaliação
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão não iniciada']);
        exit();
    }

    $produto_id = $_POST['produto_id'] ?? null;
    $encomenda_codigo = $_POST['encomenda_codigo'] ?? null;
    $avaliacao = $_POST['avaliacao'] ?? null;
    $comentario = $_POST['comentario'] ?? null;
    $utilizador_id = $_SESSION['utilizador'];

    if (empty($produto_id) || empty($encomenda_codigo) || empty($avaliacao)) {
        echo json_encode(['success' => false, 'message' => 'Dados obrigatórios não fornecidos']);
        exit();
    }

    $resp = $func->criarAvaliacao($produto_id, $utilizador_id, $encomenda_codigo, $avaliacao, $comentario);
    echo json_encode($resp);
    exit();
}

// Obter avaliações de um produto
if (isset($_POST['op']) && $_POST['op'] == 'obterAvaliacoes') {
    $produto_id = $_POST['produto_id'] ?? null;

    if (empty($produto_id)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido']);
        exit();
    }

    $avaliacoes = $func->obterAvaliacoesProduto($produto_id);
    $estatisticas = $func->obterEstatisticasProduto($produto_id);

    // Limpar qualquer output anterior
    ob_clean();

    echo json_encode([
        'success' => true,
        'avaliacoes' => $avaliacoes,
        'estatisticas' => $estatisticas
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Obter estatísticas de um produto
if (isset($_POST['op']) && $_POST['op'] == 'obterEstatisticas') {
    $produto_id = $_POST['produto_id'] ?? null;

    if (empty($produto_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido']);
        exit();
    }

    $estatisticas = $func->obterEstatisticasProduto($produto_id);
    echo json_encode(['success' => true, 'data' => $estatisticas]);
    exit();
}

// Verificar se já avaliou
if (isset($_POST['op']) && $_POST['op'] == 'verificarAvaliacao') {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão não iniciada']);
        exit();
    }

    $produto_id = $_POST['produto_id'] ?? null;
    $encomenda_codigo = $_POST['encomenda_codigo'] ?? null;
    $utilizador_id = $_SESSION['utilizador'];

    if (empty($produto_id) || empty($encomenda_codigo)) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit();
    }

    $avaliou = $func->verificarSeAvaliou($produto_id, $utilizador_id, $encomenda_codigo);
    echo json_encode(['success' => true, 'avaliou' => $avaliou]);
    exit();
}

// Obter produtos de uma encomenda para avaliar
if (isset($_POST['op']) && $_POST['op'] == 'obterProdutosParaAvaliar') {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão não iniciada']);
        exit();
    }

    $encomenda_codigo = $_POST['encomenda_codigo'] ?? null;
    $cliente_id = $_SESSION['utilizador'];

    if (empty($encomenda_codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código da encomenda não fornecido']);
        exit();
    }

    $produtos = $func->obterProdutosParaAvaliar($encomenda_codigo, $cliente_id);
    echo json_encode(['success' => true, 'produtos' => $produtos]);
    exit();
}

?>