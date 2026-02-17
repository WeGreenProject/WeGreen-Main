<?php

require_once 'connection.php';

class Carrinho {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getCarrinho($utilizador_id) {
        try {

        $msg = "";

        if ($utilizador_id === null || $utilizador_id === '') {
            $msg .= "<div id='emptyCart' class='empty-cart'>";
            $msg .= "<div class='empty-icon'>🛍️</div>";
            $msg .= "<h3>O carrinho está vazio</h3>";
            $msg .= "<a href='index.html' class='btn btn-primary'>Ir às Compras</a>";
            $msg .= "</div>";
            return $msg;
        }

        $sql = "SELECT
                    Produtos.Produto_id,
                    Produtos.nome,
                    Produtos.preco,
                    Produtos.foto,
                    Produtos.marca,
                    Produtos.tamanho,
                    Produtos.estado,
                    Carrinho_Itens.quantidade
                FROM Carrinho_Itens
                INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                WHERE Carrinho_Itens.utilizador_id = ?
                AND Produtos.ativo = 1
                ORDER BY Carrinho_Itens.data_adicao DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $msg .= "<div class='section-header'>";
            $msg .= "<h3>Produtos no Carrinho</h3>";
            $msg .= "<button class='btn-text' onclick='limparCarrinho()'>Limpar Tudo</button>";
            $msg .= "</div>";

            while ($row = $result->fetch_assoc()) {
                $msg .= "<div class='cart-item'>";
                $msg .= "<div class='item-image'>";
                $msg .= "<img src='".$row["foto"]."' alt='".$row["nome"]."'>";
                $msg .= "</div>";

                $msg .= "<div class='item-details'>";
                $msg .= "<h4>".$row["nome"]."</h4>";
                $msg .= "<p class='item-meta'>".$row["marca"]." · ".$row["tamanho"]." · ".$row["estado"]."</p>";
                $msg .= "<p class='item-price'>€".$row["preco"]."</p>";
                $msg .= "</div>";

                $msg .= "<div class='item-actions'>";
                $msg .= "<div class='quantity-control'>";
                $msg .= "<button onclick='atualizarQuantidade(".$row["Produto_id"].", -1)'>-</button>";
                $msg .= "<span>".$row["quantidade"]."</span>";
                $msg .= "<button onclick='atualizarQuantidade(".$row["Produto_id"].", 1)'>+</button>";
                $msg .= "</div>";
                $msg .= "<button class='btn-remove' onclick='removerDoCarrinho(".$row["Produto_id"].")'>";
                $msg .= "<i class='bi bi-trash'></i>";
                $msg .= "</button>";
                $msg .= "</div>";

                $msg .= "</div>";
            }
        } else {
            $msg .= "<div id='emptyCart' class='empty-cart'>";
            $msg .= "<div class='empty-icon'>🛍️</div>";
            $msg .= "<h3>O carrinho está vazio</h3>";
            $msg .= "<a href='index.html' class='btn btn-primary'>Ir às Compras</a>";
            $msg .= "</div>";
        }

        return $msg;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getResumoPedido() {
        try {

        $msg = "";
        $subtotal = 0;
        $shipping = 5.00;

        $utilizador_id = $this->obterOuCriarUtilizadorTemporario();

        if ($utilizador_id === null || $utilizador_id === '') {
            return "";
        }

        $sql = "SELECT
                    Produtos.preco,
                    Carrinho_Itens.quantidade
                FROM Carrinho_Itens
                INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                WHERE Carrinho_Itens.utilizador_id = ?
                AND Produtos.ativo = 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $subtotal += $row["preco"] * $row["quantidade"];
            }
        }

        $total = $subtotal + $shipping;

        $msg .= "<div class='summary-card'>";
        $msg .= "<h3>Resumo do Pedido</h3>";

        $msg .= "<div class='summary-row'>";
        $msg .= "<span>Subtotal:</span>";
        $msg .= "<span id='subtotal'>€".number_format($subtotal, 2)."</span>";
        $msg .= "</div>";

