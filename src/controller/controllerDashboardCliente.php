<?php
/**
 * Controller Dashboard Cliente - Endpoints para o dashboard do cliente
 */

session_start();
require_once '../model/modelDashboardCliente.php';

header('Content-Type: application/json');

// Verificar autenticação
if(!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

$dashboard = new DashboardCliente();
$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : 0);

switch($op) {
    case 1:
        // Obter estatísticas gerais do cliente
        echo $dashboard->getEstatisticasCliente($_SESSION['utilizador']);
        break;

    case 2:
        // Obter encomendas recentes (últimas 5)
        echo $dashboard->getEncomendasRecentes($_SESSION['utilizador'], 5);
        break;

    case 3:
        // Obter produtos recomendados
        echo $dashboard->getProdutosRecomendados($_SESSION['utilizador'], 6);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Operação inválida']);
        break;
}
?>
