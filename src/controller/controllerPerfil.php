<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelPerfil.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Perfil($conn);

if ($op == 1) {
    $utilizador_id = $_SESSION['utilizador'] ?? null;
    $tipo = $_SESSION['tipo'] ?? null;
    $resp = $func->getDadosTipoPerfilCompleto($utilizador_id, $tipo);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->logout();
    header('Location: ../../index.html');
    exit;
}

if ($op == 3) {
    if (isset($_SESSION['utilizador']) && isset($_SESSION['tipo'])) {
        $resp = $func->getDadosPlanos($_SESSION['utilizador'], $_SESSION['plano'], $_SESSION['tipo']);
        echo $resp;
    } else {
        echo "";
    }
}

if ($op == 10) {
    if (isset($_SESSION['utilizador'])) {
        $resp = $func->PerfilDoUtilizador($_SESSION['utilizador']);
        echo $resp;
    } else {
        echo "src/img/pexels-beccacorreiaph-31095884.jpg";
    }
}

if ($op == 11) {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão não iniciada'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->getDadosUtilizadorCheckout($_SESSION['utilizador']);
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
}

if ($op == 12) {
    if (isset($_SESSION['utilizador'])) {
        $resp = $func->getDadosPerfilCliente($_SESSION['utilizador']);
        echo $resp;
    } else {
        echo json_encode(['success' => false, 'message' => 'Sessão inválida'], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 13) {
    $utilizador_id = $_SESSION['utilizador'] ?? null;
    $resp = $func->getContactFormCompleto($utilizador_id);
    echo $resp;
}

if ($op == 14) {
    $mensagem = $_POST['mensagemUser'] ?? '';
    $assunto = $_POST['assuntoContato'] ?? null;

    if (isset($_SESSION['utilizador'])) {
        $resp = $func->AdicionarMensagemContacto(
            $_SESSION['utilizador'],
            $mensagem,
            null,
            null,
            $assunto
        );
    } else {
        $resp = $func->AdicionarMensagemContacto(
            null,
            $mensagem,
            $_POST['nomeUser'] ?? null,
            $_POST['emailUser'] ?? null,
            $assunto
        );
    }
    echo $resp;
}

if ($op == 15) {
    $tipoAlvo = isset($_POST['tipoAlvo']) ? (int)$_POST['tipoAlvo'] : 0;

    if (!isset($_SESSION['email']) || !isset($_SESSION['tipo'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão inválida'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!in_array($tipoAlvo, [2, 3], true)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de conta inválido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (empty($_SESSION['perfil_duplo'])) {
        echo json_encode(['success' => false, 'message' => 'Não existem duas contas associadas a este email.'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($tipoAlvo === (int)$_SESSION['tipo']) {
        echo json_encode(['success' => true, 'redirect' => ((int)$_SESSION['tipo'] === 3 ? 'DashboardAnunciante.php' : 'DashboardCliente.php')], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (isset($_SESSION['email'])) {
        $resp = $func->alternarConta($_SESSION['email'], $tipoAlvo);
        echo $resp;
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes'], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 16) {
    if (!isset($_SESSION['utilizador'])) {
        echo json_encode(['success' => false, 'message' => 'Sessão inválida'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->atualizarPerfilClienteComPost($_SESSION['utilizador'], $_POST);
    echo $resp;
}

if ($op == 17) {
    if (isset($_SESSION['utilizador']) && isset($_SESSION['email']) && isset($_SESSION['tipo'])) {
        $resp = $func->verificarContaAlternativa($_SESSION['email'], $_SESSION['tipo']);
        echo $resp;
    } else {
        echo json_encode(['existe' => false], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 'alterarSenha') {
    $utilizador = $_SESSION['utilizador'] ?? null;
    $senhaAtual = $_POST['senhaAtual'] ?? null;
    $novaSenha = $_POST['novaSenha'] ?? null;

    if ($utilizador && $senhaAtual && $novaSenha) {
        $resp = $func->alterarSenha($utilizador, $senhaAtual, $novaSenha);
        echo $resp;
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados insuficientes'], JSON_UNESCAPED_UNICODE);
    }
}
?>
