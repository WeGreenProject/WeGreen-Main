<?php
/**
 * GUIA: Como enviar emails de confirmação com Stripe e diferentes tipos de entrega
 *
 * Este arquivo documenta como usar o EmailService corretamente
 */

require_once '../config/email_config.php';
require_once '../services/EmailService.php';
require_once '../../connection.php';

// ============================================================
// EXEMPLO 1: Entrega ao Domicílio com Stripe
// ============================================================

function exemplo_entrega_domicilio($encomenda_id, $utilizador_id) {
    global $conn;

    $emailService = new EmailService();

    // Buscar dados da encomenda
    $dados_email = [
        'nome_cliente' => 'João Silva',
        'codigo_encomenda' => 'WG-2026-001234',
        'data_encomenda' => '2026-01-10 15:30:00',

        // Pagamento - Sempre Stripe
        'payment_method' => 'Stripe (Cartão de Crédito)', // Ou simplesmente 'Stripe'

        // Transportadora
        'transportadora' => 'CTT', // Ou 'DHL', 'UPS', etc.

        // Tipo de entrega
        'tipo_entrega' => 'domicilio', // 'domicilio' ou 'ponto_recolha'

        // Morada de entrega ao domicílio
        'morada' => "Rua das Flores, 123, Apartamento 4B\n1000-001 Lisboa\nPortugal",

        // Tracking (opcional)
        'tracking_code' => 'CT123456789PT',
        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',

        // Produtos
        'produtos' => [
            [
                'nome' => 'T-Shirt Sustentável',
                'quantidade' => 2,
                'preco' => 29.95,
                'subtotal' => 59.90,
                'foto' => 'cid:produto_1' // Usando imagens inline
            ]
        ],

        'total' => 59.90
    ];

    // Imagens inline (opcional)
    $imagens_inline = [
        'produto_1' => __DIR__ . '/../../src/img/tshirt.jpg'
    ];

    return $emailService->sendFromTemplate(
        'cliente@exemplo.com',
        'confirmacao_encomenda.php',
        $dados_email,
        'Confirmação de Encomenda - WeGreen',
        $imagens_inline
    );
}

// ============================================================
// EXEMPLO 2: Ponto de Recolha CTT com Stripe
// ============================================================

function exemplo_ponto_recolha($encomenda_id, $utilizador_id) {
    global $conn;

    $emailService = new EmailService();

    $dados_email = [
        'nome_cliente' => 'Maria Santos',
        'codigo_encomenda' => 'WG-2026-001235',
        'data_encomenda' => '2026-01-10 16:00:00',

        // Pagamento - Sempre Stripe
        'payment_method' => 'Stripe', // Será mostrado como "Stripe (Cartão de Crédito)"

        // Transportadora
        'transportadora' => 'CTT - Ponto de Recolha',

        // Tipo de entrega - IMPORTANTE!
        'tipo_entrega' => 'ponto_recolha', // Muda o ícone e título

        // Nome do ponto de recolha
        'nome_ponto_recolha' => 'CTT - Loja do Cidadão de Entrecampos',

        // Morada do ponto de recolha
        'morada' => "Campo Grande, 25\n1700-093 Lisboa\nPortugal",

        // Horário do ponto (opcional)
        'horario_ponto' => 'Segunda a Sexta: 08:30-19:30 | Sábado: 09:00-13:00',

        // Tracking
        'tracking_code' => 'CT987654321PT',
        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',

        // Produtos
        'produtos' => [
            [
                'nome' => 'Colar Ecológico',
                'quantidade' => 1,
                'preco' => 15.00,
                'subtotal' => 15.00,
                'foto' => 'cid:produto_1'
            ]
        ],

        'total' => 15.00
    ];

    $imagens_inline = [
        'produto_1' => __DIR__ . '/../../src/img/colar.jpg'
    ];

    return $emailService->sendFromTemplate(
        'cliente@exemplo.com',
        'confirmacao_encomenda.php',
        $dados_email,
        'Encomenda Pronta para Recolha - WeGreen',
        $imagens_inline
    );
}

// ============================================================
// EXEMPLO 3: Buscar dados da DB e enviar email
// ============================================================

