<?php
session_start();

// SIMULAR LOGIN DE CLIENTE
$_SESSION['utilizador'] = 1; // ID do cliente (ajuste se necessário)
$_SESSION['tipo'] = 2; // Tipo 2 = cliente

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Teste PHP</title></head><body>";
echo "<h1>Teste de Diagnóstico</h1>";
echo "<p>✅ PHP está funcionando</p>";

// Verificar sessão
if(isset($_SESSION['utilizador'])) {
    echo "<p>✅ Sessão SIMULADA: Utilizador ID = " . $_SESSION['utilizador'] . "</p>";
    echo "<p>✅ Tipo: " . $_SESSION['tipo'] . " (cliente)</p>";
} else {
    echo "<p>❌ Sem sessão ativa</p>";
}

// Testar JavaScript
echo '<script>';
echo 'console.log("✅ Console log funciona");';
echo 'alert("✅ POPUP FUNCIONA! Se vê isto, JavaScript está OK");';
echo '</script>';

echo "<hr>";
echo "<h2>Agora vou incluir minhasEncomendas.php e ver se há erro:</h2>";

// Tentar carregar minhasEncomendas.php e capturar erros
ob_start();
try {
    include 'minhasEncomendas.php';
    $content = ob_get_contents();
    ob_end_clean();

    echo "<p>✅ minhasEncomendas.php carregou sem erros fatais</p>";
    echo "<p>Tamanho do output: " . strlen($content) . " bytes</p>";

    // Verificar se tem o script de teste
    if (strpos($content, '🔴🔴🔴 TESTE INICIAL') !== false) {
        echo "<p>✅ Script de teste encontrado no HTML</p>";
    } else {
        echo "<p>❌ Script de teste NÃO encontrado!</p>";
    }

    // Mostrar início do HTML
    echo "<h3>Primeiros 500 caracteres do HTML:</h3>";
    echo "<pre>" . htmlspecialchars(substr($content, 0, 500)) . "</pre>";

} catch (Exception $e) {
    ob_end_clean();
    echo "<p style='color:red'>❌ ERRO: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
