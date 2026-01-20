<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'src/model/connection.php';

echo "<h2>🗑️ Remover IDs Duplicados Específicos</h2>";
echo "<style>body{font-family:sans-serif;padding:20px;} .success{color:#0f5132;background:#d1e7dd;padding:15px;border-radius:8px;margin:15px 0;} .error{color:#721c24;background:#f8d7da;padding:15px;border-radius:8px;margin:15px 0;}</style>";

if (!isset($conn)) {
    echo "<div class='error'>❌ Erro: Conexão à BD</div>";
    exit;
}

// IDs 14 e 25 são duplicados - vamos manter o 14 (mais antigo) e deletar o 25
$idToDelete = 25;

echo "<div class='error'>";
echo "<p><strong>⚠️ Duplicata encontrada:</strong></p>";
echo "<p>ID 14: Olá Maria! Sim, estou interessado no casaco verde.</p>";
echo "<p>ID 25: Olá Maria Santos! Sim, estou interessado no casaco...</p>";
echo "<p><strong>Ação:</strong> Deletar ID 25 (mantém ID 14)</p>";
echo "</div>";

$sql = "DELETE FROM mensagensadmin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idToDelete);

if ($stmt->execute()) {
    echo "<div class='success'>✅ ID $idToDelete removido com sucesso!</div>";
} else {
    echo "<div class='error'>❌ Erro: " . $stmt->error . "</div>";
}

// Verificar resultado
$count = $conn->query("SELECT COUNT(*) as total FROM mensagensadmin WHERE remetente_id = 2 OR destinatario_id = 2");
$row = $count->fetch_assoc();

echo "<div class='success'>";
echo "<p><strong>Total de mensagens restantes:</strong> {$row['total']}</p>";
echo "</div>";

$stmt->close();
$conn->close();

echo "<p style='text-align:center;'><a href='test_chat_debug.php' style='display:inline-block;background:#3cb371;color:white;padding:12px 24px;text-decoration:none;border-radius:8px;'>🧪 Testar</a></p>";
?>
