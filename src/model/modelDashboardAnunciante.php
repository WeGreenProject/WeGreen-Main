<?php

require_once 'connection.php';

class DashboardAnunciante {

    function getDadosPlanos($ID_User, $plano) {
        global $conn;

        $sql = "SELECT p.nome AS plano_nome FROM Utilizadores u LEFT JOIN Planos p ON u.plano_id = p.id WHERE u.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $planoNome = $row['plano_nome'] ?? 'N/A';

            $stmt->close();
            return json_encode(['success' => true, 'plano' => $planoNome]);
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro na conta']);
    }

    function carregarProdutos($ID_User) {
        global $conn;

        $sql = "SELECT COUNT(*) AS total FROM Produtos WHERE anunciante_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (int)($row['total'] ?? 0);
        $stmt->close();

        return json_encode(['total' => $total]);
    }

    function carregarPontos($ID_User) {
        global $conn;

        $sql = "SELECT pontos_conf FROM Utilizadores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $pontos = (int)($row['pontos_conf'] ?? 0);
        $stmt->close();

        return json_encode(['pontos' => $pontos]);
    }

    function getEstatisticasProdutos($ID_User) {
        global $conn;

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as ativos,
                    SUM(CASE WHEN ativo = 0 THEN 1 ELSE 0 END) as inativos,
                    SUM(CASE WHEN stock < 5 THEN 1 ELSE 0 END) as stockBaixo
                FROM Produtos
                WHERE anunciante_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stats = array(
            'total' => (int)$row['total'],
            'ativos' => (int)$row['ativos'],
            'inativos' => (int)$row['inativos'],
            'stockBaixo' => (int)$row['stockBaixo']
        );

        $stmt->close();
        return json_encode($stats);
    }

    function getGastos($ID_User) {
        global $conn;

        $sql = "SELECT SUM(gastos.valor) AS total FROM gastos WHERE anunciante_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $stmt->close();

        return json_encode(['total' => $total]);
    }

    function getLucroTotal($ID_User) {
        global $conn;

        $sql = "SELECT SUM(lucro) AS total FROM Vendas WHERE anunciante_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $stmt->close();

        return json_encode(['total' => $total]);
    }

    function getVendasMensais($ID_User) {
        global $conn;
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $dados = array_fill(0, 12, 0);

        $sql = "SELECT MONTH(data_venda) AS mes, SUM(valor) AS total
                FROM Vendas
                WHERE anunciante_id = ?
                GROUP BY MONTH(data_venda)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[(int)$row['mes'] - 1] = (float)$row['total'];
        }

