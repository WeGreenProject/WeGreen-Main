<?php
session_start();

echo "<h2>Informações da Sessão:</h2>";
echo "<pre>";
echo "Utilizador ID: " . ($_SESSION['utilizador'] ?? 'NÃO DEFINIDO') . "\n";
echo "Nome: " . ($_SESSION['nome'] ?? 'NÃO DEFINIDO') . "\n";
echo "Email: " . ($_SESSION['email'] ?? 'NÃO DEFINIDO') . "\n";
echo "Tipo: " . ($_SESSION['tipo'] ?? 'NÃO DEFINIDO') . "\n";
echo "</pre>";

if (isset($_SESSION['utilizador'])) {
    include_once 'connection.php';

    echo "<h2>Devoluções para anunciante_id: " . $_SESSION['utilizador'] . "</h2>";

    $stmt = $conn->prepare("
        SELECT codigo_devolucao, estado, motivo, anunciante_id
        FROM devolucoes
        WHERE anunciante_id = ?
    ");
    $stmt->bind_param("i", $_SESSION['utilizador']);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<pre>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
    } else {
        echo "NENHUMA DEVOLUÇÃO ENCONTRADA PARA ANUNCIANTE_ID: " . $_SESSION['utilizador'];
    }
    echo "</pre>";

    echo "<h2>Todas as devoluções no sistema:</h2>";
    $result = $conn->query("SELECT codigo_devolucao, anunciante_id FROM devolucoes");
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
}
?>
