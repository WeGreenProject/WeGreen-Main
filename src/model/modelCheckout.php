<?php
require_once 'connection.php';

class Checkout {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function getPlanosComprar($utilizador,$plano){
        try {

        $msg = "";
        $row = "";

        $sql = "SELECT * from utilizadores where id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $utilizador);
        $stmt->execute();
        $result = $stmt->get_result();

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

        return ($msg);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function getProdutosCarrinho($utilizador_id) {
        try {

        $sql = "SELECT
                    Produtos.Produto_id,
                    Produtos.nome,
                    Produtos.preco,
                    Produtos.foto,
                    Carrinho_Itens.quantidade
                FROM Carrinho_Itens
                INNER JOIN Produtos ON Carrinho_Itens.produto_id = Produtos.Produto_id
                WHERE Carrinho_Itens.utilizador_id = ?
                AND Produtos.ativo = 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $utilizador_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $produtos = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $produtos[] = $row;
            }
        }

        return $produtos;
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }

    function guardarDadosCheckout($nome, $email, $morada, $codigo_postal, $metodo_entrega, $metodo_pagamento, $dados_pagamento) {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['checkout'] = [
                'nome' => $nome,
                'email' => $email,
                'morada' => $morada,
                'codigo_postal' => $codigo_postal,
                'metodo_entrega' => $metodo_entrega,
                'metodo_pagamento' => $metodo_pagamento,
                'dados_pagamento' => $dados_pagamento
            ];
            return json_encode(['success' => true, 'message' => 'Dados guardados com sucesso'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro ao guardar dados'], JSON_UNESCAPED_UNICODE);
        }
    }

    function obterDadosCheckout() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['checkout'])) {
                return json_encode(['success' => true, 'dados' => $_SESSION['checkout']], JSON_UNESCAPED_UNICODE);
            }
            return json_encode(['success' => false, 'message' => 'Nenhum dado de checkout encontrado'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro ao obter dados'], JSON_UNESCAPED_UNICODE);
        }
    }

    function limparDadosCheckout() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            unset($_SESSION['checkout']);
            return json_encode(['success' => true, 'message' => 'Dados do checkout limpos'], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro ao limpar dados'], JSON_UNESCAPED_UNICODE);
        }
    }

}
?>