        $stmt->close();
        return [
            'dados1' => $meses,
            'dados2' => $dados
        ];
    }

    function getTopProdutos($ID_User) {
        global $conn;
        $dados = [];

        $sql = "SELECT p.nome, SUM(v.quantidade) AS vendidos
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = ?
                GROUP BY v.produto_id
                ORDER BY vendidos DESC
                LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = ['nome' => $row['nome'], 'vendidos' => (int)$row['vendidos']];
        }

        $stmt->close();
        return $dados;
    }

    function getLucroPorProduto($ID_User) {
        global $conn;
        $dados = [];

        $sql = "SELECT p.nome, SUM(v.lucro) AS lucro_total
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = ?
                GROUP BY v.produto_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $dados[] = ['nome' => $row['nome'], 'lucro' => (float)$row['lucro_total']];
        }

        $stmt->close();
        return $dados;
    }

    function getMargemLucro($ID_User) {
        global $conn;
        $dados = [];

        $sql = "SELECT p.nome, SUM(v.lucro) AS lucro, SUM(v.valor) AS total_vendas
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = ?
                GROUP BY v.produto_id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $margem = $row['total_vendas'] != 0 ? ($row['lucro'] / $row['total_vendas']) * 100 : 0;
            $dados[] = ['nome' => $row['nome'], 'margem' => round($margem, 2)];
        }

        $stmt->close();
        return $dados;
    }

    function getEvolucaoVendas($ID_User) {
        global $conn;
        $dados = [];

        $sql = "SELECT DATE_FORMAT(data_venda, '%Y-%m') AS periodo,
                       SUM(valor) AS total,
                       COUNT(*) AS quantidade
                FROM Vendas
                WHERE anunciante_id = ?
                GROUP BY DATE_FORMAT(data_venda, '%Y-%m')
                ORDER BY periodo ASC
                LIMIT 12";
        $stmt = $conn->prepare($sql);
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
        return $dados;
    }

    function getProdutosRecentes($ID_User) {
        global $conn;
        $produtos = [];

        $sql = "SELECT p.*, t.descricao as tipo_produto,
                DATE_FORMAT(p.data_criacao, '%d/%m/%Y') as data_formatada
                FROM Produtos p
                LEFT JOIN Tipo_Produtos t ON p.tipo_produto_id = t.id
                WHERE p.anunciante_id = ?
                ORDER BY p.data_criacao DESC
                LIMIT 5";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        $stmt->close();
        return json_encode($produtos);
    }

    function getTodosProdutos($ID_User) {
        global $conn;
        $produtos = [];

        $sql = "SELECT p.*, t.descricao as tipo_descricao
                FROM Produtos p
                LEFT JOIN Tipo_Produtos t ON p.tipo_produto_id = t.id
                WHERE p.anunciante_id = ?
                ORDER BY p.data_criacao DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        $stmt->close();
        return json_encode($produtos);
    }

    function getTiposProdutos() {
        global $conn;
        $tipos = [];

        $sql = "SELECT id, descricao FROM Tipo_Produtos ORDER BY descricao";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $tipos[] = $row;
        }

        return json_encode($tipos);
    }

    function getLimiteProdutos($ID_User) {
        global $conn;
        $sql = "SELECT p.limite_produtos, COUNT(pr.Produto_id) as current
                FROM Utilizadores u
                JOIN Planos p ON u.plano_id = p.id
                LEFT JOIN Produtos pr ON pr.anunciante_id = u.id
                WHERE u.id = ?
                GROUP BY u.id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return json_encode(['max' => (int)($row['limite_produtos'] ?? 0), 'current' => (int)($row['current'] ?? 0)]);
    }

    function getProdutoById($id) {
        global $conn;
        $sql = "SELECT * FROM Produtos WHERE Produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produto = $result->fetch_assoc();
        $stmt->close();

        return json_encode($produto);
    }

    function deleteProduto($id) {
        global $conn;
        $sql = "DELETE FROM Produtos WHERE Produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        return "Produto removido";
    }

    function atualizarAtivoEmMassa($ids, $ativo) {
        global $conn;
        if (empty($ids) || !is_array($ids)) {
            return false;
        }

        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE Produtos SET ativo = ? WHERE Produto_id IN ($marcadores)";
        $stmt = $conn->prepare($sql);

        $tipos = 'i' . str_repeat('i', count($ids));
        $parametros = array_merge([$ativo], $ids);
        $stmt->bind_param($tipos, ...$parametros);

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    function removerProdutosEmMassa($ids) {
        global $conn;
        if (empty($ids) || !is_array($ids)) {
            return false;
        }

        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM Produtos WHERE Produto_id IN ($marcadores)";
        $stmt = $conn->prepare($sql);

        $tipos = str_repeat('i', count($ids));
        $stmt->bind_param($tipos, ...$ids);

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    function alterarEstadoEmMassa($ids, $estado) {
        global $conn;
        if (empty($ids) || !is_array($ids)) {
            return false;
        }

        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE Produtos SET estado = ? WHERE Produto_id IN ($marcadores)";
        $stmt = $conn->prepare($sql);

        $tipos = 's' . str_repeat('i', count($ids));
        $parametros = array_merge([$estado], $ids);
        $stmt->bind_param($tipos, ...$parametros);

        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }

    function updateProduto($id, $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao) {
        global $conn;
        $sql = "UPDATE Produtos SET nome=?, tipo_produto_id=?, preco=?, stock=?, marca=?, tamanho=?, estado=?, genero=?, descricao=? WHERE Produto_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidiissssi", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $id);
        $stmt->execute();
        $stmt->close();

        return "Produto atualizado";
    }

    function insertProduto($nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $anunciante_id, $fotos = []) {
        global $conn;

        // Inserir o produto principal
        $foto_principal = !empty($fotos) ? $fotos[0] : '';
        $ativo = 1;

        $sql = "INSERT INTO Produtos (nome, tipo_produto_id, preco, stock, marca, tamanho, estado, genero, descricao, anunciante_id, foto, ativo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            error_log('Erro ao preparar statement INSERT Produtos: ' . $conn->error);
            return "Erro ao preparar inserção do produto";
        }

        $stmt->bind_param("sidisssssisi", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $anunciante_id, $foto_principal, $ativo);

        if (!$stmt->execute()) {
            error_log('Erro ao executar INSERT Produtos: ' . $stmt->error);
            $stmt->close();
            return "Erro ao inserir produto: " . $stmt->error;
        }

        $produto_id = $conn->insert_id;
        $stmt->close();

        // Se houver mais fotos, adicionar à tabela Produto_Fotos
        if (count($fotos) > 1) {
            $sqlFotos = "INSERT INTO Produto_Fotos (produto_id, foto) VALUES (?, ?)";
            $stmtFotos = $conn->prepare($sqlFotos);
            if ($stmtFotos) {
                for ($i = 1; $i < count($fotos); $i++) {
                    $stmtFotos->bind_param("is", $produto_id, $fotos[$i]);
                    $stmtFotos->execute();
                }
                $stmtFotos->close();
            }
        }

        return "Produto adicionado com sucesso";
    }

    function atualizarProduto($id, $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $fotos = []) {
        global $conn;

        // Se houver novas fotos, atualizar a foto principal
        if (!empty($fotos)) {
            $foto_principal = $fotos[0];
            $sql = "UPDATE Produtos SET nome = ?, tipo_produto_id = ?, preco = ?, stock = ?, marca = ?, tamanho = ?, estado = ?, genero = ?, descricao = ?, foto = ? WHERE Produto_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sidissssssi", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $foto_principal, $id);
        } else {
            // Sem novas fotos, atualizar apenas outros campos
            $sql = "UPDATE Produtos SET nome = ?, tipo_produto_id = ?, preco = ?, stock = ?, marca = ?, tamanho = ?, estado = ?, genero = ?, descricao = ? WHERE Produto_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sidisssssi", $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $id);
        }

        $stmt->execute();
        $stmt->close();

        // Se houver múltiplas fotos novas, atualizar tabela de fotos adicionais
        if (count($fotos) > 1) {
            // Remover fotos antigas
            $sqlDelete = "DELETE FROM Produto_Fotos WHERE produto_id = ?";
            $stmtDelete = $conn->prepare($sqlDelete);
            if ($stmtDelete) {
                $stmtDelete->bind_param("i", $id);
                $stmtDelete->execute();
                $stmtDelete->close();
            }

            // Adicionar novas fotos
            $sqlFotos = "INSERT INTO Produto_Fotos (produto_id, foto) VALUES (?, ?)";
            $stmtFotos = $conn->prepare($sqlFotos);
            if ($stmtFotos) {
                for ($i = 1; $i < count($fotos); $i++) {
                    $stmtFotos->bind_param("is", $id, $fotos[$i]);
                    $stmtFotos->execute();
                }
                $stmtFotos->close();
            }
        }

        return "Produto atualizado com sucesso";
    }

    function getReceitaTotal($ID_User, $periodo = 'all') {
        global $conn;
        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT SUM(valor) AS total FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $stmt->close();

        return $total;
    }

    function getTotalPedidos($ID_User, $periodo = 'all') {
        global $conn;
        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT COUNT(*) AS total FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (int)($row['total'] ?? 0);
        $stmt->close();

        return $total;
    }

    function getTicketMedio($ID_User, $periodo = 'all') {
        global $conn;
        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT SUM(valor) AS total, COUNT(*) AS quantidade FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = (float)($row['total'] ?? 0);
        $quantidade = (int)($row['quantidade'] ?? 0);
        $ticket = $quantidade > 0 ? $total / $quantidade : 0;
        $stmt->close();

        return round($ticket, 2);
    }

    function getMargemLucroGeral($ID_User, $periodo = 'all') {
        global $conn;
        $filtroData = "";

        if ($periodo == 'month') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $filtroData = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }

        $sql = "SELECT (SUM(lucro) / SUM(valor)) * 100 AS margem FROM Vendas WHERE anunciante_id = ?" . $filtroData;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $margem = (float)($row['margem'] ?? 0);
        $stmt->close();

        return round($margem, 2);
    }

    function getVendasPorCategoria($ID_User, $periodo = 'all') {
        global $conn;
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
        $stmt = $conn->prepare($sql);
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
        return $dados;
    }

    function getReceitaDiaria($ID_User, $periodo = 'all') {
        global $conn;
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

        $stmt = $conn->prepare($sql);
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
        return $dados;
    }

    function getRelatoriosProdutos($ID_User, $periodo = 'all') {
        global $conn;
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
        $stmt = $conn->prepare($sql);
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
        return $dados;
    }



    function getDadosPerfil($ID_User) {
        global $conn;

        $sql = "SELECT u.id, u.nome, u.email, u.nif, u.telefone, u.morada, u.foto, u.pontos_conf, u.plano_id,
                       r.nome AS ranking_nome, r.pontos AS ranking_pontos_atuais,
                       p.nome AS plano_nome, p.preco AS plano_preco, p.limite_produtos AS plano_limite,
                       COUNT(DISTINCT pr.Produto_id) AS total_produtos
                FROM Utilizadores u
                LEFT JOIN Ranking r ON u.ranking_id = r.id
                LEFT JOIN Planos p ON u.plano_id = p.id
                LEFT JOIN Produtos pr ON pr.anunciante_id = u.id AND pr.ativo = 1
                WHERE u.id = ?
                GROUP BY u.id, u.nome, u.email, u.nif, u.telefone, u.morada, u.foto, u.pontos_conf, u.plano_id,
                         r.nome, r.pontos, p.nome, p.preco, p.limite_produtos";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Obter próximo ranking
            $sqlProximo = "SELECT nome, pontos FROM Ranking WHERE pontos > ? ORDER BY pontos ASC LIMIT 1";
            $stmtProximo = $conn->prepare($sqlProximo);
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
            return json_encode($row);
        }

        $stmt->close();
        return json_encode(['error' => 'Utilizador não encontrado']);
    }

    function atualizarPerfil($ID_User, $nome, $email, $telefone = null, $nif = null, $morada = null) {
        global $conn;

        // Validações
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

        // Verificar se email já existe (exceto o próprio utilizador)
        $sqlCheck = "SELECT id FROM utilizadores WHERE email = ? AND id != ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("si", $email, $ID_User);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $stmtCheck->close();
            return json_encode(['success' => false, 'message' => 'Email já está em uso']);
        }
        $stmtCheck->close();

        // Atualizar com todos os campos incluindo morada
        $sql = "UPDATE utilizadores SET nome = ?, email = ?, nif = ?, telefone = ?, morada = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $email, $nif, $telefone, $morada, $ID_User);

        if ($stmt->execute()) {
            $stmt->close();
            return json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso']);
        }

        $stmt->close();
        return json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil']);
    }

    function atualizarFotoPerfil($ID_User, $foto) {
        global $conn;

        $targetDir = "src/img/";
        $fileName = time() . '_' . basename($foto["name"]);
        $targetFile = $targetDir . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Verificar se é imagem
        $check = getimagesize($foto["tmp_name"]);
        if ($check === false) {
            return json_encode(['success' => false, 'message' => 'Ficheiro não é uma imagem']);
        }

        // Verificar tamanho (max 5MB)
        if ($foto["size"] > 5000000) {
            return json_encode(['success' => false, 'message' => 'Ficheiro muito grande (máx 5MB)']);
        }

        // Permitir apenas certos formatos
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return json_encode(['success' => false, 'message' => 'Apenas JPG, JPEG, PNG, GIF e WEBP são permitidos']);
        }

        if (move_uploaded_file($foto["tmp_name"], $targetFile)) {
            $sql = "UPDATE utilizadores SET foto = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $targetFile, $ID_User);

            if ($stmt->execute()) {
                $stmt->close();
                return json_encode(['success' => true, 'message' => 'Foto atualizada com sucesso', 'foto' => $targetFile]);
            }
            $stmt->close();
        }

        return json_encode(['success' => false, 'message' => 'Erro ao fazer upload da foto']);
    }

    function alterarPassword($ID_User, $senha_atual, $senha_nova) {
        global $conn;

        // Verificar senha atual
        $sql = "SELECT password FROM utilizadores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Comparar senha (assumindo que está armazenada em texto plano - NOTA: deveria usar password_hash)
            if ($row['password'] !== $senha_atual) {
                $stmt->close();
                return json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
            }

            // Atualizar senha
            $sqlUpdate = "UPDATE utilizadores SET password = ? WHERE id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
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
    }

