<?php

require_once '../config/email_config.php';
require_once '../services/EmailService.php';
require_once '../../connection.php';

function gerarMapaEstatico($morada) {
    if (empty($morada)) {
        return '';
    }

    try {
        
        $morada_encoded = urlencode($morada . ', Portugal');
        $nominatim_url = "https://nominatim.openstreetmap.org/search?q={$morada_encoded}&format=json&limit=1";

        
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: WeGreen-Ecommerce/1.0\r\n"
            ]
        ]);

        $geocode_result = @file_get_contents($nominatim_url, false, $context);

        if ($geocode_result === false) {
            return '';
        }

        $geocode_data = json_decode($geocode_result, true);

        if (empty($geocode_data) || !isset($geocode_data[0]['lat']) || !isset($geocode_data[0]['lon'])) {
            return '';
        }

        $lat = $geocode_data[0]['lat'];
        $lon = $geocode_data[0]['lon'];

        
        $zoom = 15;
        $width = 600;
        $height = 300;

        
        $mapa_url = "https://staticmap.openstreetmap.de/staticmap.php?" .
                   "center={$lat},{$lon}" .
                   "&zoom={$zoom}" .
                   "&size={$width}x{$height}" .
                   "&markers={$lat},{$lon},green";

        return $mapa_url;

    } catch (Exception $e) {
        return '';
    }
}

function enviarEmailConfirmacaoEncomenda($encomenda_id, $utilizador_id) {
    global $conn;

    
    $sql_cliente = "SELECT nome, email FROM Utilizadores WHERE id = ?";
    $stmt = $conn->prepare($sql_cliente);
    $stmt->bind_param("i", $utilizador_id);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();

    if (!$cliente) {
        return false;
    }

    
    $sql_encomenda = "SELECT
            codigo_encomenda,
            total,
            data_encomenda,
            morada_entrega,
            metodo_pagamento,
            transportadora,
            tracking_code,
            tipo_entrega,
            nome_ponto_recolha,
            morada_ponto_recolha,
            morada_completa,
            nome_destinatario
        FROM Encomendas
        WHERE encomenda_id = ?";

    $stmt = $conn->prepare($sql_encomenda);
    $stmt->bind_param("i", $encomenda_id);
    $stmt->execute();
    $encomenda = $stmt->get_result()->fetch_assoc();

    if (!$encomenda) {
        return false;
    }

    
    $morada_mapa = '';
    if ($encomenda['tipo_entrega'] === 'ponto_recolha') {
        $morada_mapa = $encomenda['morada_ponto_recolha'];
    } else {
        $morada_mapa = $encomenda['morada_completa'] ?: $encomenda['morada_entrega'];
    }

    
    $mapa_url = '';
    if (!empty($morada_mapa)) {
        $mapa_url = gerarMapaEstatico($morada_mapa);
    }

    
    $sql_produtos = "SELECT
            p.nome,
            p.preco,
            p.foto,
            ei.quantidade,
            (p.preco * ei.quantidade) as subtotal
        FROM Encomenda_Itens ei
        INNER JOIN Produtos p ON ei.produto_id = p.Produto_id
        WHERE ei.encomenda_id = ?
        ORDER BY ei.item_id";

    $stmt = $conn->prepare($sql_produtos);
    $stmt->bind_param("i", $encomenda_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $produtos = [];
    while ($produto = $result->fetch_assoc()) {
        
        
        $foto_url = 'http://localhost/WeGreen-Main/' . $produto['foto'];

        
        

        $produtos[] = [
            'nome' => $produto['nome'],
            'quantidade' => $produto['quantidade'],
            'preco' => $produto['preco'],
            'subtotal' => $produto['subtotal'],
            'foto' => $foto_url 
        ];
    }

    
    $dados_email = [
        'nome_cliente' => $cliente['nome'],
        'codigo_encomenda' => $encomenda['codigo_encomenda'],
        'data_encomenda' => date('d/m/Y H:i', strtotime($encomenda['data_encomenda'])),
        'payment_method' => $encomenda['metodo_pagamento'],
        'transportadora' => $encomenda['transportadora'],
        'tracking_code' => $encomenda['tracking_code'],
        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',
        'morada' => $encomenda['morada_entrega'],
        'tipo_entrega' => $encomenda['tipo_entrega'] ?: 'domicilio',
        'nome_ponto_recolha' => $encomenda['nome_ponto_recolha'],
        'morada_ponto_recolha' => $encomenda['morada_ponto_recolha'],
        'morada_completa' => $encomenda['morada_completa'] ?: $encomenda['morada_entrega'],
        'nome_destinatario' => $encomenda['nome_destinatario'],
        'mapa_url' => $mapa_url,
        'produtos' => $produtos, 
        'total' => $encomenda['total']
    ];

    
    try {
        $emailService = new EmailService($conn);

        $resultado = $emailService->sendFromTemplate(
            $cliente['email'],
            'confirmacao_encomenda.php',
            $dados_email,
            'ConfirmaÃ§Ã£o de Encomenda - WeGreen'
        );

        if ($resultado) {
            return true;
        } else {
            return false;
        }

    } catch (Exception $e) {
        return false;
    }
}

function buscarProdutosCarrinhoComFotos($utilizador_id) {
    global $conn;

    $sql = "SELECT
                p.Produto_id,
                p.nome,
                p.preco,
                p.foto,
                ci.quantidade,
                (p.preco * ci.quantidade) as subtotal
            FROM Carrinho_Itens ci
            INNER JOIN Produtos p ON ci.produto_id = p.Produto_id
            WHERE ci.utilizador_id = ?
            AND p.ativo = 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $utilizador_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $produtos = [];
    while ($produto = $result->fetch_assoc()) {
        
        $foto_url = 'http://localhost/WeGreen-Main/' . $produto['foto'];

        $produtos[] = [
            'id' => $produto['Produto_id'],
            'nome' => $produto['nome'],
            'quantidade' => $produto['quantidade'],
            'preco' => $produto['preco'],
            'subtotal' => $produto['subtotal'],
            'foto' => $foto_url
        ];
    }

    return $produtos;
}

?>
