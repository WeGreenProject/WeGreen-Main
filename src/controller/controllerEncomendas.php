<?php
include_once '../model/modelEncomendas.php';
session_start();

$func = new Encomendas();

// Verificar se utilizador está logado
if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão não iniciada']);
    exit();
}

if (isset($_POST['op']) && $_POST['op'] == 'listarEncomendasCliente') {
    $cliente_id = $_SESSION['utilizador'];
    $resp = $func->listarPorCliente($cliente_id);

    if ($resp) {
        echo json_encode(['success' => true, 'data' => $resp]);
    } else {
        echo json_encode(['success' => true, 'data' => []]);
    }
    exit();
}

if (isset($_POST['op']) && $_POST['op'] == 'detalhesEncomenda') {
    $codigo = $_POST['codigo'];
    $cliente_id = $_SESSION['utilizador'];

    if (empty($codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código inválido']);
        exit();
    }

    $resp = $func->obterDetalhes($codigo, $cliente_id);

    if ($resp) {
        echo json_encode(['success' => true, 'data' => $resp]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Encomenda não encontrada']);
    }
    exit();
}

if (isset($_POST['op']) && $_POST['op'] == 'cancelarEncomenda') {
    $codigo = $_POST['codigo'];
    $cliente_id = $_SESSION['utilizador'];

    if (empty($codigo)) {
        echo json_encode(['success' => false, 'message' => 'Código inválido']);
        exit();
    }

    // Verificar se encomenda pertence ao cliente e está em estado cancelável
    $detalhes = $func->obterDetalhes($codigo, $cliente_id);

    if (!$detalhes) {
        echo json_encode(['success' => false, 'message' => 'Encomenda não encontrada']);
        exit();
    }

    if ($detalhes['estado'] !== 'pendente' && $detalhes['estado'] !== 'processando') {
        echo json_encode(['success' => false, 'message' => 'Encomenda não pode ser cancelada']);
        exit();
    }

    $resp = $func->cancelar($codigo, $cliente_id);

    if ($resp) {
        echo json_encode(['success' => true, 'message' => 'Encomenda cancelada com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao cancelar encomenda']);
    }
    exit();
}

if (isset($_GET['op']) && $_GET['op'] == 'gerarFatura') {
    $codigo = $_GET['codigo'];
    $cliente_id = $_SESSION['utilizador'];

    if (empty($codigo)) {
        die('Código inválido');
    }

    $detalhes = $func->obterDetalhes($codigo, $cliente_id);

    if (!$detalhes) {
        die('Encomenda não encontrada');
    }

    // Gerar PDF simples
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="fatura_'.$codigo.'.pdf"');

    echo "PDF da fatura " . $codigo . " será gerado aqui";
}
?>