function getEncomendas($ID_User) {
        global $conn;

        $sql = "SELECT
                    e.id,
                    e.codigo_encomenda,
                    e.data_envio,
                    e.estado,
                    e.morada,
                    u.nome AS cliente_nome,
                    u.email AS cliente_email,
                    p.nome AS produto_nome,
                    p.foto AS produto_foto,
                    v.quantidade,
                    v.valor,
                    t.nome AS transportadora_nome
                FROM Encomendas e
                INNER JOIN Utilizadores u ON e.cliente_id = u.id
                LEFT JOIN Produtos p ON e.produto_id = p.Produto_id
                LEFT JOIN Vendas v ON e.id = v.encomenda_id
                LEFT JOIN Transportadora t ON e.transportadora_id = t.id
                WHERE e.anunciante_id = ?
                ORDER BY e.data_envio DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        $encomendas = [];
        while ($row = $result->fetch_assoc()) {
            $encomendas[] = [
                'id' => (int)$row['id'],
                'codigo' => $row['codigo_encomenda'],
                'data' => date('d/m/Y', strtotime($row['data_envio'])),
                'data_completa' => $row['data_envio'],
                'estado' => $row['estado'],
                'morada' => $row['morada'],
                'cliente_nome' => $row['cliente_nome'],
                'cliente_email' => $row['cliente_email'],
                'produto_nome' => $row['produto_nome'],
                'produto_foto' => $row['produto_foto'],
                'quantidade' => (int)$row['quantidade'],
                'valor' => (float)$row['valor'],
                'transportadora' => $row['transportadora_nome']
            ];
        }

        $stmt->close();

        return json_encode($encomendas);
    }

    function atualizarStatusEncomenda($encomenda_id, $novo_estado, $observacao = '') {
        global $conn;

        // Atualiza o estado na tabela Encomendas
        $sql = "UPDATE Encomendas SET estado = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $novo_estado, $encomenda_id);

        if (!$stmt->execute()) {
            $stmt->close();
            return json_encode(['success' => false, 'message' => 'Erro ao atualizar status']);
        }
        $stmt->close();

        // Registra no histórico
        $descricao = empty($observacao) ? "Status alterado para: $novo_estado" : $observacao;
        $sqlHist = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao) VALUES (?, ?, ?)";
        $stmtHist = $conn->prepare($sqlHist);
        $stmtHist->bind_param("iss", $encomenda_id, $novo_estado, $descricao);
        $stmtHist->execute();
        $stmtHist->close();

        return json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
    }

    function getHistoricoEncomenda($encomenda_id) {
        global $conn;

        $sql = "SELECT estado_encomenda, descricao, data_atualizacao
                FROM Historico_Produtos
                WHERE encomenda_id = ?
                ORDER BY data_atualizacao ASC";

        $stmt = $conn->prepare($sql);
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
    }

}
?>
