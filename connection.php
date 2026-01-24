<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "wegreen";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    error_log("Erro de conexão MySQL: " . $conn->connect_error);
    die("Erro de conexão com a base de dados");
}

// Definir charset para UTF-8
$conn->set_charset('utf8mb4');
?>
