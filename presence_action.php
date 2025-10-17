presence_actions: <?php
session_start();
require 'conexao.php';
header('Content-Type: application/json');

if (!isset($_SESSION['logado']) || !$_SESSION['logado']) {
    echo json_encode([]);
    exit;
}

$currentUserId = $_SESSION['user_id'];

// Busca usuários online nos últimos 60 segundos
$stmt = $conn->prepare("
    SELECT u.id, u.nome, u.foto
    FROM presence p
    JOIN usuarios u ON p.usuario_id = u.id
    WHERE p.usuario_id != ? AND p.last_seen >= (NOW() - INTERVAL 60 SECOND)
    ORDER BY u.nome
");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>