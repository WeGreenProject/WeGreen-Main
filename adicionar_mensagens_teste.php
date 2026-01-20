<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'src/model/connection.php';

echo "<h2>🔧 Adicionar Mensagens de Teste ao Chat</h2>";
echo "<style>body{font-family:sans-serif;padding:20px;} .success{color:#0f5132;background:#d1e7dd;padding:10px;border-radius:5px;margin:10px 0;} .error{color:#721c24;background:#f8d7da;padding:10px;border-radius:5px;margin:10px 0;}</style>";

if (!isset($conn)) {
    echo "<div class='error'>❌ Erro: Conexão à BD não estabelecida</div>";
    exit;
}

// Inserir mensagens Admin → Cliente
$sql1 = "INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem, created_at) VALUES
(1, 2, 'Olá! Em que posso ajudar?', '2026-01-17 14:00:00'),
(2, 1, 'Olá! Gostaria de saber sobre o estado da minha encomenda.', '2026-01-17 14:05:00'),
(1, 2, 'Claro! Pode fornecer o número da encomenda?', '2026-01-17 14:10:00'),
(2, 1, 'Sim, é a encomenda #12345', '2026-01-17 14:12:00'),
(1, 2, 'Obrigado! A sua encomenda foi enviada ontem e deverá chegar em 2-3 dias úteis.', '2026-01-17 14:15:00'),
(2, 1, 'Perfeito! Muito obrigado pela ajuda! 😊', '2026-01-17 14:20:00')";

echo "<h3>1. Adicionando conversa com Admin...</h3>";
if ($conn->multi_query($sql1)) {
    echo "<div class='success'>✅ Conversa com Admin adicionada com sucesso!</div>";
    // Limpar resultados
    while ($conn->next_result()) {;}
} else {
    echo "<div class='error'>❌ Erro: " . $conn->error . "</div>";
}

// Verificar se anunciante ID 3 existe
$check = $conn->query("SELECT nome FROM utilizadores WHERE id = 3");
if ($check && $check->num_rows > 0) {
    $row = $check->fetch_assoc();
    $nomeAnunciante = $row['nome'];

    // Inserir mensagens Anunciante → Cliente
    $sql2 = "INSERT INTO mensagensadmin (remetente_id, destinatario_id, mensagem, created_at) VALUES
    (3, 2, 'Olá! Vi que adicionou um dos meus produtos aos favoritos. Posso ajudar?', '2026-01-18 10:00:00'),
    (2, 3, 'Olá $nomeAnunciante! Sim, estou interessado no casaco verde. Tem em tamanho M?', '2026-01-18 10:15:00'),
    (3, 2, 'Sim, temos em tamanho M! É feito com algodão orgânico certificado. Quer que reserve?', '2026-01-18 10:20:00'),
    (2, 3, 'Sim, por favor! Quanto fica com o envio?', '2026-01-18 10:25:00'),
    (3, 2, 'O produto custa 45€ e o envio é grátis em encomendas acima de 30€! 🎉', '2026-01-18 10:30:00')";

    echo "<h3>2. Adicionando conversa com Anunciante ($nomeAnunciante)...</h3>";
    if ($conn->multi_query($sql2)) {
        echo "<div class='success'>✅ Conversa com Anunciante adicionada com sucesso!</div>";
        while ($conn->next_result()) {;}
    } else {
        echo "<div class='error'>❌ Erro: " . $conn->error . "</div>";
    }
} else {
    echo "<h3>2. Anunciante ID=3 não encontrado</h3>";
    echo "<div class='error'>⚠️ Pulando conversa com anunciante</div>";
}

// Mostrar resultado final
echo "<h3>3. Mensagens do Cliente (ID=2):</h3>";
$result = $conn->query("
    SELECT
        m.id,
        u1.nome as remetente,
        u2.nome as destinatario,
        m.mensagem,
        m.created_at
    FROM mensagensadmin m
    JOIN utilizadores u1 ON m.remetente_id = u1.id
    JOIN utilizadores u2 ON m.destinatario_id = u2.id
    WHERE m.remetente_id = 2 OR m.destinatario_id = 2
    ORDER BY m.created_at DESC
    LIMIT 20
");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
    echo "<tr style='background:#f8f9fa;'><th>ID</th><th>De</th><th>Para</th><th>Mensagem</th><th>Data</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['remetente']}</td>";
        echo "<td>{$row['destinatario']}</td>";
        echo "<td>" . substr($row['mensagem'], 0, 60) . "...</td>";
        echo "<td>" . date('d/m H:i', strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<div class='success'>✅ Total: {$result->num_rows} mensagens</div>";
} else {
    echo "<div class='error'>❌ Nenhuma mensagem encontrada</div>";
}

$conn->close();

echo "<br><hr><br>";
echo "<h3>✅ Próximos Passos:</h3>";
echo "<ol>";
echo "<li>Faça <a href='login.html'>login como Cliente</a></li>";
echo "<li>Aceda ao <a href='ChatCliente.php'><strong>ChatCliente.php</strong></a></li>";
echo "<li>Deve ver 2 conversas: uma com Admin e outra com Anunciante</li>";
echo "<li>Teste enviar mensagens</li>";
echo "</ol>";
echo "<br>";
echo "<a href='verificar_chat.php'>🔙 Voltar à Verificação</a> | ";
echo "<a href='test_chat_debug.php'>🧪 Debug Completo</a> | ";
echo "<a href='ChatCliente.php'>💬 Abrir Chat</a>";
?>
