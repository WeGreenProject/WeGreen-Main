<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelAvaliacoes.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida']);
    exit;
}

$func = new Avaliacoes($conn);

if ($op == 'criarAvaliacao') {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão não iniciada']);
        exit;
    }

    $produto_id = $_POST['produto_id'] ?? null;
    $encomenda_codigo = $_POST['encomenda_codigo'] ?? null;
    $avaliacao = $_POST['avaliacao'] ?? null;
    $comentario = $_POST['comentario'] ?? null;
    $utilizador_id = $_SESSION['utilizador'];

    if (empty($produto_id) || empty($encomenda_codigo) || empty($avaliacao)) {
        echo json_encode(['success' => false, 'message' => 'Dados obrigatórios não fornecidos']);
        exit;
    }

    $resp = $func->criarAvaliacao($produto_id, $utilizador_id, $encomenda_codigo, $avaliacao, $comentario);
    echo json_encode(['success' => (bool)($resp['success'] ?? false), 'message' => $resp['message'] ?? '']);
}

if ($op == 'obterAvaliacoes') {
    $produto_id = $_POST['produto_id'] ?? null;

    if (empty($produto_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido']);
        exit;
    }

    $avaliacoes = $func->obterAvaliacoesProduto($produto_id);
    $estatisticas = $func->obterEstatisticasProduto($produto_id);

    if (!is_array($avaliacoes)) {
        $avaliacoes = [];
    }

    if (!is_array($estatisticas)) {
        $estatisticas = [
            'total' => 0,
            'media' => 0,
            'estrelas_5' => 0,
            'estrelas_4' => 0,
            'estrelas_3' => 0,
            'estrelas_2' => 0,
            'estrelas_1' => 0
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => 'OK',
        'avaliacoes' => $avaliacoes,
        'estatisticas' => $estatisticas
    ], JSON_UNESCAPED_UNICODE);
}

if ($op == 'obterEstatisticas') {
    $produto_id = $_POST['produto_id'] ?? null;

    if (empty($produto_id)) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido']);
        exit;
    }

    $resp = $func->obterEstatisticasProduto($produto_id);
    echo json_encode([
        'success' => true,
        'message' => 'OK',
        'estatisticas' => $resp
    ], JSON_UNESCAPED_UNICODE);
}

if ($op == 'verificarAvaliacao') {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão não iniciada']);
        exit;
    }

    $produto_id = $_POST['produto_id'] ?? null;
    $encomenda_codigo = $_POST['encomenda_codigo'] ?? null;
    $utilizador_id = $_SESSION['utilizador'];

    if (empty($produto_id) || empty($encomenda_codigo)) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
        exit;
    }

    $avaliou = $func->verificarSeAvaliou($produto_id, $utilizador_id, $encomenda_codigo);
    echo json_encode([
        'success' => true,
        'message' => 'OK',
        'avaliou' => $avaliou ? 1 : 0
    ]);
}

if ($op == 'obterProdutosParaAvaliar') {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão não iniciada']);
        exit;
    }

    $encomenda_codigo = $_POST['encomenda_codigo'] ?? null;
    $cliente_id = $_SESSION['utilizador'];

    if (empty($encomenda_codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código da encomenda não fornecido']);
        exit;
    }

    $resp = $func->obterProdutosParaAvaliar($encomenda_codigo, $cliente_id);
    echo json_encode([
        'success' => true,
        'message' => 'OK',
        'produtos' => $resp
    ], JSON_UNESCAPED_UNICODE);
}
?>
