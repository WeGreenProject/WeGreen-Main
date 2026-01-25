<?php
// Script para criar devoluções de teste
include_once 'connection.php';

try {
    // Buscar encomendas entregues do anunciante
    $result = $conn->query("
        SELECT e.id, e.codigo_encomenda, e.cliente_id, e.anunciante_id, e.produto_id
        FROM encomendas e
        WHERE e.estado = 'entregue'
        AND e.anunciante_id = 12
        LIMIT 3
    ");

    $encomendas = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $encomendas[] = $row;
        }
    }

    if (empty($encomendas)) {
        echo "Nenhuma encomenda entregue encontrada. Vou atualizar uma encomenda para 'entregue'...\n";

        $conn->query("UPDATE encomendas SET estado = 'entregue' WHERE anunciante_id = 12 LIMIT 1");

        $result = $conn->query("
            SELECT e.id, e.codigo_encomenda, e.cliente_id, e.anunciante_id, e.produto_id
            FROM encomendas e
            WHERE e.estado = 'entregue'
            AND e.anunciante_id = 12
            LIMIT 3
        ");

        $encomendas = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $encomendas[] = $row;
            }
        }
    }

    $motivos = ['defeituoso', 'tamanho_errado', 'nao_como_descrito', 'arrependimento', 'outro'];
    $estados = ['solicitada', 'aprovada', 'rejeitada', 'produto_recebido', 'reembolsada'];

    foreach ($encomendas as $index => $enc) {
        $stmt = $conn->prepare("SELECT preco FROM Produtos WHERE id = ?");
        $stmt->bind_param("i", $enc['produto_id']);
        $stmt->execute();
        $stmt->bind_result($preco);
        $stmt->fetch();
        $valor_reembolso = $preco ?? 25.00;
        $stmt->close();

        $codigo_devolucao = 'DEV-' . date('Ymd') . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
        $motivo = $motivos[$index % count($motivos)];
        $estado = $estados[$index % count($estados)];

        $stmt = $conn->prepare("SELECT id FROM devolucoes WHERE codigo_devolucao = ?");
        $stmt->bind_param("s", $codigo_devolucao);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo "Devolução $codigo_devolucao já existe. Pulando...\n";
            $stmt->close();
            continue;
        }
        $stmt->close();

        $sql = "INSERT INTO devolucoes (
            encomenda_id, codigo_devolucao, cliente_id, anunciante_id, produto_id,
            valor_reembolso, motivo, motivo_detalhe, notas_cliente, estado, data_solicitacao
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $motivo_detalhe = "Detalhe sobre o motivo: " . $motivo;
        $notas = "Notas do cliente sobre a devolução";
        $stmt->bind_param(
            "isiisdssss",
            $enc['id'], $codigo_devolucao, $enc['cliente_id'], $enc['anunciante_id'],
            $enc['produto_id'], $valor_reembolso, $motivo, $motivo_detalhe, $notas, $estado
        );
        $stmt->execute();
        $stmt->close();

        echo "✅ Devolução criada: $codigo_devolucao (Estado: $estado, Motivo: $motivo, Valor: €$valor_reembolso)\n";
    }

    echo "\n✅ Devoluções de teste criadas com sucesso!\n";
    echo "Acesse: http://localhost/WeGreen-Main/gestaoDevolucoesAnunciante.php\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
?>