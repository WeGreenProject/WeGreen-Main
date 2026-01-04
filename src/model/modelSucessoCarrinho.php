<?php
require_once 'connection.php';

class SucessoCarrinho {

    function processarPagamentoStripe($session_id, $utilizador_id) {
        global $conn;

        require_once '../vendor/autoload.php';
        \Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

        try {
            // Recuperar sessão do Stripe
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            // Verificar pagamento
            if ($session->payment_status !== 'paid') {
                return false;
            }

            // Extrair dados do pagamento Stripe
            $payment_intent_id = $session->payment_intent ?? '';
            $payment_method = 'card'; // padrão
            $payment_status = $session->payment_status;

            // Obter método de pagamento real
            if ($payment_intent_id) {
                $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                $payment_method_details = $payment_intent->charges->data[0]->payment_method_details ?? null;
                if ($payment_method_details) {
                    $payment_method = $payment_method_details->type ?? 'card';
                }
            }

            $transportadora_id = $session->metadata->transportadora_id ?? 1;

            // Construir morada de envio
            $morada = trim(
                $session->metadata->shipping_address1 . ' ' .
                $session->metadata->shipping_address2 . ', ' .
                $session->metadata->shipping_zipCode . ' ' .
                $session->metadata->shipping_city . ', ' .
                $session->metadata->shipping_state
            );

            // Buscar produtos do carrinho
            $sql = "SELECT ci.produto_id, ci.quantidade, p.preco, p.nome, p.anunciante_id
                    FROM Carrinho_Itens ci
                    INNER JOIN Produtos p ON ci.produto_id = p.Produto_id
                    WHERE ci.utilizador_id = $utilizador_id AND p.ativo = 1";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Gerar código único
                $codigo_encomenda = 'WG' . time() . rand(100, 999);
                $total = $session->amount_total / 100;
                $produtos_nomes = array();

                // Processar cada produto
                while ($row = $result->fetch_assoc()) {
                    $produto_id = $row['produto_id'];
                    $quantidade = $row['quantidade'];
                    $preco = $row['preco'];
                    $anunciante_id = $row['anunciante_id'];
                    $produtos_nomes[] = $row['nome'];

                    // Inserir encomenda com dados Stripe
                    $sql_encomenda = "INSERT INTO Encomendas (codigo_encomenda, payment_id, payment_method, payment_status, cliente_id, anunciante_id, transportadora_id, produto_id, data_envio, morada, estado, plano_rastreio)
                                      VALUES ('$codigo_encomenda', '$payment_intent_id', '$payment_method', '$payment_status', $utilizador_id, $anunciante_id, $transportadora_id, $produto_id, NOW(), '$morada', 'Pendente', 'Básico')";
                    $conn->query($sql_encomenda);
                    $encomenda_id = $conn->insert_id;

                    // Inserir venda com session_id
                    $valor_total = $preco * $quantidade;
                    $lucro = $valor_total * 0.06;

                    $sql_venda = "INSERT INTO Vendas (encomenda_id, stripe_session_id, anunciante_id, produto_id, quantidade, valor, lucro, data_venda)
                                  VALUES ($encomenda_id, '$session_id', $anunciante_id, $produto_id, $quantidade, $valor_total, $lucro, NOW())";
                    $conn->query($sql_venda);

                    // Inserir histórico
                    $descricao = "Encomenda criada - Aguardando confirmação";
                    $sql_historico = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao, data_atualizacao)
                                      VALUES ($encomenda_id, 'Pendente', '$descricao', NOW())";
                    $conn->query($sql_historico);

                    // Registrar rendimento
                    $descricao_rend = "Comissão venda - Encomenda $codigo_encomenda";
                    $sql_rendimento = "INSERT INTO Rendimento (valor, anunciante_id, descricao, data_registo)
                                       VALUES ($lucro, $anunciante_id, '$descricao_rend', NOW())";
                    $conn->query($sql_rendimento);
                }

                // Limpar carrinho
                $sql_delete = "DELETE FROM Carrinho_Itens WHERE utilizador_id = $utilizador_id";
                $conn->query($sql_delete);

                // Retornar resultado
                return array(
                    'sucesso' => true,
                    'codigo_encomenda' => $codigo_encomenda,
                    'total' => $total,
                    'produtos' => implode(', ', $produtos_nomes)
                );
            }

            return false;

        } catch (Exception $e) {
            error_log("Erro ao processar pagamento: " . $e->getMessage());
            return false;
        }
    }
}
?>
