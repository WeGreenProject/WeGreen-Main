<?php
session_start();
include_once '../model/modelDevolucoes.php';

$func = new ModelDevolucoes();

header('Content-Type: application/json');

// op=1: Solicitar devolução
if (isset($_POST['op']) && $_POST['op'] == 1) {
    try {
        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado. Faça login.']);
            exit;
        }

        $encomenda_id = $_POST['encomenda_id'];
        $motivo = $_POST['motivo'];
        $motivo_detalhe = $_POST['motivo_detalhe'] ?? '';
        $notas_cliente = $_POST['notas_cliente'] ?? '';
        $fotos = json_decode($_POST['fotos'] ?? '[]', true);

        if (empty($encomenda_id) || empty($motivo)) {
            echo json_encode([
                'success' => false,
                'message' => 'Dados incompletos.'
            ]);
            exit;
        }

        if ($_SESSION['tipo'] != 2) {
            echo json_encode([
                'success' => false,
                'message' => 'Apenas clientes podem solicitar devoluções.'
            ]);
            exit;
        }

        $cliente_id = $_SESSION['utilizador'];
        $resultado = $func->solicitarDevolucao($encomenda_id, $cliente_id, $motivo, $motivo_detalhe, $notas_cliente, $fotos);
        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=2: Listar devoluções do cliente
if (isset($_POST['op']) && $_POST['op'] == 2) {
    try {
        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }

        if ($_SESSION['tipo'] != 2) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        $cliente_id = $_SESSION['utilizador'];
        $devolucoes = $func->listarDevolucoesPorCliente($cliente_id);
        echo json_encode(['success' => true, 'data' => $devolucoes]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=3: Listar devoluções do anunciante
if ((isset($_POST['op']) && $_POST['op'] == 4) || (isset($_GET['op']) && $_GET['op'] == 4)) {
    try {
        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }

        if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        $anunciante_id = $_SESSION['utilizador'];
        $filtro_estado = $_GET['filtro_estado'] ?? $_POST['filtro_estado'] ?? null;
        $devolucoes = $func->listarDevolucoesPorAnunciante($anunciante_id, $filtro_estado);
        echo json_encode(['success' => true, 'data' => $devolucoes]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=4: Obter detalhes
if ((isset($_POST['op']) && $_POST['op'] == 4) || (isset($_GET['op']) && $_GET['op'] == 4)) {
    try {
        $devolucao_id = $_POST['devolucao_id'] ?? $_GET['devolucao_id'];
        $detalhes = $func->obterDetalhes($devolucao_id);

        if ($detalhes) {
            echo json_encode(['success' => true, 'data' => $detalhes]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Devolução não encontrada.']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=5: Aprovar devolução
if (isset($_POST['op']) && $_POST['op'] == 5) {
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
        $anunciante_id = $_SESSION['utilizador'];
        $notas_anunciante = $_POST['notas_anunciante'] ?? '';

        $resultado = $func->aprovarDevolucao($devolucao_id, $anunciante_id, $notas_anunciante);
        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=6: Rejeitar devolução
if (isset($_POST['op']) && $_POST['op'] == 6) {
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
        $anunciante_id = $_SESSION['utilizador'];
        $notas_anunciante = $_POST['notas_anunciante'];

        if (empty($notas_anunciante)) {
            echo json_encode(['success' => false, 'message' => 'Motivo da rejeição é obrigatório.']);
            exit;
        }

        $resultado = $func->rejeitarDevolucao($devolucao_id, $anunciante_id, $notas_anunciante);
        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=7: Processar reembolso
if (isset($_POST['op']) && $_POST['op'] == 7) {
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
        $resultado = $func->processarReembolso($devolucao_id);
        echo json_encode($resultado);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=8: Verificar elegibilidade
if (isset($_GET['op']) && $_GET['op'] == 8) {
    try {
        // Debug: log da requisição
        error_log("DEBUG op=8: Iniciando verificação de elegibilidade");
        error_log("DEBUG: GET params: " . print_r($_GET, true));
        error_log("DEBUG: SESSION: " . print_r($_SESSION, true));

        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            error_log("DEBUG: Não autenticado");
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }

        if (!isset($_GET['encomenda_id'])) {
            error_log("DEBUG: ID da encomenda não fornecido");
            echo json_encode(['success' => false, 'message' => 'ID da encomenda não fornecido.']);
            exit;
        }

        $encomenda_id = $_GET['encomenda_id'];
        $cliente_id = $_SESSION['utilizador'];

        error_log("DEBUG: encomenda_id=$encomenda_id, cliente_id=$cliente_id");

        $elegibilidade = $func->verificarElegibilidade($encomenda_id, $cliente_id);

        error_log("DEBUG: Resultado: " . print_r($elegibilidade, true));

        echo json_encode(['success' => true, 'data' => $elegibilidade]);

    } catch (Exception $e) {
        error_log("DEBUG: Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// op=9: Upload de foto
if (isset($_POST['op']) && $_POST['op'] == 9) {
    try {
        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }

        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Erro no upload da foto.']);
            exit;
        }

        $file = $_FILES['foto'];
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        if (!in_array($file['type'], $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Formato de imagem inválido. Use JPG, PNG ou GIF.']);
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Imagem muito grande. Máximo 5MB.']);
            exit;
        }

        $upload_dir = __DIR__ . '/../../assets/media/devolucoes/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = uniqid('dev_') . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $url = 'assets/media/devolucoes/' . $filename;
            echo json_encode(['success' => true, 'url' => $url]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar imagem.']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// op=10: Estatísticas
if (isset($_GET['op']) && $_GET['op'] == 10) {
    try {
        if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
            exit;
        }

        if ($_SESSION['tipo'] != 3 && $_SESSION['tipo'] != 1) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        $anunciante_id = $_SESSION['utilizador'];
        $estatisticas = $func->obterEstatisticas($anunciante_id);
        echo json_encode(['success' => true, 'data' => $estatisticas]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
