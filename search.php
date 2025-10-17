<?php
require 'conexao.php';
session_start();

$q = trim($_GET['q'] ?? '');
if ($q === '') exit;

$search = "%" . $q . "%";

$current_user = $_SESSION['user_id'] ?? 0;

// ===== PESQUISA DE USUÁRIOS =====
$stmt_users = $conn->prepare("SELECT id, nome, foto FROM usuarios WHERE nome LIKE ? LIMIT 5");
$stmt_users->bind_param("s", $search);
$stmt_users->execute();
$result_users = $stmt_users->get_result();

// ===== PESQUISA DE PUBLICAÇÕES =====
$stmt_posts = $conn->prepare("SELECT id, conteudo, imagem FROM posts WHERE conteudo LIKE ? LIMIT 5");
$stmt_posts->bind_param("s", $search);
$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();

echo "<div style='padding: 8px;'>";

// ===== EXIBIR USUÁRIOS =====
if ($result_users->num_rows > 0) {
    echo "<h4 style='color:#fa8fbd;font-size:15px;margin:8px 0; display:flex; align-items:center; gap:6px;'>
        <img src='usuario.png' alt='Usuários' style='width:16px; height:16px;'> 
        Usuários
      </h4>";


    while ($u = $result_users->fetch_assoc()) {
        $foto = $u['foto'] ?: 'user.jpg';
        $nome = htmlspecialchars($u['nome']);
        $user_id = $u['id'];

        // Conta seguidores
        $stmt_follow = $conn->prepare("SELECT COUNT(*) AS total FROM followers WHERE followed_id = ?");
        $stmt_follow->bind_param("i", $user_id);
        $stmt_follow->execute();
        $res_follow = $stmt_follow->get_result()->fetch_assoc();
        $followers = (int)($res_follow['total'] ?? 0);
        $stmt_follow->close();

        echo "
        <div class='user-item' data-user='{$user_id}' 
            style='display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;background:#fff;margin-bottom:8px;
            box-shadow:0 1px 3px rgba(0,0,0,0.1);cursor:pointer;transition:0.2s;'>
            <img src='{$foto}' style='width:45px;height:45px;border-radius:50%;object-fit:cover;border:1px solid #eee;'>
            <div style='flex:1;'>
                <strong style='color:#333;font-size:15px;'>{$nome}</strong>
                <p style='color:#666;font-size:13px;margin:4px 0;'><img src='segui.png' alt='Seguidores' style='width:16px; height:16px;'>  Seguidores: {$followers}</p>
            </div>
        </div>";
    }
}

// ===== EXIBIR POSTS =====
if ($result_posts->num_rows > 0) {
    echo "<h4 style='color:#fa8fbd;font-size:15px;margin:10px 0;'><img src='publi.png' alt='Publicacoes' style='width:16px; height:16px;'> Publicações</h4>";
    while ($p = $result_posts->fetch_assoc()) {
        $preview = htmlspecialchars(substr($p['conteudo'], 0, 80));
        echo "
        <a href='ver_post.php?id={$p['id']}' 
           style='display:block;padding:8px 10px;border-radius:8px;text-decoration:none;color:#333;background:#fff;margin-bottom:8px;box-shadow:0 1px 3px rgba(0,0,0,0.05);'>
           {$preview}...
        </a>";
    }
}

if ($result_users->num_rows === 0 && $result_posts->num_rows === 0) {
    echo "<p style='color:#999;text-align:center;margin:10px 0;'>Nenhum resultado encontrado.</p>";
}

echo "</div>";

$stmt_users->close();
$stmt_posts->close();
$conn->close();
?>
