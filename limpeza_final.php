<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'src/model/connection.php';

echo "<h2>🔧 Limpeza Final - Duplicatas e Inconsistências</h2>";
echo "<style>body{font-family:sans-serif;padding:20px;background:#f8f9fa;} .success{color:#0f5132;background:#d1e7dd;padding:15px;border-radius:8px;margin:15px 0;} .error{color:#721c24;background:#f8d7da;padding:15px;border-radius:8px;margin:15px 0;} .info{color:#055160;background:#cff4fc;padding:15px;border-radius:8px;margin:15px 0;} table{border-collapse:collapse;width:100%;margin:20px 0;} th,td{padding:10px;border:1px solid #ddd;text-align:left;font-size:13px;} th{background:#2c3e50;color:white;} .delete{background:#f8d7da;}</style>";

if (!isset($conn)) {
    echo "<div class='error'>❌ Erro: Conexão à BD não estabelecida</div>";
    exit;
}

echo "<div class='info'><strong>🔍 A procurar duplicatas...</strong></div>";

// 1. Duplicatas EXATAS
echo "<h3>1. Duplicatas Exatas (remetente + destinatário + mensagem + data)</h3>";
$sql1 = "SELECT
    remetente_id,
    destinatario_id,
    mensagem,
    created_at,
    COUNT(*) as total,
    GROUP_CONCAT(id ORDER BY id) as ids
FROM mensagensadmin
GROUP BY remetente_id, destinatario_id, mensagem, created_at
HAVING COUNT(*) > 1
ORDER BY created_at DESC";

$result1 = $conn->query($sql1);
$idsToDelete = [];

if ($result1 && $result1->num_rows > 0) {
    echo "<table><tr><th>De→Para</th><th>Mensagem</th><th>Data</th><th>Qty</th><th>IDs</th><th>Ação</th></tr>";

    while ($row = $result1->fetch_assoc()) {
        $ids = explode(',', $row['ids']);
        $keepId = array_shift($ids); // Mantém o primeiro
        $deleteIds = $ids; // Deleta os restantes

        echo "<tr>";
        echo "<td>{$row['remetente_id']}→{$row['destinatario_id']}</td>";
        echo "<td>" . substr($row['mensagem'], 0, 40) . "...</td>";
        echo "<td>" . date('d/m H:i', strtotime($row['created_at'])) . "</td>";
        echo "<td style='color:red;font-weight:bold;'>{$row['total']}</td>";
        echo "<td>Manter: <strong>$keepId</strong><br>Deletar: " . implode(', ', $deleteIds) . "</td>";
        echo "<td class='delete'>❌ Remover</td>";
        echo "</tr>";

        $idsToDelete = array_merge($idsToDelete, $deleteIds);
    }
    echo "</table>";
    echo "<div class='error'>❌ Total a remover: <strong>" . count($idsToDelete) . "</strong></div>";
} else {
    echo "<div class='success'>✅ Sem duplicatas exatas!</div>";
}

// 2. Duplicatas SIMILARES (mesmo remetente + destinatário + hora próxima)
echo "<h3>2. Duplicatas Similares (mesmo contexto, hora próxima)</h3>";
$sql2 = "SELECT
    m1.id as id1,
    m1.mensagem as msg1,
    m1.created_at as data1,
    m2.id as id2,
    m2.mensagem as msg2,
    m2.created_at as data2,
    m1.remetente_id,
    m1.destinatario_id,
    TIMESTAMPDIFF(SECOND, m1.created_at, m2.created_at) as diff_seconds
FROM mensagensadmin m1
JOIN mensagensadmin m2 ON (
    m1.remetente_id = m2.remetente_id
    AND m1.destinatario_id = m2.destinatario_id
    AND m1.id < m2.id
    AND ABS(TIMESTAMPDIFF(SECOND, m1.created_at, m2.created_at)) < 60
    AND (
        m1.mensagem = m2.mensagem
        OR LEFT(m1.mensagem, 40) = LEFT(m2.mensagem, 40)
    )
)
WHERE (m1.remetente_id = 2 OR m1.destinatario_id = 2)
ORDER BY m1.created_at DESC";

$result2 = $conn->query($sql2);

