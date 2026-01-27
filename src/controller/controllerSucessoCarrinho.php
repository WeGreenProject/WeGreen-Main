<?php
// Iniciar sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Usar caminho absoluto com __DIR__ para evitar problemas de paths relativos
require_once __DIR__ . '/../model/modelSucessoCarrinho.php';

$func = new SucessoCarrinho();

// Verificar se está autenticado
if(!isset($_SESSION['utilizador'])){
    error_log("ERRO: Utilizador não autenticado ao processar pagamento");
    header('Location: /wegreen-main/login.html');
    exit();
}

if (isset($_GET['session_id'])) {
    $session_id = $_GET['session_id'];
    $utilizador_id = $_SESSION['utilizador'];

    error_log("Controller: Processando session_id: " . $session_id . " para utilizador: " . $utilizador_id);

    $resultado = $func->processarPagamentoStripe($session_id, $utilizador_id);

    if ($resultado && $resultado['sucesso']) {
        error_log("Controller: Pagamento processado com sucesso! Código: " . $resultado['codigo_encomenda']);
        $_SESSION['resultado_pagamento'] = $resultado;
        header('Location: /wegreen-main/sucess_carrinho.php');
        exit();
    } else {
        error_log("Controller: FALHA ao processar pagamento. Redirecionando para carrinho.");
        header('Location: /wegreen-main/Carrinho.html?erro=processamento');
        exit();
    }
}

// Se não houver session_id, redirecionar para carrinho
error_log("Controller: Nenhum session_id recebido");
header('Location: /wegreen-main/Carrinho.html');
exit();
?>
