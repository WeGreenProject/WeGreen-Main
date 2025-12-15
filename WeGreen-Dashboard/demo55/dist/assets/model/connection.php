<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "WeGreen";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'msg' => 'Falha na conexão: '.$conn->connect_error]);
    exit;
}
?>