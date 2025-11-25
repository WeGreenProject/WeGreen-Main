<?php
session_start();
include_once '../model/modelCarrinho.php';

$func = new Carrinho();


if ($_POST['op'] == 1) {
    $resp = $func->getCarrinho($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->getResumoPedido(); 
    echo $resp;
}

if ($_POST['op'] == 3) {
    $produto_id = $_POST['produto_id'];
    $mudanca = $_POST['mudanca'];
    $resp = $func->atualizarQuantidade($produto_id, $mudanca);
    echo $resp;
}

if ($_POST['op'] == 4) {
    $produto_id = $_POST['produto_id'];
    $resp = $func->removerDoCarrinho($produto_id);
    echo $resp;
}

if ($_POST['op'] == 5) {
    $resp = $func->limparCarrinho();
    echo $resp;
}

if ($_POST['op'] == 6) {
    $codigo = $_POST['codigo'];
    if (strtoupper($codigo) == 'WEGREEN10') {
        $_SESSION['cupao_desconto'] = 10;
        echo "sucesso|Cupão aplicado com sucesso! Desconto de 10%.";
    } else {
        echo "erro|Cupão inválido ou expirado.";
    }
}

if ($_POST['op'] == 7) {
    $produto_id = $_POST['produto_id'];
    $resp = $func->adicionarAoCarrinho($produto_id);
    echo $resp;
}

if ($_POST['op'] == 8) {
    unset($_SESSION['cupao_desconto']);
    echo "Cupão removido com sucesso.";
}

if ($_POST['op'] == 9) {
    // Verificar se há produtos no carrinho
    $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : 1;
    
    $sql = "SELECT COUNT(*) as total FROM Carrinho_Itens WHERE utilizador_id = $utilizador_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    echo json_encode(['tem_produtos' => $row['total'] > 0]);
}

?>