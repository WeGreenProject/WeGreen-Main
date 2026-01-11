<?php
/**
 * DIAGNÓSTICO: Verificar porque o mapa não aparece no modal
 */
require_once 'connection.php';
session_start();

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Diagnóstico Mapa</title>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#f2f2f2;}</style>";
echo "</head><body>";

echo "<h1>🔍 Diagnóstico do Mapa - Encomenda #WG12345</h1>";

// 1. Verificar se tabela existe
echo "<h2>1️⃣ Verificar Tabela Encomendas</h2>";
$sql_check = "SHOW TABLES LIKE 'Encomendas'";
$result = $conn->query($sql_check);
if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✅ Tabela 'Encomendas' existe</p>";
} else {
    echo "<p class='error'>❌ Tabela 'Encomendas' NÃO existe!</p>";
    exit;
}

// 2. Verificar estrutura da tabela
echo "<h2>2️⃣ Estrutura da Tabela (campos novos)</h2>";
$campos_novos = ['tipo_entrega', 'ponto_recolha_id', 'nome_ponto_recolha', 'morada_ponto_recolha', 'morada_completa', 'nome_destinatario'];
$campos_existentes = [];
$campos_faltando = [];

$sql_structure = "DESCRIBE Encomendas";
$result = $conn->query($sql_structure);
$all_columns = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $all_columns[] = $row['Field'];
    }
}

foreach ($campos_novos as $campo) {
    if (in_array($campo, $all_columns)) {
        $campos_existentes[] = $campo;
        echo "<p class='success'>✅ Campo '$campo' existe</p>";
    } else {
        $campos_faltando[] = $campo;
        echo "<p class='error'>❌ Campo '$campo' NÃO EXISTE - EXECUTE A MIGRAÇÃO!</p>";
    }
}

// 3. Buscar dados da encomenda WG12345
echo "<h2>3️⃣ Dados da Encomenda #WG12345</h2>";
$sql_encomenda = "SELECT * FROM Encomendas WHERE codigo_encomenda = 'WG12345' LIMIT 1";
$result = $conn->query($sql_encomenda);

if ($result && $result->num_rows > 0) {
    $encomenda = $result->fetch_assoc();
    echo "<p class='success'>✅ Encomenda encontrada</p>";

    echo "<h3>Campos de Morada:</h3>";
    echo "<table>";
    echo "<tr><th>Campo</th><th>Valor</th><th>Status</th></tr>";

    $morada_antiga = $encomenda['morada'] ?? null;
    echo "<tr><td><strong>morada</strong> (campo antigo)</td><td>" . htmlspecialchars($morada_antiga) . "</td><td>" . ($morada_antiga ? "✅ Preenchido" : "❌ Vazio") . "</td></tr>";

    foreach ($campos_novos as $campo) {
        if (isset($encomenda[$campo])) {
            $valor = $encomenda[$campo];
            $status = !empty($valor) ? "✅ Preenchido" : "⚠️ Vazio (NULL/empty)";
            $class = !empty($valor) ? "success" : "warning";
            echo "<tr><td><strong>$campo</strong></td><td>" . htmlspecialchars($valor ?? 'NULL') . "</td><td class='$class'>$status</td></tr>";
        } else {
            echo "<tr><td><strong>$campo</strong></td><td colspan='2' class='error'>❌ Campo não existe na tabela</td></tr>";
        }
    }
    echo "</table>";

    // 4. Testar lógica do mapa
    echo "<h2>4️⃣ Simulação da Lógica do Mapa (JavaScript)</h2>";

    $tipo_entrega = $encomenda['tipo_entrega'] ?? 'domicilio';
    $morada_completa = $encomenda['morada_completa'] ?? $encomenda['morada'] ?? '';
    $morada_ponto_recolha = $encomenda['morada_ponto_recolha'] ?? '';
    $nome_ponto_recolha = $encomenda['nome_ponto_recolha'] ?? '';

    echo "<ul>";
    echo "<li><strong>tipo_entrega detectado:</strong> <code>" . htmlspecialchars($tipo_entrega) . "</code></li>";

    if ($tipo_entrega === 'ponto_recolha') {
        $morada_final = $morada_ponto_recolha;
        echo "<li><strong>Modo:</strong> Ponto de Recolha</li>";
        echo "<li><strong>Nome do ponto:</strong> " . htmlspecialchars($nome_ponto_recolha) . "</li>";
        echo "<li><strong>Morada usada para mapa:</strong> <code>" . htmlspecialchars($morada_final) . "</code></li>";
    } else {
        $morada_final = $morada_completa;
        echo "<li><strong>Modo:</strong> Domicílio</li>";
        echo "<li><strong>Morada usada para mapa:</strong> <code>" . htmlspecialchars($morada_final) . "</code></li>";
    }
    echo "</ul>";

    if (empty($morada_final)) {
        echo "<p class='error'>❌ <strong>PROBLEMA ENCONTRADO:</strong> A morada final está vazia! O mapa NÃO pode ser gerado.</p>";
        echo "<p><strong>Solução:</strong> Execute o UPDATE abaixo para preencher os dados.</p>";
    } else {
        echo "<p class='success'>✅ Morada OK para gerar mapa</p>";

        // Gerar preview do mapa
        $endereco_encoded = urlencode($morada_final);
        $maps_url = "https://maps.google.com/maps?q={$endereco_encoded}&t=&z=15&ie=UTF8&iwloc=&output=embed";

        echo "<h3>Preview do Mapa:</h3>";
        echo "<div style='border:2px solid #22c55e; border-radius:8px; overflow:hidden; width:600px;'>";
        echo "<iframe width='600' height='300' frameborder='0' src='$maps_url'></iframe>";
        echo "</div>";
        echo "<p><a href='https://www.google.com/maps/search/?api=1&query={$endereco_encoded}' target='_blank'>🔗 Abrir no Google Maps</a></p>";
    }

} else {
    echo "<p class='error'>❌ Encomenda #WG12345 NÃO encontrada na base de dados!</p>";
}

