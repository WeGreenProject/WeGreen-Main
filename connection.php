<?php
$host = "localhost";      
$user = "root";          
$pass = "";               
$dbname = "wegreen";         

$conn = new mysqli($host, $user, $pass, $dbname);


if ($conn->connect_error) {
    die("Falha" . $conn->connect_error);
} else {
    echo "Feito";
}
?>