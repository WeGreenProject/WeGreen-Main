<?php

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../services/RankingService.php';
require_once __DIR__ . '/../services/ProfileAddressFieldsService.php';

class DashboardAnunciante {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    private function gerarCodigoConfirmacaoRececao() {
        do {
            $codigo = 'CONF-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 6));
            $stmt = $this->conn->prepare("SELECT id FROM Encomendas WHERE codigo_confirmacao_recepcao = ? LIMIT 1");
            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $res = $stmt->get_result();
            $existe = ($res && $res->num_rows > 0);
            $stmt->close();
        } while ($existe);

        return $codigo;
    }

    private function obterCodigoConfirmacaoPorCodigoEncomenda($codigoEncomenda) {
        $sql = "SELECT codigo_confirmacao_recepcao
                FROM Encomendas
                WHERE codigo_encomenda = ?
                  AND codigo_confirmacao_recepcao IS NOT NULL
                  AND codigo_confirmacao_recepcao <> ''
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $codigoEncomenda);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return $row['codigo_confirmacao_recepcao'] ?? null;
    }

    private function garantirCamposEnderecoPerfil() {
        ProfileAddressFieldsService::garantirCamposEnderecoPerfil($this->conn);
    }


    private function getTaxaComissaoPorProduto($produtoId) {
        $sql = "SELECT sustentavel, tipo_material FROM Produtos WHERE Produto_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 0.06;
        }
        $stmt->bind_param("i", $produtoId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row || !(int)$row['sustentavel']) {
            return 0.06;
        }

        $material = $row['tipo_material'] ?? '';
        $taxas = [
            '100_reciclavel' => 0.04,
            '70_reciclavel'  => 0.05,
            '50_reciclavel'  => 0.05,
            '30_reciclavel'  => 0.06
        ];
        return $taxas[$material] ?? 0.06;
    }

    function getDadosPlanos($ID_User, $plano) {
        try {

        $sql = "SELECT p.id AS plano_id, p.nome AS plano_nome, p.rastreio_tipo, p.relatorio_pdf
            FROM Utilizadores u
            LEFT JOIN Planos p ON u.plano_id = p.id
            WHERE u.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $planoNome = $row['plano_nome'] ?? 'N/A';
            $planoId = $row['plano_id'] ?? 1;

            $stmt->close();
            return json_encode([
                'success' => true,
                'plano' => $planoNome,
                'plano_id' => $planoId,
                'taxa_comissao_info' => 'Comissão por produto: 4-6% (baseada na sustentabilidade)',
                'rastreio_tipo' => $row['rastreio_tipo'] ?? 'Básico',
                'relatorio_pdf' => (int)($row['relatorio_pdf'] ?? 0)
            ]);
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro na conta']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function carregarProdutos($ID_User) {
        try {

        $sql = "SELECT COUNT(*) AS total FROM Produtos WHERE anunciante_id = ? AND stock > 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (int)($row['total'] ?? 0);
        $stmt->close();

        return json_encode(['total' => $total]);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function carregarPontos($ID_User) {
        try {

        try {
            $rankingService = new RankingService($this->conn);
            $rankingService->recalcularPontosCompleto((int)$ID_User);
        } catch (Exception $rankEx) {
        }

        $sql = "SELECT pontos_conf FROM Utilizadores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $pontos = (int)($row['pontos_conf'] ?? 0);
        $stmt->close();

        return json_encode(['pontos' => $pontos]);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getEstatisticasProdutos($ID_User) {
        try {

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN ativo = 1 AND stock > 0 THEN 1 ELSE 0 END) as ativos,
                    SUM(CASE WHEN ativo = 0 OR stock = 0 THEN 1 ELSE 0 END) as inativos,
                    SUM(CASE WHEN stock > 0 AND stock < 5 THEN 1 ELSE 0 END) as stockBaixo,
                    SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as esgotados
                FROM Produtos
                WHERE anunciante_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stats = array(
            'total' => (int)$row['total'],
            'ativos' => (int)$row['ativos'],
            'inativos' => (int)$row['inativos'],
            'stockBaixo' => (int)$row['stockBaixo'],
            'esgotados' => (int)$row['esgotados']
        );

        $stmt->close();
        return json_encode($stats);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getGastos($ID_User) {
        try {

        $sql = "SELECT SUM(gastos.valor) AS total FROM gastos WHERE anunciante_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $stmt->close();

        return json_encode(['total' => $total]);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getLucroTotal($ID_User) {
        try {

        $sql = "SELECT SUM(lucro) AS total FROM Vendas WHERE anunciante_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $stmt->close();

        return json_encode(['total' => $total]);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getVendasMensais($ID_User) {
        try {

        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $dados = array_fill(0, 12, 0);

        $sql = "SELECT MONTH(data_venda) AS mes, SUM(valor) AS total
                FROM Vendas
                WHERE anunciante_id = ?
                GROUP BY MONTH(data_venda)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[(int)$row['mes'] - 1] = (float)$row['total'];
        }

        $stmt->close();
        return json_encode([
            'dados1' => $meses,
            'dados2' => $dados
        ]);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getTopProdutos($ID_User) {
        try {

        $dados = [];

        $sql = "SELECT p.nome, SUM(v.quantidade) AS vendidos
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = ?
                GROUP BY v.produto_id
                ORDER BY vendidos DESC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = ['nome' => $row['nome'], 'vendidos' => (int)$row['vendidos']];
        }

        $stmt->close();
        return json_encode($dados);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getLucroPorProduto($ID_User) {
        try {

        $dados = [];

        $sql = "SELECT p.nome, SUM(v.lucro) AS lucro_total
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = ?
                GROUP BY v.produto_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = ['nome' => $row['nome'], 'lucro' => (float)$row['lucro_total']];
        }

        $stmt->close();
        return json_encode($dados);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getMargemLucro($ID_User) {
        try {

        $dados = [];

        $sql = "SELECT p.nome, SUM(v.lucro) AS lucro, SUM(v.valor) AS total_vendas
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = ?
                GROUP BY v.produto_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $margem = $row['total_vendas'] != 0 ? ($row['lucro'] / $row['total_vendas']) * 100 : 0;
            $dados[] = ['nome' => $row['nome'], 'margem' => round($margem, 2)];
        }

        $stmt->close();
        return json_encode($dados);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getEvolucaoVendas($ID_User) {
        try {

        $dados = [];

        $sql = "SELECT DATE_FORMAT(data_venda, '%Y-%m') AS periodo,
                       SUM(valor) AS total,
                       COUNT(*) AS quantidade
                FROM Vendas
                WHERE anunciante_id = ?
                GROUP BY DATE_FORMAT(data_venda, '%Y-%m')
                ORDER BY periodo ASC
                LIMIT 12";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = [
                'periodo' => $row['periodo'],
                'total' => (float)$row['total'],
                'quantidade' => (int)$row['quantidade']
            ];
        }

        $stmt->close();
        return json_encode($dados);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getProdutosRecentes($ID_User) {
        try {

        $produtos = [];

        $sql = "SELECT p.*, t.descricao as tipo_produto,
                DATE_FORMAT(p.data_criacao, '%d/%m/%Y') as data_formatada
                FROM Produtos p
                LEFT JOIN Tipo_Produtos t ON p.tipo_produto_id = t.id
                WHERE p.anunciante_id = ?
                ORDER BY p.data_criacao DESC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        $stmt->close();
        return json_encode($produtos);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getTodosProdutos($ID_User) {
        try {

        $produtos = [];

        $sql = "SELECT p.*, t.descricao as tipo_descricao
                FROM Produtos p
                LEFT JOIN Tipo_Produtos t ON p.tipo_produto_id = t.id
                WHERE p.anunciante_id = ?
                ORDER BY p.data_criacao DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        $stmt->close();
        return json_encode($produtos);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getTiposProdutos() {
        try {

        $tipos = [];

        $sql = "SELECT id, descricao FROM Tipo_Produtos ORDER BY descricao";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }

        if (isset($stmt) && $stmt) {
            $stmt->close();
        }

        return json_encode($tipos);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getLimiteProdutos($ID_User) {
        try {

        $sql = "SELECT p.limite_produtos, COUNT(pr.Produto_id) as current
                FROM Utilizadores u
                JOIN Planos p ON u.plano_id = p.id
            LEFT JOIN Produtos pr ON pr.anunciante_id = u.id AND pr.ativo = 1 AND pr.stock > 0
                WHERE u.id = ?
                GROUP BY u.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return json_encode(['max' => (int)($row['limite_produtos'] ?? 0), 'current' => (int)($row['current'] ?? 0)]);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getProdutoById($id) {
        try {

        $sql = "SELECT * FROM Produtos WHERE Produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produto = $result->fetch_assoc();
        $stmt->close();

        if ($produto && !empty($produto['foto'])) {
            $produto['fotos_array'] = explode(',', $produto['foto']);
        } else {
            $produto['fotos_array'] = [];
        }

        return json_encode($produto);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function atualizarAtivoEmMassa($ids, $ativo) {
        try {

        if (empty($ids) || !is_array($ids)) {
            return false;
        }

        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE Produtos SET ativo = ? WHERE Produto_id IN ($marcadores)";
        $stmt = $this->conn->prepare($sql);

        $tipos = 'i' . str_repeat('i', count($ids));
        $parametros = array_merge([$ativo], $ids);
        $stmt->bind_param($tipos, ...$parametros);

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }
function removerProdutosEmMassa($ids) {
        try {
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $ids = array_values(array_filter(array_map('intval', $ids), function($id) {
                return $id > 0;
            }));

            if (empty($ids)) {
                return json_encode(['success' => false, 'message' => 'Nenhum produto selecionado']);
            }

            $removidos = 0;
            $desativados = 0;

            foreach ($ids as $id) {
                $sqlCheck = "SELECT
                    (SELECT COUNT(*) FROM Encomendas WHERE produto_id = ?) as encomendas,
                    (SELECT COUNT(*) FROM Vendas WHERE produto_id = ?) as vendas";
                $stmtCheck = $this->conn->prepare($sqlCheck);
                $stmtCheck->bind_param("ii", $id, $id);
                $stmtCheck->execute();
                $resultCheck = $stmtCheck->get_result();
                $rowCheck = $resultCheck ? $resultCheck->fetch_assoc() : ['encomendas' => 0, 'vendas' => 0];
                $stmtCheck->close();

                $temEncomendas = ((int)($rowCheck['encomendas'] ?? 0)) > 0;
                $temVendas = ((int)($rowCheck['vendas'] ?? 0)) > 0;

                if ($temEncomendas || $temVendas) {
                    $sqlUpdate = "UPDATE Produtos SET ativo = 0 WHERE Produto_id = ?";
                    $stmtUpdate = $this->conn->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("i", $id);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();
                    $desativados++;
                    continue;
                }

                $sqlFotos = "DELETE FROM Produto_Fotos WHERE produto_id = ?";
                $stmtFotos = $this->conn->prepare($sqlFotos);
                if ($stmtFotos) {
                    $stmtFotos->bind_param("i", $id);
                    $stmtFotos->execute();
                    $stmtFotos->close();
                }

                $sqlDelete = "DELETE FROM Produtos WHERE Produto_id = ?";
                $stmtDelete = $this->conn->prepare($sqlDelete);
                $stmtDelete->bind_param("i", $id);
                $apagou = $stmtDelete->execute();
                $afetadas = $stmtDelete->affected_rows;
                $stmtDelete->close();

                if ($apagou && $afetadas > 0) {
                    $removidos++;
                } else {
                    $sqlUpdate = "UPDATE Produtos SET ativo = 0 WHERE Produto_id = ?";
                    $stmtUpdate = $this->conn->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("i", $id);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();
                    $desativados++;
                }
            }

            $mensagens = [];
            if ($removidos > 0) {
                $mensagens[] = "$removidos produto(s) removido(s)";
            }
            if ($desativados > 0) {
                $mensagens[] = "$desativados produto(s) desativado(s) por estarem ligados a encomendas/vendas ou outros registos";
            }

            if (empty($mensagens)) {
                return json_encode(['success' => false, 'message' => 'Nenhum produto foi removido ou desativado']);
            }

            return json_encode(['success' => true, 'message' => implode(' e ', $mensagens)]);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro ao remover produtos: ' . $e->getMessage()]);
        }
}

    function alterarEstadoEmMassa($ids, $estado) {
        try {

        if (empty($ids) || !is_array($ids)) {
            return false;
        }

        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE Produtos SET estado = ? WHERE Produto_id IN ($marcadores)";
        $stmt = $this->conn->prepare($sql);

        $tipos = 's' . str_repeat('i', count($ids));
        $parametros = array_merge([$estado], $ids);
        $stmt->bind_param($tipos, ...$parametros);

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function updateProduto($id, $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao) {
        try {

        $sql = "UPDATE Produtos SET nome=?, tipo_produto_id=?, preco=?, stock=?, marca=?, tamanho=?, estado=?, genero=?, descricao=? WHERE Produto_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sidiissssi", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $id);
        $stmt->execute();
        $stmt->close();

        return "Produto atualizado";
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function insertProduto($nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $anunciante_id, $fotos = [], $sustentavel = 0, $tipo_material = null) {
        try {

        $limite = $this->getLimiteProdutos($anunciante_id);
        $limiteDados = json_decode($limite, true);
        $maxProdutos = isset($limiteDados['max']) ? (int)$limiteDados['max'] : 0;
        $produtosAtuais = isset($limiteDados['current']) ? (int)$limiteDados['current'] : 0;

        if ($maxProdutos > 0 && $produtosAtuais >= $maxProdutos) {
            return json_encode(['flag' => false, 'msg' => 'Limite de produtos do plano atingido']);
        }

        $upload = $this->processarUploadsProduto($fotos, $nome);
        if (!$upload['flag']) {
            return json_encode(['flag' => false, 'msg' => $upload['msg']]);
        }

        $fotosProcessadas = $upload['fotos'];
        if (empty($fotosProcessadas)) {
            return json_encode(['flag' => false, 'msg' => 'É necessário adicionar pelo menos uma foto válida']);
        }

        $foto_principal = $fotosProcessadas[0];
        $ativo = 0;
        $motivo_rejeicao = 'PENDENTE_REVISAO_ANUNCIANTE';

        $sql = "INSERT INTO Produtos (nome, tipo_produto_id, preco, stock, marca, tamanho, estado, genero, descricao, anunciante_id, foto, ativo, motivo_rejeicao, sustentavel, tipo_material) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao preparar inserção do produto']);
        }

        $sustentavel_int = (int)$sustentavel;
        $stmt->bind_param("sidisssssisisis", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $anunciante_id, $foto_principal, $ativo, $motivo_rejeicao, $sustentavel_int, $tipo_material);

        if (!$stmt->execute()) {
            $erro = $stmt->error;
            $stmt->close();
            return json_encode(['flag' => false, 'msg' => 'Erro ao inserir produto: ' . $erro]);
        }

        $produto_id = $this->conn->insert_id;
        $stmt->close();

    $this->reabrirNotificacaoProdutoParaAdmin($produto_id);

        if (count($fotosProcessadas) > 1) {
            $sqlFotos = "INSERT INTO Produto_Fotos (produto_id, foto) VALUES (?, ?)";
            $stmtFotos = $this->conn->prepare($sqlFotos);
            if ($stmtFotos) {
                for ($i = 1; $i < count($fotosProcessadas); $i++) {
                    $stmtFotos->bind_param("is", $produto_id, $fotosProcessadas[$i]);
                    $stmtFotos->execute();
                }
                $stmtFotos->close();
            }
        }

        return json_encode(['flag' => true, 'msg' => 'Produto adicionado com sucesso']);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro interno do servidor']);
        }
    }

    function atualizarProduto($id, $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $fotos = [], $sustentavel = 0, $tipo_material = null) {
        try {

        $fotosProcessadas = [];
        $temUpload = isset($fotos['name']) && is_array($fotos['name']) && count($fotos['name']) > 0;

        if ($temUpload) {
            $upload = $this->processarUploadsProduto($fotos, $nome);
            if (!$upload['flag']) {
                return json_encode(['flag' => false, 'msg' => $upload['msg']]);
            }
            $fotosProcessadas = $upload['fotos'];
        }

        $sustentavel_int = (int)$sustentavel;

        if (!empty($fotosProcessadas)) {
            $foto_principal = $fotosProcessadas[0];
            $sql = "UPDATE Produtos SET nome = ?, tipo_produto_id = ?, preco = ?, stock = ?, marca = ?, tamanho = ?, estado = ?, genero = ?, descricao = ?, foto = ?, sustentavel = ?, tipo_material = ? WHERE Produto_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sidissssssisi", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $foto_principal, $sustentavel_int, $tipo_material, $id);
        } else {
            $sql = "UPDATE Produtos SET nome = ?, tipo_produto_id = ?, preco = ?, stock = ?, marca = ?, tamanho = ?, estado = ?, genero = ?, descricao = ?, sustentavel = ?, tipo_material = ? WHERE Produto_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sidisssssisi", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $sustentavel_int, $tipo_material, $id);
        }

        if (!$stmt->execute()) {
            $erro = $stmt->error;
            $stmt->close();
            return json_encode(['flag' => false, 'msg' => 'Erro ao atualizar produto: ' . $erro]);
        }
        $stmt->close();

        if (!empty($fotosProcessadas)) {
            $sqlDelete = "DELETE FROM Produto_Fotos WHERE produto_id = ?";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            if ($stmtDelete) {
                $stmtDelete->bind_param("i", $id);
                $stmtDelete->execute();
                $stmtDelete->close();
            }

            if (count($fotosProcessadas) > 1) {
                $sqlFotos = "INSERT INTO Produto_Fotos (produto_id, foto) VALUES (?, ?)";
                $stmtFotos = $this->conn->prepare($sqlFotos);
                if ($stmtFotos) {
                    for ($i = 1; $i < count($fotosProcessadas); $i++) {
                        $stmtFotos->bind_param("is", $id, $fotosProcessadas[$i]);
                        $stmtFotos->execute();
                    }
                    $stmtFotos->close();
                }
            }
        }


        $sqlAtivo = "UPDATE Produtos SET ativo = 0, motivo_rejeicao = 'PENDENTE_REVISAO_ANUNCIANTE' WHERE Produto_id = ?";
        $stmtAtivo = $this->conn->prepare($sqlAtivo);
        if ($stmtAtivo) {
            $stmtAtivo->bind_param("i", $id);
            $stmtAtivo->execute();
            $stmtAtivo->close();
        }

        $this->reabrirNotificacaoProdutoParaAdmin($id);

        return json_encode(['flag' => true, 'msg' => 'Produto atualizado com sucesso']);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro interno do servidor']);
        }
    }

    private function reabrirNotificacaoProdutoParaAdmin($produto_id) {
        try {

        $produto_id = (int)$produto_id;
        if ($produto_id <= 0) {
            return;
        }

        $sql = "DELETE FROM notificacoes_lidas WHERE referencia_id = ? AND tipo_notificacao = 'produto'";
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $produto_id);
            $stmt->execute();
            $stmt->close();
        }

        } catch (Exception $e) {
        }
    }

    private function processarUploadsProduto($fotos, $nomeProduto) {
        try {

        if (!isset($fotos) || !is_array($fotos) || !isset($fotos['name']) || !is_array($fotos['name'])) {
            return ['flag' => false, 'msg' => 'Dados de fotos inválidos', 'fotos' => []];
        }

        $dirFisico = __DIR__ . '/../../assets/media/products/';
        $dirWeb = 'assets/media/products/';

        if (!is_dir($dirFisico) && !mkdir($dirFisico, 0777, true)) {
            return ['flag' => false, 'msg' => 'Não foi possível preparar a pasta de upload', 'fotos' => []];
        }

        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fotosProcessadas = [];

        $total = count($fotos['name']);
        for ($i = 0; $i < $total; $i++) {
            $erro = $fotos['error'][$i] ?? UPLOAD_ERR_NO_FILE;
            if ($erro === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($erro !== UPLOAD_ERR_OK) {
                return ['flag' => false, 'msg' => 'Erro no upload de uma das fotos', 'fotos' => []];
            }

            $tmpName = $fotos['tmp_name'][$i] ?? '';
            $nomeOriginal = $fotos['name'][$i] ?? '';
            $tamanho = isset($fotos['size'][$i]) ? (int)$fotos['size'][$i] : 0;

            if (!is_uploaded_file($tmpName)) {
                return ['flag' => false, 'msg' => 'Ficheiro inválido no upload', 'fotos' => []];
            }

            if ($tamanho > 5 * 1024 * 1024) {
                return ['flag' => false, 'msg' => 'Uma imagem excede o limite de 5MB', 'fotos' => []];
            }

            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            if (!in_array($extensao, $extensoesPermitidas, true)) {
                return ['flag' => false, 'msg' => 'Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF', 'fotos' => []];
            }

            $checkImagem = @getimagesize($tmpName);
            if ($checkImagem === false) {
                return ['flag' => false, 'msg' => 'Uma das fotos não é uma imagem válida', 'fotos' => []];
            }

            $nomeBase = preg_replace('/[^a-zA-Z0-9]/', '_', $nomeProduto);
            $novoNome = 'produto_' . $nomeBase . '_' . date('YmdHis') . '_' . $i . '_' . mt_rand(1000, 9999) . '.' . $extensao;
            $destinoFisico = $dirFisico . $novoNome;
            $destinoWeb = $dirWeb . $novoNome;

            if (!move_uploaded_file($tmpName, $destinoFisico)) {
                return ['flag' => false, 'msg' => 'Erro ao guardar foto no servidor', 'fotos' => []];
            }

            $fotosProcessadas[] = $destinoWeb;
        }

        return ['flag' => true, 'msg' => 'OK', 'fotos' => $fotosProcessadas];
        } catch (Exception $e) {
            return ['flag' => false, 'msg' => 'Erro interno no processamento de fotos', 'fotos' => []];
        }
    }

    function getReceitaTotal($ID_User, $periodo = 'all') {
        try {

        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT SUM(valor) AS total FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $stmt->close();

        return $total;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getTotalPedidos($ID_User, $periodo = 'all') {
        try {

        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT COUNT(*) AS total FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (int)($row['total'] ?? 0);
        $stmt->close();

        return $total;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getTicketMedio($ID_User, $periodo = 'all') {
        try {

        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT SUM(valor) AS total, COUNT(*) AS quantidade FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $quantidade = (int)($row['quantidade'] ?? 0);
        $ticket = $quantidade > 0 ? $total / $quantidade : 0;
        $stmt->close();

        return round($ticket, 2);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getMargemLucroGeral($ID_User, $periodo = 'all') {
        try {

        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT (SUM(lucro) / SUM(valor)) * 100 AS margem FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $margem = (float)($row['margem'] ?? 0);
        $stmt->close();

        return round($margem, 2);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getVendasPorCategoria($ID_User, $periodo = 'all') {
        try {

        $dados = [];
        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT tp.descricao AS categoria, SUM(v.quantidade) AS vendas, SUM(v.valor) AS receita
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id
                WHERE v.anunciante_id = ?" . $filtroData . "
                GROUP BY tp.id
                ORDER BY receita DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = [
                'categoria' => $row['categoria'],
                'vendas' => (int)$row['vendas'],
                'receita' => (float)$row['receita']
            ];
        }

        $stmt->close();
        return json_encode($dados);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getReceitaDiaria($ID_User, $periodo = 'all') {
        try {

        $dados = [];

        if ($periodo == 'month') {
            $sql = "SELECT DATE(data_venda) AS data, SUM(valor) AS receita
                    FROM Vendas
                    WHERE anunciante_id = ? AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY DATE(data_venda)
                    ORDER BY data ASC";
        } elseif ($periodo == 'year') {
            $sql = "SELECT DATE_FORMAT(data_venda, '%Y-%m') AS data, SUM(valor) AS receita
                    FROM Vendas
                    WHERE anunciante_id = ? AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(data_venda, '%Y-%m')
                    ORDER BY data ASC";
        } else {
            $sql = "SELECT DATE_FORMAT(data_venda, '%Y-%m') AS data, SUM(valor) AS receita
                    FROM Vendas
                    WHERE anunciante_id = ?
                    GROUP BY DATE_FORMAT(data_venda, '%Y-%m')
                    ORDER BY data ASC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = [
                'data' => $row['data'],
                'receita' => (float)$row['receita']
            ];
        }

        $stmt->close();
        return json_encode($dados);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getRelatoriosProdutos($ID_User, $periodo = 'all') {
        try {

        $dados = [];
        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT p.nome AS produto, SUM(v.quantidade) AS vendas, SUM(v.valor) AS receita, SUM(v.lucro) AS lucro
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = ?" . $filtroData . "
                GROUP BY p.Produto_id
                ORDER BY receita DESC
                LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = [
                'produto' => $row['produto'],
                'vendas' => (int)$row['vendas'],
                'receita' => (float)$row['receita'],
                'lucro' => (float)$row['lucro']
            ];
        }

        $stmt->close();
        return json_encode($dados);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function getDadosPerfil($ID_User) {
        try {

        try {
            $rankingService = new RankingService($this->conn);
            $rankingService->recalcularPontosCompleto((int)$ID_User);
        } catch (Exception $rankEx) {
        }

        $this->garantirCamposEnderecoPerfil();


        $this->verificarExpiracaoPlano($ID_User);

        $sql = "SELECT u.id, u.nome, u.email, u.nif, u.telefone, u.morada, u.distrito, u.localidade, u.codigo_postal, u.foto, u.pontos_conf, u.plano_id,
                       r.nome AS ranking_nome, r.pontos AS ranking_pontos_atuais,
                       p.nome AS plano_nome, p.preco AS plano_preco, p.limite_produtos AS plano_limite,
                       p.rastreio_tipo AS plano_rastreio_tipo, p.relatorio_pdf AS plano_relatorio_pdf,
                       COUNT(DISTINCT pr.Produto_id) AS total_produtos
                FROM Utilizadores u
                LEFT JOIN Ranking r ON u.ranking_id = r.id
                LEFT JOIN Planos p ON u.plano_id = p.id
                LEFT JOIN Produtos pr ON pr.anunciante_id = u.id AND pr.ativo = 1
                WHERE u.id = ?
                GROUP BY u.id, u.nome, u.email, u.nif, u.telefone, u.morada, u.distrito, u.localidade, u.codigo_postal, u.foto, u.pontos_conf, u.plano_id,
                         r.nome, r.pontos, p.nome, p.preco, p.limite_produtos, p.rastreio_tipo, p.relatorio_pdf";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {


            $row['taxa_comissao_info'] = 'A comissão varia entre 4% e 6% consoante a sustentabilidade de cada produto';
            $row['taxa_comissao_min'] = 4;
            $row['taxa_comissao_max'] = 6;

            $sqlProximo = "SELECT nome, pontos FROM Ranking WHERE pontos > ? ORDER BY pontos ASC LIMIT 1";
            $stmtProximo = $this->conn->prepare($sqlProximo);
            $stmtProximo->bind_param("i", $row['pontos_conf']);
            $stmtProximo->execute();
            $resultProximo = $stmtProximo->get_result();

            if ($rowProximo = $resultProximo->fetch_assoc()) {
                $row['proximo_ranking_nome'] = $rowProximo['nome'];
                $row['proximo_ranking_pontos'] = $rowProximo['pontos'];
            } else {
                $row['proximo_ranking_nome'] = null;
                $row['proximo_ranking_pontos'] = null;
            }
            $stmtProximo->close();

            $stmt->close();
            return json_encode($row, JSON_UNESCAPED_UNICODE);
        }

        $stmt->close();
        return json_encode(['error' => 'Utilizador não encontrado'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function atualizarPerfil($ID_User, $nome, $email, $telefone = null, $nif = null, $morada = null, $distrito = null, $localidade = null, $codigo_postal = null) {
        try {

        $this->garantirCamposEnderecoPerfil();

        if (empty($nome) || strlen($nome) < 3) {
            return json_encode(['success' => false, 'message' => 'Nome deve ter no mínimo 3 caracteres']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['success' => false, 'message' => 'Email inválido']);
        }

        if (!empty($nif) && !preg_match('/^[0-9]{9}$/', $nif)) {
            return json_encode(['success' => false, 'message' => 'NIF deve conter exatamente 9 dígitos']);
        }

        if (!empty($telefone) && !preg_match('/^[0-9]{9}$/', $telefone)) {
            return json_encode(['success' => false, 'message' => 'Telefone deve conter exatamente 9 dígitos']);
        }

        if (!empty($codigo_postal) && !preg_match('/^[0-9]{4}-[0-9]{3}$/', $codigo_postal)) {
            return json_encode(['success' => false, 'message' => 'Código postal inválido (formato XXXX-XXX)']);
        }

        $sqlCheck = "SELECT id FROM utilizadores WHERE email = ? AND id != ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param("si", $email, $ID_User);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $stmtCheck->close();
            return json_encode(['success' => false, 'message' => 'Email já está em uso']);
        }
        $stmtCheck->close();

        $sql = "UPDATE utilizadores SET nome = ?, email = ?, nif = ?, telefone = ?, morada = ?, distrito = ?, localidade = ?, codigo_postal = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $nome, $email, $nif, $telefone, $morada, $distrito, $localidade, $codigo_postal, $ID_User);

        if ($stmt->execute()) {
            $stmt->close();
            return json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso']);
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function atualizarFotoPerfil($ID_User, $foto) {
        try {

        $targetDir = "src/img/";
        $fileName = time() . '_' . basename($foto["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $check = getimagesize($foto["tmp_name"]);
        if ($check === false) {
            return json_encode(['success' => false, 'message' => 'Ficheiro não é uma imagem']);
        }

        if ($foto["size"] > 5000000) {
            return json_encode(['success' => false, 'message' => 'Ficheiro muito grande (máx 5MB)']);
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return json_encode(['success' => false, 'message' => 'Apenas JPG, JPEG, PNG, GIF e WEBP são permitidos']);
        }

        if (move_uploaded_file($foto["tmp_name"], $targetFile)) {
            $sql = "UPDATE utilizadores SET foto = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $targetFile, $ID_User);

            if ($stmt->execute()) {
                $stmt->close();
                return json_encode(['success' => true, 'message' => 'Foto atualizada com sucesso', 'foto' => $targetFile]);
            }
            $stmt->close();
        }

        return json_encode(['success' => false, 'message' => 'Erro ao fazer upload da foto']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function alterarPassword($ID_User, $senha_atual, $senha_nova) {
        try {

        $sql = "SELECT password FROM utilizadores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['password'] !== $senha_atual) {
                $stmt->close();
                return json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
            }

            $sqlUpdate = "UPDATE utilizadores SET password = ? WHERE id = ?";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $senha_nova, $ID_User);

            if ($stmtUpdate->execute()) {
                $stmtUpdate->close();
                $stmt->close();
                return json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
            }
            $stmtUpdate->close();
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro ao alterar senha']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

function getEncomendas($ID_User) {
        try {

        $sql = "SELECT
                    e.id,
                    e.codigo_encomenda,
                    e.payment_id,
                    e.payment_method,
                    e.payment_status,
                    e.data_envio,
                    e.estado,
                    e.morada,
                    e.morada_completa,
                    e.nome_destinatario,
                    e.tipo_entrega,
                    e.codigo_confirmacao_recepcao,
                    u.nome AS cliente_nome,
                    u.email AS cliente_email,
                    t.nome AS transportadora_nome,
                    SUM(CASE WHEN v.anunciante_id = ? THEN v.valor ELSE 0 END) AS valor_total,
                    SUM(CASE WHEN v.anunciante_id = ? THEN v.lucro ELSE 0 END) AS lucro_total
                FROM Encomendas e
                INNER JOIN Utilizadores u ON e.cliente_id = u.id
                INNER JOIN Vendas v ON e.id = v.encomenda_id
                LEFT JOIN Transportadora t ON e.transportadora_id = t.id
                WHERE v.anunciante_id = ?
                GROUP BY e.id, e.codigo_encomenda, e.payment_id, e.payment_method, e.payment_status,
                         e.data_envio, e.estado, e.morada, e.morada_completa, e.nome_destinatario,
                         e.tipo_entrega, e.codigo_confirmacao_recepcao, u.nome, u.email, t.nome
                ORDER BY e.data_envio DESC";

        $stmt_enc = $this->conn->prepare($sql);
        $stmt_enc->bind_param("iii", $ID_User, $ID_User, $ID_User);
        $stmt_enc->execute();
        $result = $stmt_enc->get_result();

        $encomendas = [];
        while ($row = $result->fetch_assoc()) {
            $valor_bruto = (float)$row['valor_total'];
            $comissaoGuardada = isset($row['lucro_total']) ? (float)$row['lucro_total'] : 0.0;


            $sql_produtos = "SELECT p.nome, p.foto, p.Produto_id, p.sustentavel, p.tipo_material, v.quantidade, v.valor, v.lucro
                            FROM Vendas v
                            INNER JOIN Produtos p ON v.produto_id = p.Produto_id
                            WHERE v.encomenda_id = ? AND v.anunciante_id = ?";
            $stmt_produtos = $this->conn->prepare($sql_produtos);
            $stmt_produtos->bind_param("ii", $row['id'], $ID_User);
            $stmt_produtos->execute();
            $result_produtos = $stmt_produtos->get_result();

            $produtos = [];
            $quantidade_total = 0;
            $comissaoCalculada = 0;
            while ($produto = $result_produtos->fetch_assoc()) {
                $taxa_prod = $this->getTaxaComissaoPorProduto((int)$produto['Produto_id']);
                $val_prod = (float)$produto['valor'];
                $comissaoCalculada += $val_prod * $taxa_prod;

                $produtos[] = [
                    'id' => (int)$produto['Produto_id'],
                    'nome' => $produto['nome'],
                    'foto' => $produto['foto'],
                    'quantidade' => (int)$produto['quantidade'],
                    'valor' => (float)$produto['valor'],
                    'taxa_comissao_percent' => round($taxa_prod * 100, 2),
                    'sustentavel' => (int)$produto['sustentavel']
                ];
                $quantidade_total += (int)$produto['quantidade'];
            }
            $stmt_produtos->close();

            $comissao = $comissaoGuardada > 0 ? $comissaoGuardada : $comissaoCalculada;
            $lucro_liquido = $valor_bruto - $comissao;
            $taxaMedia = $valor_bruto > 0 ? ($comissao / $valor_bruto) : 0.06;

            $encomendas[] = [
                'id' => (int)$row['id'],
                'codigo' => $row['codigo_encomenda'],
                'payment_id' => $row['payment_id'],
                'payment_method' => $row['payment_method'] ?: 'N/A',
                'payment_status' => $row['payment_status'] ?: 'N/A',
                'data' => date('d/m/Y', strtotime($row['data_envio'])),
                'data_completa' => $row['data_envio'],
                'estado' => $row['estado'] ?: 'Pendente',
                'morada' => $row['morada'],
                'morada_completa' => $row['morada_completa'] ?: $row['morada'],
                'nome_destinatario' => $row['nome_destinatario'] ?: $row['cliente_nome'],
                'tipo_entrega' => $row['tipo_entrega'] ?: 'domicilio',
                'codigo_confirmacao_recepcao' => $row['codigo_confirmacao_recepcao'],
                'cliente_nome' => $row['cliente_nome'],
                'cliente_email' => $row['cliente_email'],
                'produtos' => $produtos,
                'quantidade' => $quantidade_total,
                'valor' => $valor_bruto,
                'comissao' => $comissao,
                'taxa_comissao' => $taxaMedia,
                'taxa_comissao_percent' => round($taxaMedia * 100, 2),
                'lucro_liquido' => $lucro_liquido,
                'transportadora' => $row['transportadora_nome']
            ];
        }

        return json_encode($encomendas);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function atualizarStatusEncomenda($encomenda_id, $novo_estado, $observacao = '') {

        try {
                 $sql_dados = "SELECT e.cliente_id, e.codigo_encomenda, e.morada, e.transportadora_id, e.data_envio, e.estado, e.codigo_confirmacao_recepcao,
                                 u.nome as cliente_nome, p.nome as produto_nome, p.foto
                          FROM Encomendas e
                          INNER JOIN Utilizadores u ON e.cliente_id = u.id
                          LEFT JOIN Produtos p ON e.produto_id = p.Produto_id
                          WHERE e.id = ?
                          LIMIT 1";
            $stmt_dados = $this->conn->prepare($sql_dados);
            $stmt_dados->bind_param("i", $encomenda_id);
            $stmt_dados->execute();
            $result_dados = $stmt_dados->get_result();
            $dados_encomenda = $result_dados->fetch_assoc();
            $stmt_dados->close();

            if (!$dados_encomenda) {
                return json_encode(['success' => false, 'message' => 'Encomenda não encontrada']);
            }

            $estado_atual = $dados_encomenda['estado'] ?? 'Pendente';
            $transicoes_permitidas = [
                'Pendente' => ['Pendente', 'Processando', 'Cancelado'],
                'Processando' => ['Processando', 'Enviado', 'Cancelado'],
                'Enviado' => ['Enviado', 'Entregue'],
                'Entregue' => ['Entregue'],
                'Cancelado' => ['Cancelado']
            ];

            $permitidas = $transicoes_permitidas[$estado_atual] ?? [$estado_atual];
            if (!in_array($novo_estado, $permitidas, true)) {
                return json_encode([
                    'success' => false,
                    'message' => "Transição inválida: não é possível alterar de {$estado_atual} para {$novo_estado}."
                ], JSON_UNESCAPED_UNICODE);
            }

            $codigo_confirmacao_recepcao = $dados_encomenda['codigo_confirmacao_recepcao'] ?? null;

            if ($novo_estado === 'Enviado') {
                $codigo_confirmacao_recepcao = $this->obterCodigoConfirmacaoPorCodigoEncomenda($dados_encomenda['codigo_encomenda']);
                if (empty($codigo_confirmacao_recepcao)) {
                    $codigo_confirmacao_recepcao = $this->gerarCodigoConfirmacaoRececao();
                }

                $sql = "UPDATE Encomendas SET estado = ?, codigo_confirmacao_recepcao = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ssi", $novo_estado, $codigo_confirmacao_recepcao, $encomenda_id);
            } else {
                $sql = "UPDATE Encomendas SET estado = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("si", $novo_estado, $encomenda_id);
            }

            if (!$stmt->execute()) {
                $stmt->close();
                return json_encode(['success' => false, 'message' => 'Erro ao atualizar status: ' . $this->conn->error]);
            }
            $stmt->close();

            if ($novo_estado === 'Enviado' && !empty($codigo_confirmacao_recepcao)) {
                $sqlSyncCodigo = "UPDATE Encomendas
                                  SET codigo_confirmacao_recepcao = ?
                                  WHERE codigo_encomenda = ?
                                    AND (codigo_confirmacao_recepcao IS NULL OR codigo_confirmacao_recepcao = '')";
                $stmtSync = $this->conn->prepare($sqlSyncCodigo);
                if ($stmtSync) {
                    $codigoEnc = $dados_encomenda['codigo_encomenda'];
                    $stmtSync->bind_param("ss", $codigo_confirmacao_recepcao, $codigoEnc);
                    $stmtSync->execute();
                    $stmtSync->close();
                }
            }

            $descricao = empty($observacao) ? "Status alterado para: $novo_estado" : $observacao;
            if ($novo_estado === 'Enviado' && !empty($codigo_confirmacao_recepcao)) {
                $descricao .= " - Código de confirmação de receção: $codigo_confirmacao_recepcao";
            }

            $sqlHist = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao) VALUES (?, ?, ?)";
            $stmtHist = $this->conn->prepare($sqlHist);
            $stmtHist->bind_param("iss", $encomenda_id, $novo_estado, $descricao);
            $stmtHist->execute();
            $stmtHist->close();

            $this->enviarEmailMudancaStatus($dados_encomenda, $novo_estado, $codigo_confirmacao_recepcao, $observacao);


            if ($novo_estado === 'Cancelado') {
                try {
                    $rankingService = new RankingService($this->conn);

                    $stmtAnunc = $this->conn->prepare("SELECT DISTINCT anunciante_id FROM Vendas WHERE encomenda_id = ?");
                    $stmtAnunc->bind_param("i", $encomenda_id);
                    $stmtAnunc->execute();
                    $resultAnunc = $stmtAnunc->get_result();
                    while ($rowAnunc = $resultAnunc->fetch_assoc()) {
                        $rankingService->removerPontosCancelamento((int)$rowAnunc['anunciante_id']);
                    }
                    $stmtAnunc->close();
                } catch (Exception $rankEx) {
                }
            }

            return json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);

        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro ao processar atualização: ' . $e->getMessage()]);
        }
    }


    private function enviarEmailMudancaStatus($dados_encomenda, $novo_estado, $codigo_confirmacao_recepcao = null, $observacao = '') {

        try {
            $emailService = new EmailService($this->conn);

            $transportadoras = [
                1 => 'CTT - Correios de Portugal',
                2 => 'DPD - Entrega Rápida',
                3 => 'UPS',
                4 => 'Chronopost',
                5 => 'Entrega WeGreen'
            ];
            $transportadora = $transportadoras[$dados_encomenda['transportadora_id']] ?? 'CTT';

            $dadosEmail = [
                'codigo_encomenda' => $dados_encomenda['codigo_encomenda'],
                'data_encomenda' => $dados_encomenda['data_envio'],
                'transportadora' => $transportadora,
                'morada' => $dados_encomenda['morada'],
                'nome_cliente' => $dados_encomenda['cliente_nome'] ?? 'Cliente'
            ];

            if (!empty(trim((string)$observacao))) {
                $dadosEmail['observacao_status'] = trim((string)$observacao);
            }

            $stmt_prod_email = $this->conn->prepare("SELECT p.nome, p.preco, p.Foto_Produto
                            FROM Encomendas e
                            INNER JOIN Produtos p ON e.produto_id = p.Produto_id
                            WHERE e.codigo_encomenda = ?");
            $codigo_enc = $dados_encomenda['codigo_encomenda'];
            $stmt_prod_email->bind_param("s", $codigo_enc);
            $stmt_prod_email->execute();
            $result_produtos = $stmt_prod_email->get_result();
            $produtos_array = [];

            if ($result_produtos && $result_produtos->num_rows > 0) {
                while ($prod = $result_produtos->fetch_assoc()) {
                    $produtos_array[] = [
                        'nome' => $prod['nome'],
                        'preco' => $prod['preco'],
                        'quantidade' => 1,
                        'foto' => !empty($prod['Foto_Produto']) ? 'http://localhost/WeGreen-Main/' . $prod['Foto_Produto'] : ''
                    ];
                }
            }

            $dadosEmail['produtos'] = $produtos_array;

            $template = '';
            if ($novo_estado === 'Processando') {
                $template = 'status_processando';

            } elseif ($novo_estado === 'Enviado') {
                $template = 'status_enviado';
                $dadosEmail['data_envio'] = date('Y-m-d H:i:s');
                $dadosEmail['codigo_confirmacao_recepcao'] = $codigo_confirmacao_recepcao;
                $dadosEmail['link_confirmacao_recepcao'] = !empty($codigo_confirmacao_recepcao)
                    ? ('http://localhost/WeGreen-Main/confirmar_entrega.php?cod=' . urlencode($codigo_confirmacao_recepcao))
                    : 'http://localhost/WeGreen-Main/confirmar_entrega.php';

                $prazo_dias = ($dados_encomenda['transportadora_id'] == 2) ? 2 : 4;
                $data_estimada = date('d/m/Y', strtotime("+{$prazo_dias} days"));
                $dadosEmail['prazo_estimado'] = $data_estimada;

            } elseif ($novo_estado === 'Entregue') {
                $template = 'status_entregue';
                $dadosEmail['data_entregue'] = date('Y-m-d H:i:s');
                if (!empty($codigo_confirmacao_recepcao)) {
                    $dadosEmail['codigo_confirmacao_recepcao'] = $codigo_confirmacao_recepcao;
                }

            } elseif ($novo_estado === 'Cancelado') {
                $template = 'cancelamento';
                $dadosEmail['data_cancelamento'] = date('Y-m-d H:i:s');
                $dadosEmail['motivo_cancelamento'] = !empty(trim((string)$observacao))
                    ? trim((string)$observacao)
                    : 'Cancelado pelo vendedor';

            } else {
                return;
            }

            if (!empty($template)) {
                $enviado = $emailService->sendFromTemplate($dados_encomenda['cliente_id'], $template, $dadosEmail, 'cliente');
                if (!$enviado) {
                    error_log('WeGreen Email: falha ao enviar template ' . $template . ' para cliente_id=' . (int)$dados_encomenda['cliente_id'] . ' encomenda=' . ($dados_encomenda['codigo_encomenda'] ?? 'N/A'));

                    if ($template === 'status_enviado') {
                        $stmtEmail = $this->conn->prepare("SELECT email FROM Utilizadores WHERE id = ? LIMIT 1");
                        if ($stmtEmail) {
                            $clienteId = (int)$dados_encomenda['cliente_id'];
                            $stmtEmail->bind_param("i", $clienteId);
                            $stmtEmail->execute();
                            $resultEmail = $stmtEmail->get_result();
                            $rowEmail = $resultEmail ? $resultEmail->fetch_assoc() : null;
                            $stmtEmail->close();

                            if (!empty($rowEmail['email'])) {
                                $emailService->enviarEmailStatusEncomenda(
                                    $rowEmail['email'],
                                    $dadosEmail['nome_cliente'] ?? 'Cliente',
                                    $dados_encomenda['codigo_encomenda'] ?? '',
                                    'Enviado',
                                    $codigo_confirmacao_recepcao
                                );
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {

        }
    }

    function getHistoricoEncomenda($encomenda_id) {
        try {

        $sql = "SELECT estado_encomenda, descricao, data_atualizacao
                FROM Historico_Produtos
                WHERE encomenda_id = ?
                ORDER BY data_atualizacao ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $encomenda_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $historico = [];
        while ($row = $result->fetch_assoc()) {
            $historico[] = [
                'estado' => $row['estado_encomenda'],
                'descricao' => $row['descricao'],
                'data' => date('d/m/Y', strtotime($row['data_atualizacao']))
            ];
        }

        $stmt->close();

        return json_encode($historico);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

    function ativarPlanoPago($ID_User, $plano_id) {
        try {

        $sql = "SELECT nome, preco FROM Planos WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $plano_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return json_encode(['success' => false, 'message' => 'Plano nao encontrado']);
        }

        $plano = $result->fetch_assoc();
        $stmt->close();

        $data_expiracao = date('Y-m-d H:i:s', strtotime('+30 days'));

        $sql = "UPDATE Utilizadores SET plano_id = ?, data_expiracao_plano = ?, ultimo_email_expiracao = NULL WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isi", $plano_id, $data_expiracao, $ID_User);

        if ($stmt->execute()) {
            $_SESSION['plano'] = $plano_id;
            $_SESSION['plano_nome'] = $plano['nome'];

            $this->enviarEmailAtivacaoPlano($ID_User, $plano['nome'], $data_expiracao);

            $stmt->close();
            return json_encode([
                'success' => true,
                'message' => 'Plano ativado com sucesso!',
                'plano' => $plano['nome'],
                'data_expiracao' => $data_expiracao
            ]);
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro ao ativar plano']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }


    private function verificarExpiracaoPlano($ID_User) {
        try {
            $sql = "SELECT u.plano_id, u.data_expiracao_plano,
                           p.nome AS plano_nome, p.preco,
                           (SELECT pa.data_fim FROM planos_ativos pa WHERE pa.anunciante_id = u.id AND pa.ativo = 1 ORDER BY pa.data_fim DESC LIMIT 1) AS plano_ativo_data_fim
                    FROM Utilizadores u
                    LEFT JOIN Planos p ON u.plano_id = p.id
                    WHERE u.id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $ID_User);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $plano_id = (int)$row['plano_id'];
                $preco = (float)$row['preco'];

                if ($plano_id <= 1 || $preco <= 0) {
                    $stmt->close();
                    return;
                }


                $data_expiracao = $row['data_expiracao_plano'];
                $data_fim_ativo = $row['plano_ativo_data_fim'];

                if (!$data_expiracao && $data_fim_ativo) {
                    $data_expiracao = $data_fim_ativo;
                } elseif ($data_expiracao && $data_fim_ativo) {
                    $data_expiracao = max($data_expiracao, $data_fim_ativo);
                }

                if ($data_expiracao) {
                    $agora = new DateTime();
                    $expira = new DateTime($data_expiracao);

                    if ($expira < $agora) {
                        $this->reverterPlanoGratuito($ID_User, $row['plano_nome']);
                    }
                }
            }
            $stmt->close();
        } catch (Exception $e) {
        }
    }

    function getInfoExpiracaoPlano($ID_User) {
        try {

            $this->verificarExpiracaoPlano($ID_User);

            $sql = "SELECT u.plano_id, u.data_expiracao_plano,
                           p.nome AS plano_nome, p.preco,
                           (SELECT pa.data_fim FROM planos_ativos pa WHERE pa.anunciante_id = u.id AND pa.ativo = 1 ORDER BY pa.data_fim DESC LIMIT 1) AS plano_ativo_data_fim
                    FROM Utilizadores u
                    LEFT JOIN Planos p ON u.plano_id = p.id
                    WHERE u.id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $ID_User);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $plano_id = (int)$row['plano_id'];
                $preco = (float)$row['preco'];
                $plano_nome = $row['plano_nome'];


                $data_expiracao = $row['data_expiracao_plano'];
                $data_fim_ativo = $row['plano_ativo_data_fim'];
                if (!$data_expiracao && $data_fim_ativo) {
                    $data_expiracao = $data_fim_ativo;
                } elseif ($data_expiracao && $data_fim_ativo) {
                    $data_expiracao = max($data_expiracao, $data_fim_ativo);
                }

                $dias_restantes = null;
                $status_plano = 'gratuito';

                if ($plano_id > 1 && $preco > 0 && $data_expiracao) {
                    $agora = new DateTime();
                    $expira = new DateTime($data_expiracao);
                    $diff = $agora->diff($expira);
                    $dias_restantes = $diff->days;
                    $status_plano = 'ativo';
                }

                $stmt->close();
                return json_encode([
                    'success' => true,
                    'plano_nome' => $plano_nome,
                    'data_expiracao' => $data_expiracao,
                    'dias_restantes' => $dias_restantes,
                    'status_plano' => $status_plano
                ]);
            }

            $stmt->close();
            return json_encode(['success' => false, 'message' => 'Usuario nao encontrado']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }


    private function reverterPlanoGratuito($ID_User, $plano_nome_anterior) {
        try {

            $sqlCheck = "SELECT plano_id, ultimo_email_expiracao FROM Utilizadores WHERE id = ?";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->bind_param("i", $ID_User);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $rowCheck = $resultCheck->fetch_assoc();
            $stmtCheck->close();


            if ($rowCheck && (int)$rowCheck['plano_id'] === 1) {
                return;
            }

            $jaEnviouEmail = ($rowCheck && $rowCheck['ultimo_email_expiracao'] === 'expirado');


            $sql = "UPDATE Utilizadores SET plano_id = 1, data_expiracao_plano = NULL, ultimo_email_expiracao = 'expirado' WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $ID_User);
            $stmt->execute();
            $stmt->close();


            $sql = "UPDATE planos_ativos SET ativo = 0 WHERE anunciante_id = ? AND ativo = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $ID_User);
            $stmt->execute();
            $stmt->close();


            if (isset($_SESSION['plano'])) {
                $_SESSION['plano'] = 1;
                $_SESSION['plano_nome'] = 'Essencial Verde';
            }


            if (!$jaEnviouEmail) {
                $this->enviarEmailPlanoExpirado($ID_User, $plano_nome_anterior);
            }

        } catch (Exception $e) {
        }
    }


    private function enviarEmailPlanoExpirado($ID_User, $plano_nome_anterior) {
        try {
            $sql = "SELECT nome, email FROM Utilizadores WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $ID_User);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $emailService = new EmailService($this->conn);

                $emailService->enviarEmail(
                    $user['email'],
                    'plano_expirado',
                    [
                        'nome_utilizador' => $user['nome'],
                        'plano_anterior' => $plano_nome_anterior,
                        'plano_atual' => 'Essencial Verde'
                    ],
                    $ID_User,
                    'anunciante'
                );
            }
            $stmt->close();
        } catch (Exception $e) {
        }
    }

    private function enviarEmailAtivacaoPlano($ID_User, $plano_nome, $data_expiracao) {
        try {

        $sql = "SELECT nome, email FROM Utilizadores WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $emailService = new EmailService($this->conn);

            $data_formatada = date('d/m/Y', strtotime($data_expiracao));

            $assunto = "Plano $plano_nome Ativado com Sucesso - WeGreen";
            $corpo = "
                <h2 style='color: #3cb371;'>Plano Ativado!</h2>
                <p>Ola {$user['nome']},</p>
                <p>O seu plano <strong>$plano_nome</strong> foi ativado com sucesso!</p>
                <p><strong>Data de expiracao:</strong> $data_formatada</p>
                <p>Aproveite todas as funcionalidades do seu plano.</p>
                <p>Equipa WeGreen</p>
            ";

            $emailService->enviarEmail($user['email'], $assunto, $corpo);
        }

        $stmt->close();
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
        }
    }

}
?>
