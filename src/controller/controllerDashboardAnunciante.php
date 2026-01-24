<?php
// Desabilitar exibição de erros para evitar quebrar JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../error.log');

include_once '../model/modelDashboardAnunciante.php';
session_start();

// Validações de segurança
if (!isset($_SESSION['utilizador'])) {
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

if (!isset($_POST['op'])) {
    echo json_encode(['error' => 'Operação inválida']);
    exit;
}

$func = new DashboardAnunciante();

if ($_POST['op'] == 1) {
    $resp = $func->getDadosPlanos($_SESSION['utilizador'], $_SESSION['plano']);
    echo $resp;
}

if ($_POST['op'] == 2) {
    $resp = $func->carregarProdutos($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 3) {
    $resp = $func->carregarPontos($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 4) {
    $resp = $func->getGastos($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 12) {
        $resp = $func->getLucroTotal($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 5) {
    $resp = $func->getVendasMensais($_SESSION['utilizador']);
    echo json_encode($resp);
}

if ($_POST['op'] == 6) {
    $resp = $func->getTopProdutos($_SESSION['utilizador']);
    echo json_encode($resp);
}

if ($_POST['op'] == 7) {
    $resp = $func->getProdutosRecentes($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 8) {
    $resp = $func->getTodosProdutos($_SESSION['utilizador']);
    echo $resp;
}

if ($_POST['op'] == 9) {
    $resp = $func->getEvolucaoVendas($_SESSION['utilizador']);
    echo json_encode($resp);
}

// op 10 - Lucro por Produto (JSON para gráfico)
if ($_POST['op'] == 10) {
    $resp = $func->getLucroPorProduto($_SESSION['utilizador']);
    echo json_encode($resp);
}

// op 11 - Margem de Lucro (JSON para gráfico)
if ($_POST['op'] == 11) {
    $resp = $func->getMargemLucro($_SESSION['utilizador']);
    echo json_encode($resp);
}

// op 13 - Tipos de Produtos
if ($_POST['op'] == 13) {
    $resp = $func->getTiposProdutos();
    echo $resp;
}

// op 14 - Limite de Produtos
if ($_POST['op'] == 14) {
    $resp = $func->getLimiteProdutos($_SESSION['utilizador']);
    echo $resp;
}

// op 15 - Buscar Produto por ID
if ($_POST['op'] == 15) {
    $resp = $func->getProdutoById($_POST['id']);
    echo $resp;
}

// op 16 - Deletar Produto
if ($_POST['op'] == 16) {
    $resp = $func->deleteProduto($_POST['id']);
    echo $resp;
}

// op 17 - Atualizar Produto (Editar)
if ($_POST['op'] == 17) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $tipo_produto_id = $_POST['tipo_produto_id'];
    $preco = $_POST['preco'];
    $stock = $_POST['stock'] ?? 0;
    $marca = $_POST['marca'] ?? '';
    $tamanho = $_POST['tamanho'] ?? '';
    $estado = $_POST['estado'];
    $genero = $_POST['genero'];
    $descricao = $_POST['descricao'] ?? '';

    // Processar fotos se existirem
    $fotos = [];
    if (isset($_FILES['foto']) && !empty($_FILES['foto']['name'][0])) {
        foreach ($_FILES['foto']['name'] as $key => $filename) {
            if ($_FILES['foto']['error'][$key] === 0) {
                $tmpName = $_FILES['foto']['tmp_name'][$key];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $newFilename = uniqid() . '_' . time() . '.' . $extension;
                $uploadPath = __DIR__ . '/../img/' . $newFilename;

                if (move_uploaded_file($tmpName, $uploadPath)) {
                    $fotos[] = 'src/img/' . $newFilename;
                }
            }
        }
    }

    $resp = $func->atualizarProduto($id, $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $fotos);
    echo json_encode(['success' => true, 'message' => $resp]);
}

// op 18 - Adicionar Produto (Novo)
if ($_POST['op'] == 18) {
    try {
        $nome = $_POST['nome'] ?? '';
        $tipo_produto_id = $_POST['tipo_produto_id'] ?? 0;
        $preco = $_POST['preco'] ?? 0;
        $stock = $_POST['stock'] ?? 0;
        $marca = $_POST['marca'] ?? '';
        $tamanho = $_POST['tamanho'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $genero = $_POST['genero'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $anunciante_id = $_SESSION['utilizador'];

        // Processar upload de fotos
        $fotos = [];
        if (isset($_FILES['foto']) && !empty($_FILES['foto']['name'][0])) {
            $uploadDir = __DIR__ . '/../img/';

            // Verificar se o diretório existe
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['foto']['name'] as $key => $filename) {
                if ($_FILES['foto']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['foto']['tmp_name'][$key];
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($extension, $allowedExt)) {
                        $newFilename = uniqid() . '_' . time() . '.' . $extension;
                        $uploadPath = $uploadDir . $newFilename;

                        if (move_uploaded_file($tmpName, $uploadPath)) {
                            $fotos[] = 'src/img/' . $newFilename;
                        }
                    }
                }
            }
        }

        // Verificar se tem pelo menos uma foto
        if (empty($fotos)) {
            echo json_encode(['success' => false, 'message' => 'É necessário adicionar pelo menos uma foto']);
            exit;
        }

        $resp = $func->insertProduto($nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $anunciante_id, $fotos);
        echo json_encode(['success' => true, 'message' => $resp]);
    } catch (Exception $e) {
        error_log('Erro ao adicionar produto: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erro ao adicionar produto: ' . $e->getMessage()]);
    }
}

// op 19 - Ativar/Desativar múltiplos produtos
if ($_POST['op'] == 19) {
    $ids = $_POST['ids'];
    $ativo = $_POST['ativo'];
    $resp = $func->atualizarAtivoEmMassa($ids, $ativo);
    echo json_encode(['success' => $resp]);
}

// op 20 - Receita Total
if ($_POST['op'] == 20) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getReceitaTotal($_SESSION['utilizador'], $periodo);
    echo $resp;
}

// op 21 - Total de Pedidos
if ($_POST['op'] == 21) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getTotalPedidos($_SESSION['utilizador'], $periodo);
    echo $resp;
}

// op 22 - Ticket Médio
if ($_POST['op'] == 22) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getTicketMedio($_SESSION['utilizador'], $periodo);
    echo $resp;
}

// op 23 - Vendas por Categoria
if ($_POST['op'] == 23) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getVendasPorCategoria($_SESSION['utilizador'], $periodo);
    echo json_encode($resp);
}

