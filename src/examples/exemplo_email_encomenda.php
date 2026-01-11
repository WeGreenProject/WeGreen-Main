<?php
/**
 * EXEMPLO: Como enviar email de confirmação de encomenda com fotos dos produtos da DB
 *
 * Use este código no controlador após confirmar pagamento
 */

require_once '../config/email_config.php';
require_once '../services/EmailService.php';
require_once '../../connection.php';

/**
 * Função: Gerar URL de mapa estático com marcador
 * Usa Nominatim (OpenStreetMap) para geocoding e StaticMap para imagem
 *
 * @param string $morada Morada completa para geocodificar
 * @return string URL da imagem do mapa ou string vazia se falhar
 */
function gerarMapaEstatico($morada) {
    if (empty($morada)) {
        return '';
    }

    try {
        // 1. Geocodificar morada usando Nominatim (OpenStreetMap)
        $morada_encoded = urlencode($morada . ', Portugal');
        $nominatim_url = "https://nominatim.openstreetmap.org/search?q={$morada_encoded}&format=json&limit=1";

        // Configurar context para incluir User-Agent (obrigatório para Nominatim)
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: WeGreen-Ecommerce/1.0\r\n"
            ]
        ]);

        $geocode_result = @file_get_contents($nominatim_url, false, $context);

        if ($geocode_result === false) {
            error_log("Falha ao geocodificar morada: $morada");
            return '';
        }

        $geocode_data = json_decode($geocode_result, true);

        if (empty($geocode_data) || !isset($geocode_data[0]['lat']) || !isset($geocode_data[0]['lon'])) {
            error_log("Nenhuma coordenada encontrada para morada: $morada");
            return '';
        }

        $lat = $geocode_data[0]['lat'];
        $lon = $geocode_data[0]['lon'];

        // 2. Gerar URL de mapa estático com marcador verde
        $zoom = 15;
        $width = 600;
        $height = 300;

        // Usando StaticMap.openstreetmap.de (alternativa: staticmap.org)
        $mapa_url = "https://staticmap.openstreetmap.de/staticmap.php?" .
                   "center={$lat},{$lon}" .
                   "&zoom={$zoom}" .
                   "&size={$width}x{$height}" .
                   "&markers={$lat},{$lon},green";

        return $mapa_url;

    } catch (Exception $e) {
        error_log("Erro ao gerar mapa estático: " . $e->getMessage());
        return '';
    }
}

/**
 * Função: Enviar Email de Confirmação de Encomenda
 *
 * @param int $encomenda_id ID da encomenda na DB
 * @param int $utilizador_id ID do cliente
 * @return bool True se email enviado com sucesso
 */
function enviarEmailConfirmacaoEncomenda($encomenda_id, $utilizador_id) {
    global $conn;

    // 1. Buscar dados do cliente
    $sql_cliente = "SELECT nome, email FROM Utilizadores WHERE id = ?";
    $stmt = $conn->prepare($sql_cliente);
    $stmt->bind_param("i", $utilizador_id);
    $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc();

    if (!$cliente) {
        error_log("Cliente não encontrado: $utilizador_id");
        return false;
    }

    // 2. Buscar dados da encomenda (com novos campos de entrega)
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
        error_log("Encomenda não encontrada: $encomenda_id");
        return false;
    }

    // Determinar morada para gerar mapa
    $morada_mapa = '';
    if ($encomenda['tipo_entrega'] === 'ponto_recolha') {
        $morada_mapa = $encomenda['morada_ponto_recolha'];
    } else {
        $morada_mapa = $encomenda['morada_completa'] ?: $encomenda['morada_entrega'];
    }

    // Gerar mapa estático se houver morada
    $mapa_url = '';
    if (!empty($morada_mapa)) {
        $mapa_url = gerarMapaEstatico($morada_mapa);
    }

    // 3. Buscar produtos da encomenda COM FOTOS
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
        // Construir URL completa da foto
        // Assumindo que 'foto' na DB é o caminho relativo (ex: 'assets/media/products/foto.jpg')
        $foto_url = 'http://localhost/WeGreen-Main/' . $produto['foto'];

        // Alternativamente, se a foto for apenas o nome do arquivo:
        // $foto_url = 'http://localhost/WeGreen-Main/assets/media/products/' . $produto['foto'];

        $produtos[] = [
            'nome' => $produto['nome'],
            'quantidade' => $produto['quantidade'],
            'preco' => $produto['preco'],
            'subtotal' => $produto['subtotal'],
            'foto' => $foto_url // ← URL completa da imagem
        ];
    }

    // 4. Preparar dados para o template
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
        'produtos' => $produtos, // Array com fotos incluídas
        'total' => $encomenda['total']
    ];

    // 5. Enviar email
    try {
        $emailService = new EmailService();

        $resultado = $emailService->sendFromTemplate(
            $cliente['email'],
            'confirmacao_encomenda.php',
            $dados_email,
            'Confirmação de Encomenda - WeGreen'
        );

        if ($resultado) {
            error_log("Email de confirmação enviado com sucesso para: " . $cliente['email']);
            return true;
        } else {
            error_log("Falha ao enviar email de confirmação para: " . $cliente['email']);
            return false;
        }

    } catch (Exception $e) {
        error_log("Erro ao enviar email: " . $e->getMessage());
        return false;
    }
}

