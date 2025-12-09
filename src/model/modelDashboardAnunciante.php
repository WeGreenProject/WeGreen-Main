<?php

require_once 'connection.php';

class DashboardAnunciante {

    function getDadosPlanos($ID_User, $plano) {
        global $conn;
        $msg = "";

        $sql = "SELECT * FROM Utilizadores WHERE id = $ID_User";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ($plano == 1) {
                    $msg  = "<div class='stat-icon'><i class='fas fa-crown'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='stat-value'>Free</div>";
                    $msg .= "</div>";
                } else if ($plano == 2) {
                    $msg  = "<div class='stat-icon'><i class='fas fa-crown'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='stat-value'>Premium</div>";
                    $msg .= "</div>";
                } else if ($plano == 3) {
                    $msg  = "<div class='stat-icon'><i class='fas fa-crown'></i></div>";
                    $msg .= "<div class='stat-content'>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='stat-value'>Enterprise</div>";
                    $msg .= "</div>";
                }
            }
        } else {
            $msg .= "<li><div class='dropdown-header d-flex align-items-center'>";
            $msg .= "<h6 class='mb-0 text-wegreen-accent'>Detectamos um erro na sua conta!</h6>";
            $msg .= "</div></li>";
            $msg .= "<li><a class='dropdown-item' href='login.html'>Mudar de Conta</a></li>";
        }

        $conn->close();
        return $msg;
    }

    function CarregaProdutos($ID_User) {
        global $conn;
        $msg = "";

        $sql = "SELECT COUNT(*) AS StockProdutos FROM Produtos WHERE anunciante_id = $ID_User";
        $result = $conn->query($sql);

        if ($row = $result->fetch_assoc()) {
            $msg  = "<div class='stat-icon'><i class='fas fa-box'></i></div>";
            $msg .= "<div class='stat-content'>";
            $msg .= "<div class='stat-label'>Produtos em Stock</div>";
            $msg .= "<div class='stat-value'>".$row["StockProdutos"]."</div>";
            $msg .= "</div>";
        } else {
            $msg  = "<div class='stat-icon'><i class='fas fa-box'></i></div>";
            $msg .= "<div class='stat-content'>";
            $msg .= "<div class='stat-label'>Produtos em Stock</div>";
            $msg .= "<div class='stat-value'>0</div>";
            $msg .= "</div>";
        }

        $conn->close();
        return $msg;
    }




    function CarregaPontos($ID_User) {
        global $conn;
        $msg = "";

        $sql = "SELECT pontos_conf FROM Utilizadores WHERE id = $ID_User";
        $result = $conn->query($sql);

        if ($row = $result->fetch_assoc()) {
            $msg  = "<div class='stat-icon'><i class='fas fa-star'></i></div>";
            $msg .= "<div class='stat-content'>";
            $msg .= "<div class='stat-label'>Pontos Confiança</div>";
            $msg .= "<div class='stat-value'>".$row['pontos_conf']."</div>";
            $msg .= "</div>";
        } else {
            $msg  = "<div class='stat-icon'><i class='fas fa-star'></i></div>";
            $msg .= "<div class='stat-content'>";
            $msg .= "<div class='stat-label'>Pontos Confiança</div>";
            $msg .= "<div class='stat-value'>0</div>";
            $msg .= "</div>";
        }

        $conn->close();
        return $msg;
    }

    function getEstatisticasProdutos($ID_User) {
        global $conn;

        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN ativo = 1 THEN 1 ELSE 0 END) as ativos,
                    SUM(CASE WHEN ativo = 0 THEN 1 ELSE 0 END) as inativos,
                    SUM(CASE WHEN stock < 5 THEN 1 ELSE 0 END) as stockBaixo
                FROM Produtos
                WHERE anunciante_id = $ID_User";

        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        $stats = array(
            'total' => (int)$row['total'],
            'ativos' => (int)$row['ativos'],
            'inativos' => (int)$row['inativos'],
            'stockBaixo' => (int)$row['stockBaixo']
        );

        return json_encode($stats);
    }

    function getGastos($ID_User){
    global $conn;

    $msg = "";

    $sql = "SELECT SUM(gastos.valor) AS TotalGastos FROM gastos WHERE anunciante_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ID_User);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $msg  = "<div class='stat-icon'><i class='fas fa-wallet'></i></div>";
        $msg .= "<div class='stat-content'>";
        $msg .= "<div class='stat-label'>Gastos Totais</div>";
        $msg .= "<div class='stat-value'>€".number_format($row['TotalGastos'], 2, ',', '.')."</div>";
        $msg .= "</div>";

    } else {

        $msg  = "<div class='stat-icon'><i class='fas fa-wallet'></i></div>";
        $msg .= "<div class='stat-content'>";
        $msg .= "<div class='stat-label'>Gastos Totais</div>";
        $msg .= "<div class='stat-value'>€0,00</div>";
        $msg .= "</div>";

    }

    return $msg;
}


    function getVendasMensais($ID_User) {
        global $conn;
        $dados = array_fill(1, 12, 0);
        $sql = "SELECT MONTH(data_venda) AS mes, SUM(valor) AS total
                FROM Vendas
                WHERE anunciante_id = $ID_User
                GROUP BY MONTH(data_venda)";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $dados[(int)$row['mes']] = (float)$row['total'];
        }

        $conn->close();
        return $dados;
    }

    function getTopProdutos($ID_User) {
        global $conn;
        $dados = [];

        $sql = "SELECT p.nome, SUM(v.quantidade) AS vendidos
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = $ID_User
                GROUP BY v.produto_id
                ORDER BY vendidos DESC
                LIMIT 5";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $dados[] = ['nome' => $row['nome'], 'vendidos' => (int)$row['vendidos']];
        }

        $conn->close();
        return $dados;
    }

    function getLucroPorProduto($ID_User) {
        global $conn;
        $dados = [];

        $sql = "SELECT p.nome, SUM(v.lucro) AS lucro_total
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = $ID_User
                GROUP BY v.produto_id";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $dados[] = ['nome' => $row['nome'], 'lucro' => (float)$row['lucro_total']];
        }

        $conn->close();
        return $dados;
    }

    function getMargemLucro($ID_User) {
        global $conn;
        $dados = [];

        $sql = "SELECT p.nome, SUM(v.lucro) AS lucro, SUM(v.valor) AS total_vendas
                FROM Vendas v
                JOIN Produtos p ON v.produto_id = p.Produto_id
                WHERE v.anunciante_id = $ID_User
                GROUP BY v.produto_id";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $margem = $row['total_vendas'] != 0 ? ($row['lucro'] / $row['total_vendas']) * 100 : 0;
            $dados[] = ['nome' => $row['nome'], 'margem' => round($margem, 2)];
        }

        $conn->close();
        return $dados;
    }

    function getProdutosRecentes($ID_User) {
        global $conn;
        $html = "";

        $sql = "SELECT * FROM Produtos
                WHERE anunciante_id = $ID_User
                ORDER BY data_criacao DESC
                LIMIT 5";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $html .= "<div class='product-info-row'>
                        <div style='display: flex; align-items: center; gap: 15px;'>
                            <span style='font-size: 30px;'>👕</span>
                            <div>
                                <div style='font-weight: 600; color: #fff;'>".$row['nome']."</div>
                                <div style='color: #888; font-size: 14px;'>".$row['tamanho']." em stock</div>
                            </div>
                        </div>
                        <div style='text-align: right;'>
                            <div style='color: #ffd700; font-weight: 600;'>€".$row['preco']."</div>
                        </div>
                      </div>";
        }

        $conn->close();
        return $html;
    }

    function getTodosProdutos($ID_User) {
        global $conn;
        $produtos = [];

        $sql = "SELECT p.*, t.descricao as tipo_descricao
                FROM Produtos p
                LEFT JOIN Tipo_Produtos t ON p.tipo_produto_id = t.id
                WHERE p.anunciante_id = $ID_User
                ORDER BY p.data_criacao DESC";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        $conn->close();
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

        $conn->close();
        return json_encode($tipos);
    }

    function getLimiteProdutos($ID_User) {
        global $conn;
        $sql = "SELECT p.limite_produtos, COUNT(pr.Produto_id) as current
                FROM Utilizadores u
                JOIN Planos p ON u.plano_id = p.id
                LEFT JOIN Produtos pr ON pr.anunciante_id = u.id
                WHERE u.id = $ID_User
                GROUP BY u.id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

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

        return json_encode($produto);
    }

    function deleteProduto($id) {
        global $conn;
        $sql = "DELETE FROM Produtos WHERE Produto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

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
        return $resultado;
    }

    function updateProduto($id, $nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao) {
        global $conn;
        $sql = "UPDATE Produtos SET nome='$nome', tipo_produto_id=$tipo_produto_id, preco=$preco, stock=$stock, marca='$marca', tamanho='$tamanho', estado='$estado', genero='$genero', descricao='$descricao' WHERE Produto_id=$id";
        $conn->query($sql);

        $conn->close();
        return "Produto atualizado";
    }

    function insertProduto($nome, $tipo_produto_id, $preco, $stock, $marca, $tamanho, $estado, $genero, $descricao, $anunciante_id) {
        global $conn;
        $sql = "INSERT INTO Produtos (nome, tipo_produto_id, preco, stock, marca, tamanho, estado, genero, descricao, anunciante_id) VALUES ('$nome', $tipo_produto_id, $preco, $stock, '$marca', '$tamanho', '$estado', '$genero', '$descricao', $anunciante_id)";
        $conn->query($sql);

        $conn->close();
        return "Produto adicionado";
    }

    function getReceitaTotal($ID_User, $periodo = 'all') {
        global $conn;
        $dateFilter = "";
        if ($periodo == 'month') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }
        $sql = "SELECT SUM(valor) AS total FROM Vendas WHERE anunciante_id = ?" . $dateFilter;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = $row['total'] ?? 0;
        $stmt->close();
        return (float)$total;
    }

    function getTotalPedidos($ID_User, $periodo = 'all') {
        global $conn;
        $dateFilter = "";
        if ($periodo == 'month') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }
        $sql = "SELECT COUNT(*) AS total FROM Vendas WHERE anunciante_id = ?" . $dateFilter;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = $row['total'] ?? 0;
        $stmt->close();
        return (int)$total;
    }

    function getTicketMedio($ID_User, $periodo = 'all') {
        global $conn;
        $dateFilter = "";
        if ($periodo == 'month') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }
        $sql = "SELECT SUM(valor) AS total, COUNT(*) AS count FROM Vendas WHERE anunciante_id = ?" . $dateFilter;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total = $row['total'] ?? 0;
        $count = $row['count'] ?? 0;
        $ticket = $count > 0 ? $total / $count : 0;
        $stmt->close();
        return round((float)$ticket, 2);
    }

    function getMargemLucroGeral($ID_User, $periodo = 'all') {
        global $conn;
        $dateFilter = "";
        if ($periodo == 'month') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $dateFilter = " AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }
        $sql = "SELECT (SUM(lucro) / SUM(valor)) * 100 AS margem FROM Vendas WHERE anunciante_id = ?" . $dateFilter;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $margem = $row['margem'] ?? 0;
        $stmt->close();
        return round((float)$margem, 2);
    }

    function getVendasPorCategoria($ID_User, $periodo = 'all') {
        global $conn;
        $dados = [];
        $dateFilter = "";
        if ($periodo == 'month') {
            $dateFilter = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $dateFilter = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }
        $sql = "SELECT tp.descricao AS categoria, SUM(v.quantidade) AS vendas, SUM(v.valor) AS receita FROM Vendas v JOIN Produtos p ON v.produto_id = p.Produto_id JOIN Tipo_Produtos tp ON p.tipo_produto_id = tp.id WHERE v.anunciante_id = ?" . $dateFilter . " GROUP BY tp.id ORDER BY receita DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $dados[] = ['categoria' => $row['categoria'], 'vendas' => (int)$row['vendas'], 'receita' => (float)$row['receita']];
        }
        $stmt->close();
        return $dados;
    }

    function getReceitaDiaria($ID_User, $periodo = 'all') {
        global $conn;
        $dados = [];

        if ($periodo == 'month') {
            // Dados diários dos últimos 30 dias
            $sql = "SELECT DATE(data_venda) AS data, SUM(valor) AS receita FROM Vendas WHERE anunciante_id = ? AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(data_venda) ORDER BY data ASC";
        } elseif ($periodo == 'year') {
            // Dados mensais dos últimos 12 meses
            $sql = "SELECT DATE_FORMAT(data_venda, '%Y-%m') AS data, SUM(valor) AS receita FROM Vendas WHERE anunciante_id = ? AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(data_venda, '%Y-%m') ORDER BY data ASC";
        } else {
            // Dados mensais de todo o período
            $sql = "SELECT DATE_FORMAT(data_venda, '%Y-%m') AS data, SUM(valor) AS receita FROM Vendas WHERE anunciante_id = ? GROUP BY DATE_FORMAT(data_venda, '%Y-%m') ORDER BY data ASC";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $dados[] = ['data' => $row['data'], 'receita' => (float)$row['receita']];
        }
        $stmt->close();
        return $dados;
    }

    function getRelatoriosProdutos($ID_User, $periodo = 'all') {
        global $conn;
        $dados = [];
        $dateFilter = "";
        if ($periodo == 'month') {
            $dateFilter = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        } elseif ($periodo == 'year') {
            $dateFilter = " AND v.data_venda >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        }
        $sql = "SELECT p.nome AS produto, SUM(v.quantidade) AS vendas, SUM(v.valor) AS receita, SUM(v.lucro) AS lucro FROM Vendas v JOIN Produtos p ON v.produto_id = p.Produto_id WHERE v.anunciante_id = ?" . $dateFilter . " GROUP BY p.Produto_id ORDER BY receita DESC LIMIT 10";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $dados[] = ['produto' => $row['produto'], 'vendas' => (int)$row['vendas'], 'receita' => (float)$row['receita'], 'lucro' => (float)$row['lucro']];
        }
        $stmt->close();
        return $dados;
    }

    // ========================
    // FUNÇÕES DE PERFIL
    // ========================

    function getDadosPerfil($ID_User) {
        global $conn;

        $sql = "SELECT u.id, u.nome, u.email, u.nif, u.telefone, u.morada, u.foto, u.pontos_conf, u.plano_id,
                       r.nome AS ranking_nome, r.pontos AS ranking_pontos_necessarios,
                       p.nome AS plano_nome, p.preco AS plano_preco, p.limite_produtos AS plano_limite,
                       COUNT(DISTINCT pr.Produto_id) AS total_produtos
                FROM utilizadores u
                LEFT JOIN ranking r ON u.ranking_id = r.id
                LEFT JOIN planos p ON u.plano_id = p.id
                LEFT JOIN produtos pr ON pr.anunciante_id = u.id
                WHERE u.id = ?
                GROUP BY u.id";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ID_User);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            $conn->close();
            return json_encode($row);
        }

        $stmt->close();
        $conn->close();
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
            $conn->close();
            return json_encode(['success' => false, 'message' => 'Email já está em uso']);
        }
        $stmtCheck->close();

        // Atualizar com todos os campos incluindo morada
        $sql = "UPDATE utilizadores SET nome = ?, email = ?, nif = ?, telefone = ?, morada = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $email, $nif, $telefone, $morada, $ID_User);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso']);
        }

        $stmt->close();
        $conn->close();
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
                $conn->close();
                return json_encode(['success' => true, 'message' => 'Foto atualizada com sucesso', 'foto' => $targetFile]);
            }
            $stmt->close();
        }

        $conn->close();
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
                $conn->close();
                return json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
            }

            // Atualizar senha
            $sqlUpdate = "UPDATE utilizadores SET password = ? WHERE id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $senha_nova, $ID_User);

            if ($stmtUpdate->execute()) {
                $stmtUpdate->close();
                $stmt->close();
                $conn->close();
                return json_encode(['success' => true, 'message' => 'Senha alterada com sucesso']);
            }
            $stmtUpdate->close();
        }

        $stmt->close();
        $conn->close();
        return json_encode(['success' => false, 'message' => 'Erro ao alterar senha']);
    }

}
?>
