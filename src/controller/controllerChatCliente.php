<?php
include_once __DIR__ . '/../model/modelChatCliente.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$func = new ChatCliente();

// op=1: Listar vendedores/anunciantes com quem o cliente tem conversas
if ($_POST['op'] == 1) {
    $resp = $func->getSideBar($_SESSION['utilizador']);
    echo $resp;
}

// op=2: Buscar mensagens com vendedor específico
if ($_POST['op'] == 2) {
    if (isset($_POST['IdVendedor']) && !empty($_POST['IdVendedor'])) {
        $resp = $func->getConversas($_SESSION['utilizador'], $_POST['IdVendedor']);
        echo $resp;
    } else {
        echo '
        <div class="empty-chat">
            <i class="fas fa-comments" style="font-size: 64px; color: #e0e0e0; margin-bottom: 16px;"></i>
            <h3>Nenhuma conversa selecionada</h3>
            <p>Escolha um vendedor à esquerda para começar</p>
        </div>
        ';
    }
}

// op=3: Enviar mensagem
if ($_POST['op'] == 3) {
    $resp = $func->enviarMensagem($_SESSION['utilizador'], $_POST['IdVendedor'], $_POST['mensagem']);
    echo $resp;
}

// op=4: Pesquisar vendedores
if ($_POST['op'] == 4) {
    $resp = $func->pesquisarChat($_POST['pesquisa'], $_SESSION['utilizador']);
    echo $resp;
}
?>