function enviarEmailConfirmacao($encomenda_id) {
    global $conn;

    // 1. Buscar dados do cliente
    $sql_encomenda = "SELECT
            e.codigo_encomenda,
            e.total,
            e.data_encomenda,
            e.tipo_entrega,
            e.transportadora,
            e.tracking_code,
            u.nome as cliente_nome,
            u.email as cliente_email
        FROM Encomendas e
        INNER JOIN Utilizadores u ON e.utilizador_id = u.id
        WHERE e.encomenda_id = ?";

    $stmt = $conn->prepare($sql_encomenda);
    $stmt->bind_param("i", $encomenda_id);
    $stmt->execute();
    $encomenda = $stmt->get_result()->fetch_assoc();

    if (!$encomenda) {
        return false;
    }

    // 2. Buscar morada baseado no tipo de entrega
    if ($encomenda['tipo_entrega'] === 'ponto_recolha') {
        // Buscar ponto de recolha
        $sql_ponto = "SELECT nome, morada, horario
                      FROM Pontos_Recolha
                      WHERE ponto_id = (
                          SELECT ponto_recolha_id FROM Encomendas WHERE encomenda_id = ?
                      )";
        $stmt = $conn->prepare($sql_ponto);
        $stmt->bind_param("i", $encomenda_id);
        $stmt->execute();
        $ponto = $stmt->get_result()->fetch_assoc();

        $dados_email = [
            'tipo_entrega' => 'ponto_recolha',
            'nome_ponto_recolha' => $ponto['nome'],
            'morada' => $ponto['morada'],
            'horario_ponto' => $ponto['horario']
        ];
    } else {
        // Buscar morada de entrega do cliente
        $sql_morada = "SELECT morada_entrega
                       FROM Encomendas
                       WHERE encomenda_id = ?";
        $stmt = $conn->prepare($sql_morada);
        $stmt->bind_param("i", $encomenda_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $dados_email = [
            'tipo_entrega' => 'domicilio',
            'morada' => $result['morada_entrega']
        ];
    }

    // 3. Buscar produtos com fotos
    $sql_produtos = "SELECT
            p.nome,
            p.preco,
            p.foto,
            ei.quantidade,
            (p.preco * ei.quantidade) as subtotal
        FROM Encomenda_Itens ei
        INNER JOIN Produtos p ON ei.produto_id = p.Produto_id
        WHERE ei.encomenda_id = ?";

    $stmt = $conn->prepare($sql_produtos);
    $stmt->bind_param("i", $encomenda_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $produtos = [];
    $imagens_inline = [];
    $contador = 1;

    while ($produto = $result->fetch_assoc()) {
        $cid = 'produto_' . $contador;

        // Caminho da foto
        $foto_path = __DIR__ . '/../../' . $produto['foto'];

        $produtos[] = [
            'nome' => $produto['nome'],
            'quantidade' => $produto['quantidade'],
            'preco' => $produto['preco'],
            'subtotal' => $produto['subtotal'],
            'foto' => 'cid:' . $cid
        ];

        // Anexar foto se existir
        if (file_exists($foto_path)) {
            $imagens_inline[$cid] = $foto_path;
        }

        $contador++;
    }

    // 4. Preparar dados completos
    $dados_email = array_merge($dados_email, [
        'nome_cliente' => $encomenda['cliente_nome'],
        'codigo_encomenda' => $encomenda['codigo_encomenda'],
        'data_encomenda' => $encomenda['data_encomenda'],
        'payment_method' => 'Stripe', // Sempre Stripe
        'transportadora' => $encomenda['transportadora'],
        'tracking_code' => $encomenda['tracking_code'],
        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',
        'produtos' => $produtos,
        'total' => $encomenda['total']
    ]);

    // 5. Enviar email
    $emailService = new EmailService();
    return $emailService->sendFromTemplate(
        $encomenda['cliente_email'],
        'confirmacao_encomenda.php',
        $dados_email,
        'Confirmação de Encomenda - WeGreen',
        $imagens_inline
    );
}

// ============================================================
// RESUMO - Campos Obrigatórios e Opcionais
// ============================================================

/*
CAMPOS OBRIGATÓRIOS:
--------------------
- nome_cliente: string
- codigo_encomenda: string
- data_encomenda: string (formato Y-m-d H:i:s)
- morada: string (com \n para quebras de linha)
- produtos: array de arrays
- total: float

CAMPOS OPCIONAIS COM DEFAULTS:
-------------------------------
- payment_method: padrão 'Stripe (Cartão de Crédito)'
- transportadora: padrão 'CTT'
- tipo_entrega: padrão 'domicilio' ('domicilio' ou 'ponto_recolha')

CAMPOS ESPECÍFICOS PONTO DE RECOLHA:
-------------------------------------
- nome_ponto_recolha: string (nome do ponto CTT)
- horario_ponto: string (horário de funcionamento)

CAMPOS OPCIONAIS TRACKING:
--------------------------
- tracking_code: string
- link_tracking: string (URL de rastreamento)

ESTRUTURA DE PRODUTOS:
----------------------
[
    'nome' => string,
    'quantidade' => int,
    'preco' => float,
    'subtotal' => float,
    'foto' => 'cid:produto_X' (para inline) ou URL externa
]

IMAGENS INLINE:
---------------
$imagens_inline = [
    'produto_1' => '/caminho/absoluto/para/imagem.jpg',
    'produto_2' => '/caminho/absoluto/para/imagem2.jpg'
];
*/
?>
