<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelCarrinho.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['flag' => false, 'msg' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Carrinho($conn);

if ($op == 1) {
    $utilizador_id = $func->obterOuCriarUtilizadorTemporario();
    $resp = $func->getCarrinho($utilizador_id);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getResumoPedido();
    echo $resp;
}

if ($op == 3) {
    $produto_id = $_POST['produto_id'] ?? null;
    $mudanca = $_POST['mudanca'] ?? null;

    if (!$produto_id || !isset($mudanca)) {
        echo json_encode(['flag' => false, 'msg' => 'Parâmetros inválidos'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->atualizarQuantidade($produto_id, $mudanca);

    if (is_string($resp)) {
        $isErro = stripos($resp, 'Erro') === 0;
        echo json_encode(['flag' => !$isErro, 'msg' => $resp], JSON_UNESCAPED_UNICODE);
    } else {
        echo $resp;
    }
}

if ($op == 4) {
    $produto_id = $_POST['produto_id'] ?? null;

    if (!$produto_id) {
        echo json_encode(['flag' => false, 'msg' => 'ID do produto não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->removerDoCarrinho($produto_id);

    if (is_string($resp)) {
        $isErro = stripos($resp, 'Erro') === 0;
        echo json_encode(['flag' => !$isErro, 'msg' => $resp], JSON_UNESCAPED_UNICODE);
    } else {
        echo $resp;
    }
}

if ($op == 5) {
    $resp = $func->limparCarrinho();

    if (is_string($resp)) {
        $isErro = stripos($resp, 'Erro') === 0;
        echo json_encode(['flag' => !$isErro, 'msg' => $resp], JSON_UNESCAPED_UNICODE);
    } else {
        echo $resp;
    }
}

if ($op == 6) {
    $codigo = $_POST['codigo'] ?? null;

    if (!$codigo) {
        echo json_encode(['flag' => false, 'msg' => 'Código do cupão não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->aplicarCupao($codigo);
    echo $resp;
}

if ($op == 7) {
    
    if (isset($_SESSION['tipo']) && ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3)) {
        echo json_encode(['flag' => false, 'msg' => 'Apenas clientes podem adicionar produtos ao carrinho'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $produto_id = $_POST['produto_id'] ?? null;
    $quantidade = $_POST['quantidade'] ?? 1;
    $quantidade = (int)$quantidade;
    if ($quantidade <= 0) {
        $quantidade = 1;
    }

    if (!$produto_id) {
        echo json_encode(['flag' => false, 'msg' => 'ID do produto não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $utilizador_id = $func->obterOuCriarUtilizadorTemporario();
    $resp = $func->adicionarAoCarrinho($produto_id, $quantidade);

    
    if (is_string($resp) && strpos($resp, 'Erro') !== false) {
        echo json_encode(['flag' => false, 'msg' => $resp], JSON_UNESCAPED_UNICODE);
    } elseif (is_string($resp) && json_decode($resp) === null) {
        echo json_encode(['flag' => true, 'msg' => $resp], JSON_UNESCAPED_UNICODE);
    } else {
        echo $resp;
    }
}

if ($op == 8) {
    $resp = $func->removerCupao();
    echo $resp;
}

if ($op == 9) {
    $utilizador_id = $func->obterOuCriarUtilizadorTemporario();
    $resp = $func->temProdutosNoCarrinho($utilizador_id);
    echo $resp;
}

if ($op == 10) {
    $utilizador_id = $func->obterOuCriarUtilizadorTemporario();
    $resp = $func->getDadosCarrinhoJSON($utilizador_id);
    echo $resp;
}

if ($op == 11) {
    $resp = $func->getDadosUtilizadorCompleto();
    echo $resp;
}

?>
