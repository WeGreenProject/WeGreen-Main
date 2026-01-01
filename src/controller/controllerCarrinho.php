<?php
session_start();
include_once '../model/modelCarrinho.php';

$func = new Carrinho();


if ($_POST['op'] == 1) {
    // Criar ID temporário se não estiver logado
    if (!isset($_SESSION['utilizador'])) {
        if (!isset($_SESSION['temp_user_id'])) {
            $_SESSION['temp_user_id'] = 'temp_' . uniqid();
        }
        $_SESSION['utilizador'] = $_SESSION['temp_user_id'];
    }

    $utilizador_id = $_SESSION['utilizador'];
    $resp = $func->getCarrinho($utilizador_id);
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->getResumoPedido();
    echo $resp;
}

if ($_POST['op'] == 3) {
    if (!isset($_POST['produto_id']) || !isset($_POST['mudanca'])) {
        echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
        exit;
    }

    $produto_id = $_POST['produto_id'];
    $mudanca = $_POST['mudanca'];
    $resp = $func->atualizarQuantidade($produto_id, $mudanca);

    // Converter para JSON
    if (strpos($resp, 'Erro') === 0 || strpos($resp, 'Produto não encontrado') === 0) {
        echo json_encode(['success' => false, 'message' => $resp]);
    } else {
        echo json_encode(['success' => true, 'message' => $resp]);
    }
}

if ($_POST['op'] == 4) {
    $produto_id = $_POST['produto_id'];
    $resp = $func->removerDoCarrinho($produto_id);

    // Converter para JSON
    if (strpos($resp, 'Erro') === 0) {
        echo json_encode(['success' => false, 'message' => $resp]);
    } else {
        echo json_encode(['success' => true, 'message' => $resp]);
    }
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

    // Criar ID temporário se não estiver logado
    if (!isset($_SESSION['utilizador'])) {
        if (!isset($_SESSION['temp_user_id'])) {
            $_SESSION['temp_user_id'] = 'temp_' . uniqid();
        }
        $_SESSION['utilizador'] = $_SESSION['temp_user_id'];
    }

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

if ($_POST['op'] == 10) {
    // Get cart data in JSON format for checkout steps
    // Criar ID temporário se não estiver logado
    if (!isset($_SESSION['utilizador'])) {
        if (!isset($_SESSION['temp_user_id'])) {
            $_SESSION['temp_user_id'] = 'temp_' . uniqid();
        }
        $_SESSION['utilizador'] = $_SESSION['temp_user_id'];
    }

    $utilizador_id = $_SESSION['utilizador'];

    if ($utilizador_id === null || $utilizador_id === '') {
        echo json_encode(['produtos' => [], 'total' => 0]);
    } else {
        global $conn;
        require_once '../model/connection.php';

        $sql = "SELECT
                    Produtos.Produto_id,
                    Produtos.nome,
                    Produtos.preco,
                    Produtos.foto,
                    Produtos.marca,
                    Produtos.tamanho,
                    Produtos.estado,
                    Produtos.stock,
                    Carrinho_Itens.quantidade
                FROM Carrinho_Itens
                INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                WHERE Carrinho_Itens.utilizador_id = '$utilizador_id'
                AND Produtos.ativo = 1
                ORDER BY Carrinho_Itens.data_adicao DESC";

        $result = $conn->query($sql);
        $produtos = [];
        $total = 0;

        while ($row = $result->fetch_assoc()) {
            $produtos[] = [
                'id' => $row['Produto_id'],
                'nome' => $row['nome'],
                'preco' => floatval($row['preco']),
                'foto' => $row['foto'],
                'marca' => $row['marca'],
                'tamanho' => $row['tamanho'],
                'estado' => $row['estado'],
                'stock' => intval($row['stock']),
                'quantidade' => intval($row['quantidade'])
            ];
            $total += $row['preco'] * $row['quantidade'];
        }

        echo json_encode(['produtos' => $produtos, 'total' => $total]);
    }
}

if ($_POST['op'] == 11) {
    // Get user data for display
    $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;
    $is_temp = isset($_SESSION['temp_user_id']) && $_SESSION['temp_user_id'] === $utilizador_id;

    if ($utilizador_id === null || $is_temp) {
        // Usuário não logado ou ID temporário
        echo json_encode([
            'success' => false,
            'nome' => 'Visitante',
            'email' => '',
            'foto' => 'assets/media/avatars/blank.png'
        ]);
    } else {
        global $conn;
        require_once '../model/connection.php';

        $sql = "SELECT nome, email, foto FROM Utilizadores WHERE id = $utilizador_id";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'nome' => $row['nome'],
                'email' => $row['email'],
                'foto' => $row['foto'] ? $row['foto'] : 'assets/media/avatars/blank.png'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'nome' => 'Visitante',
                'email' => '',
                'foto' => 'assets/media/avatars/blank.png'
            ]);
        }
    }
}

?>
