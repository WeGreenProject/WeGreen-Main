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
    // Limpar qualquer output anterior ANTES de processar
    ob_clean();

    $produto_id = $_POST['produto_id'] ?? null;

    error_log("=== CONTROLLER AVALIACOES ===");
    error_log("Produto ID recebido: " . $produto_id);

    if (empty($produto_id)) {
        error_log("Erro: Produto ID vazio");
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido']);
        exit();
    }

    try {
        error_log("Chamando obterAvaliacoesProduto...");
        $avaliacoes = $func->obterAvaliacoesProduto($produto_id);
        error_log("Avaliações retornadas: " . count($avaliacoes));

        error_log("Chamando obterEstatisticasProduto...");
        $estatisticas = $func->obterEstatisticasProduto($produto_id);
        error_log("Estatísticas - Total: " . $estatisticas['total'] . ", Média: " . $estatisticas['media']);

        // Garantir que avaliacoes é sempre um array
        if (!is_array($avaliacoes)) {
            error_log("AVISO: avaliacoes não é array, convertendo...");
            $avaliacoes = [];
        }

        // Garantir que estatisticas tem a estrutura correta
        if (!is_array($estatisticas)) {
            error_log("AVISO: estatisticas não é array, usando valores padrão...");
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

        $response = [
            'success' => true,
            'avaliacoes' => $avaliacoes,
            'estatisticas' => $estatisticas
        ];

        error_log("Enviando resposta JSON: " . json_encode($response));
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        error_log("ERRO EXCEPTION: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao buscar avaliações: ' . $e->getMessage()
        ]);
    }
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
