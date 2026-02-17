<?php

require_once '../config/email_config.php';
require_once '../services/EmailService.php';
require_once '../../connection.php';

function exemplo_entrega_domicilio($encomenda_id, $utilizador_id) {
    global $conn;

    $emailService = new EmailService($conn);

    
    $dados_email = [
        'nome_cliente' => 'João Silva',
        'codigo_encomenda' => 'WG-2026-001234',
        'data_encomenda' => '2026-01-10 15:30:00',

        
        'payment_method' => 'Stripe (Cartão de Crédito)', 

        
        'transportadora' => 'CTT', 

        
        'tipo_entrega' => 'domicilio', 

        
        'morada' => "Rua das Flores, 123, Apartamento 4B\n1000-001 Lisboa\nPortugal",

        
        'tracking_code' => 'CT123456789PT',
        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',

        
        'produtos' => [
            [
                'nome' => 'T-Shirt Sustentável',
                'quantidade' => 2,
                'preco' => 29.95,
                'subtotal' => 59.90,
                'foto' => 'cid:produto_1' 
            ]
        ],

        'total' => 59.90
    ];

    
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

function exemplo_ponto_recolha($encomenda_id, $utilizador_id) {
    global $conn;

    $emailService = new EmailService($conn);

    $dados_email = [
        'nome_cliente' => 'Maria Santos',
        'codigo_encomenda' => 'WG-2026-001235',
        'data_encomenda' => '2026-01-10 16:00:00',

        
        'payment_method' => 'Stripe', 

        
        'transportadora' => 'CTT - Ponto de Recolha',

        
        'tipo_entrega' => 'ponto_recolha', 

        
        'nome_ponto_recolha' => 'CTT - Loja do Cidadão de Entrecampos',

        
        'morada' => "Campo Grande, 25\n1700-093 Lisboa\nPortugal",

        
        'horario_ponto' => 'Segunda a Sexta: 08:30-19:30 | Sábado: 09:00-13:00',

        
        'tracking_code' => 'CT987654321PT',
        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',

        
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

function enviarEmailConfirmacao($encomenda_id) {
    global $conn;

    
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

    
    if ($encomenda['tipo_entrega'] === 'ponto_recolha') {
        
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

        
        $foto_path = __DIR__ . '/../../' . $produto['foto'];

        $produtos[] = [
            'nome' => $produto['nome'],
            'quantidade' => $produto['quantidade'],
            'preco' => $produto['preco'],
            'subtotal' => $produto['subtotal'],
            'foto' => 'cid:' . $cid
        ];

        
        if (file_exists($foto_path)) {
            $imagens_inline[$cid] = $foto_path;
        }

        $contador++;
    }

    
    $dados_email = array_merge($dados_email, [
        'nome_cliente' => $encomenda['cliente_nome'],
        'codigo_encomenda' => $encomenda['codigo_encomenda'],
        'data_encomenda' => $encomenda['data_encomenda'],
        'payment_method' => 'Stripe', 
        'transportadora' => $encomenda['transportadora'],
        'tracking_code' => $encomenda['tracking_code'],
        'link_tracking' => 'https://www.ctt.pt/feapl_2/app/open/objectSearch',
        'produtos' => $produtos,
        'total' => $encomenda['total']
    ]);

    
    $emailService = new EmailService($conn);
    return $emailService->sendFromTemplate(
        $encomenda['cliente_email'],
        'confirmacao_encomenda.php',
        $dados_email,
        'Confirmação de Encomenda - WeGreen',
        $imagens_inline
    );
}

?>
