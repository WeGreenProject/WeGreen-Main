<?php
session_start();
include('db_connect.php'); // ficheiro com a ligação à BD
$anunciante_id = $_SESSION['id']; // anunciante logado

header('Content-Type: application/json');

$data = [];


$sql = "
SELECT 
    DATE_FORMAT(data_venda, '%Y-%m') AS mes,
    COUNT(*) AS total_vendas
FROM Vendas
WHERE anunciante_id = ? 
AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY mes
ORDER BY mes ASC;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $anunciante_id);
$stmt->execute();
$result = $stmt->get_result();
$data['vendasPorMes'] = $result->fetch_all(MYSQLI_ASSOC);


$sql = "
SELECT 
    DATE_FORMAT(data_venda, '%Y-%m') AS mes,
    SUM(lucro) AS lucro_total
FROM Vendas
WHERE anunciante_id = ? 
AND data_venda >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY mes
ORDER BY mes ASC;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $anunciante_id);
$stmt->execute();
$result = $stmt->get_result();
$data['lucroPorMes'] = $result->fetch_all(MYSQLI_ASSOC);


$sql = "
SELECT tp.descricao AS categoria, COUNT(p.id) AS total
FROM Produtos p
JOIN Tipo_Produtos tp ON tp.id = p.tipo_produto_id
WHERE p.anunciante_id = ? AND p.ativo = 1
GROUP BY tp.descricao;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $anunciante_id);
$stmt->execute();
$result = $stmt->get_result();
$data['produtosPorCategoria'] = $result->fetch_all(MYSQLI_ASSOC);


$sql = "
SELECT estado, COUNT(id) AS total
FROM Encomendas
WHERE anunciante_id = ?
GROUP BY estado;
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $anunciante_id);
$stmt->execute();
$result = $stmt->get_result();
$data['estadoEncomendas'] = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);
?>
