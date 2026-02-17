<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelNotificacoes.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new Notificacoes($conn);

if ($op == 1) {
    $preferencias = $func->getPreferencias($_SESSION['utilizador'], $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante');

    if ($preferencias) {
        echo json_encode([
            'success' => true,
            'preferencias' => $preferencias
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao obter preferências'
        ], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 2) {
    $tipo_user = $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante';
    $preferencias = [
        'email_encomendas' => isset($_POST['email_encomendas']) ? 1 : 0,
        'email_promocoes' => isset($_POST['email_promocoes']) ? 1 : 0,
        'email_newsletter' => isset($_POST['email_newsletter']) ? 1 : 0,
        'notif_encomendas' => isset($_POST['notif_encomendas']) ? 1 : 0,
        'notif_mensagens' => isset($_POST['notif_mensagens']) ? 1 : 0,
        'notif_promocoes' => isset($_POST['notif_promocoes']) ? 1 : 0
    ];
    $resp = $func->atualizarPreferencias($_SESSION['utilizador'], $tipo_user, $preferencias);

    if ($resp) {
        echo json_encode([
            'success' => true,
            'message' => 'Preferências atualizadas com sucesso!'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar preferências'
        ], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 3) {
    $tipo_user = $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante';
    $resp = $func->desativarTodasNotificacoes($_SESSION['utilizador'], $tipo_user);

    if ($resp) {
        echo json_encode([
            'success' => true,
            'message' => 'Todas as notificações foram desativadas'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao desativar notificações'
        ], JSON_UNESCAPED_UNICODE);
    }
}

if ($op == 4) {
    $tipo_user = $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante';
    $resp = $func->ativarTodasNotificacoes($_SESSION['utilizador'], $tipo_user);

    if ($resp) {
        echo json_encode([
            'success' => true,
            'message' => 'Todas as notificações foram ativadas'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao ativar notificações'
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>
