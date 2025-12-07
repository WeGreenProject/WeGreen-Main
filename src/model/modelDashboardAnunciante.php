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
                    $msg  = "<div class='stat-icon'>⭐</div>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='plan-badge'>Free</div>";
                    $msg .= "<div class='stat-change'>Plano Atual Infinito</div>";
                } else if ($plano == 2) {
                    $msg  = "<div class='stat-icon'>⭐</div>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='plan-badge'>Premium</div>";
                    $msg .= "<div class='stat-change'>Renovação em 23 dias</div>";
                } else if ($plano == 3) {
                    $msg  = "<div class='stat-icon'>⭐</div>";
                    $msg .= "<div class='stat-label'>Plano Atual</div>";
                    $msg .= "<div class='plan-badge'>EnterPrise</div>";
                    $msg .= "<div class='stat-change'>Renovação em 23 dias</div>";
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
            $msg  = "<div class='stat-icon'>📦</div>";
            $msg .= "<div class='stat-label'>Produtos em Stock</div>";
            $msg .= "<div class='stat-value'>".$row["StockProdutos"]."</div>";
            $msg .= "<div class='stat-change'>+3 produtos novos</div>";
        } else {
            $msg  = "<div class='stat-icon'>📦</div>";
            $msg .= "<div class='stat-label'>Produtos em Stock</div>";
            $msg .= "<div class='plan-badge'>Erro a Encontrar Produtos</div>";
            $msg .= "<div class='stat-change'>+3 produtos novos</div>";
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
            $msg  = "<div class='stat-icon'>🎯</div>";
            $msg .= "<div class='stat-label'>Pontos de Confiança</div>";
            $msg .= "<div class='stat-value'>".$row['pontos_conf']."</div>";
            $msg .= "<div class='stat-change'>↑ Baseado nas suas vendas</div>";
        } else {
            $msg  = "<div class='stat-icon'>🎯</div>";
            $msg .= "<div class='stat-label'>Pontos de Confiança</div>";
            $msg .= "<div class='stat-value'>Pontos de Confiança não encontrado!</div>";
            $msg .= "<div class='stat-change'>↑ Baseado nas suas vendas</div>";
        }

        $conn->close();
        return $msg;
    }

    function getGastos(){
    global $conn;

    $msg = "";

    $sql = "SELECT SUM(gastos.valor) AS TotalGastos FROM gastos";
    $result = $conn->query($sql);

    $novos = $this->getNovosGastos();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        $msg  = "<div class='stat-icon'>💸</div>";
        $msg .= "<div class='stat-label'>Gastos Totais</div>";
        $msg .= "<div class='stat-value'>".$row['TotalGastos']."€</div>";
        $msg .= "<div class='stat-change'>+ ".$novos."€ gastos recentes</div>";

    } else {

        $msg  = "<div class='stat-icon'>💸</div>";
        $msg .= "<div class='stat-label'>Gastos Totais</div>";
        $msg .= "<div class='stat-value'>Nenhum gasto encontrado</div>";

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
                JOIN Produtos p ON v.produto_id = p.id
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
                JOIN Produtos p ON v.produto_id = p.id
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
                JOIN Produtos p ON v.produto_id = p.id
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
                LEFT JOIN Produtos pr ON pr.anunciante_id = u.id AND pr.ativo = 1
                WHERE u.id = $ID_User
                GROUP BY u.id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        $conn->close();
        return json_encode(['max' => $row['limite_produtos'] ?? 0, 'current' => $row['current'] ?? 0]);
    }

    function getProdutoById($id) {
        global $conn;
        $sql = "SELECT * FROM Produtos WHERE Produto_id = $id";
        $result = $conn->query($sql);
        $produto = $result->fetch_assoc();

        $conn->close();
        return json_encode($produto);
    }

    function deleteProduto($id) {
        global $conn;
        $sql = "DELETE FROM Produtos WHERE Produto_id = $id";
        $conn->query($sql);

        $conn->close();
        return "Produto removido";
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

}
?>
