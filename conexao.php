<?php
// conexao.php
$host = 'localhost';
$user = 'root';
$pass = ''; 
$db   = 'rede_social';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_errno) {
    die('Falha na conexão: ' . $conn->connect_error);
}
?>
