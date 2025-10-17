<?php
require 'conexao.php';
session_start();
header('Content-Type: application/json');

$current_user = $_SESSION['user_id'] ?? 0;
$target = (int) ($_POST['id'] ?? 0);

if (!$current_user || !$target || $current_user == $target) {
    echo json_encode(['success' => false]);
    exit;
}

// Verifica se já segue
$stmt = $conn->prepare("SELECT * FROM followers WHERE follower_id = ? AND followed_id = ?");
$stmt->bind_param("ii", $current_user, $target);
$stmt->execute();
$exists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($exists) {
    $stmt = $conn->prepare("DELETE FROM followers WHERE follower_id = ? AND followed_id = ?");
    $stmt->bind_param("ii", $current_user, $target);
    $stmt->execute();
    $stmt->close();
    $following = false;
} else {
    $stmt = $conn->prepare("INSERT INTO followers (follower_id, followed_id, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $current_user, $target);
    $stmt->execute();
    $stmt->close();
    $following = true;
}

// Contar seguidores atualizados
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM followers WHERE followed_id = ?");
$stmt->bind_param("i", $target);
$stmt->execute();
$followers = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

echo json_encode([
    'success' => true,
    'following' => $following,
    'followers' => $followers
]);

$conn->close();
?>