// op 24 - Receita Diária
if ($_POST['op'] == 24) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getReceitaDiaria($_SESSION['utilizador'], $periodo);
    echo json_encode($resp);
}

// op 25 - Relatórios de Produtos
if ($_POST['op'] == 25) {
    $periodo = $_POST['periodo'] ?? 'all';
    $resp = $func->getRelatoriosProdutos($_SESSION['utilizador'], $periodo);
    echo json_encode($resp);
}

// op 27 - Obter dados do perfil
if ($_POST['op'] == 27) {
    $resp = $func->getDadosPerfil($_SESSION['utilizador']);
    echo $resp;
}

// op 28 - Atualizar dados de perfil
if ($_POST['op'] == 28) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $nif = isset($_POST['nif']) ? $_POST['nif'] : null;
    $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : null;
    $morada = isset($_POST['morada']) ? $_POST['morada'] : null;
    echo $func->atualizarPerfil($_SESSION['utilizador'], $nome, $email, $telefone, $nif, $morada);
}

// op 29 - Atualizar foto de perfil
if ($_POST['op'] == 29) {
    if (isset($_FILES['foto'])) {
        $resp = $func->atualizarFotoPerfil($_SESSION['utilizador'], $_FILES['foto']);
        echo $resp;
    }
}

// op 30 - Alterar password
if ($_POST['op'] == 30) {
    $resp = $func->alterarPassword(
        $_SESSION['utilizador'],
        $_POST['senha_atual'],
        $_POST['senha_nova']
    );
    echo $resp;
}

// op 31 - Obter estatísticas de produtos
if ($_POST['op'] == 31) {
    $resp = $func->getEstatisticasProdutos($_SESSION['utilizador']);
    echo $resp;
}

// op 32 - Obter lista de encomendas
if ($_POST['op'] == 32) {
    $resp = $func->getEncomendas($_SESSION['utilizador']);
    echo $resp;
}

// op 33 - Atualizar status da encomenda
if ($_POST['op'] == 33) {
    $encomenda_id = $_POST['encomenda_id'];
    $novo_estado = $_POST['novo_estado'];
    $observacao = $_POST['observacao'] ?? '';
    $codigo_rastreio = $_POST['codigo_rastreio'] ?? null;

    $resp = $func->atualizarStatusEncomenda($encomenda_id, $novo_estado, $observacao, $codigo_rastreio);

    // Enviar email ao cliente sobre a alteração de status
    $resposta = json_decode($resp, true);
    if ($resposta['success']) {
        try {
            require_once(__DIR__ . '/../services/EmailService.php');

            // Buscar dados do cliente e encomenda
            $detalhes = $func->obterDetalhesEncomenda($encomenda_id);
            if ($detalhes) {
                $emailService = new EmailService();
                $emailService->enviarEmailStatusEncomenda(
                    $detalhes['cliente_email'],
                    $detalhes['cliente_nome'],
                    $detalhes['codigo_encomenda'],
                    $novo_estado,
                    $codigo_rastreio
                );
            }
        } catch (Exception $e) {
            error_log("Erro ao enviar email de status: " . $e->getMessage());
            // Não falhar a operação se o email não enviar
        }
    }

    echo $resp;
}

// op 34 - Obter histórico da encomenda
if ($_POST['op'] == 34) {
    $encomenda_id = $_POST['encomenda_id'];
    $resp = $func->getHistoricoEncomenda($encomenda_id);
    echo $resp;
}

// op 36 - Remover produtos em massa
if ($_POST['op'] == 36) {
    $ids = $_POST['ids'];
    $resp = $func->removerProdutosEmMassa($ids);
    echo $resp; // Já vem como JSON do model
}

// Fechar conexão global no final
global $conn;
if (isset($conn)) {
    $conn->close();
}
?>
