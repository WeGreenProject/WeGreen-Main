<?php
include_once '../model/modelDevolucoes.php';
session_start();
$func = new ModelDevolucoes();

if (isset($_POST['op']) && $_POST['op'] == 1) {
    if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
        echo json_encode(['success' => false, 'message' => 'Não autenticado']);
        exit;
    }

    if ($_SESSION['tipo'] != 2) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $encomenda_id = $_POST['encomenda_id'];
    $motivo = $_POST['motivo'];
    $motivo_detalhe = $_POST['motivo_detalhe'] ?? '';
    $notas_cliente = $_POST['notas_cliente'] ?? '';
    $fotos = json_decode($_POST['fotos'] ?? '[]', true);
    $produtos_selecionados = json_decode($_POST['produtos_selecionados'] ?? '[]', true);

    $resp = $func->solicitarDevolucao($encomenda_id, $_SESSION['utilizador'], $motivo, $motivo_detalhe, $notas_cliente, $fotos, $produtos_selecionados);
    echo json_encode($resp);
}

if (isset($_POST['op']) && $_POST['op'] == 2) {
    if (!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $resp = $func->listarDevolucoesPorCliente($_SESSION['utilizador']);
    echo json_encode(['success' => true, 'data' => $resp]);
}

if ((isset($_POST['op']) && $_POST['op'] == 3) || (isset($_GET['op']) && $_GET['op'] == 3)) {
    if (!isset($_SESSION['utilizador']) || ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1)) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $filtro_estado = $_GET['filtro_estado'] ?? $_POST['filtro_estado'] ?? null;
    $resp = $func->listarDevolucoesPorAnunciante($_SESSION['utilizador'], $filtro_estado);
    echo json_encode(['success' => true, 'data' => $resp]);
}

if ((isset($_POST['op']) && $_POST['op'] == 4) || (isset($_GET['op']) && $_GET['op'] == 4)) {
    $devolucao_id = $_POST['devolucao_id'] ?? $_GET['devolucao_id'];
    $resp = $func->obterDetalhes($devolucao_id);

    if ($resp) {
        echo json_encode(['success' => true, 'data' => $resp]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Devolução não encontrada']);
    }
}

if (isset($_POST['op']) && $_POST['op'] == 5) {
    if (!isset($_SESSION['utilizador']) || ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1)) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'];
    $notas_anunciante = $_POST['notas_anunciante'] ?? '';
    $resp = $func->aprovarDevolucao($devolucao_id, $_SESSION['utilizador'], $notas_anunciante);
    echo json_encode($resp);
}

if (isset($_POST['op']) && $_POST['op'] == 6) {
    if (!isset($_SESSION['utilizador']) || ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1)) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'];
    $notas_anunciante = $_POST['notas_anunciante'];
    $resp = $func->rejeitarDevolucao($devolucao_id, $_SESSION['utilizador'], $notas_anunciante);
    echo json_encode($resp);
}

if (isset($_POST['op']) && $_POST['op'] == 7) {
    if (!isset($_SESSION['utilizador']) || ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1)) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $devolucao_id = $_POST['devolucao_id'];
    $resp = $func->processarReembolso($devolucao_id);
    echo json_encode($resp);
}

if (isset($_GET['op']) && $_GET['op'] == 8) {
    if (!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $encomenda_id = $_GET['encomenda_id'];
    $resp = $func->verificarElegibilidade($encomenda_id, $_SESSION['utilizador']);
    echo json_encode(['success' => true, 'data' => $resp]);
}

if (isset($_POST['op']) && $_POST['op'] == 9) {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Não autenticado']);
        exit;
    }

    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Erro no upload']);
        exit;
    }

    $file = $_FILES['foto'];
    $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

    if (!in_array($file['type'], $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Formato inválido']);
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Arquivo muito grande']);
        exit;
    }

    $upload_dir = __DIR__ . '/../../assets/media/devolucoes/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = uniqid('dev_') . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode(['success' => true, 'url' => 'assets/media/devolucoes/' . $filename]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao salvar']);
    }
}

// op=10: Obter estatísticas do anunciante
if (isset($_GET['op']) && $_GET['op'] == 10) {
    if (!isset($_SESSION['utilizador']) || ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1)) {
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    $anunciante_id = $_SESSION['utilizador'];
    $estatisticas = $func->obterEstatisticas($anunciante_id);
    echo json_encode(['success' => true, 'data' => $estatisticas]);
}

// op=11: Confirmar envio pelo cliente
if (isset($_POST['op']) && $_POST['op'] == 11) {
    try {
        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }

        if ($_SESSION['tipo'] != 2) {
            echo json_encode(['success' => false, 'message' => 'Apenas clientes podem confirmar envio.']);
            exit;
        }

        $devolucao_id = $_POST['devolucao_id'];
        $codigo_rastreio = $_POST['codigo_rastreio'] ?? '';
        $cliente_id = $_SESSION['utilizador'];

        $resultado = $func->confirmarEnvioCliente($devolucao_id, $cliente_id, $codigo_rastreio);
        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=12: Confirmar recebimento pelo vendedor
if (isset($_POST['op']) && $_POST['op'] == 12) {
    try {
        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }

        if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        $devolucao_id = $_POST['devolucao_id'];
        $notas_recebimento = $_POST['notas_recebimento'] ?? '';
        $anunciante_id = $_SESSION['utilizador'];

        $resultado = $func->confirmarRecebimentoVendedor($devolucao_id, $anunciante_id, $notas_recebimento);
        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>