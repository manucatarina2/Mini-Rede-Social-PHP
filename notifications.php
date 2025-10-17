<?php
session_start();
require 'conexao.php';

// Exibir erros (para debug)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se logado
if (!isset($_SESSION['logado']) || !$_SESSION['logado'] || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = $_SESSION['user_id'];

// ===== CONTAGEM DE NOTIFICAÇÕES =====

// Notificações não lidas (likes/comentários/seguidores)
$count_stmt = $conn->prepare("
    SELECT COUNT(*) AS nao_lidas 
    FROM notificacoes 
    WHERE usuario_id = ? AND lida = 0
");
$count_stmt->bind_param("i", $currentUserId);
$count_stmt->execute();
$nao_lidas = $count_stmt->get_result()->fetch_assoc()['nao_lidas'] ?? 0;

// Mensagens não lidas
$msg_stmt = $conn->prepare("
    SELECT COUNT(*) AS mensagens_nao_lidas 
    FROM mensagens 
    WHERE to_id = ? AND lida = 0
");
$msg_stmt->bind_param("i", $currentUserId);
$msg_stmt->execute();
$msg_nao_lidas = $msg_stmt->get_result()->fetch_assoc()['mensagens_nao_lidas'] ?? 0;


// ===== DADOS DO USUÁRIO =====
$stmtUser = $conn->prepare("SELECT id, nome, email, foto FROM usuarios WHERE id = ?");
$stmtUser->bind_param("i", $currentUserId);
$stmtUser->execute();
$currentUser = $stmtUser->get_result()->fetch_assoc();

// ===== BUSCA DE NOTIFICAÇÕES =====
$stmt = $conn->prepare("
    SELECT n.*, u.nome AS origem_nome, u.foto AS origem_foto, p.conteudo AS post_conteudo
    FROM notificacoes n
    LEFT JOIN usuarios u ON n.origem_usuario_id = u.id
    LEFT JOIN posts p ON n.referencia_id = p.id
    WHERE n.usuario_id = ?
    ORDER BY n.created_at DESC
    LIMIT 50
");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// ===== MARCA COMO LIDAS =====
$update_stmt = $conn->prepare("UPDATE notificacoes SET lida = 1 WHERE usuario_id = ? AND lida = 0");
$update_stmt->bind_param("i", $currentUserId);
$update_stmt->execute();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Notificações - XoXo</title>
<style>
/* ===== RESET E BASE ===== */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body { 
  display: flex; 
  min-height: 100vh; 
  position: relative; 
  background-color: #fff1f6ff; 
}
body::before { 
  content: ""; 
  position: absolute; 
  inset: 0; 
  background: url('fundo.png') center/cover no-repeat;
  opacity: 0.1; 
}
.main-content,.sidebar,.top-bar{position:relative;z-index:1;}

/* ===== SIDEBAR ===== */
.sidebar{width:280px;background:linear-gradient(135deg,#fa8fbdff,#f84a95ff);color:#fff;display:flex;flex-direction:column;padding:40px 20px;position:fixed;top:0;bottom:0;left:0;transition:all 0.3s ease;}
.sidebar.hidden{transform:translateX(-100%);}
.logo-container{display:flex;align-items:center;gap:8px;margin-bottom:30px;}
.logo-container img{height:40px;width:auto;}
.menu{display:flex;flex-direction:column;gap:15px;}
.menu a{color:#fff;text-decoration:none;font-weight:500;font-size:16px;padding:10px 15px;border-radius:8px;display:flex;align-items:center;gap:10px;transition:background 0.2s ease,transform 0.2s ease;}
.menu a:hover{background:rgba(255,255,255,0.15);transform:translateX(5px);}
.menu a.active{background:rgba(255,255,255,0.25);font-weight:700;}
.badge{background:#ff4757;color:white;border-radius:50%;padding:2px 6px;font-size:12px;margin-left:5px;font-weight:bold;}
.user-profile-sidebar{margin-top:auto;padding:20px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;gap:15px;}
.user-avatar{width:50px;height:50px;border-radius:50%;overflow:hidden;flex-shrink:0;}
.user-avatar img{width:100%;height:100%;object-fit:cover;}
.user-details{display:flex;flex-direction:column;}
.user-name{font-weight:600;color:#fff;font-size:16px;}
.user-email{font-size:14px;color:#eee;}

/* ===== TOP BAR ===== */
.top-bar{position:fixed;left:280px;right:0;top:0;height:60px;background:rgba(255,255,255,0.9);display:flex;align-items:center;justify-content:space-between;padding:0 20px;color:#333;z-index:2;transition:left 0.3s ease;}
.top-bar.sidebar-hidden{left:0;}
.menu-toggle{background:none;border:none;cursor:pointer;color:#333;}
.menu-toggle svg{width:24px;height:24px;}
.user-greeting{font-size:18px;font-weight:500;color:#333;}

/* ===== MAIN CONTENT ===== */
.main-content{margin-left:280px;padding:100px 140px 140px 80px;transition:margin-left 0.3s ease;}
.main-content.sidebar-hidden{margin-left:0;}

/* ===== NOTIFICAÇÕES ===== */
.notifications-list{display:flex;flex-direction:column;gap:20px;}
.notification{background:rgba(255,255,255,0.8);display:flex;gap:15px;align-items:flex-start;border-radius:12px;padding:20px;box-shadow:0 4px 12px rgba(0,0,0,0.1);transition:transform 0.2s ease,box-shadow 0.2s ease;}
.notification:hover{transform:translateY(-3px);box-shadow:0 6px 16px rgba(0,0,0,0.15);}
.notification img{width:60px;height:60px;border-radius:50%;object-fit:cover;background:#eee;}
.notification-content{flex:1;display:flex;flex-direction:column;gap:5px;}
.notification-content strong{font-size:16px;color:#d31977;}
.notification-content p{font-size:14px;color:#444;margin:0;}
.notification-link{display:inline-block;font-size:14px;color:#f84a95;text-decoration:none;margin-top:5px;font-weight:500;}
.notification-link:hover{text-decoration:underline;}
.notification-time{font-size:12px;color:#777;margin-top:5px;}
.no-notifications{background:rgba(255,255,255,0.9);padding:40px;border-radius:12px;text-align:center;font-size:18px;color:#555;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.notification.unread{border-left:5px solid #f84a95;background:#fff1f7;}

/* ===== RESPONSIVO ===== */
@media(max-width:900px){
  body{flex-direction:column;}
  .sidebar{width:100%;position:relative;padding:30px;text-align:center;transform:translateX(0);}
  .menu a{display:inline-block;margin:5px 0;}
  .main-content{margin-left:0;padding:20px;}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="logo-container">
    <img src="logo.png" alt="Logo">
    <img src="image.png" alt="XoXo">
  </div>

  <div class="menu">
    <a href="index.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg> Feed
    </a>
    <a href="perfil.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8V22h19.2v-2.8c0-3.2-6.4-4.8-9.6-4.8z"/></svg> Perfil
    </a>
    <a href="chat.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M4 4h16v12H5.17L4 17.17V4z"/></svg> Mensagens
      <?php if ($msg_nao_lidas > 0): ?><span class="badge"><?= $msg_nao_lidas ?></span><?php endif; ?>
    </a>

    <a href="notifications.php" class="active">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5S10 3.17 10 4v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg> Notificações
      <?php if ($nao_lidas>0): ?><span class="badge"><?= $nao_lidas ?></span><?php endif; ?>
    </a>

    <a href="logout.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M16 13v-2H7V8l-5 4 5 4v-3z"/></svg> Sair
    </a>
  </div>

  <div class="user-profile-sidebar">
    <div class="user-avatar">
      <img src="<?= !empty($currentUser['foto']) ? htmlspecialchars($currentUser['foto']) : 'user.jpg' ?>" alt="Avatar">
    </div>
    <div class="user-details">
      <div class="user-name"><?= htmlspecialchars($currentUser['nome']) ?></div>
      <div class="user-email"><?= htmlspecialchars($currentUser['email']) ?></div>
    </div>
  </div>
</div>

<!-- TOP BAR -->
<div class="top-bar" id="topBar">
  <button class="menu-toggle" onclick="toggleSidebar()">
    <svg viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
  </button>
  <div class="user-greeting">Olá, <?= htmlspecialchars($currentUser['nome']) ?>!</div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">
  <h2>Notificações</h2><br>
  <?php if (empty($notifications)): ?>
    <div class="no-notifications">Você não tem notificações novas.</div>
  <?php else: ?>
    <div class="notifications-list">
      <?php foreach ($notifications as $n): ?>
        <?php
          switch ($n['tipo']) {
            case 'mensagem':   $titulo = 'Nova mensagem'; break;
            case 'like':       $titulo = 'Curtiu sua publicação'; break;
            case 'comentario': $titulo = 'Comentou em sua publicação'; break;
            case 'seguir':     $titulo = 'Começou a te seguir'; break;
            case 'seguido':    $titulo = 'Você seguiu um novo usuário'; break;
            default:           $titulo = 'Notificação'; break;
          }
          $nomeOrigem = $n['origem_nome'] ?? 'Usuário';
          $fotoOrigem = (!empty($n['origem_foto']) && file_exists('uploads/' . $n['origem_foto'])) ? 'uploads/' . $n['origem_foto'] : 'user.jpg';
          $mensagem = $n['mensagem'] ?? '';
          $postID = $n['referencia_id'] ?? '';
          $postLink = $postID ? "ver_post.php?id=$postID" : '#';
          $dataHora = $n['created_at'] ?? '';
        ?>
        <div class="notification <?= $n['lida'] == 0 ? 'unread' : '' ?>">
          <img src="<?= htmlspecialchars($fotoOrigem) ?>" alt="Foto de <?= htmlspecialchars($nomeOrigem) ?>">
          <div class="notification-content">
            <strong><?= htmlspecialchars($nomeOrigem) ?> - <?= htmlspecialchars($titulo) ?></strong>
            <?php if (!empty($mensagem)): ?><p><?= htmlspecialchars($mensagem) ?></p><?php endif; ?>
            <?php if (!empty($n['post_conteudo'])): ?><a class="notification-link" href="<?= $postLink ?>">Ver publicação</a><?php endif; ?>
            <div class="notification-time"><?= date('d/m/Y H:i', strtotime($dataHora)) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('hidden');
  document.getElementById('topBar').classList.toggle('sidebar-hidden');
  document.getElementById('mainContent').classList.toggle('sidebar-hidden');
}
</script>

</body>
</html>
