<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

include_once '../model/modelChatCliente.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new ChatCliente($conn);

if ($op == 1) {
    $resp = $func->getSideBar($_SESSION['utilizador']);
    echo $resp;
}

if ($op == 2) {
    $id_vendedor = $_POST['IdVendedor'] ?? null;

    if ($id_vendedor && $id_vendedor !== 'undefined' && !empty($id_vendedor)) {
        $resp = $func->getConversas($_SESSION['utilizador'], $id_vendedor);
    } else {
        $resp = $func->getMensagemConversaNaoSelecionada();
    }
    echo $resp;
}

if ($op == 3) {
    $anexo = null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'application/octet-stream',
        ];

        $allowedExts = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'txt',
        ];

        $maxSize = 10 * 1024 * 1024;
        $mime = strtolower($_FILES['imagem']['type'] ?? '');
        $ext = strtolower(pathinfo($_FILES['imagem']['name'] ?? '', PATHINFO_EXTENSION));

        if (empty($ext)) {
            $mimeToExt = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'application/vnd.ms-excel' => 'xls',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                'text/plain' => 'txt',
            ];
            if (isset($mimeToExt[$mime])) {
                $ext = $mimeToExt[$mime];
            }
        }

        $isMimeAllowed = in_array($mime, $allowedTypes, true);
        $isExtAllowed = in_array($ext, $allowedExts, true);

        if (($isMimeAllowed || $isExtAllowed) && $_FILES['imagem']['size'] <= $maxSize) {
            $uploadDir = __DIR__ . '/../uploads/chat/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $safeExt = $ext ?: 'bin';
            $nomeArquivo = 'chat_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $safeExt;
            $destino = $uploadDir . $nomeArquivo;

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $anexo = 'src/uploads/chat/' . $nomeArquivo;
            }
        }
    }

    $resp = $func->enviarMensagem($_SESSION['utilizador'], $_POST['IdVendedor'], $_POST['mensagem'], $anexo);
    echo $resp;
}

if ($op == 4) {
    $resp = $func->pesquisarChat($_POST['pesquisa'], $_SESSION['utilizador']);
    echo $resp;
}

if ($op == 5) {
    $resp = $func->getDadosVendedor($_POST['vendedorId']);
    echo $resp;
}
?>
