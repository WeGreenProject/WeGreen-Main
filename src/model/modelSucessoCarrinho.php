<?php
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../services/RankingService.php';

class SucessoCarrinho {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    private function getTaxaComissaoPorProduto($produtoId) {
        $sql = "SELECT sustentavel, tipo_material FROM Produtos WHERE Produto_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 0.06;
        }
        $stmt->bind_param("i", $produtoId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row || !(int)$row['sustentavel']) {
            return 0.06;
        }

        $material = $row['tipo_material'] ?? '';
        $taxas = [
            '100_reciclavel' => 0.04,
            '70_reciclavel'  => 0.05,
            '50_reciclavel'  => 0.05,
            '30_reciclavel'  => 0.06
        ];
        return $taxas[$material] ?? 0.06;
    }

    private function getRastreioTipoPorAnunciante($anuncianteId) {
        $sql = "SELECT p.rastreio_tipo
                FROM Utilizadores u
                LEFT JOIN Planos p ON u.plano_id = p.id
                WHERE u.id = ?
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 'Básico';
        }

        $stmt->bind_param("i", $anuncianteId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return !empty($row['rastreio_tipo']) ? $row['rastreio_tipo'] : 'Básico';
    }

    function processarPagamentoStripe($session_id, $utilizador_id) {
        try {

        require_once __DIR__ . '/../vendor/autoload.php';
        \Stripe\Stripe::setApiKey('sk_test_51SAniYBgsjq4eGslagm3l86yXwCOicwq02ABZ54SCT7e8p9HiOTdciQcB3hQXxN4i6hVwlxohVvbtzQXEoPhg7yd009a6ubA3l');

        $this->conn->begin_transaction();

        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if ($session->payment_status !== 'paid') {
                $this->conn->rollback();
                return false;
            }

            $payment_intent_id = $session->payment_intent ?? '';
            $payment_method = 'card';
            $payment_status = $session->payment_status;

            if ($payment_intent_id) {
                $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
                $payment_method_details = $payment_intent->charges->data[0]->payment_method_details ?? null;
                if ($payment_method_details) {
                    $payment_method = $payment_method_details->type ?? 'card';
                }
            }

            $transportadora_id_raw = $session->metadata->transportadora_id ?? null;


            $mapeamento_transportadoras = [
                '1' => 1,
                '2' => 1,
                '3' => 2,
                '4' => 2,
                '5' => 1,
            ];

            if (empty($transportadora_id_raw)) {
                $transportadora_id = 1;
                $tipo_entrega = 'domicilio';
            } else {
                $opcao_envio = strval($transportadora_id_raw);
                $transportadora_id = $mapeamento_transportadoras[$opcao_envio] ?? 1;


                $tipo_entrega = (in_array($opcao_envio, ['2', '4'])) ? 'ponto_recolha' : 'domicilio';

            }

            $ponto_recolha_id = $session->metadata->pickup_point_id ?? null;
            $nome_ponto_recolha = $session->metadata->pickup_point_name ?? null;
            $morada_ponto_recolha = $session->metadata->pickup_point_address ?? null;

            $nome_destinatario = trim(
                $session->metadata->shipping_firstName . ' ' .
                $session->metadata->shipping_lastName
            );

            $morada_completa = trim(
                $nome_destinatario . ', ' .
                $session->metadata->shipping_address1 . ' ' .
                $session->metadata->shipping_address2 . ', ' .
                $session->metadata->shipping_zipCode . ' ' .
                $session->metadata->shipping_city . ', ' .
                $session->metadata->shipping_state
            );


            $morada = trim(
                $session->metadata->shipping_address1 . ' ' .
                $session->metadata->shipping_address2 . ', ' .
                $session->metadata->shipping_zipCode . ' ' .
                $session->metadata->shipping_city . ', ' .
                $session->metadata->shipping_state
            );

            if (empty($morada) || empty($morada_completa)) {
                $stmt_user = $this->conn->prepare("SELECT nome, morada FROM Utilizadores WHERE id = ? LIMIT 1");
                $stmt_user->bind_param("i", $utilizador_id);
                $stmt_user->execute();
                $result_user = $stmt_user->get_result();

                if ($result_user && $result_user->num_rows > 0) {
                    $user_data = $result_user->fetch_assoc();
                    $nome_destinatario = $user_data['nome'];
                    $morada = $user_data['morada'] ?? 'Morada não cadastrada';
                    $morada_completa = $nome_destinatario . ', ' . $morada;
                }
            }

            $stmt_cart = $this->conn->prepare("SELECT ci.produto_id, ci.quantidade, p.preco, p.nome, p.anunciante_id, p.foto
                    FROM Carrinho_Itens ci
                    INNER JOIN Produtos p ON ci.produto_id = p.Produto_id
                    WHERE ci.utilizador_id = ? AND p.ativo = 1");
            $stmt_cart->bind_param("i", $utilizador_id);
            $stmt_cart->execute();
            $result = $stmt_cart->get_result();

            if ($result->num_rows > 0) {

                do {
                    $codigo_encomenda = 'WG' . time() . rand(10000, 99999) . substr(uniqid(), -4);
                    $stmt_check = $this->conn->prepare("SELECT id FROM Encomendas WHERE codigo_encomenda = ? LIMIT 1");
                    $stmt_check->bind_param("s", $codigo_encomenda);
                    $stmt_check->execute();
                    $check_result = $stmt_check->get_result();
                } while ($check_result && $check_result->num_rows > 0);

                $codigo_confirmacao = 'CONF-' . strtoupper(substr(md5(uniqid($codigo_encomenda, true)), 0, 6));

                $dias_prazo = ($transportadora_id == 2) ? 2 : 4;
                $prazo_estimado = date('Y-m-d', strtotime("+{$dias_prazo} days"));

                $total = $session->amount_total / 100;
                $produtos_nomes = array();
                $produtos_detalhes = array();
                $produtos_array = [];
                $primeiro_produto_id = null;
                $primeiro_anunciante_id = null;

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

                $plano_rastreio = $this->getRastreioTipoPorAnunciante((int)$primeiro_anunciante_id);


                $stmt_encomenda = $this->conn->prepare("INSERT INTO Encomendas (
                    codigo_encomenda, payment_id, payment_method, payment_status,
                    cliente_id, transportadora_id, produto_id,
                    data_envio, morada, tipo_entrega, ponto_recolha_id,
                    nome_ponto_recolha, morada_ponto_recolha, morada_completa,
                    nome_destinatario, estado, plano_rastreio,
                    codigo_confirmacao_recepcao, prazo_estimado_entrega, lembrete_confirmacao_enviado
                ) VALUES (
                    ?, ?, ?, ?,
                    ?, ?, ?,
                    NOW(), ?, ?, ?,
                    ?, ?, ?,
                    ?, 'Pendente', ?,
                    ?, ?, 0
                )");
                $stmt_encomenda->bind_param(
                    "ssssiiissssssssss",
                    $codigo_encomenda, $payment_intent_id, $payment_method, $payment_status,
                    $utilizador_id, $transportadora_id, $primeiro_produto_id,
                    $morada, $tipo_entrega, $ponto_recolha_id,
                    $nome_ponto_recolha, $morada_ponto_recolha, $morada_completa,
                    $nome_destinatario, $plano_rastreio,
                    $codigo_confirmacao, $prazo_estimado
                );

                if (!$stmt_encomenda->execute()) {
                    throw new Exception("Erro ao criar encomenda: " . $stmt_encomenda->error);
                }

                $encomenda_id = $this->conn->insert_id;

                foreach ($produtos_array as $produto) {
                    $produto_id = $produto['produto_id'];
                    $quantidade = $produto['quantidade'];
                    $preco = $produto['preco'];
                    $anunciante_id = $produto['anunciante_id'];

                    $valor_total = $preco * $quantidade;
                    $taxa_comissao = $this->getTaxaComissaoPorProduto((int)$produto_id);
                    $lucro = $valor_total * $taxa_comissao;

                    $stmt_venda = $this->conn->prepare("INSERT INTO Vendas (encomenda_id, stripe_session_id, anunciante_id, produto_id, quantidade, valor, lucro, data_venda)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                    $stmt_venda->bind_param("isiiidd", $encomenda_id, $session_id, $anunciante_id, $produto_id, $quantidade, $valor_total, $lucro);

                    if (!$stmt_venda->execute()) {
                        throw new Exception("Erro ao criar venda: " . $stmt_venda->error);
                    }

                    $descricao_rend = "Comissão venda - Encomenda $codigo_encomenda - Produto #$produto_id";
                    $stmt_rend = $this->conn->prepare("INSERT INTO Rendimento (valor, anunciante_id, descricao, data_registo)
                                       VALUES (?, ?, ?, NOW())");
                    $stmt_rend->bind_param("dis", $lucro, $anunciante_id, $descricao_rend);
                    $stmt_rend->execute();


                    try {
                        $rankingService = new RankingService($this->conn);
                        $rankingService->adicionarPontosVenda((int)$anunciante_id);
                    } catch (Exception $rankEx) {
                    }


                    $stmt_stock = $this->conn->prepare("UPDATE Produtos SET stock = GREATEST(0, stock - ?) WHERE Produto_id = ?");
                    $stmt_stock->bind_param("ii", $quantidade, $produto_id);
                    $stmt_stock->execute();
                    $stmt_stock->close();


                    $stmt_check_stock = $this->conn->prepare("SELECT stock FROM Produtos WHERE Produto_id = ? LIMIT 1");
                    $stmt_check_stock->bind_param("i", $produto_id);
                    $stmt_check_stock->execute();
                    $row_stock = $stmt_check_stock->get_result()->fetch_assoc();
                    $stmt_check_stock->close();


                    if ($row_stock && (int)$row_stock['stock'] <= 0) {
                        $stmt_deactivate = $this->conn->prepare("UPDATE Produtos SET ativo = 0 WHERE Produto_id = ?");
                        $stmt_deactivate->bind_param("i", $produto_id);
                        $stmt_deactivate->execute();
                        $stmt_deactivate->close();
                    }

                }

                $descricao = "Encomenda criada - Aguardando confirmação";
                $stmt_hist = $this->conn->prepare("INSERT INTO Historico_Produtos (encomenda_id, estado_encomenda, descricao, data_atualizacao)
                                  VALUES (?, 'Pendente', ?, NOW())");
                $stmt_hist->bind_param("is", $encomenda_id, $descricao);
                $stmt_hist->execute();

                $stmt_del = $this->conn->prepare("DELETE FROM Carrinho_Itens WHERE utilizador_id = ?");
                $stmt_del->bind_param("i", $utilizador_id);
                if ($stmt_del->execute()) {
                }

                $this->conn->commit();

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
                } catch (Exception $e) {

                }

                return array(
                    'sucesso' => true,
                    'codigo_encomenda' => $codigo_encomenda,
                    'total' => $total,
                    'produtos' => implode(', ', $produtos_nomes),
                    'produtos_detalhes' => $produtos_detalhes
                );
            } else {
                $this->conn->rollback();
            }

            $this->conn->rollback();
            return false;

        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }


    private function enviarEmailsConfirmacao($cliente_id, $codigo_encomenda, $anunciante_id, $payment_method, $transportadora_id, $morada, $total, $produtos_nomes, $dadosEntrega = []) {

        try {
            $emailService = new EmailService($this->conn);

            $transportadoras = [
                1 => 'CTT - Correios de Portugal',
                2 => 'DPD - Entrega Rápida',
                3 => 'UPS',
                4 => 'Chronopost',
                5 => 'Entrega WeGreen'
            ];
            $transportadora = $transportadoras[$transportadora_id] ?? 'CTT';

            $stmt_produtos = $this->conn->prepare("SELECT p.nome, p.preco, p.foto, 1 as quantidade
                            FROM Encomendas e
                            INNER JOIN Produtos p ON e.produto_id = p.Produto_id
                            WHERE e.codigo_encomenda = ?");
            $stmt_produtos->bind_param("s", $codigo_encomenda);
            $stmt_produtos->execute();
            $result_produtos = $stmt_produtos->get_result();
            $produtos_array = [];
            $inline_images = [];

            if ($result_produtos && $result_produtos->num_rows > 0) {
                $index = 1;
                while ($prod = $result_produtos->fetch_assoc()) {
                    $foto_path = $prod['foto'];
                    $cid = "produto_$index";

                    if (!empty($foto_path)) {
                        $foto_path = str_replace('src/img/', '', $foto_path);
                        $foto_path = str_replace('src\\img\\', '', $foto_path);

                        $absolute_path = __DIR__ . '/../../src/img/' . $foto_path;

                        if (file_exists($absolute_path)) {
                            $inline_images[$cid] = $absolute_path;
                            $foto_url = "cid:$cid";
                        } else {

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
                        'foto' => $foto_url
                    ];

                    $index++;
                }
            }

            $morada_para_mapa = ($dadosEntrega['tipo_entrega'] ?? 'domicilio') === 'ponto_recolha'
                ? ($dadosEntrega['morada_ponto_recolha'] ?? $morada)
                : ($dadosEntrega['morada_completa'] ?? $morada);

            $mapa_url = $this->gerarMapaEstatico($morada_para_mapa);

            $dadosCliente = [
                'codigo_encomenda' => $codigo_encomenda,
                'data_encomenda' => date('Y-m-d H:i:s'),
                'payment_method' => 'Stripe (Cartão de Crédito)',
                'transportadora' => $transportadora,
                'morada' => $morada,
                'total' => $total,
                'produtos' => $produtos_array,
                'tipo_entrega' => $dadosEntrega['tipo_entrega'] ?? 'domicilio',
                'nome_ponto_recolha' => $dadosEntrega['nome_ponto_recolha'] ?? null,
                'morada_ponto_recolha' => $dadosEntrega['morada_ponto_recolha'] ?? null,
                'morada_completa' => $dadosEntrega['morada_completa'] ?? $morada,
                'nome_destinatario' => $dadosEntrega['nome_destinatario'] ?? 'Cliente',
                'mapa_url' => $mapa_url
            ];

            $emailService->sendFromTemplate(
                $cliente_id,
                'confirmacao_encomenda',
                $dadosCliente,
                'cliente',
                $inline_images
            );

            $stmt_cliente = $this->conn->prepare("SELECT nome, email FROM Utilizadores WHERE id = ? LIMIT 1");
            $stmt_cliente->bind_param("i", $cliente_id);
            $stmt_cliente->execute();
            $result_cliente = $stmt_cliente->get_result();
            $cliente = $result_cliente->fetch_assoc();


            $comissao = 0;
            foreach ($produtos_array as $prod_email) {
                $val_prod = $prod_email['preco'] * $prod_email['quantidade'];
                $taxa_prod = $this->getTaxaComissaoPorProduto((int)$prod_email['produto_id']);
                $comissao += $val_prod * $taxa_prod;
            }
            $lucro_liquido = $total - $comissao;
            $taxaComissaoMedia = $total > 0 ? ($comissao / $total) : 0.06;

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
                'taxa_comissao_percent' => round($taxaComissaoMedia * 100, 2),
                'lucro_liquido' => $lucro_liquido,
                'tipo_entrega' => $dadosEntrega['tipo_entrega'] ?? 'domicilio',
                'nome_ponto_recolha' => $dadosEntrega['nome_ponto_recolha'] ?? null,
                'morada_ponto_recolha' => $dadosEntrega['morada_ponto_recolha'] ?? null,
                'morada_completa' => $dadosEntrega['morada_completa'] ?? $morada,
                'nome_destinatario' => $dadosEntrega['nome_destinatario'] ?? $cliente['nome']
            ];

            $emailService->sendFromTemplate(
                $anunciante_id,
                'nova_encomenda_anunciante',
                $dadosAnunciante,
                'anunciante',
                $inline_images
            );

        } catch (Exception $e) {

        }
    }


    private function gerarMapaEstatico($morada) {
        try {

        $morada_clean = trim($morada);
        if (empty($morada_clean)) {
            return '';
        }


        $nominatim_url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($morada_clean) . '&countrycodes=pt&limit=1';


        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: WeGreen-Marketplace/1.0\r\n"
            ]
        ]);

        try {
            $response = @file_get_contents($nominatim_url, false, $context);

            if ($response === false) {
                return '';
            }

            $data = json_decode($response, true);

            if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
                return '';
            }

            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];



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
            return '';
        }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => 'Erro interno do servidor'], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
