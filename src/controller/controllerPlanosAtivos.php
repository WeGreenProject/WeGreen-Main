<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelPlanosAtivos.php';

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new PlanosAtivos($conn);

if ($op == 'verificar_expirados' || $op == 'desativar_expirados' || $op == 1) {
    $resp = $func->desativarPlanosExpirados();
    echo $resp;
}

if ($op == 'proximos_expiracao' || $op == 2) {
    $resp = $func->verificarPlanosProximosExpiracao();
    echo $resp;
}

if ($op == 'info_plano' || $op == 3) {
    $anunciante_id = $_POST['anunciante_id'] ?? $_GET['anunciante_id'] ?? $_SESSION['utilizador'] ?? null;

    if (!$anunciante_id) {
        echo json_encode(['success' => false, 'message' => 'ID do anunciante não fornecido'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->getPlanoAtivoAnunciante($anunciante_id);
    echo $resp;
}
?>
