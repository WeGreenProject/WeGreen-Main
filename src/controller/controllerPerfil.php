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
    if (isset($_SESSION['email']) && isset($_POST['tipoAlvo'])) {
        $resp = $func->alternarConta($_SESSION['email'], $_POST['tipoAlvo']);
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