// 5. Verificar resposta do controller
echo "<h2>5️⃣ Testar Controller (AJAX Response)</h2>";
if (isset($_SESSION['utilizador'])) {
    $cliente_id = $_SESSION['utilizador'];
    echo "<p>Cliente ID da sessão: <strong>$cliente_id</strong></p>";

    include_once 'src/model/modelEncomendas.php';
    $model = new Encomendas();
    $detalhes = $model->obterDetalhes('WG12345', $cliente_id);

    if ($detalhes) {
        echo "<p class='success'>✅ Model retorna dados</p>";
        echo "<h4>JSON que seria enviado ao JavaScript:</h4>";
        echo "<pre style='background:#f5f5f5;padding:15px;border-radius:5px;overflow-x:auto;'>";
        echo json_encode($detalhes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        echo "</pre>";
    } else {
        echo "<p class='error'>❌ Model NÃO retorna dados para este cliente</p>";
    }
} else {
    echo "<p class='warning'>⚠️ Sessão não iniciada - faça login para testar controller</p>";
}

// 6. Soluções
echo "<h2>6️⃣ Soluções</h2>";

if (!empty($campos_faltando)) {
    echo "<div style='background:#fff3cd;padding:15px;border-left:4px solid #ffc107;margin:10px 0;'>";
    echo "<h3>⚠️ MIGRAÇÃO NECESSÁRIA</h3>";
    echo "<p>Execute este SQL no phpMyAdmin (aba SQL da database WeGreen):</p>";
    echo "<p><a href='src/database/migration_encomendas_pickup.sql' target='_blank' style='background:#22c55e;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;'>📄 Abrir Ficheiro de Migração</a></p>";
    echo "<p>Ou copie o conteúdo de: <code>src/database/migration_encomendas_pickup.sql</code></p>";
    echo "</div>";
}

if (empty($campos_faltando) && isset($encomenda)) {
    $morada_atual = $encomenda['morada_completa'] ?? $encomenda['morada'] ?? '';

    if (empty($morada_atual)) {
        echo "<div style='background:#f8d7da;padding:15px;border-left:4px solid #dc3545;margin:10px 0;'>";
        echo "<h3>❌ DADOS VAZIOS</h3>";
        echo "<p>Os campos existem mas estão vazios. Execute este UPDATE:</p>";
        echo "<pre style='background:#fff;padding:10px;border:1px solid #ddd;overflow-x:auto;'>";
        echo "UPDATE Encomendas \n";
        echo "SET tipo_entrega = 'domicilio',\n";
        echo "    morada_completa = 'Rua das Flores 15, 1200-001 Lisboa'\n";
        echo "WHERE codigo_encomenda = 'WG12345';";
        echo "</pre>";
        echo "<p><small>Depois, recarregue a página minhasEncomendas.php</small></p>";
        echo "</div>";
    } else {
        echo "<div style='background:#d4edda;padding:15px;border-left:4px solid #28a745;margin:10px 0;'>";
        echo "<h3>✅ TUDO OK!</h3>";
        echo "<p>Os campos existem e têm dados. O mapa deve aparecer!</p>";
        echo "<p><strong>Próximos passos:</strong></p>";
        echo "<ol>";
        echo "<li>Abra: <a href='minhasEncomendas.php' target='_blank'>minhasEncomendas.php</a></li>";
        echo "<li>Faça login se necessário</li>";
        echo "<li>Clique em 'Ver Detalhes' da encomenda #WG12345</li>";
        echo "<li>O mapa deve aparecer abaixo da morada</li>";
        echo "</ol>";
        echo "<p><small>Se ainda não aparecer, abra o Console do browser (F12) e veja se há erros JavaScript</small></p>";
        echo "</div>";
    }
}

echo "</body></html>";
?>
