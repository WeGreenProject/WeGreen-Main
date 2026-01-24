<?php
header('Content-Type: text/html; charset=UTF-8');
require_once 'connection.php';

// Definir charset UTF-8
$conn->set_charset("utf8mb4");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Correção de Encoding UTF-8</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #3cb371;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #2e8b57;
            border-bottom: 2px solid #3cb371;
            padding-bottom: 10px;
        }
        .produto {
            background: #f8f9fa;
            padding: 12px;
            margin: 8px 0;
            border-left: 4px solid #3cb371;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border: 1px solid #bee5eb;
        }
        del {
            color: #dc3545;
            text-decoration: line-through;
        }
        strong {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Correção Completa de Encoding UTF-8</h1>

<?php

// Mapa completo de correções
$mapaCorrecoes = [
    // Ç
    'cole??o' => 'coleção',
    'Cole??o' => 'Coleção',
    'COLE??O' => 'COLEÇÃO',
    'descri??o' => 'descrição',
    'Descri??o' => 'Descrição',
    'cal?ado' => 'calçado',
    'Cal?ado' => 'Calçado',
    'CAL?ADO' => 'CALÇADO',
    'Cal?as' => 'Calças',
    'cal?as' => 'calças',
    'crian?a' => 'criança',
    'Crian?a' => 'Criança',
    'CRIAN?A' => 'CRIANÇA',
    'com?rcio' => 'comércio',
    'Com?rcio' => 'Comércio',
    'avan?ado' => 'avançado',
    'Avan?ado' => 'Avançado',

    // Õ
    'edi??o' => 'edição',
    'Edi??o' => 'Edição',
    'EDI??O' => 'EDIÇÃO',
    'algod?o' => 'algodão',
    'Algod?o' => 'Algodão',
    'bot?o' => 'botão',
    'Bot?o' => 'Botão',
    'blus?o' => 'blusão',
    'Blus?o' => 'Blusão',

    // Á
    'f?sicas' => 'físicas',
    'F?sicas' => 'Físicas',
    'pr?tico' => 'prático',
    'Pr?tico' => 'Prático',
    'el?stico' => 'elástico',
    'El?stico' => 'Elástico',
    'b?sico' => 'básico',
    'B?sico' => 'Básico',
    'cl?ssico' => 'clássico',
    'Cl?ssico' => 'Clássico',

    // É
    'caf?' => 'café',
    'Caf?' => 'Café',
    't?nis' => 'tênis',
    'T?nis' => 'Tênis',

    // Ó
    'acess?rio' => 'acessório',
    'Acess?rio' => 'Acessório',
    'Acess?rios' => 'Acessórios',
    'acess?rios' => 'acessórios',
    'ACESS?RIOS' => 'ACESSÓRIOS',
    'hist?ria' => 'história',
    'Hist?ria' => 'História',
    'sofistica??o' => 'sofisticação',
    'Sofistica??o' => 'Sofisticação',

    // Ú
    '?nico' => 'único',
    '?nica' => 'única',
    'Tamanho ?nico' => 'Tamanho único',

    // Ô
    '?timo' => 'ótimo',
    '?tima' => 'ótima',

    // Ã
    'sust?vel' => 'sustável',
    'Sust?vel' => 'Sustável',
    'confort?vel' => 'confortável',
    'Confort?vel' => 'Confortável',
    'confort?veis' => 'confortáveis',
    'Confort?veis' => 'Confortáveis',
    'dispon?vel' => 'disponível',
    'Dispon?vel' => 'Disponível',
    'dur?vel' => 'durável',
    'Dur?vel' => 'Durável',
    'respir?vel' => 'respirável',
    'Respir?vel' => 'Respirável',
    'port?til' => 'portátil',
    'Port?til' => 'Portátil',
    'pr?tica' => 'prática',
    'Pr?tica' => 'Prática',
    'pr?ticas' => 'práticas',
    'Pr?ticas' => 'Práticas',

    // Í
    'incompar?vel' => 'incomparável',
    'Incompar?vel' => 'Incomparável',
    'di?rio' => 'diário',
    'Di?rio' => 'Diário',

    // Outros
    'vestu?rio' => 'vestuário',
    'Vestu?rio' => 'Vestuário',
    'VESTU?RIO' => 'VESTUÁRIO',
    'ru?do' => 'ruído',
    'Ru?do' => 'Ruído',
    'eleg?ncia' => 'elegância',
    'Eleg?ncia' => 'Elegância',
    'm?o' => 'mão',
    'M?o' => 'Mão',
    '? m?o' => 'à mão',
];

echo "<h2>📦 Corrigindo Produtos</h2>";

$sql = "SELECT Produto_id, nome, descricao, marca FROM Produtos";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $corrigidos = 0;
    $semProblemas = 0;

    while ($row = $result->fetch_assoc()) {
        $id = $row['Produto_id'];
        $nome = $row['nome'];
        $descricao = $row['descricao'];
        $marca = $row['marca'];

        $nomeOriginal = $nome;
        $descricaoOriginal = $descricao;
        $marcaOriginal = $marca;

        // Aplicar todas as correções
        foreach ($mapaCorrecoes as $errado => $correto) {
            $nome = str_replace($errado, $correto, $nome);
            $descricao = str_replace($errado, $correto, $descricao);
            $marca = str_replace($errado, $correto, $marca);
        }

        // Verificar se houve mudanças
        if ($nome != $nomeOriginal || $descricao != $descricaoOriginal || $marca != $marcaOriginal) {
            $stmt = $conn->prepare("UPDATE Produtos SET nome = ?, descricao = ?, marca = ? WHERE Produto_id = ?");
            $stmt->bind_param("sssi", $nome, $descricao, $marca, $id);

            if ($stmt->execute()) {
                echo "<div class='produto'>";
                echo "<strong>Produto #{$id}:</strong><br>";
                if ($nome != $nomeOriginal) {
                    echo "Nome: <del>{$nomeOriginal}</del> → <strong>{$nome}</strong><br>";
                }
                if ($descricao != $descricaoOriginal) {
                    echo "Descrição: <del>" . substr($descricaoOriginal, 0, 50) . "...</del> → <strong>" . substr($descricao, 0, 50) . "...</strong><br>";
                }
                if ($marca != $marcaOriginal) {
                    echo "Marca: <del>{$marcaOriginal}</del> → <strong>{$marca}</strong><br>";
                }
                echo "</div>";
                $corrigidos++;
            }
            $stmt->close();
        } else {
            $semProblemas++;
        }
    }

    echo "<div class='success'>";
    echo "✅ Correção de produtos concluída!<br>";
    echo "• {$corrigidos} produtos corrigidos<br>";
    echo "• {$semProblemas} produtos sem problemas<br>";
    echo "</div>";

} else {
    echo "<div class='error'>Nenhum produto encontrado.</div>";
}