        $msg .= "<div class='summary-row'>";
        $msg .= "<span>Envio:</span>";
        $msg .= "<span id='shipping'>€".number_format($shipping, 2)."</span>";
        $msg .= "</div>";

        $msg .= "<div class='summary-row highlight'>";
        $msg .= "<span>Total:</span>";
        $msg .= "<span id='total'>€".number_format($total, 2)."</span>";
        $msg .= "</div>";
        $msg .= "</div>";
        $msg .= "</div>";

        $msg .= "<div class='AplicarCupao'>";
        $msg .= "<h4>Tem um cupão?</h4>";
        $msg .= "<div class='coupon-input'>";
        $msg .= "<input type='text' id='couponCode' class='form-control' placeholder='Código do cupão'>";
        $msg .= "<button onclick='aplicarCupao()'>Aplicar</button>";
        $msg .= "</div>";
        $msg .= "</div>";

        return $msg;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function adicionarAoCarrinho($produto_id, $quantidade = 1) {
        try {

        $utilizador_id = $this->obterOuCriarUtilizadorTemporario();

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        $stmt_prod = $this->conn->prepare("SELECT Produto_id, stock FROM Produtos WHERE Produto_id = ? AND ativo = 1");
        $stmt_prod->bind_param("i", $produto_id);
        $stmt_prod->execute();
        $result_produto = $stmt_prod->get_result();

        if ($result_produto->num_rows == 0) {
            return "Erro: Produto não encontrado ou inativo";
        }

        $row_prod = $result_produto->fetch_assoc();
        if ((int)$row_prod['stock'] <= 0) {
            return "Erro: Produto esgotado";
        }

        $stmt_check = $this->conn->prepare("SELECT * FROM Carrinho_Itens WHERE utilizador_id = ? AND produto_id = ?");
        $stmt_check->bind_param("ii", $utilizador_id, $produto_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if (!$result) {
            return "Erro: " . $this->conn->error;
        }

        if ($result->num_rows > 0) {
            $stmt_up = $this->conn->prepare("UPDATE Carrinho_Itens SET quantidade = quantidade + ? WHERE utilizador_id = ? AND produto_id = ?");
            $stmt_up->bind_param("iii", $quantidade, $utilizador_id, $produto_id);
            if (!$stmt_up->execute()) {
                return "Erro: " . $this->conn->error;
            }
        } else {
            $stmt_ins = $this->conn->prepare("INSERT INTO Carrinho_Itens (utilizador_id, produto_id, quantidade) VALUES (?, ?, ?)");
            if (!$stmt_ins) {
                return "Erro: " . $this->conn->error;
            }
            $stmt_ins->bind_param("iii", $utilizador_id, $produto_id, $quantidade);
            if (!$stmt_ins->execute()) {
                return "Erro: " . $this->conn->error;
            }
        }

        return "Produto adicionado ao carrinho";
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function atualizarQuantidade($produto_id, $mudanca) {
        try {

        $utilizador_id = $this->obterOuCriarUtilizadorTemporario();

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        if ($mudanca > 0) {
            $stmt_stock = $this->conn->prepare("SELECT Produtos.stock, Carrinho_Itens.quantidade
                          FROM Carrinho_Itens
                          INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                          WHERE Carrinho_Itens.utilizador_id = ?
                          AND Carrinho_Itens.produto_id = ?");
            $stmt_stock->bind_param("ii", $utilizador_id, $produto_id);
            $stmt_stock->execute();
            $result_stock = $stmt_stock->get_result();

            if ($result_stock && $result_stock->num_rows > 0) {
                $row_stock = $result_stock->fetch_assoc();
                $stock_disponivel = $row_stock['stock'];
                $quantidade_atual = $row_stock['quantidade'];

                if ($quantidade_atual >= $stock_disponivel) {
                    return "Erro: Stock insuficiente. Disponível: " . $stock_disponivel;
                }
            }
        }

        $stmt_qty = $this->conn->prepare("SELECT quantidade FROM Carrinho_Itens WHERE utilizador_id = ? AND produto_id = ?");
        $stmt_qty->bind_param("ii", $utilizador_id, $produto_id);
        $stmt_qty->execute();
        $result = $stmt_qty->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nova_quantidade = $row['quantidade'] + $mudanca;

            if ($nova_quantidade <= 0) {
                $stmt_del = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ? AND produto_id = ?");
                $stmt_del->bind_param("ii", $utilizador_id, $produto_id);
                $stmt_del->execute();
            } else {
                $stmt_upd = $this->conn->prepare("UPDATE Carrinho_Itens SET quantidade = ? WHERE utilizador_id = ? AND produto_id = ?");
                $stmt_upd->bind_param("iii", $nova_quantidade, $utilizador_id, $produto_id);
                $stmt_upd->execute();
            }

            return "Quantidade atualizada";
        }

        return "Produto não encontrado no carrinho";
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function removerDoCarrinho($produto_id) {
        try {

        $utilizador_id = $this->obterOuCriarUtilizadorTemporario();

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        $stmt = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ? AND produto_id = ?");
        $stmt->bind_param("ii", $utilizador_id, $produto_id);

        if ($stmt->execute()) {
            return "Produto removido do carrinho";
        } else {
            return "Erro: " . $this->conn->error;
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function limparCarrinho() {
        try {

        $utilizador_id = $this->obterOuCriarUtilizadorTemporario();

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        $stmt = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ?");
        $stmt->bind_param("i", $utilizador_id);

        if ($stmt->execute()) {
            return "Carrinho limpo com sucesso";
        } else {
            return "Erro: " . $this->conn->error;
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function transferirCarrinhoTemporario($temp_user_id, $user_id_real) {
        try {

        $stmt_check = $this->conn->prepare("SELECT * FROM Carrinho_Itens WHERE utilizador_id = ?");
        $stmt_check->bind_param("i", $temp_user_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $produto_id = $row['produto_id'];
                $quantidade_temp = $row['quantidade'];

                $stmt_existe = $this->conn->prepare("SELECT quantidade FROM Carrinho_Itens WHERE utilizador_id = ? AND produto_id = ?");
                $stmt_existe->bind_param("ii", $user_id_real, $produto_id);
                $stmt_existe->execute();
                $result_existe = $stmt_existe->get_result();

                if ($result_existe->num_rows > 0) {
                    $row_existe = $result_existe->fetch_assoc();
                    $nova_quantidade = $row_existe['quantidade'] + $quantidade_temp;

                    $stmt_update = $this->conn->prepare("UPDATE Carrinho_Itens SET quantidade = ? WHERE utilizador_id = ? AND produto_id = ?");
                    $stmt_update->bind_param("iii", $nova_quantidade, $user_id_real, $produto_id);
                    $stmt_update->execute();
                } else {
                    $stmt_transfer = $this->conn->prepare("UPDATE Carrinho_Itens SET utilizador_id = ? WHERE utilizador_id = ? AND produto_id = ?");
                    $stmt_transfer->bind_param("iii", $user_id_real, $temp_user_id, $produto_id);
                    $stmt_transfer->execute();
                }
            }

            $stmt_delete = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ?");
            $stmt_delete->bind_param("i", $temp_user_id);
            $stmt_delete->execute();
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function gerarIdTemporarioCarrinho($seed) {
        $hashUnsigned = (int) sprintf('%u', crc32((string) $seed));
        $idNormalizado = $hashUnsigned % 2000000000;

        if ($idNormalizado <= 0) {
            $idNormalizado = 1;
        }

        return -1 * $idNormalizado;
    }

    function obterOuCriarUtilizadorTemporario() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $utilizadorAutenticado = $_SESSION['utilizador'] ?? null;
            if (is_numeric($utilizadorAutenticado) && (int)$utilizadorAutenticado > 0) {
                return (int)$utilizadorAutenticado;
            }

            $utilizadorTemporario = $_SESSION['temp_user_id'] ?? null;
            if (is_numeric($utilizadorTemporario) && (int)$utilizadorTemporario < 0) {
                return (int)$utilizadorTemporario;
            }

            $seedSessao = session_id();
            if (empty($seedSessao)) {
                $seedSessao = (string) microtime(true);
            }

            $novoIdTemporario = $this->gerarIdTemporarioCarrinho($seedSessao);
            $_SESSION['temp_user_id'] = $novoIdTemporario;

            return $novoIdTemporario;
        } catch (Exception $e) {
            return null;
        }
    }

    function aplicarCupao($codigo) {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $stmt = $this->conn->prepare("SELECT * FROM Cupoes WHERE codigo = ? AND ativo = 1 AND data_expiracao >= CURDATE() AND (limite_uso IS NULL OR usos_atuais < limite_uso)");
            $stmt->bind_param("s", $codigo);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $cupao = $result->fetch_assoc();
                $_SESSION['cupao'] = $cupao;
                return json_encode(['flag' => true, 'msg' => 'Cupão aplicado com sucesso', 'desconto' => $cupao['desconto']], JSON_UNESCAPED_UNICODE);
            }
            return json_encode(['flag' => false, 'msg' => 'Cupão inválido ou expirado'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao aplicar cupão'], JSON_UNESCAPED_UNICODE);
        }
    }

    function removerCupao() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            unset($_SESSION['cupao']);
            return json_encode(['flag' => true, 'msg' => 'Cupão removido com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao remover cupão'], JSON_UNESCAPED_UNICODE);
        }
    }

    function temProdutosNoCarrinho($utilizador_id) {
        try {
            if (!$utilizador_id) {
                return json_encode(['flag' => false, 'tem_produtos' => false], JSON_UNESCAPED_UNICODE);
            }
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM Carrinho_Itens WHERE utilizador_id = ?");
            $stmt->bind_param("i", $utilizador_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return json_encode(['flag' => true, 'tem_produtos' => $row['total'] > 0, 'total' => $row['total']], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'tem_produtos' => false], JSON_UNESCAPED_UNICODE);
        }
    }

    function getDadosCarrinhoJSON($utilizador_id) {
        try {
            if (!$utilizador_id) {
                return json_encode(['flag' => false, 'produtos' => []], JSON_UNESCAPED_UNICODE);
            }
                $sql = "SELECT p.Produto_id, p.nome, p.preco, p.foto, p.marca, p.tamanho, p.estado, p.stock, ci.quantidade, (p.preco * ci.quantidade) AS subtotal
                    FROM Carrinho_Itens ci
                    INNER JOIN Produtos p ON ci.produto_id = p.Produto_id
                    WHERE ci.utilizador_id = ? AND p.ativo = 1
                    ORDER BY ci.data_adicao DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $utilizador_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $produtos = [];
            $total = 0;
            while ($row = $result->fetch_assoc()) {
                $produtos[] = $row;
                $total += $row['subtotal'];
            }
            return json_encode(['flag' => true, 'produtos' => $produtos, 'total' => $total], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'produtos' => []], JSON_UNESCAPED_UNICODE);
        }
    }

    function getDadosUtilizadorCompleto() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['utilizador'])) {
                return json_encode(['flag' => false, 'msg' => 'Utilizador não autenticado'], JSON_UNESCAPED_UNICODE);
            }
            $stmt = $this->conn->prepare("SELECT id, nome, email, morada, codigo_postal, telefone, foto FROM utilizadores WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['utilizador']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                return json_encode(['flag' => true, 'dados' => $user], JSON_UNESCAPED_UNICODE);
            }
            return json_encode(['flag' => false, 'msg' => 'Utilizador não encontrado'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['flag' => false, 'msg' => 'Erro ao obter dados do utilizador'], JSON_UNESCAPED_UNICODE);
        }
    }

}

?>
