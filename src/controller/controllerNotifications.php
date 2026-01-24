<?php
session_start();
require_once('../model/modelNotifications.php');

header('Content-Type: application/json');

// Log de debug
error_log("[Notifications] Request recebido: op=" . ($_GET['op'] ?? 'none'));
error_log("[Notifications] Session user: " . ($_SESSION['utilizador'] ?? 'none'));
error_log("[Notifications] Session tipo: " . ($_SESSION['tipo'] ?? 'none'));

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
    error_log("[Notifications] Erro: Não autenticado");
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit;
}

$modelNotifications = new ModelNotifications();
$utilizador_id = $_SESSION['utilizador'];
$tipo_utilizador = $_SESSION['tipo'];

// op=1: Contar notificações não lidas (todos os tipos)
if (isset($_GET['op']) && $_GET['op'] == 1) {
    try {
        $count = 0;

        switch($tipo_utilizador) {
            case 1: // Admin
                $count = $modelNotifications->contarNotificacoesAdmin();
                error_log("[Notifications] Admin count: $count");
                break;
            case 2: // Cliente
                $count = $modelNotifications->contarNotificacoesCliente($utilizador_id);
                error_log("[Notifications] Cliente count: $count");
                break;
            case 3: // Anunciante
                $count = $modelNotifications->contarNotificacoesAnunciante($utilizador_id);
                error_log("[Notifications] Anunciante count: $count");
                break;
        }

        echo json_encode(['success' => true, 'count' => $count]);

    } catch (Exception $e) {
        error_log("[Notifications] Erro ao contar: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// op=2: Listar notificações detalhadas (todos os tipos)
if (isset($_GET['op']) && $_GET['op'] == 2) {
    try {
        $notificacoes = [];

        switch($tipo_utilizador) {
            case 1: // Admin
                $notificacoes = $modelNotifications->listarNotificacoesAdmin();
                error_log("[Notifications] Admin notificações: " . count($notificacoes));
                break;
            case 2: // Cliente
                $notificacoes = $modelNotifications->listarNotificacoesCliente($utilizador_id);
                error_log("[Notifications] Cliente notificações: " . count($notificacoes));
                break;
            case 3: // Anunciante
                $notificacoes = $modelNotifications->listarNotificacoesAnunciante($utilizador_id);
                error_log("[Notifications] Anunciante notificações: " . count($notificacoes));
                break;
        }

        error_log("[Notifications] Enviando resposta com " . count($notificacoes) . " notificações");
        echo json_encode(['success' => true, 'data' => $notificacoes]);

    } catch (Exception $e) {
        error_log("[Notifications] Erro ao listar: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// op=3: Marcar notificação como lida
if (isset($_POST['op']) && $_POST['op'] == 3) {
    try {
        $tipo_notificacao = $_POST['tipo'] ?? '';
        $referencia_id = $_POST['id'] ?? 0;

        if ($referencia_id > 0 && !empty($tipo_notificacao)) {
            $resultado = $modelNotifications->marcarComoLida($utilizador_id, $tipo_notificacao, $referencia_id);
            echo json_encode(['success' => $resultado]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
        }

    } catch (Exception $e) {
        error_log("[Notifications] Erro ao marcar como lida: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// op=4: Marcar todas como lidas
if (isset($_POST['op']) && $_POST['op'] == 4) {
    try {
        $resultado = $modelNotifications->marcarTodasComoLidas($utilizador_id, $tipo_utilizador);
        echo json_encode(['success' => $resultado]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// op=5: Listar TODAS as notificações (lidas + não lidas) para página de histórico
if (isset($_GET['op']) && $_GET['op'] == 5) {
    try {
        $notificacoes = [];

        switch($tipo_utilizador) {
            case 1: // Admin
                $notificacoes = $modelNotifications->listarTodasNotificacoesAdmin();
                break;
            case 2: // Cliente
                $notificacoes = $modelNotifications->listarTodasNotificacoesCliente($utilizador_id);
                break;
            case 3: // Anunciante
                $notificacoes = $modelNotifications->listarTodasNotificacoesAnunciante($utilizador_id);
                break;
        }

        echo json_encode(['success' => true, 'data' => $notificacoes]);

    } catch (Exception $e) {
        error_log("[Notifications] Erro ao listar todas: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>
