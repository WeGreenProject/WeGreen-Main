<?php
session_start();
include_once '../model/modelFavoritos.php';

$func = new Favoritos();

header('Content-Type: application/json');

// Verificar autenticação
if (!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado. Apenas clientes podem gerir favoritos.']);
    exit;
}

$cliente_id = $_SESSION['utilizador'];

// op=1: Adicionar produto aos favoritos
if (isset($_POST['op']) && $_POST['op'] == 1) {
    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.']);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];
    echo $func->adicionarFavorito($cliente_id, $produto_id);
    exit;
}

// op=2: Remover produto dos favoritos
if (isset($_POST['op']) && $_POST['op'] == 2) {
    if (!isset($_POST['produto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.']);
        exit;
    }

    $produto_id = (int)$_POST['produto_id'];
    echo $func->removerFavorito($cliente_id, $produto_id);
    exit;
}

// op=3: Listar todos os favoritos
if (isset($_GET['op']) && $_GET['op'] == 3) {
    echo $func->listarFavoritos($cliente_id);
    exit;
}

// op=4: Verificar se produto está nos favoritos
if (isset($_GET['op']) && $_GET['op'] == 4) {
    if (!isset($_GET['produto_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.']);
        exit;
    }

    $produto_id = (int)$_GET['produto_id'];
    echo $func->verificarFavorito($cliente_id, $produto_id);
    exit;
}

// op=5: Contar favoritos
if (isset($_GET['op']) && $_GET['op'] == 5) {
    echo $func->contarFavoritos($cliente_id);
    exit;
}

// op=6: Limpar favoritos inativos
if (isset($_POST['op']) && $_POST['op'] == 6) {
    echo $func->limparFavoritosInativos($cliente_id);
    exit;
}

// Operação inválida
echo json_encode(['success' => false, 'message' => 'Operação inválida.']);
?>
