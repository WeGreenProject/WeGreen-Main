<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste Marketplace - Debug</h2>";

// Testar conexão
require_once 'src/model/connection.php';
echo "<p>✅ Conexão estabelecida com sucesso!</p>";

// Testar query básica
$sql = "SELECT
            p.Produto_id,
            p.nome,
            p.preco,
            p.foto,
            p.marca,
            p.genero,
            p.estado,
            p.tamanho,
            p.designer_id,
            p.artesao_id,
            p.utilizador_id
        FROM Produtos p
        WHERE p.ativo = 1
        LIMIT 5";

$result = $conn->query($sql);

if (!$result) {
    echo "<p>❌ Erro na query: " . $conn->error . "</p>";
    exit;
}

echo "<p>✅ Query executada com sucesso!</p>";
echo "<p>📊 Total de produtos encontrados (limit 5): " . $result->num_rows . "</p>";

if ($result->num_rows > 0) {
    echo "<h3>Produtos:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Preço</th><th>Marca</th><th>Gênero</th><th>Tipo Vendedor</th></tr>";

    while($row = $result->fetch_assoc()) {
        $tipoVendedor = 'particular';
        if ($row['designer_id']) $tipoVendedor = 'designer';
        if ($row['artesao_id']) $tipoVendedor = 'artesao';

        echo "<tr>";
        echo "<td>" . $row['Produto_id'] . "</td>";
        echo "<td>" . $row['nome'] . "</td>";
        echo "<td>€" . $row['preco'] . "</td>";
        echo "<td>" . ($row['marca'] ?: 'N/A') . "</td>";
        echo "<td>" . ($row['genero'] ?: 'N/A') . "</td>";
        echo "<td>" . $tipoVendedor . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>⚠️ Nenhum produto ativo encontrado na base de dados</p>";
}

// Testar se as colunas existem
echo "<h3>Estrutura da Tabela Produtos:</h3>";
$sql = "DESCRIBE Produtos";
$result = $conn->query($sql);
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>
