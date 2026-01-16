<?php
session_start();
include_once '../model/modelNotificacoes.php';

$func = new Notificacoes();

// Verificar se utilizador está autenticado
if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$user_id = $_SESSION['utilizador'];

// op=1: Obter preferências
if ($_GET['op'] == 1 || $_POST['op'] == 1) {
    $preferencias = $func->getPreferencias($user_id, $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante');

    if ($preferencias) {
        echo json_encode([
            'success' => true,
            'preferencias' => $preferencias
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao obter preferências'
        ]);
    }
}

// op=2: Salvar preferências
if ($_POST['op'] == 2) {
    $preferencias = [];

    // Preferências de Cliente
    if (isset($_POST['email_confirmacao'])) {
        $preferencias['email_confirmacao'] = intval($_POST['email_confirmacao']);
    }
    if (isset($_POST['email_processando'])) {
        $preferencias['email_processando'] = intval($_POST['email_processando']);
    }
    if (isset($_POST['email_enviado'])) {
        $preferencias['email_enviado'] = intval($_POST['email_enviado']);
    }
    if (isset($_POST['email_entregue'])) {
        $preferencias['email_entregue'] = intval($_POST['email_entregue']);
    }
    if (isset($_POST['email_cancelamento'])) {
        $preferencias['email_cancelamento'] = intval($_POST['email_cancelamento']);
    }

    // Preferências de Anunciante
    if (isset($_POST['email_novas_encomendas_anunciante'])) {
        $preferencias['email_novas_encomendas_anunciante'] = intval($_POST['email_novas_encomendas_anunciante']);
    }
    if (isset($_POST['email_encomendas_urgentes'])) {
        $preferencias['email_encomendas_urgentes'] = intval($_POST['email_encomendas_urgentes']);
    }

    $tipo_user = $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante';
    $resultado = $func->atualizarPreferencias($user_id, $tipo_user, $preferencias);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Preferências atualizadas com sucesso!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao atualizar preferências'
        ]);
    }
}

// op=3: Desativar todas
if ($_POST['op'] == 3) {
    $tipo_user = $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante';
    $resultado = $func->desativarTodasNotificacoes($user_id, $tipo_user);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Todas as notificações foram desativadas'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao desativar notificações'
        ]);
    }
}

// op=4: Ativar todas
if ($_POST['op'] == 4) {
    $tipo_user = $_SESSION['tipo'] == 2 ? 'cliente' : 'anunciante';
    $resultado = $func->ativarTodasNotificacoes($user_id, $tipo_user);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Todas as notificações foram ativadas'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao ativar notificações'
        ]);
    }
}
?>
