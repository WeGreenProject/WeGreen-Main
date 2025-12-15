<?php
include_once '../model/modelChatAdmin.php';
session_start();

$func = new ChatAdmin();

if ($_POST['op'] == 1) {
    $resp = $func->getSideBar();
    echo $resp;
}
if ($_POST['op'] == 2) {
    $resp = $func->getFaixa($_POST["IdUtilizador"]);
    echo $resp;
}
if ($_POST['op'] == 3) {
    $resp = $func->getFaixa($_POST["IdUtilizador"]);
    echo $resp;
}
if ($_POST['op'] == 4) {
    if (isset($_POST['IdUtilizador']) && $_POST['IdUtilizador'] !== 'undefined' && !empty($_POST['IdUtilizador'])) {
        $resp = $func->getConversas($_POST['IdUtilizador'], $_SESSION['utilizador']);
        echo $resp;
    } else {
        echo '
        <div class="empty-chat-modern">
            <div class="chat-icon-wrapper">
                <div class="chat-icon-bg"></div>
                <div class="chat-icon-main">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            
            <h3 class="empty-title">Nenhuma conversa selecionada</h3>
            <p class="empty-subtitle">
                Escolha uma conversa da lista ao lado para visualizar as mensagens e começar a responder aos seus clientes
            </p>
            
            </div>
        </div>
        ';
    }
}
if ($_POST['op'] == 5) {
    $resp = $func->getBotao($_POST["IdUtilizador"]);
    echo $resp;
}
if ($_POST['op'] == 6) {
    $resp = $func->ConsumidorRes($_SESSION["utilizador"],$_POST["IdUtilizador"],$_POST["mensagem"]);
    echo $resp;
}
if ($_POST['op'] == 7) {
    $resp = $func->pesquisarChat($_POST["pesquisa"]);
    echo $resp;
}
if ($_POST['op'] == 8) {
    $resp = $func->getInativos();
    echo $resp;
}
?>