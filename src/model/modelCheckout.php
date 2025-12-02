<?php
require_once 'connection.php';

class Checkout {

    function getPlanosComprar($utilizador,$plano){
        global $conn;
        $msg = "";
        $row = "";

        $sql = "SELECT * from utilizadores where id = ". $utilizador;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                if($plano == 2)
                {
                    $msg .= "<span class='detail-label'>Plano Adquirido</span>";
                    $msg .= "<span class='plan-badge plan-premium' id='planBadge'>";
                    $msg .= "<span class='plan-icon'></span>";
                    $msg .= "<span id='planName'>Crescimento Circular</span>";
                    $msg .= "</span>";
                }
                else if($plano == 3)
                {
                    $msg .= "<span class='detail-label'>Plano Adquirido</span>";
                    $msg .= "<span class='plan-badge plan-premium' id='planBadge'>";
                    $msg .= "<span class='plan-icon'></span>";
                    $msg .= "<span id='planName'>Profissional Eco +</span>";
                    $msg .= "</span>";
                }
            }
        }
        $conn->close();
        
        return ($msg);
    }

    function getProdutosCarrinho($utilizador_id) {
        global $conn;
        
        $sql = "SELECT 
                    Produtos.Produto_id,
                    Produtos.nome,
                    Produtos.preco,
                    Produtos.foto,
                    Carrinho_Itens.quantidade
                FROM Carrinho_Itens
                INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                WHERE Carrinho_Itens.utilizador_id = $utilizador_id 
                AND Produtos.ativo = 1";
        
        $result = $conn->query($sql);
        $produtos = [];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $produtos[] = $row;
            }
        }
        
        return $produtos;
    }
        
}
?>