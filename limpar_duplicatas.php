<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'src/model/connection.php';

echo "<h2>🧹 Limpar Mensagens Duplicadas</h2>";
echo "<style>body{font-family:sans-serif;padding:20px;} .success{color:#0f5132;background:#d1e7dd;padding:10px;border-radius:5px;margin:10px 0;} .error{color:#721c24;background:#f8d7da;padding:10px;border-radius:5px;margin:10px 0;} table{border-collapse:collapse;width:100%;margin:20px 0;} th,td{padding:8px;border:1px solid #ddd;text-align:left;} th{background:#f8f9fa;}</style>";

if (!isset($conn)) {
    echo "<div class='error'>❌ Erro: Conexão à BD não estabelecida</div>";
    exit;
}

// Mostrar duplicatas
echo "<h3>1. Mensagens Duplicadas Encontradas:</h3>";
$sql = "SELECT
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

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Remetente</th><th>Destinatário</th><th>Mensagem</th><th>Duplicatas</th><th>IDs</th></tr>";

    $totalDuplicates = 0;
    $idsToDelete = [];

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['remetente_id']}</td>";
        echo "<td>{$row['destinatario_id']}</td>";
        echo "<td>" . substr($row['mensagem'], 0, 50) . "...</td>";
        echo "<td style='color:red;font-weight:bold;'>{$row['total']}</td>";
        echo "<td>{$row['ids']}</td>";
        echo "</tr>";

        // Guardar IDs para deletar (manter apenas o primeiro)
        $ids = explode(',', $row['ids']);
        array_shift($ids); // Remove o primeiro (mantém)
        $idsToDelete = array_merge($idsToDelete, $ids);
        $totalDuplicates += count($ids);
    }
    echo "</table>";

    echo "<div class='error'>❌ Total de duplicatas a remover: <strong>$totalDuplicates</strong></div>";

    // Deletar duplicatas
    if (!empty($idsToDelete)) {
        $idsString = implode(',', $idsToDelete);
        $deleteSql = "DELETE FROM mensagensadmin WHERE id IN ($idsString)";

        if ($conn->query($deleteSql)) {
            echo "<div class='success'>✅ Duplicatas removidas com sucesso!</div>";
        } else {
            echo "<div class='error'>❌ Erro ao remover duplicatas: " . $conn->error . "</div>";
        }
    }

} else {
    echo "<div class='success'>✅ Nenhuma duplicata encontrada!</div>";
}

// Mostrar mensagens finais
echo "<h3>2. Mensagens Finais (Cliente ID=2):</h3>";
$sql2 = "SELECT
    m.id,
    u1.nome as remetente,
    u2.nome as destinatario,
    m.mensagem,
    m.created_at
FROM mensagensadmin m
JOIN utilizadores u1 ON m.remetente_id = u1.id
JOIN utilizadores u2 ON m.destinatario_id = u2.id
WHERE m.remetente_id = 2 OR m.destinatario_id = 2
ORDER BY m.created_at ASC";

$result2 = $conn->query($sql2);

if ($result2 && $result2->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>De</th><th>Para</th><th>Mensagem</th><th>Data</th></tr>";
    while ($row = $result2->fetch_assoc()) {
        $direction = ($row['remetente'] == 'Joao') ? '→' : '←';
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['remetente']}</td>";
        echo "<td>$direction</td>";
        echo "<td>{$row['destinatario']}</td>";
        echo "<td>" . substr($row['mensagem'], 0, 60) . "</td>";
        echo "<td>" . date('d/m H:i', strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<div class='success'>✅ Total: {$result2->num_rows} mensagens únicas</div>";
} else {
    echo "<div class='error'>❌ Nenhuma mensagem encontrada</div>";
}

$conn->close();

echo "<br><hr><br>";
echo "<h3>✅ Próximos Passos:</h3>";
echo "<p>1. Duplicatas removidas ✅</p>";
echo "<p>2. Testa novamente: <a href='test_chat_debug.php'><strong>test_chat_debug.php</strong></a></p>";
echo "<p>3. Abre o chat: <a href='ChatCliente.php'><strong>ChatCliente.php</strong></a></p>";
?>
