<?php

require_once 'connection.php';

class Carrinho {

    function getCarrinho($utilizador_id) {
        global $conn;
        $msg = "";
    
        
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
                WHERE Carrinho_Itens.utilizador_id = $utilizador_id 
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
            $utilizador_id = isset($_SESSION['utilizador']) ? $_SESSION['utilizador'] : 1;

            $sql = "SELECT 
                        Produtos.preco,
                        Carrinho_Itens.quantidade
                    FROM Carrinho_Itens
                    INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                    WHERE Carrinho_Itens.utilizador_id = $utilizador_id 
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
            $msg .= "<span id='subtotal'>€".$subtotal."</span>";
            $msg .= "</div>";

            $msg .= "<div class='summary-row'>";
            $msg .= "<span>Envio:</span>";
            $msg .= "<span id='shipping'>€".$shipping."</span>";
            $msg .= "</div>";

            $msg .= "<div class='summary-row highlight'>";
            $msg .= "<span>Total:</span>";
            $msg .= "<span id='total'>€".$total."</span>";
            $msg .= "</div>";

            $msg .= "<button class='btn-checkout mt-3' onclick='irParaCheckout()'>";
            $msg .= "<span>Finalizar Checkout</span>";
            $msg .= "<span>→</span>";
            $msg .= "</button>";

            $msg .= "<div class='secure-checkout'>";
            $msg .= "<span>🔒</span>";
            $msg .= "<span>Pagamento 100% Seguro</span>";
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
        session_start();
        $utilizador_id = isset($_SESSION['utilizador_id']) ? $_SESSION['utilizador_id'] : 1;
        
        $sql = "SELECT * FROM Carrinho_Itens 
                WHERE utilizador_id = $utilizador_id 
                AND produto_id = $produto_id";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $sql = "UPDATE Carrinho_Itens 
                    SET quantidade = quantidade + $quantidade 
                    WHERE utilizador_id = $utilizador_id 
                    AND produto_id = $produto_id";
        } else {
            $sql = "INSERT INTO Carrinho_Itens (utilizador_id, produto_id, quantidade) 
                    VALUES ($utilizador_id, $produto_id, $quantidade)";
        }
        
        if ($conn->query($sql) === TRUE) {
            return "Produto adicionado ao carrinho";
        } else {
            return "Erro: " . $conn->error;
        }
    }
    
    function atualizarQuantidade($produto_id, $mudanca) {
        global $conn;
        session_start();
        $utilizador_id = isset($_SESSION['utilizador_id']) ? $_SESSION['utilizador_id'] : 1;
        
        $sql = "SELECT quantidade FROM Carrinho_Itens 
                WHERE utilizador_id = $utilizador_id 
                AND produto_id = $produto_id";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nova_quantidade = $row['quantidade'] + $mudanca;
            
            if ($nova_quantidade <= 0) {
                $sql = "DELETE FROM Carrinho_Itens 
                        WHERE utilizador_id = $utilizador_id 
                        AND produto_id = $produto_id";
            } else {
                $sql = "UPDATE Carrinho_Itens 
                        SET quantidade = $nova_quantidade 
                        WHERE utilizador_id = $utilizador_id 
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
        session_start();
        $utilizador_id = isset($_SESSION['utilizador_id']) ? $_SESSION['utilizador_id'] : 1;
        
        $sql = "DELETE FROM Carrinho_Itens 
                WHERE utilizador_id = $utilizador_id 
                AND produto_id = $produto_id";
        
        if ($conn->query($sql) === TRUE) {
            return "Produto removido do carrinho";
        } else {
            return "Erro: " . $conn->error;
        }
    }
    
    function limparCarrinho() {
        global $conn;
        session_start();
        $utilizador_id = isset($_SESSION['utilizador_id']) ? $_SESSION['utilizador_id'] : 1;
        
        $sql = "DELETE FROM Carrinho_Itens WHERE utilizador_id = $utilizador_id";
        
        if ($conn->query($sql) === TRUE) {
            return "Carrinho limpo com sucesso";
        } else {
            return "Erro: " . $conn->error;
        }
    }

}

?>