if ($result2 && $result2->num_rows > 0) {
    echo "<table><tr><th>IDs</th><th>De→Para</th><th>Mensagem 1</th><th>Mensagem 2</th><th>Dif. Tempo</th><th>Ação</th></tr>";

    while ($row = $result2->fetch_assoc()) {
        echo "<tr>";
        echo "<td>ID {$row['id1']}<br>ID {$row['id2']}</td>";
        echo "<td>{$row['remetente_id']}→{$row['destinatario_id']}</td>";
        echo "<td>" . substr($row['msg1'], 0, 35) . "...</td>";
        echo "<td>" . substr($row['msg2'], 0, 35) . "...</td>";
        echo "<td>{$row['diff_seconds']}s</td>";
        echo "<td class='delete'>Deletar ID {$row['id2']}</td>";
        echo "</tr>";

        $idsToDelete[] = $row['id2'];
    }
    echo "</table>";
    echo "<div class='error'>❌ Duplicatas similares: <strong>{$result2->num_rows}</strong></div>";
} else {
    echo "<div class='success'>✅ Sem duplicatas similares!</div>";
}

// 3. EXECUTAR REMOÇÃO
if (!empty($idsToDelete)) {
    $idsToDelete = array_unique($idsToDelete); // Remove IDs duplicados da lista
    $idsString = implode(',', $idsToDelete);

    echo "<h3>3. Executar Remoção</h3>";
    echo "<div class='info'><strong>IDs a remover:</strong> $idsString</div>";

    $deleteSql = "DELETE FROM mensagensadmin WHERE id IN ($idsString)";

    if ($conn->query($deleteSql)) {
        $removidos = $conn->affected_rows;
        echo "<div class='success'>✅ <strong>$removidos mensagens removidas com sucesso!</strong></div>";
    } else {
        echo "<div class='error'>❌ Erro ao remover: " . $conn->error . "</div>";
    }
} else {
    echo "<h3>3. Nada a Remover</h3>";
    echo "<div class='success'>✅ Base de dados está limpa!</div>";
}

// 4. RESULTADO FINAL
echo "<h3>4. Estado Final da BD</h3>";
$final = $conn->query("SELECT COUNT(*) as total FROM mensagensadmin WHERE remetente_id = 2 OR destinatario_id = 2");
$rowFinal = $final->fetch_assoc();

echo "<div class='success'>";
echo "<p><strong>Total de mensagens do Cliente (ID 2):</strong> {$rowFinal['total']}</p>";
echo "</div>";

// Listar mensagens finais
echo "<h4>Mensagens Finais (ordem cronológica):</h4>";
$mensagens = $conn->query("
    SELECT
        m.id,
        u1.nome as remetente,
        u2.nome as destinatario,
        LEFT(m.mensagem, 50) as mensagem,
        m.created_at
    FROM mensagensadmin m
    JOIN utilizadores u1 ON m.remetente_id = u1.id
    JOIN utilizadores u2 ON m.destinatario_id = u2.id
    WHERE m.remetente_id = 2 OR m.destinatario_id = 2
    ORDER BY m.created_at ASC
");

if ($mensagens->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>De</th><th>Para</th><th>Mensagem</th><th>Data</th></tr>";
    while($msg = $mensagens->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$msg['id']}</td>";
        echo "<td>{$msg['remetente']}</td>";
        echo "<td>{$msg['destinatario']}</td>";
        echo "<td>{$msg['mensagem']}...</td>";
        echo "<td>" . date('d/m H:i', strtotime($msg['created_at'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();

echo "<hr style='margin:40px 0;'>";
echo "<div style='text-align:center;'>";
echo "<h3>✅ Limpeza Concluída!</h3>";
echo "<p><a href='test_chat_debug.php' style='display:inline-block;background:#3cb371;color:white;padding:12px 24px;text-decoration:none;border-radius:8px;margin:5px;'>🧪 Testar Debug</a></p>";
echo "<p><a href='ChatCliente.php' style='display:inline-block;background:#2c3e50;color:white;padding:12px 24px;text-decoration:none;border-radius:8px;margin:5px;'>💬 Abrir Chat</a></p>";
echo "</div>";
?>
