<?php
require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

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

            // Determinar tipo de entrega baseado na transportadora
            // 2 = CTT Pickup, 4 = DPD Pickup
            $tipo_entrega = (in_array($transportadora_id, [2, 4])) ? 'ponto_recolha' : 'domicilio';

            // Extrair dados do ponto de recolha (se aplicável)
            $ponto_recolha_id = $session->metadata->pickup_point_id ?? null;
            $nome_ponto_recolha = $session->metadata->pickup_point_name ?? null;
            $morada_ponto_recolha = $session->metadata->pickup_point_address ?? null;

            // Construir nome completo do destinatário
            $nome_destinatario = trim(
                $session->metadata->shipping_firstName . ' ' .
                $session->metadata->shipping_lastName
            );

            // Construir morada completa de envio (para entrega ao domicílio)
            $morada_completa = trim(
                $nome_destinatario . ', ' .
                $session->metadata->shipping_address1 . ' ' .
                $session->metadata->shipping_address2 . ', ' .
                $session->metadata->shipping_zipCode . ' ' .
                $session->metadata->shipping_city . ', ' .
                $session->metadata->shipping_state
            );

            // Morada antiga (compatibilidade)
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

                    // Inserir encomenda com dados Stripe e informações de entrega
                    $sql_encomenda = "INSERT INTO Encomendas (codigo_encomenda, payment_id, payment_method, payment_status, cliente_id, anunciante_id, transportadora_id, produto_id, data_envio, morada, tipo_entrega, ponto_recolha_id, nome_ponto_recolha, morada_ponto_recolha, morada_completa, nome_destinatario, estado, plano_rastreio)
                                      VALUES ('$codigo_encomenda', '$payment_intent_id', '$payment_method', '$payment_status', $utilizador_id, $anunciante_id, $transportadora_id, $produto_id, NOW(), '$morada', '$tipo_entrega', " . ($ponto_recolha_id ? "'$ponto_recolha_id'" : "NULL") . ", " . ($nome_ponto_recolha ? "'$nome_ponto_recolha'" : "NULL") . ", " . ($morada_ponto_recolha ? "'" . $conn->real_escape_string($morada_ponto_recolha) . "'" : "NULL") . ", '" . $conn->real_escape_string($morada_completa) . "', '" . $conn->real_escape_string($nome_destinatario) . "', 'Pendente', 'Básico')";
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

                // Enviar emails de notificação com dados completos de entrega
                $dadosEntrega = [
                    'tipo_entrega' => $tipo_entrega,
                    'ponto_recolha_id' => $ponto_recolha_id,
                    'nome_ponto_recolha' => $nome_ponto_recolha,
                    'morada_ponto_recolha' => $morada_ponto_recolha,
                    'morada_completa' => $morada_completa,
                    'nome_destinatario' => $nome_destinatario
                ];

                $this->enviarEmailsConfirmacao(
                    $utilizador_id,
                    $codigo_encomenda,
                    $anunciante_id,
                    $payment_method,
                    $transportadora_id,
                    $morada,
                    $total,
                    $produtos_nomes,
                    $dadosEntrega
                );

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

    /**
     * Enviar emails de confirmação de encomenda ao cliente e ao anunciante
     */
    private function enviarEmailsConfirmacao($cliente_id, $codigo_encomenda, $anunciante_id, $payment_method, $transportadora_id, $morada, $total, $produtos_nomes, $dadosEntrega = []) {
        global $conn;

        try {
            $emailService = new EmailService();

            // Obter nome da transportadora
            $transportadoras = [
                1 => 'CTT - Correios de Portugal',
                2 => 'DPD - Entrega Rápida',
                3 => 'UPS',
                4 => 'Chronopost',
                5 => 'Entrega WeGreen'
            ];
            $transportadora = $transportadoras[$transportadora_id] ?? 'CTT';

            // Buscar produtos completos para o template
            $sql_produtos = "SELECT p.nome, p.preco, p.Foto_Produto, 1 as quantidade
                            FROM Encomendas e
                            INNER JOIN Produtos p ON e.produto_id = p.Produto_id
                            WHERE e.codigo_encomenda = '$codigo_encomenda'";

            $result_produtos = $conn->query($sql_produtos);
            $produtos_array = [];
            $inline_images = []; // Array para imagens inline (CID)

            if ($result_produtos && $result_produtos->num_rows > 0) {
                $index = 1;
                while ($prod = $result_produtos->fetch_assoc()) {
                    $foto_path = $prod['Foto_Produto'];
                    $cid = "produto_$index";

                    // Processar caminho da foto para inline image
                    if (!empty($foto_path)) {
                        // Remover possível prefixo duplicado
                        $foto_path = str_replace('src/img/', '', $foto_path);
                        $foto_path = str_replace('src\\img\\', '', $foto_path);

                        // Construir caminho absoluto
                        $absolute_path = __DIR__ . '/../../src/img/' . $foto_path;

                        // Verificar se arquivo existe
                        if (file_exists($absolute_path)) {
                            $inline_images[$cid] = $absolute_path;
                            $foto_url = "cid:$cid"; // Usar Content-ID para inline
                        } else {
                            // Fallback para placeholder
                            $foto_url = "cid:placeholder";
                            $inline_images['placeholder'] = __DIR__ . '/../../assets/media/products/placeholder.jpg';
                        }
                    } else {
                        $foto_url = "cid:placeholder";
                        $inline_images['placeholder'] = __DIR__ . '/../../assets/media/products/placeholder.jpg';
                    }

                    $produtos_array[] = [
                        'nome' => $prod['nome'],
                        'preco' => $prod['preco'],
                        'quantidade' => $prod['quantidade'],
                        'subtotal' => $prod['preco'] * $prod['quantidade'],
                        'foto' => $foto_url // CID format
                    ];

                    $index++;
                }
            }

            // Gerar URL do mapa estático
            $morada_para_mapa = ($dadosEntrega['tipo_entrega'] ?? 'domicilio') === 'ponto_recolha'
                ? ($dadosEntrega['morada_ponto_recolha'] ?? $morada)
                : ($dadosEntrega['morada_completa'] ?? $morada);

            $mapa_url = $this->gerarMapaEstatico($morada_para_mapa);

            // Dados para o email do cliente
            $dadosCliente = [
                'codigo_encomenda' => $codigo_encomenda,
                'data_encomenda' => date('Y-m-d H:i:s'),
                'payment_method' => 'Stripe (Cartão de Crédito)', // Sempre Stripe
                'transportadora' => $transportadora,
                'morada' => $morada,
                'total' => $total,
                'produtos' => $produtos_array,
                // Novos campos de entrega
                'tipo_entrega' => $dadosEntrega['tipo_entrega'] ?? 'domicilio',
                'nome_ponto_recolha' => $dadosEntrega['nome_ponto_recolha'] ?? null,
                'morada_ponto_recolha' => $dadosEntrega['morada_ponto_recolha'] ?? null,
                'morada_completa' => $dadosEntrega['morada_completa'] ?? $morada,
                'nome_destinatario' => $dadosEntrega['nome_destinatario'] ?? 'Cliente',
                'mapa_url' => $mapa_url // URL do mapa estático
            ];

            // Enviar email ao cliente com imagens inline
            $emailService->sendFromTemplate(
                $cliente_id,
                'confirmacao_encomenda',
                $dadosCliente,
                'cliente',
                $inline_images // Array de imagens inline
            );

            // Obter dados do cliente para email do anunciante
            $sql_cliente = "SELECT nome, email FROM Utilizadores WHERE id = $cliente_id LIMIT 1";
            $result_cliente = $conn->query($sql_cliente);
            $cliente = $result_cliente->fetch_assoc();

            // Calcular valores financeiros
            $comissao = $total * 0.06;
            $lucro_liquido = $total - $comissao;

            // Dados para o email do anunciante
            $dadosAnunciante = [
                'codigo_encomenda' => $codigo_encomenda,
                'data_encomenda' => date('Y-m-d H:i:s'),
                'payment_method' => 'Stripe (Cartão de Crédito)',
                'payment_status' => 'Pago',
                'nome_cliente' => $cliente['nome'],
                'email_cliente' => $cliente['email'],
                'morada' => $morada,
                'transportadora' => $transportadora,
                'produtos' => $produtos_array,
                'valor_bruto' => $total,
                'comissao' => $comissao,
                'lucro_liquido' => $lucro_liquido,
                // Dados de entrega
                'tipo_entrega' => $dadosEntrega['tipo_entrega'] ?? 'domicilio',
                'nome_ponto_recolha' => $dadosEntrega['nome_ponto_recolha'] ?? null,
                'morada_ponto_recolha' => $dadosEntrega['morada_ponto_recolha'] ?? null,
                'morada_completa' => $dadosEntrega['morada_completa'] ?? $morada,
                'nome_destinatario' => $dadosEntrega['nome_destinatario'] ?? $cliente['nome']
            ];

            // Enviar email ao anunciante com imagens inline
            $emailService->sendFromTemplate(
                $anunciante_id,
                'nova_encomenda_anunciante',
                $dadosAnunciante,
                'anunciante',
                $inline_images
            );

        } catch (Exception $e) {
            error_log("Erro ao enviar emails de confirmação: " . $e->getMessage());
            // Não falhar o processo se o email falhar
        }
    }

    /**
     * Gera URL de mapa estático usando OpenStreetMap (gratuito, sem API key)
     *
     * @param string $morada Morada para geocodificar
     * @return string URL do mapa estático ou string vazia se falhar
     */
    private function gerarMapaEstatico($morada) {
        // Limpar e codificar morada
        $morada_clean = trim($morada);
        if (empty($morada_clean)) {
            return '';
        }

        // Fazer geocoding usando Nominatim (OpenStreetMap - gratuito)
        $nominatim_url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($morada_clean) . '&countrycodes=pt&limit=1';

        // Configurar headers para Nominatim (requer User-Agent)
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: WeGreen-Marketplace/1.0\r\n"
            ]
        ]);

        try {
            // Obter coordenadas
            $response = @file_get_contents($nominatim_url, false, $context);

            if ($response === false) {
                error_log("Erro ao geocodificar morada: $morada_clean");
                return '';
            }

            $data = json_decode($response, true);

            if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
                error_log("Coordenadas não encontradas para: $morada_clean");
                return '';
            }

            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];

            // Gerar URL de mapa estático usando StaticMap.org (gratuito, sem API key)
            // Documentação: https://staticmap.org/
            $zoom = 15;
            $width = 600;
            $height = 300;

            $mapa_url = "https://staticmap.openstreetmap.de/staticmap.php?" . http_build_query([
                'center' => "$lat,$lon",
                'zoom' => $zoom,
                'size' => "{$width}x{$height}",
                'markers' => "$lat,$lon,lightgreen"
            ]);

            return $mapa_url;

        } catch (Exception $e) {
            error_log("Erro ao gerar mapa estático: " . $e->getMessage());
            return '';
        }
    }
}
?>
