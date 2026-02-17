<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelChatAdmin.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new ChatAdmin($conn);

if ($op == 1) {
    $resp = $func->getSideBar($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 2) {
    $resp = $func->getFaixa($_POST["IdUtilizador"]);
    echo $resp;
}

if ($op == 3) {
    $resp = $func->getFaixa($_POST["IdUtilizador"]);
    echo $resp;
}

if ($op == 4) {
    $id_utilizador = $_POST['IdUtilizador'] ?? null;

    if ($id_utilizador && $id_utilizador !== 'undefined' && !empty($id_utilizador)) {
        $resp = $func->getConversas($id_utilizador, $_SESSION['utilizador']);
    } else {
        $resp = $func->getMensagemConversaNaoSelecionada();
    }
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getBotao($_POST["IdUtilizador"]);
    echo $resp;
}

if ($op == 6) {
    $anexo = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg','image/jpg','image/png','image/gif','image/webp','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','text/plain'];
        $maxSize = 10 * 1024 * 1024; 
        if (in_array($_FILES['imagem']['type'], $allowedTypes) && $_FILES['imagem']['size'] <= $maxSize) {
            $uploadDir = __DIR__ . '/../uploads/chat/';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0755, true); }
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $nomeArquivo = 'chat_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $destino = $uploadDir . $nomeArquivo;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $anexo = 'src/uploads/chat/' . $nomeArquivo;
            }
        }
    }
    $resp = $func->ConsumidorRes($_SESSION["utilizador"], $_POST["IdUtilizador"], $_POST["mensagem"], $anexo);
    echo $resp;
}

if ($op == 7) {
    $resp = $func->pesquisarChat($_POST["pesquisa"], $_SESSION['utilizador']);
    echo $resp;
}

if ($op == 8) {
    
    
    $resp = json_encode(['success' => false, 'message' => 'Função não implementada'], JSON_UNESCAPED_UNICODE);
    echo $resp;
}
?>
