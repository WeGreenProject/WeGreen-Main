<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Simular sessão de cliente
$_SESSION['utilizador'] = 2;
$_SESSION['tipo'] = 2;
$_SESSION['nome'] = 'Cliente Teste';

echo "<h2>Teste Controller ChatCliente</h2>";
echo "<pre>";

// Simular POST op=1
$_POST['op'] = 1;

echo "Testando op=1 (getSideBar)...\n\n";

try {
    include_once 'src/controller/controllerChatCliente.php';
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "</pre>";
?>