// Corrigir Tipos de Produto
echo "<h2>🏷️ Corrigindo Tipos de Produto</h2>";

$sql = "SELECT id, descricao FROM tipo_produtos";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $corrigidosTipo = 0;

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $descricao = $row['descricao'];
        $descricaoOriginal = $descricao;

        // Aplicar correções
        foreach ($mapaCorrecoes as $errado => $correto) {
            $descricao = str_replace($errado, $correto, $descricao);
        }

        if ($descricao != $descricaoOriginal) {
            $stmt = $conn->prepare("UPDATE tipo_produtos SET descricao = ? WHERE id = ?");
            $stmt->bind_param("si", $descricao, $id);

            if ($stmt->execute()) {
                echo "<div class='produto'>";
                echo "<strong>Tipo #{$id}:</strong> <del>{$descricaoOriginal}</del> → <strong>{$descricao}</strong>";
                echo "</div>";
                $corrigidosTipo++;
            }
            $stmt->close();
        }
    }

    if ($corrigidosTipo > 0) {
        echo "<div class='success'>✅ {$corrigidosTipo} tipos de produto corrigidos!</div>";
    } else {
        echo "<div class='info'>✓ Todos os tipos de produto já estão corretos.</div>";
    }
} else {
    echo "<div class='error'>Nenhum tipo de produto encontrado.</div>";
}

// Corrigir Fornecedores
echo "<h2>🏭 Corrigindo Fornecedores</h2>";

$sql = "SELECT id, nome, descricao FROM fornecedores";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $corrigidosForn = 0;

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $nome = $row['nome'];
        $descricao = $row['descricao'];

        $nomeOriginal = $nome;
        $descricaoOriginal = $descricao;

        // Aplicar correções
        foreach ($mapaCorrecoes as $errado => $correto) {
            $nome = str_replace($errado, $correto, $nome);
            $descricao = str_replace($errado, $correto, $descricao);
        }

        if ($nome != $nomeOriginal || $descricao != $descricaoOriginal) {
            $stmt = $conn->prepare("UPDATE fornecedores SET nome = ?, descricao = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $descricao, $id);

            if ($stmt->execute()) {
                echo "<div class='produto'>";
                echo "<strong>Fornecedor #{$id}:</strong> <del>{$descricaoOriginal}</del> → <strong>{$descricao}</strong>";
                echo "</div>";
                $corrigidosForn++;
            }
            $stmt->close();
        }
    }

    if ($corrigidosForn > 0) {
        echo "<div class='success'>✅ {$corrigidosForn} fornecedores corrigidos!</div>";
    } else {
        echo "<div class='info'>✓ Todos os fornecedores já estão corretos.</div>";
    }
}

$conn->close();

echo "
    <div style='margin-top: 30px; padding: 20px; background: #e8f5e9; border-radius: 8px; text-align: center;'>
        <strong style='color: #2e7d32; font-size: 20px;'>✨ Processo Concluído!</strong><br>
        <p style='margin-top: 10px; color: #555;'>Todas as correções foram aplicadas com sucesso.</p>
        <p style='margin-top: 10px; color: #555;'>Recarregue as páginas do sistema para ver as alterações.</p>
    </div>
</div>
</body>
</html>";
?>
