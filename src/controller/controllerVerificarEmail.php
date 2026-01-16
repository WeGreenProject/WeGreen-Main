<?php
/**
 * Controller para verificação de email
 */

// Limpar qualquer output anterior
ob_start();

include_once '../model/modelVerificarEmail.php';

// Limpar buffer e enviar apenas JSON
ob_end_clean();
header('Content-Type: application/json; charset=utf-8');

$verificacao = new VerificarEmail();

// Operação 1: Verificar email com token
if (isset($_POST['op']) && $_POST['op'] == 1) {
    if (empty($_POST['token'])) {
        echo json_encode([
            'flag' => false,
            'msg' => 'Token não fornecido.'
        ]);
        exit;
    }

    $resp = $verificacao->verificarToken($_POST['token']);
    echo $resp;
    exit;
}

// Operação 2: Reenviar email de verificação
if (isset($_POST['op']) && $_POST['op'] == 2) {
    if (empty($_POST['email'])) {
        echo json_encode([
            'flag' => false,
            'msg' => 'Email não fornecido.'
        ]);
        exit;
    }

    $resp = $verificacao->reenviarVerificacao($_POST['email']);
    echo $resp;
    exit;
}

echo json_encode([
    'flag' => false,
    'msg' => 'Operação inválida.'
]);
?>
