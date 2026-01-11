<?php
session_start();
$_SESSION['utilizador'] = 1;
$_SESSION['tipo'] = 2;

// Capturar o HTML completo de minhasEncomendas.php
ob_start();
include 'minhasEncomendas.php';
$html = ob_get_clean();

// Salvar em arquivo para inspeção
file_put_contents('debug_minhasEncomendas.html', $html);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Debug HTML</title></head><body>";
echo "<h1>Análise do HTML de minhasEncomendas.php</h1>";

echo "<h2>1️⃣ Verificar se o script de teste existe:</h2>";
if (strpos($html, '🔴🔴🔴 TESTE INICIAL') !== false) {
    echo "<p style='color:green'>✅ Script de teste ENCONTRADO no HTML</p>";
} else {
    echo "<p style='color:red'>❌ Script de teste NÃO ENCONTRADO!</p>";
}

echo "<h2>2️⃣ Verificar se há erros JavaScript visíveis:</h2>";
$erros = [];
if (preg_match_all('/<script[^>]*>(.*?)<\/script>/is', $html, $matches)) {
    echo "<p>Total de tags &lt;script&gt;: " . count($matches[0]) . "</p>";

    // Procurar por sintaxe comum de erro
    foreach ($matches[1] as $i => $script) {
        if (stripos($script, 'error') !== false || stripos($script, 'undefined') !== false) {
            $erros[] = "Script #" . ($i+1) . " pode ter erro";
        }
    }
}

if (empty($erros)) {
    echo "<p style='color:green'>✅ Nenhum erro óbvio detectado</p>";
} else {
    echo "<p style='color:orange'>⚠️ Possíveis problemas: " . implode(', ', $erros) . "</p>";
}

echo "<h2>3️⃣ HTML completo salvo em:</h2>";
echo "<p><a href='debug_minhasEncomendas.html' target='_blank'>debug_minhasEncomendas.html</a></p>";
echo "<p><strong>INSTRUÇÃO:</strong> Abra este arquivo, pressione CTRL+F e procure por '🔴🔴🔴'</p>";

echo "<h2>4️⃣ Verificar ordem dos scripts:</h2>";
$scripts = [];
if (preg_match_all('/<script[^>]*src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches)) {
    foreach ($matches[1] as $src) {
        $scripts[] = $src;
    }
}
echo "<ol>";
foreach ($scripts as $script) {
    echo "<li>" . htmlspecialchars($script) . "</li>";
}
echo "</ol>";

echo "<h2>5️⃣ Primeiros 2000 caracteres do HTML:</h2>";
echo "<pre style='background:#f5f5f5;padding:10px;overflow:auto;max-height:400px;'>";
echo htmlspecialchars(substr($html, 0, 2000));
echo "</pre>";

echo "<h2>6️⃣ TESTE DIRETO:</h2>";
echo "<p>Vou renderizar a página abaixo. Veja se o popup aparece:</p>";
echo "<hr>";
echo $html;

echo "</body></html>";
?>
