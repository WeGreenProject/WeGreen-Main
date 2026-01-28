<?php
require_once 'connection.php';
require_once __DIR__ . '/../services/EmailService.php';

class SucessoCarrinho {

    function processarPagamentoStripe($session_id, $utilizador_id) {
        global $conn;

        // Log para debug
        error_log("=== PROCESSANDO PAGAMENTO STRIPE ===");
        error_log("Session ID: " . $session_id);
        error_log("Utilizador ID: " . $utilizador_id);

        require_once __DIR__ . '/../vendor/autoload.php';
        \Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

        // Iniciar transação do banco de dados
        $conn->begin_transaction();
        error_log("✓ Transação do banco iniciada");

        try {
            // Recuperar sessão do Stripe
            $session = \Stripe\Checkout\Session::retrieve($session_id);
            error_log("Stripe Session Status: " . $session->payment_status);

            // Verificar pagamento
            if ($session->payment_status !== 'paid') {
                error_log("ERRO: Pagamento não confirmado. Status: " . $session->payment_status);
                $conn->rollback();
                return false;
            }

            error_log("Pagamento confirmado! Processando encomenda...");

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

            // Extrair e validar transportadora_id
            $transportadora_id_raw = $session->metadata->transportadora_id ?? null;

            // Mapear opções de envio (1-5) para transportadoras reais (1=CTT, 2=DPD)
            // 1 = CTT Standard → 1 (CTT)
            // 2 = CTT Pickup → 1 (CTT)
            // 3 = DPD Standard → 2 (DPD)
            // 4 = DPD Pickup → 2 (DPD)
            // 5 = Entrega em Casa → 1 (CTT)

            $mapeamento_transportadoras = [
                '1' => 1, // CTT Standard
                '2' => 1, // CTT Pickup
                '3' => 2, // DPD Standard
                '4' => 2, // DPD Pickup
                '5' => 1, // Entrega em Casa (CTT)
            ];

            if (empty($transportadora_id_raw)) {
                $transportadora_id = 1; // CTT por padrão
                $tipo_entrega = 'domicilio';
                error_log("⚠️ Transportadora vazia. Usando CTT (1) domicílio como padrão.");
            } else {
                $opcao_envio = strval($transportadora_id_raw);
                $transportadora_id = $mapeamento_transportadoras[$opcao_envio] ?? 1;

                // Determinar tipo de entrega baseado na opção
                // 2 = CTT Pickup, 4 = DPD Pickup
                $tipo_entrega = (in_array($opcao_envio, ['2', '4'])) ? 'ponto_recolha' : 'domicilio';

                error_log("✓ Opção de envio: $opcao_envio → Transportadora: $transportadora_id, Tipo: $tipo_entrega");
            }

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

            // Se morada estiver vazia, buscar do perfil do utilizador
            if (empty($morada) || empty($morada_completa)) {
                $sql_user = "SELECT nome, morada FROM Utilizadores WHERE id = $utilizador_id LIMIT 1";
                $result_user = $conn->query($sql_user);

                if ($result_user && $result_user->num_rows > 0) {
                    $user_data = $result_user->fetch_assoc();
                    $nome_destinatario = $user_data['nome'];
                    $morada = $user_data['morada'] ?? 'Morada não cadastrada';
                    $morada_completa = $nome_destinatario . ', ' . $morada;
                }
            }

            // Buscar produtos do carrinho
            $sql = "SELECT ci.produto_id, ci.quantidade, p.preco, p.nome, p.anunciante_id, p.foto
                    FROM Carrinho_Itens ci
                    INNER JOIN Produtos p ON ci.produto_id = p.Produto_id
                    WHERE ci.utilizador_id = $utilizador_id AND p.ativo = 1";

            $result = $conn->query($sql);
            error_log("Produtos no carrinho: " . $result->num_rows);

            if ($result->num_rows > 0) {
                // Gerar código ÚNICO da encomenda (1 código para todos os produtos)
                do {
                    $codigo_encomenda = 'WG' . time() . rand(10000, 99999) . substr(uniqid(), -4);
                    $check_sql = "SELECT id FROM Encomendas WHERE codigo_encomenda = '" . $conn->real_escape_string($codigo_encomenda) . "' LIMIT 1";
                    $check_result = $conn->query($check_sql);
                } while ($check_result && $check_result->num_rows > 0);

                error_log("✓ Código único da encomenda: $codigo_encomenda");

                // Gerar código de confirmação de receção
                $codigo_confirmacao = 'CONF-' . strtoupper(substr(md5(uniqid($codigo_encomenda, true)), 0, 6));

                // Calcular prazo estimado de entrega
                $dias_prazo = ($transportadora_id == 2) ? 2 : 4;
                $prazo_estimado = date('Y-m-d', strtotime("+{$dias_prazo} days"));

                $total = $session->amount_total / 100;
                $produtos_nomes = array();
                $produtos_detalhes = array();
                $produtos_array = [];
                $primeiro_produto_id = null;
                $primeiro_anunciante_id = null;

                // Coletar todos os produtos primeiro
                while ($row = $result->fetch_assoc()) {
                    if ($primeiro_produto_id === null) {
                        $primeiro_produto_id = $row['produto_id'];
                        $primeiro_anunciante_id = $row['anunciante_id'];
                    }
                    $produtos_array[] = $row;
                    $produtos_nomes[] = $row['nome'];
                    $produtos_detalhes[] = array(
                        'nome' => $row['nome'],
                        'imagem' => $row['foto'],
                        'quantidade' => $row['quantidade'],
                        'preco' => $row['preco']
                    );
                }

                // Criar UMA ÚNICA ENCOMENDA (sem anunciante_id - relação via tabela Vendas)
                $sql_encomenda = "INSERT INTO Encomendas (
                    codigo_encomenda, payment_id, payment_method, payment_status,
                    cliente_id, transportadora_id, produto_id,
                    data_envio, morada, tipo_entrega, ponto_recolha_id,
                    nome_ponto_recolha, morada_ponto_recolha, morada_completa,
                    nome_destinatario, estado, plano_rastreio,
                    codigo_confirmacao_recepcao, prazo_estimado_entrega, lembrete_confirmacao_enviado
                ) VALUES (
                    '$codigo_encomenda', '$payment_intent_id', '$payment_method', '$payment_status',
                    $utilizador_id, $transportadora_id, $primeiro_produto_id,
                    NOW(), '" . $conn->real_escape_string($morada) . "', '$tipo_entrega', " . ($ponto_recolha_id ? "'" . $conn->real_escape_string($ponto_recolha_id) . "'" : "NULL") . ",
                    " . ($nome_ponto_recolha ? "'" . $conn->real_escape_string($nome_ponto_recolha) . "'" : "NULL") . ",
                    " . ($morada_ponto_recolha ? "'" . $conn->real_escape_string($morada_ponto_recolha) . "'" : "NULL") . ",
                    '" . $conn->real_escape_string($morada_completa) . "',
                    '" . $conn->real_escape_string($nome_destinatario) . "',
                    'Pendente', 'Básico',
                    '$codigo_confirmacao', '$prazo_estimado', 0
                )";

                if (!$conn->query($sql_encomenda)) {
                    error_log("❌ ERRO ao inserir encomenda: " . $conn->error);
                    error_log("SQL: " . $sql_encomenda);
                    throw new Exception("Erro ao criar encomenda: " . $conn->error);
                }

                $encomenda_id = $conn->insert_id;
                error_log("✓ Encomenda única criada: ID=$encomenda_id, Código=$codigo_encomenda");

                // Criar VENDAS para cada produto
                foreach ($produtos_array as $produto) {
                    $produto_id = $produto['produto_id'];
                    $quantidade = $produto['quantidade'];
                    $preco = $produto['preco'];
                    $anunciante_id = $produto['anunciante_id'];

                    $valor_total = $preco * $quantidade;
                    $lucro = $valor_total * 0.06;

                    $sql_venda = "INSERT INTO Vendas (encomenda_id, stripe_session_id, anunciante_id, produto_id, quantidade, valor, lucro, data_venda)
                                  VALUES ($encomenda_id, '$session_id', $anunciante_id, $produto_id, $quantidade, $valor_total, $lucro, NOW())";

                    if (!$conn->query($sql_venda)) {
                        error_log("❌ ERRO ao inserir venda: " . $conn->error);
                        throw new Exception("Erro ao criar venda: " . $conn->error);
                    }

                    error_log("✓ Venda criada: Produto ID=$produto_id, Qtd=$quantidade, Valor=€$valor_total");

                    // Registrar rendimento por anunciante
                    $descricao_rend = "Comissão venda - Encomenda $codigo_encomenda - Produto #$produto_id";
                    $sql_rendimento = "INSERT INTO Rendimento (valor, anunciante_id, descricao, data_registo)
                                       VALUES ($lucro, $anunciante_id, '$descricao_rend', NOW())";
                    $conn->query($sql_rendimento);
                }

                // Inserir histórico UMA VEZ para a encomenda
                $descricao = "Encomenda criada - Aguardando confirmação";
                $sql_historico = "INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao, data_atualizacao)
                                  VALUES ($encomenda_id, 'Pendente', '$descricao', NOW())";
                $conn->query($sql_historico);

                // Limpar carrinho UMA VEZ
                $sql_delete = "DELETE FROM Carrinho_Itens WHERE utilizador_id = $utilizador_id";
                if ($conn->query($sql_delete)) {
                    error_log("✓ Carrinho limpo: removidos " . $conn->affected_rows . " itens");
                }

                // COMMIT da transação - garantir que tudo foi salvo
                $conn->commit();
                error_log("✓ Transação do banco confirmada (COMMIT)");

                // Enviar emails de notificação com dados completos de entrega
                $dadosEntrega = [
                    'tipo_entrega' => $tipo_entrega,
                    'ponto_recolha_id' => $ponto_recolha_id,
                    'nome_ponto_recolha' => $nome_ponto_recolha,
                    'morada_ponto_recolha' => $morada_ponto_recolha,
                    'morada_completa' => $morada_completa,
                    'nome_destinatario' => $nome_destinatario,
                    'codigo_confirmacao_recepcao' => $codigo_confirmacao,
                    'prazo_estimado_entrega' => $prazo_estimado
                ];

                // Tentar enviar emails, mas não falhar o checkout se houver erro
                try {
                    $this->enviarEmailsConfirmacao(
                        $utilizador_id,
                        $codigo_encomenda,
                        $primeiro_anunciante_id,
                        $payment_method,
                        $transportadora_id,
                        $morada,
                        $total,
                        $produtos_nomes,
                        $dadosEntrega
                    );
                    error_log("✓ Emails de confirmação enviados com sucesso");
                } catch (Exception $e) {
                    error_log("⚠️ AVISO: Falha ao enviar emails (checkout continua): " . $e->getMessage());
                    // Não bloquear o processo - a encomenda já foi criada
                }

                // Retornar resultado
                error_log("Encomenda processada com sucesso! Código: " . $codigo_encomenda);
                return array(
                    'sucesso' => true,
                    'codigo_encomenda' => $codigo_encomenda,
                    'total' => $total,
                    'produtos' => implode(', ', $produtos_nomes),
                    'produtos_detalhes' => $produtos_detalhes
                );
            } else {
                error_log("ERRO: Carrinho vazio! Nenhum produto encontrado.");
                $conn->rollback();
            }

            $conn->rollback();
            return false;

        } catch (Exception $e) {
            // Rollback em caso de erro
            $conn->rollback();
            error_log("ERRO CRÍTICO ao processar pagamento: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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
            $sql_produtos = "SELECT p.nome, p.preco, p.foto, 1 as quantidade
                            FROM Encomendas e
                            INNER JOIN Produtos p ON e.produto_id = p.Produto_id
                            WHERE e.codigo_encomenda = '$codigo_encomenda'";

            $result_produtos = $conn->query($sql_produtos);
            $produtos_array = [];
            $inline_images = []; // Array para imagens inline (CID)

            if ($result_produtos && $result_produtos->num_rows > 0) {
                $index = 1;
                while ($prod = $result_produtos->fetch_assoc()) {
                    $foto_path = $prod['foto'];
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
