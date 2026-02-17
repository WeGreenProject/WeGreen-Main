<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelDevolucoes.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Devolucoes($conn);

if ($op == 1) {
    if ($_SESSION['tipo'] != 2) {
        echo json_encode(['flag' => false, 'msg' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $encomenda_id = $_POST['encomenda_id'] ?? null;
    $motivo = $_POST['motivo'] ?? null;
    $motivo_detalhe = $_POST['motivo_detalhe'] ?? '';
    $notas_cliente = $_POST['notas_cliente'] ?? '';
    $fotos = json_decode($_POST['fotos'] ?? '[]', true);
    $produtos_selecionados = json_decode($_POST['produtos_selecionados'] ?? '[]', true);

    if (!$encomenda_id || !$motivo) {
        echo json_encode(['flag' => false, 'msg' => 'Dados incompletos'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->solicitarDevolucao($encomenda_id, $_SESSION['utilizador'], $motivo, $motivo_detalhe, $notas_cliente, $fotos, $produtos_selecionados);
    echo $resp;
}

if ($op == 2) {
    if ($_SESSION['tipo'] != 2) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->listarDevolucoesPorCliente($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 3) {
    if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $filtro_estado = $_POST['filtro_estado'] ?? $_GET['filtro_estado'] ?? null;
    $resp = $func->listarDevolucoesPorAnunciante($_SESSION['utilizador'], $filtro_estado);
    echo $resp;
}

if ($op == 4) {
    $devolucao_id = $_POST['devolucao_id'] ?? $_GET['devolucao_id'] ?? null;

    if (!$devolucao_id) {
        echo json_encode(['success' => false, 'message' => 'ID da devolução não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->obterDetalhes($devolucao_id);
    echo $resp;
}

if ($op == 5) {
    if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
        echo json_encode(['flag' => false, 'msg' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'] ?? null;
    $notas_anunciante = $_POST['notas_anunciante'] ?? '';

    if (!$devolucao_id) {
        echo json_encode(['flag' => false, 'msg' => 'ID da devolução não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->aprovarDevolucao($devolucao_id, $_SESSION['utilizador'], $notas_anunciante);
    echo $resp;
}

if ($op == 6) {
    if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
        echo json_encode(['flag' => false, 'msg' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'] ?? null;
    $notas_anunciante = $_POST['notas_anunciante'] ?? null;

    if (!$devolucao_id || !$notas_anunciante) {
        echo json_encode(['flag' => false, 'msg' => 'Dados incompletos'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->rejeitarDevolucao($devolucao_id, $_SESSION['utilizador'], $notas_anunciante);
    echo $resp;
}

if ($op == 7) {
    if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
        echo json_encode(['flag' => false, 'msg' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'] ?? null;

    if (!$devolucao_id) {
        echo json_encode(['flag' => false, 'msg' => 'ID da devolução não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->processarReembolso($devolucao_id);
    echo $resp;
}

if ($op == 8) {
    if ($_SESSION['tipo'] != 2) {
        echo json_encode(['flag' => false, 'msg' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $encomenda_id = $_POST['encomenda_id'] ?? $_GET['encomenda_id'] ?? null;

    if (!$encomenda_id) {
        echo json_encode(['flag' => false, 'msg' => 'ID da encomenda não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->verificarElegibilidade($encomenda_id, $_SESSION['utilizador']);
    echo $resp;
}

if ($op == 9) {
    $foto = $_FILES['foto'] ?? null;

    if (!$foto) {
        echo json_encode(['flag' => false, 'msg' => 'Nenhuma foto foi enviada'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->uploadFotoDevolucao($foto);
    echo $resp;
}

if ($op == 10) {
    if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
        echo json_encode(['flag' => false, 'msg' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $anunciante_id = $_SESSION['utilizador'];
    $resp = $func->obterEstatisticas($anunciante_id);
    echo $resp;
}

if ($op == 11) {
    if ($_SESSION['tipo'] != 2) {
        echo json_encode(['flag' => false, 'msg' => 'Apenas clientes podem confirmar envio'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'] ?? null;
    $codigo_rastreio = $_POST['codigo_rastreio'] ?? '';

    if (!$devolucao_id) {
        echo json_encode(['flag' => false, 'msg' => 'ID da devolução não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->confirmarEnvioCliente($devolucao_id, $_SESSION['utilizador'], $codigo_rastreio);
    echo $resp;
}

if ($op == 12) {
    if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
        echo json_encode(['flag' => false, 'msg' => 'Acesso negado'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'] ?? null;
    $notas_recebimento = $_POST['notas_recebimento'] ?? '';

    if (!$devolucao_id) {
        echo json_encode(['flag' => false, 'msg' => 'ID da devolução não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->confirmarRecebimentoVendedor($devolucao_id, $_SESSION['utilizador'], $notas_recebimento);
    echo $resp;
}
?>
