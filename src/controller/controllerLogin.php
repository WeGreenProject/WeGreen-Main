<?php
// Desativar exibição de erros para evitar HTML indesejado
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

try {
    include_once '../model/modelLogin.php';

    $func = new Login();

    if (isset($_POST['op']) && $_POST['op'] == 1) {
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            echo json_encode([
                'flag' => false,
                'msg' => 'Email e password são obrigatórios'
            ]);
            exit;
        }

        $resp = $func->login1($_POST['email'], $_POST['password']);
        echo $resp;
    } else {
        echo json_encode([
            'flag' => false,
            'msg' => 'Operação inválida'
        ]);
    }
} catch (Exception $e) {
    error_log("Erro no login: " . $e->getMessage());
    echo json_encode([
        'flag' => false,
        'msg' => 'Erro interno no servidor'
    ]);
}
?>
