<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "WeGreen";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'msg' => 'Falha na conexão: '.$conn->connect_error], JSON_UNESCAPED_UNICODE);
    exit;
}
$conn->set_charset('utf8mb4');
$conn->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_general_ci'");
?>
