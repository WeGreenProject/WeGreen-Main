<?php
/**
 * Controller Notificacoes - Gestão de preferências de notificações
 *
 * Processa requisições AJAX para obter e atualizar preferências
 * de notificações de utilizadores.
 */

session_start();
require_once __DIR__ . '/../model/modelNotificacoes.php';

// Verificar se utilizador está autenticado
if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$modelNotificacoes = new Notificacoes();
$user_id = $_SESSION['utilizador'];

// Determinar tipo de utilizador
$tipo_user = 'cliente'; // default
if (isset($_SESSION['tipo_user'])) {
    if ($_SESSION['tipo_user'] == 3) {
        $tipo_user = 'anunciante';
    } elseif ($_SESSION['tipo_user'] == 1) {
        $tipo_user = 'anunciante'; // Admin pode ser anunciante
    }
}

// Obter operação
$op = $_GET['op'] ?? $_POST['op'] ?? '';

switch ($op) {

    case 'getPreferencias':
        /**
         * Obter preferências do utilizador atual
         */
        $preferencias = $modelNotificacoes->getPreferencias($user_id, $tipo_user);

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
        break;

    case 'salvarPreferencias':
        /**
         * Salvar preferências do utilizador
         * Recebe dados via POST
         */
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

        $resultado = $modelNotificacoes->atualizarPreferencias($user_id, $tipo_user, $preferencias);

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
        break;

    case 'desativarTodas':
        /**
         * Desativar todas as notificações
         */
        $resultado = $modelNotificacoes->desativarTodasNotificacoes($user_id, $tipo_user);

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
        break;

    case 'ativarTodas':
        /**
         * Ativar todas as notificações
         */
        $resultado = $modelNotificacoes->ativarTodasNotificacoes($user_id, $tipo_user);

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
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Operação inválida'
        ]);
        break;
}
?>
