<?php
session_start();
include_once '../model/modelCarrinho.php';

$func = new Carrinho();


if ($_POST['op'] == 1) {
    $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;
    if ($utilizador_id === null) {
        echo "<div class='empty-cart'><div class='empty-icon'>🛍️</div><h3>O carrinho está vazio</h3><a href='index.html' class='btn btn-primary'>Ir às Compras</a></div>";
    } else {
        $resp = $func->getCarrinho($utilizador_id);
        echo $resp;
    }
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
    $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;
    
    if ($utilizador_id === null) {
        echo json_encode(['tem_produtos' => false]);
    } else {
        global $conn;
        require_once '../model/connection.php';
        
        $sql = "SELECT COUNT(*) as total FROM Carrinho_Itens WHERE utilizador_id = $utilizador_id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        
        echo json_encode(['tem_produtos' => $row['total'] > 0]);
    }
}

?>