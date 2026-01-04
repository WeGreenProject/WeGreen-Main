<?php

require_once 'connection.php';

class Carrinho {

    function getCarrinho($utilizador_id) {
        global $conn;
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
                WHERE Carrinho_Itens.utilizador_id = '$utilizador_id'
                AND Produtos.ativo = 1
                ORDER BY Carrinho_Itens.data_adicao DESC";

        $result = $conn->query($sql);

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
    }

    function getResumoPedido() {
        global $conn;
        $msg = "";
        $subtotal = 0;
        $shipping = 5.00;

        if (!isset($_SESSION)) {
            session_start();
        }

        $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;

        if ($utilizador_id === null || $utilizador_id === '') {
            return "";
        }


        $sql = "SELECT
                    Produtos.preco,
                    Carrinho_Itens.quantidade
                FROM Carrinho_Itens
                INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                WHERE Carrinho_Itens.utilizador_id = '$utilizador_id'
                AND Produtos.ativo = 1";

        $result = $conn->query($sql);

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
    }

    function adicionarAoCarrinho($produto_id, $quantidade = 1) {
        global $conn;

        if (!isset($_SESSION)) {
            session_start();
        }

        $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        // Verificar se o produto existe
        $sql_produto = "SELECT Produto_id FROM Produtos WHERE Produto_id = $produto_id AND ativo = 1";
        $result_produto = $conn->query($sql_produto);

        if ($result_produto->num_rows == 0) {
            return "Erro: Produto não encontrado ou inativo";
        }

        $sql = "SELECT * FROM Carrinho_Itens
                WHERE utilizador_id = '$utilizador_id'
                AND produto_id = $produto_id";
        $result = $conn->query($sql);

        if (!$result) {
            return "Erro: " . $conn->error;
        }

        if ($result->num_rows > 0) {
            $sql = "UPDATE Carrinho_Itens
                    SET quantidade = quantidade + $quantidade
                    WHERE utilizador_id = '$utilizador_id'
                    AND produto_id = $produto_id";
        } else {
            $sql = "INSERT INTO Carrinho_Itens (utilizador_id, produto_id, quantidade)
                    VALUES ('$utilizador_id', $produto_id, $quantidade)";
        }

        if ($conn->query($sql) === TRUE) {
            return "Produto adicionado ao carrinho";
        } else {
            return "Erro: " . $conn->error;
        }
    }

    function atualizarQuantidade($produto_id, $mudanca) {
        global $conn;

        if (!isset($_SESSION)) {
            session_start();
        }


        $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        // Verificar stock disponível antes de aumentar
        if ($mudanca > 0) {
            $sql_stock = "SELECT Produtos.stock, Carrinho_Itens.quantidade
                          FROM Carrinho_Itens
                          INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                          WHERE Carrinho_Itens.utilizador_id = '$utilizador_id'
                          AND Carrinho_Itens.produto_id = $produto_id";
            $result_stock = $conn->query($sql_stock);

            if ($result_stock && $result_stock->num_rows > 0) {
                $row_stock = $result_stock->fetch_assoc();
                $stock_disponivel = $row_stock['stock'];
                $quantidade_atual = $row_stock['quantidade'];

                if ($quantidade_atual >= $stock_disponivel) {
                    return "Erro: Stock insuficiente. Disponível: " . $stock_disponivel;
                }
            }
        }

        $sql = "SELECT quantidade FROM Carrinho_Itens
                WHERE utilizador_id = '$utilizador_id'
                AND produto_id = $produto_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nova_quantidade = $row['quantidade'] + $mudanca;

            if ($nova_quantidade <= 0) {
                $sql = "DELETE FROM Carrinho_Itens
                        WHERE utilizador_id = '$utilizador_id'
                        AND produto_id = $produto_id";
            } else {
                $sql = "UPDATE Carrinho_Itens
                        SET quantidade = $nova_quantidade
                        WHERE utilizador_id = '$utilizador_id'
                        AND produto_id = $produto_id";
            }

            if ($conn->query($sql) === TRUE) {
                return "Quantidade atualizada";
            } else {
                return "Erro: " . $conn->error;
            }
        }

        return "Produto não encontrado no carrinho";
    }

    function removerDoCarrinho($produto_id) {
        global $conn;

        if (!isset($_SESSION)) {
            session_start();
        }

        $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        $sql = "DELETE FROM Carrinho_Itens
                WHERE utilizador_id = '$utilizador_id'
                AND produto_id = $produto_id";

        if ($conn->query($sql) === TRUE) {
            return "Produto removido do carrinho";
        } else {
            return "Erro: " . $conn->error;
        }
    }

    function limparCarrinho() {
        global $conn;

        if (!isset($_SESSION)) {
            session_start();
        }

        $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : null;

        if ($utilizador_id === null) {
            return "Erro: Utilizador não autenticado";
        }

        $sql = "DELETE FROM Carrinho_Itens WHERE utilizador_id = '$utilizador_id'";

        if ($conn->query($sql) === TRUE) {
            return "Carrinho limpo com sucesso";
        } else {
            return "Erro: " . $conn->error;
        }
    }

    function transferirCarrinhoTemporario($temp_user_id, $user_id_real) {
        global $conn;

        // Verificar se existem itens no carrinho temporário
        $sql_check = "SELECT * FROM Carrinho_Itens WHERE utilizador_id = '$temp_user_id'";
        $result = $conn->query($sql_check);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $produto_id = $row['produto_id'];
                $quantidade_temp = $row['quantidade'];

                // Verificar se o produto já existe no carrinho do usuário real
                $sql_existe = "SELECT quantidade FROM Carrinho_Itens
                              WHERE utilizador_id = '$user_id_real' AND produto_id = $produto_id";
                $result_existe = $conn->query($sql_existe);

                if ($result_existe->num_rows > 0) {
                    // Produto já existe, somar as quantidades
                    $row_existe = $result_existe->fetch_assoc();
                    $nova_quantidade = $row_existe['quantidade'] + $quantidade_temp;

                    $sql_update = "UPDATE Carrinho_Itens
                                  SET quantidade = $nova_quantidade
                                  WHERE utilizador_id = '$user_id_real' AND produto_id = $produto_id";
                    $conn->query($sql_update);
                } else {
                    // Produto não existe, transferir diretamente
                    $sql_insert = "UPDATE Carrinho_Itens
                                  SET utilizador_id = '$user_id_real'
                                  WHERE utilizador_id = '$temp_user_id' AND produto_id = $produto_id";
                    $conn->query($sql_insert);
                }
            }

            // Limpar itens restantes do carrinho temporário
            $sql_delete = "DELETE FROM Carrinho_Itens WHERE utilizador_id = '$temp_user_id'";
            $conn->query($sql_delete);
        }
    }

}

?>
