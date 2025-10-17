<?php
session_start();
require 'conexao.php';
header('Content-Type: application/json');

if(!isset($_SESSION['logado'])) exit;

$user_id = $_SESSION['user_id'];

// Atualiza last_seen
$stmt = $conn->prepare("INSERT INTO presence(usuario_id,last_seen) VALUES(?,NOW()) ON DUPLICATE KEY UPDATE last_seen=NOW()");
$stmt->bind_param("i",$user_id);
$stmt->execute();

// Pega usuários online (últimos 2 minutos)
$stmt2 = $conn->prepare("SELECT u.id,u.nome,u.foto FROM usuarios u JOIN presence p ON u.id=p.usuario_id WHERE p.last_seen >= NOW() - INTERVAL 2 MINUTE AND u.id != ?");
$stmt2->bind_param("i",$user_id);
$stmt2->execute();
$users = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($users);
