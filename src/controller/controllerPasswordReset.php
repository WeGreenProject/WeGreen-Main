<?php
/**
 * Controller para gestão de recuperação de password
 * Sistema WeGreen Marketplace
 */

include_once '../model/modelPasswordReset.php';

header('Content-Type: application/json');

$passwordReset = new PasswordReset();

// Operação 1: Solicitar recuperação de password
if (isset($_POST['op']) && $_POST['op'] == 1) {
    if (empty($_POST['email'])) {
        echo json_encode([
            'flag' => false,
            'msg' => 'Por favor, insira o seu email.'
        ]);
        exit;
    }
    
    $resp = $passwordReset->solicitarRecuperacao($_POST['email']);
    echo json_encode($resp);
    exit;
}

// Operação 2: Validar token
if (isset($_POST['op']) && $_POST['op'] == 2) {
    if (empty($_POST['token'])) {
        echo json_encode([
            'flag' => false,
            'msg' => 'Token não fornecido.'
        ]);
        exit;
    }
    
    $resp = $passwordReset->validarToken($_POST['token']);
    echo json_encode($resp);
    exit;
}

// Operação 3: Redefinir password
if (isset($_POST['op']) && $_POST['op'] == 3) {
    if (empty($_POST['token']) || empty($_POST['nova_password'])) {
        echo json_encode([
            'flag' => false,
            'msg' => 'Token ou password não fornecidos.'
        ]);
        exit;
    }
    
    // Validar força da password
    if (strlen($_POST['nova_password']) < 6) {
        echo json_encode([
            'flag' => false,
            'msg' => 'A password deve ter pelo menos 6 caracteres.'
        ]);
        exit;
    }
    
    $resp = $passwordReset->redefinirPassword($_POST['token'], $_POST['nova_password']);
    echo json_encode($resp);
    exit;
}

// Operação inválida
echo json_encode([
    'flag' => false,
    'msg' => 'Operação inválida.'
]);
?>
