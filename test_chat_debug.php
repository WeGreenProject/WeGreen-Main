<?php
session_start();

// Simular sessão de cliente (ID 2 - Cliente)
$_SESSION['utilizador'] = 2;
$_SESSION['tipo'] = 2;
$_SESSION['nome'] = 'Cliente Teste';

echo "<h2>Debug Chat Cliente</h2>";
echo "<pre>";

// Incluir model
require_once 'src/model/modelChatCliente.php';

$chat = new ChatCliente();

echo "<h3>1. Teste getSideBar() - Listar Vendedores com Conversas</h3>";
echo "Cliente ID: " . $_SESSION['utilizador'] . "\n\n";
$resultado1 = $chat->getSideBar($_SESSION['utilizador']);
echo "HTML Retornado:\n";
echo htmlspecialchars($resultado1);
echo "\n\n";

echo "<hr>";

echo "<h3>2. Teste getConversas() - Mensagens com Vendedor</h3>";
// Tentar com ID 1 (Admin/Anunciante)
echo "Buscando mensagens entre Cliente ID 2 e Vendedor ID 1\n\n";
$resultado2 = $chat->getConversas(2, 1);
echo "HTML Retornado:\n";
echo htmlspecialchars($resultado2);
echo "\n\n";

echo "<hr>";

echo "<h3>3. Verificar Estrutura da BD</h3>";
require_once 'src/model/connection.php';

// Verificar mensagens existentes
echo "Mensagens na tabela mensagensadmin:\n";
$sql = "SELECT m.id, m.remetente_id, u1.nome as remetente, m.destinatario_id, u2.nome as destinatario,
        LEFT(m.mensagem, 50) as mensagem, m.created_at
        FROM mensagensadmin m
        LEFT JOIN utilizadores u1 ON m.remetente_id = u1.id
        LEFT JOIN utilizadores u2 ON m.destinatario_id = u2.id
        ORDER BY m.created_at DESC
        LIMIT 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']} | {$row['remetente']} (ID {$row['remetente_id']}) → {$row['destinatario']} (ID {$row['destinatario_id']})\n";
        echo "Mensagem: {$row['mensagem']}\n";
        echo "Data: {$row['created_at']}\n\n";
    }
} else {
    echo "Nenhuma mensagem encontrada!\n";
}

echo "<hr>";

echo "<h3>4. Verificar Utilizadores Anunciantes</h3>";
$sql2 = "SELECT u.id, u.nome, tu.descricao as tipo
         FROM utilizadores u
         JOIN tipo_utilizadores tu ON u.tipo_utilizador_id = tu.id
         WHERE tu.descricao = 'Anunciante'
         LIMIT 5";
$result2 = $conn->query($sql2);
echo "Anunciantes na BD:\n";
if ($result2->num_rows > 0) {
    while($row = $result2->fetch_assoc()) {
        echo "ID: {$row['id']} | Nome: {$row['nome']} | Tipo: {$row['tipo']}\n";
    }
} else {
    echo "Nenhum anunciante encontrado!\n";
}

echo "</pre>";
?>
