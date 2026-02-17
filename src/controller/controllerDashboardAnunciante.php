<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../model/modelDashboardAnunciante.php';

if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
    exit;
}

$op = $_POST['op'] ?? $_GET['op'] ?? null;

if (!$op) {
    echo json_encode(['success' => false, 'message' => 'Operação inválida'], JSON_UNESCAPED_UNICODE);
    exit;
}

$func = new DashboardAnunciante($conn);
$handled = false;

if ($op == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'], $_SESSION['plano']);
    echo $resp;
    $handled = true;
}

if ($op == 2) {
    $resp = $func->carregarProdutos($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 3) {
    $resp = $func->carregarPontos($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 4) {
    $resp = $func->getGastos($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 5) {
    $resp = $func->getVendasMensais($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 6) {
    $resp = $func->getTopProdutos($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 7) {
    $resp = $func->getProdutosRecentes($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 8) {
    $resp = $func->getTodosProdutos($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 9) {
    $resp = $func->getEvolucaoVendas($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 10) {
    $resp = $func->getLucroPorProduto($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 11) {
    $resp = $func->getMargemLucro($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 13) {
    $resp = $func->getTiposProdutos();
    echo $resp;
    $handled = true;
}

if ($op == 14) {
    $resp = $func->getLimiteProdutos($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 15) {
    $resp = $func->getProdutoById($_POST['id']);
    echo $resp;
    $handled = true;
}

if ($op == 12) {
    $resp = $func->getLucroTotal($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 16) {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $resp = $func->removerProdutosEmMassa([$id]);
    } else {
        $resp = json_encode(['success' => false, 'message' => 'ID do produto não fornecido'], JSON_UNESCAPED_UNICODE);
    }
    echo $resp;
    $handled = true;
}

if ($op == 17) {
    $id = $_POST['id'] ?? null;
    $nome = $_POST['nome'] ?? null;
    $tipo_produto_id = $_POST['tipo_produto_id'] ?? null;
    $preco = $_POST['preco'] ?? null;
    $stock = $_POST['stock'] ?? 0;
    $marca = $_POST['marca'] ?? '';
    $tamanho = $_POST['tamanho'] ?? '';
    $estado = $_POST['estado'] ?? null;
    $genero = $_POST['genero'] ?? null;
    $descricao = $_POST['descricao'] ?? '';
    $sustentavel = $_POST['sustentavel'] ?? 0;
    $tipo_material = $_POST['tipo_material'] ?? null;

    if (!$id || !$nome || !$tipo_produto_id || !$preco || !$estado || !$genero) {
        echo json_encode(['flag' => false, 'msg' => 'Dados obrigatórios em falta'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    
    $fotos_files = isset($_FILES['foto']) ? $_FILES['foto'] : null;
    $resp = $func->atualizarProduto($id, $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $fotos_files, $sustentavel, $tipo_material);
    echo $resp;
    $handled = true;
}

if ($op == 18) {
    $nome = $_POST['nome'] ?? null;
    $tipo_produto_id = $_POST['tipo_produto_id'] ?? null;
    $preco = $_POST['preco'] ?? null;
    $stock = $_POST['stock'] ?? 0;
    $marca = $_POST['marca'] ?? '';
    $tamanho = $_POST['tamanho'] ?? '';
    $estado = $_POST['estado'] ?? null;
    $genero = $_POST['genero'] ?? null;
    $descricao = $_POST['descricao'] ?? '';
    $anunciante_id = $_SESSION['utilizador'];
    $sustentavel = $_POST['sustentavel'] ?? 0;
    $tipo_material = $_POST['tipo_material'] ?? null;

    if (!$nome || !$tipo_produto_id || !$preco || !$estado || !$genero) {
        echo json_encode(['flag' => false, 'msg' => 'Dados obrigatórios em falta'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    
    if (!isset($_FILES['foto']) || empty($_FILES['foto']['name'][0])) {
        echo json_encode(['flag' => false, 'msg' => 'É necessário adicionar pelo menos uma foto'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    
    $fotos_files = $_FILES['foto'];
    $resp = $func->insertProduto($nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $anunciante_id, $fotos_files, $sustentavel, $tipo_material);
    echo $resp;
    $handled = true;
}

if ($op == 19) {
    $ids = $_POST['ids'];
    $ativo = $_POST['ativo'];
    $resp = $func->atualizarAtivoEmMassa($ids, $ativo);
    echo json_encode(['success' => (bool)$resp, 'message' => $resp ? 'OK' : 'Erro ao atualizar produtos'], JSON_UNESCAPED_UNICODE);
    $handled = true;
}

if ($op == 20) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getReceitaTotal($_SESSION['utilizador'], $periodo);
    echo $resp;
    $handled = true;
}

if ($op == 21) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getTotalPedidos($_SESSION['utilizador'], $periodo);
    echo $resp;
    $handled = true;
}

if ($op == 22) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getTicketMedio($_SESSION['utilizador'], $periodo);
    echo $resp;
    $handled = true;
}

if ($op == 26) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getMargemLucroGeral($_SESSION['utilizador'], $periodo);
    echo $resp;
    $handled = true;
}

if ($op == 23) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getVendasPorCategoria($_SESSION['utilizador'], $periodo);
    echo $resp;
    $handled = true;
}

if ($op == 24) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getReceitaDiaria($_SESSION['utilizador'], $periodo);
    echo $resp;
    $handled = true;
}

if ($op == 25) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getRelatoriosProdutos($_SESSION['utilizador'], $periodo);
    echo $resp;
    $handled = true;
}

if ($op == 27) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 28) {
    $nome = $_POST['nome'] ?? null;
    $email = $_POST['email'] ?? null;
    $telefone = $_POST['telefone'] ?? null;
    $nif = $_POST['nif'] ?? null;
    $morada = $_POST['morada'] ?? null;
    $distrito = $_POST['distrito'] ?? null;
    $localidade = $_POST['localidade'] ?? null;
    $codigo_postal = $_POST['codigo_postal'] ?? null;
    $resp = $func->atualizarPerfil($_SESSION['utilizador'], $nome, $email, $telefone, $nif, $morada, $distrito, $localidade, $codigo_postal);
    echo $resp;
    $handled = true;
}

if ($op == 29) {
    $foto = $_FILES['foto'] ?? null;

    if (!$foto) {
        echo json_encode(['success' => false, 'message' => 'Nenhuma foto foi enviada'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->atualizarFotoPerfil($_SESSION['utilizador'], $foto);
    echo $resp;
    $handled = true;
}

if ($op == 30) {
    $resp = $func->alterarPassword(
        $_SESSION['utilizador'],
        $_POST['senha_atual'],
        $_POST['senha_nova']
    );
    echo $resp;
    $handled = true;
}

if ($op == 31) {
    $resp = $func->getEstatisticasProdutos($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 32) {
    $resp = $func->getEncomendas($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if ($op == 33) {
    $encomenda_id = $_POST['encomenda_id'] ?? null;
    $novo_estado = $_POST['novo_estado'] ?? null;
    $observacao = $_POST['observacao'] ?? '';
    $codigo_rastreio = $_POST['codigo_rastreio'] ?? null;

    if (!$encomenda_id || !$novo_estado) {
        echo json_encode(['success' => false, 'message' => 'Dados obrigatórios não fornecidos'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $resp = $func->atualizarStatusEncomenda($encomenda_id, $novo_estado, $observacao, $codigo_rastreio);
    echo $resp;
    $handled = true;
}

if ($op == 34) {
    $encomenda_id = $_POST['encomenda_id'];
    $resp = $func->getHistoricoEncomenda($encomenda_id);
    echo $resp;
    $handled = true;
}

if ($op == 36) {
    $idsInput = $_POST['ids'] ?? [];

    if (is_array($idsInput)) {
        $ids = array_values(array_filter(array_map('intval', $idsInput), function($id) {
            return $id > 0;
        }));
    } else {
        $idsString = trim((string)$idsInput);
        if ($idsString === '') {
            $ids = [];
        } elseif (strpos($idsString, ',') !== false) {
            $ids = array_values(array_filter(array_map('intval', explode(',', $idsString)), function($id) {
                return $id > 0;
            }));
        } else {
            $singleId = (int)$idsString;
            $ids = $singleId > 0 ? [$singleId] : [];
        }
    }

    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'Nenhum produto válido selecionado'], JSON_UNESCAPED_UNICODE);
        $handled = true;
    } else {
    $resp = $func->removerProdutosEmMassa($ids);
    echo $resp;
    $handled = true;
    }
}

if ($op == 37) {
    $plano_id = $_POST['plano_id'];
    $resp = $func->ativarPlanoPago($_SESSION['utilizador'], $plano_id);
    echo $resp;
    $handled = true;
}

if ($op == 38) {
    $resp = $func->getInfoExpiracaoPlano($_SESSION['utilizador']);
    echo $resp;
    $handled = true;
}

if (!$handled) {
    echo json_encode(['success' => false, 'message' => 'Operação não suportada'], JSON_UNESCAPED_UNICODE);
}
?>