// ============================================================
// EXEMPLO DE USO no controlador de checkout
// ============================================================

/*
// No controllerCheckout.php ou após confirmar pagamento:

if ($pagamento_confirmado) {
    // Salvar encomenda na DB
    $encomenda_id = criarEncomenda($utilizador_id, $dados_encomenda);

    // Enviar email de confirmação com fotos
    enviarEmailConfirmacaoEncomenda($encomenda_id, $utilizador_id);

    // Redirecionar para página de sucesso
    header("Location: sucess_carrinho.php?encomenda_id=$encomenda_id");
}
*/

// ============================================================
// EXEMPLO: Buscar produtos do carrinho (antes da encomenda)
// ============================================================

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
        // URL completa da foto
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

// ============================================================
// NOTAS IMPORTANTES
// ============================================================

/*
1. CAMINHO DA FOTO NA DB:
   - Se 'foto' contém caminho completo: use direto
   - Se 'foto' contém apenas nome: concatene com pasta base
   - Exemplo DB: "assets/media/products/produto123.jpg"
   - URL final: "http://localhost/WeGreen-Main/assets/media/products/produto123.jpg"

2. PRODUÇÃO (quando for para servidor real):
   - Troque 'http://localhost/WeGreen-Main' por 'https://seudominio.com'
   - Configure uma variável de ambiente para base URL

3. PERFORMANCE:
   - Use URLs externas (método atual) para emails mais leves
   - Gmail/Outlook renderizam melhor com URLs
   - Evite anexar imagens inline (deixa email pesado)

4. SEGURANÇA:
   - Valide que as imagens existem antes de enviar
   - Use prepared statements (como nos exemplos)
   - Sanitize dados antes de usar em URLs

5. MAPAS (NOVO):
   - Usa Nominatim (OpenStreetMap) para geocoding GRATUITO
   - Gera mapa estático via StaticMap.openstreetmap.de
   - Limite da API: 1 requisição por segundo (respeitar!)
   - Em produção, considere cache das coordenadas na DB

   TIPOS DE ENTREGA:
   - 'domicilio': Mostra morada_completa com mapa
   - 'ponto_recolha': Mostra nome_ponto_recolha + morada_ponto_recolha com mapa

   TESTE DE MAPA:
   1. Certifique-se que tem uma encomenda com tipo_entrega preenchido
   2. Execute: enviarEmailConfirmacaoEncomenda($encomenda_id, $utilizador_id)
   3. Verifique email: deve aparecer mapa com marcador verde
   4. Clique no mapa: abre Google Maps com a localização

   EXEMPLO DE MORADAS PARA TESTE:
   - "Rua Augusta 123, Lisboa"
   - "Avenida da Liberdade 50, Porto"
   - "Praça do Comércio, 1100-148 Lisboa"
*/
?>
