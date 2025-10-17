<?php
session_start();
require 'conexao.php';

// Verifica se logado
if (!isset($_SESSION['logado']) || !$_SESSION['logado']) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Puxa currentUser da sessão/DB
$stmt = $conn->prepare("SELECT id, nome, email, foto FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();

if (!$currentUser) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Se não tiver foto definida, usa 'user.jpg'
$currentUser['foto'] = !empty($currentUser['foto']) && file_exists($currentUser['foto']) ? $currentUser['foto'] : 'user.jpg';

// Conta notificações não lidas
$count_stmt = $conn->prepare("SELECT COUNT(*) as nao_lidas FROM notificacoes WHERE usuario_id = ? AND lida = 0");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$nao_lidas = $count_stmt->get_result()->fetch_assoc()['nao_lidas'] ?? 0;
// Mensagens não lidas
$msg_stmt = $conn->prepare("
    SELECT COUNT(*) AS mensagens_nao_lidas 
    FROM mensagens 
    WHERE to_id = ? AND lida = 0
");
$msg_stmt->bind_param("i", $user_id);
$msg_stmt->execute();
$msg_nao_lidas = $msg_stmt->get_result()->fetch_assoc()['mensagens_nao_lidas'] ?? 0;


// Buscar posts
$stmt = $conn->prepare("
    SELECT p.*, u.nome, u.foto,
           (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS total_likes,
           (SELECT COUNT(*) FROM comentarios c WHERE c.post_id = p.id) AS total_comentarios,
           EXISTS(SELECT 1 FROM likes l2 WHERE l2.post_id = p.id AND l2.usuario_id = ?) AS curtiu
    FROM posts p
    JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.created_at DESC
    LIMIT 20
");
$stmt->bind_param("i", $currentUser['id']);
$stmt->execute();
$result = $stmt->get_result();
$posts = [];
while ($row = $result->fetch_assoc()) {
    $row['curtiu'] = (bool) $row['curtiu'];
    $row['foto'] = (!empty($row['foto']) && file_exists($row['foto'])) ? $row['foto'] : 'user.jpg';
    $posts[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feed - XoXo</title>
<style>
/* ===== RESET E BASE ===== */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
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
  background: url('fundo2.jpeg') center/cover no-repeat;
  opacity: 0.1; 
}
.main-content, .sidebar, .top-bar { position: relative; z-index: 1; }

/* ===== SIDEBAR ===== */
.sidebar {
  width: 280px;
  background: linear-gradient(135deg, #fa8fbdff, #ff6dac);
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 40px 20px;
  position: fixed;
  top: 0; bottom: 0; left: 0;
  transition: all 0.3s ease;
}
.sidebar.hidden { transform: translateX(-100%); }
.user-profile-sidebar {
  margin-top: auto;
  padding: 20px;
  background: rgba(255,255,255,0.15);
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 15px;
}
.user-profile-sidebar .user-info { display: flex; align-items: center; gap: 10px; }
.user-avatar { width: 50px; height: 50px; border-radius: 50%; overflow: hidden; flex-shrink: 0; }
.user-avatar img { width: 100%; height: 100%; object-fit: cover; }
.user-details { display: flex; flex-direction: column; }
.user-name { font-weight: 600; color: #fff; font-size: 16px; }
.user-email { font-size: 14px; color: #eee; }

.logo-container { display: flex; align-items: center; gap: 8px; margin-bottom: 30px; }
.logo-container img { height: 40px; width: auto; }

.menu { display: flex; flex-direction: column; gap: 15px; }
.menu a {
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  font-size: 16px;
  padding: 10px 15px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: background 0.2s ease, transform 0.2s ease;
}
.menu a img { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
.menu a:hover { background: rgba(255,255,255,0.15); transform: translateX(5px); }
.menu a.active { background: rgba(255,255,255,0.25); font-weight: 700; }
.badge { background: #ff4757; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; margin-left: 5px; font-weight: bold; }

/* ===== TOP BAR ===== */
.top-bar {
  position: fixed;
  left: 280px;
  right: 0;
  top: 0;
  height: 60px;
  background: rgba(255,255,255,0.9);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  color: #333;
  z-index: 2;
  transition: left 0.3s ease;
}
.top-bar.sidebar-hidden { left: 0; }


.menu-toggle {
  background: none;
  border: none;
  cursor: pointer;
  color: #333;
}
.menu-toggle svg { width: 24px; height: 24px; }

.user-greeting { font-size: 18px; font-weight: 500; color: #000000ff; }

/* ===== MAIN CONTENT ===== */
.main-content {
  margin-left: 200px;
  padding: 100px 140px 140px 140px;
  transition: margin-left 0.3s ease;
}
.main-content.sidebar-hidden { margin-left: 0; }

/* ===== POST FORM ===== */
.post-form { background: #f9f9f9; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 20px; }
.post-form textarea { width: 100%; height: 80px; border: none; outline: none; resize: none; padding: 10px; font-size: 15px; border-radius: 8px; background: #f5f5f5; margin-bottom: 10px; }
.post-form input[type="file"] { margin-bottom: 10px; }
.post-form button { background: linear-gradient(135deg, #fa8fbdff, #f84a95ff); color: #fff; border: none; border-radius: 25px; padding: 12px 25px; cursor: pointer; font-weight: 600; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.post-form button:hover { transform: translateY(-2px); box-shadow: 0 4px 12px #f84a95ff; }

/* ===== POSTS ===== */
.post { background: #f9f9f9; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.post-header { display: flex; align-items: center; margin-bottom: 15px; }
.profile-pic { width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #fa8fbdff, #f84a95ff); color: white; font-weight: bold; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 18px; }
.profile-pic img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: none; }
.post-header h3 { font-size: 18px; color: #222; margin-bottom: 5px; }
.date { font-size: 14px; color: #666; }
.post-content p { margin:10px 0; white-space: pre-wrap; font-size:16px; color:#333; line-height:1.5; }
.post-content img.post-image { width:30%; border-radius:10px; margin-top:10px; }
.post-actions { display:flex; align-items:center; gap:15px; margin-top:15px; font-size:14px; }
.post-actions button.like-btn { background: linear-gradient(135deg, #fa8fbdff, #f84a95ff); border:none; color:#fff; border-radius:25px; padding:8px 20px; cursor:pointer; font-size:14px; font-weight:500; transition:0.3s; }
.post-actions button.like-btn.liked { background:#f84a95ff; }
.post-actions button.like-btn:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(233,64,137,0.4); }
.comment-count { font-size:14px; color:#555; }
.comments-section { margin-top:15px; }
.comment { background:#f5f5f9; padding:10px 15px; border-radius:10px; margin-bottom:8px; font-size:14px; }
.comment strong { color:#333; }
.comment-date { font-size:11px; color:#555; margin-left:5px; }
.comment-form { display:flex; margin-top:10px; }
.comment-form input[type="text"] { flex:1; padding:10px; border-radius:25px; border:1px solid #ccc; outline:none; margin-right:10px; font-size:14px; }
.comment-form button { padding:10px 20px; border-radius:25px; border:none; background:linear-gradient(135deg, #fa8fbdff, #f84a95ff); color:white; cursor:pointer; font-weight:600; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.comment-form button:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(233,64,137,0.4); }

/* ===== RESPONSIVO ===== */
@media (max-width: 900px) {
  body { flex-direction: column; }
  .sidebar { width:100%; position:relative; padding:30px; text-align:center; transform: translateX(0); }
  .menu a { display:inline-block; margin:5px 0; }
  .main-content { margin-left:0; padding:20px; }
}
/* === Estilo da barra de pesquisa (integrado ao topo) === */
.search-container {
  position: relative;
  width: 350px;
}

.search-container input {
  width: 100%;
  padding: 8px 14px;
  border-radius: 25px;
  border: 1px solid #ddd;
  outline: none;
  font-size: 14px;
  background: rgba(255,255,255,0.8);
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  transition: all 0.2s ease;
}

.search-container input:focus {
  background: #fff;
  border-color: #fa8fbd;
  box-shadow: 0 0 6px rgba(250,143,189,0.4);
}

#searchResults {
  position: absolute;
  top: 42px;
  left: 0;
  width: 100%;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 10px;
  max-height: 300px;
  overflow-y: auto;
  display: none;
  z-index: 2000;
}

#searchResults a {
  display: block;
  padding: 10px 15px;
  color: #333;
  text-decoration: none;
  border-bottom: 1px solid #eee;
  transition: background 0.2s ease;
}

#searchResults a:hover {
  background: #ffe7f3;
}

</style>
</head>
<body>
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="logo-container">
    <img src="logo.png" alt="Logo Kiss">
    <img src="image.png" alt="XoXo">
  </div>

  <div class="menu">
    <a href="index.php" class="active">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg> Feed
    </a>
    <a href="perfil.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8V22h19.2v-2.8c0-3.2-6.4-4.8-9.6-4.8z"/></svg> Perfil
    </a>
<a href="chat.php">
  <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24">
    <path d="M4 4h16v12H5.17L4 17.17V4z"/>
  </svg> Mensagens
  <?php if ($msg_nao_lidas > 0): ?>
    <span class="badge"><?= $msg_nao_lidas ?></span>
  <?php endif; ?>
</a>

    <a href="notifications.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5S10 3.17 10 4v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg> Notificações<?php if ($nao_lidas>0): ?> <span class="badge"><?= $nao_lidas ?></span><?php endif; ?>
    </a>
    <a href="logout.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M16 13v-2H7V8l-5 4 5 4v-3z"/></svg> Sair
    </a>
  </div>

  <!-- USUÁRIO NO FINAL -->
  <div class="user-profile-sidebar">
    <div class="user-info">
      <div class="user-avatar">
        <img src="<?= $currentUser['foto'] ?>" alt="Avatar">
      </div>
      <div class="user-details">
        <div class="user-name"><?= htmlspecialchars($currentUser['nome']) ?></div>
        <div class="user-email"><?= htmlspecialchars($currentUser['email']) ?></div>
      </div>
    </div>
  </div>
</div>

<!-- TOP BAR -->
<div class="top-bar" id="topBar">
  <button class="menu-toggle" onclick="toggleSidebar()">
    <svg viewBox="0 0 24 24"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
  </button>

  <!-- Barra de pesquisa centralizada -->
  <div class="search-container">
    <input type="text" id="searchInput" placeholder="Pesquisar usuários ou publicações...">
    <div id="searchResults"></div>
  </div>

  <div class="user-greeting">Olá, <?= htmlspecialchars($currentUser['nome']) ?>!</div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content" id="mainContent">
  <h1>Feed</h1><br>

  <div class="post-form">
    <form id="newPostForm" enctype="multipart/form-data">
      <textarea id="postContent" placeholder="O que você está pensando?" required></textarea>
      <input type="file" id="postImage" accept="image/*">
      <button type="submit">Publicar</button>
    </form>
  </div>

  <div id="postsFeed">
    <?php if (empty($posts)): ?>
      <p style="text-align: center; color: #666; font-size: 18px;">Nenhum post ainda. Seja o primeiro a postar!</p>
    <?php else: ?>
      <?php foreach ($posts as $post): ?>
      <div class="post" data-post-id="<?= $post['id'] ?>" id="post-<?= $post['id'] ?>">
        <div class="post-header"> 
          <img src="<?= htmlspecialchars($post['foto']) ?>" class="profile-pic" alt="Foto de <?= htmlspecialchars($post['nome']) ?>"> 
          <div> 
            <h3><?= htmlspecialchars($post['nome']) ?></h3> 
            <span class="date"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span> 
          </div> 
        </div>
        <div class="post-content">
          <p><?= nl2br(htmlspecialchars($post['conteudo'])) ?></p>
          <?php if (!empty($post['imagem']) && file_exists($post['imagem'])): ?>
            <img src="<?= htmlspecialchars($post['imagem']) ?>" class="post-image" alt="Imagem do post">
          <?php endif; ?>
        </div>
        <div class="post-actions">
          <button class="like-btn <?= $post['curtiu'] ? 'liked' : '' ?>" data-post-id="<?= $post['id'] ?>">
            <?= $post['curtiu'] ? ' Curtido' : ' Curtir' ?> (<?= $post['total_likes'] ?>)
          </button>
          <span class="comment-count"><?= $post['total_comentarios'] ?> Comentários</span>
        </div>
        
        
        <div class="comments-section">
          <div id="comments-<?= $post['id'] ?>"></div>
          <form class="comment-form" data-post-id="<?= $post['id'] ?>">
            <input type="text" placeholder="Escreva um comentário..." required>
            <button type="submit">Comentar</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
<!-- POPUP DE USUÁRIO -->
<div id="userPopup" 
     style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
     width:270px;background:#fff;border-radius:10px;
     box-shadow:0 2px 12px rgba(0,0,0,0.3);
     display:none;z-index:9999;">
</div>

<!-- FUNDO ESCURO -->
<div id="overlay"
     style="position:fixed;top:0;left:0;width:100%;height:100%;
     background:rgba(0,0,0,0.4);display:none;z-index:9998;">
</div>


<script>
const searchInput = document.getElementById('searchInput');
const resultsBox = document.getElementById('searchResults');
const popup = document.getElementById('userPopup');
const overlay = document.getElementById('overlay');

// === PESQUISA DINÂMICA ===
searchInput.addEventListener('input', () => {
  const q = searchInput.value.trim();
  if (q.length < 2) {
    resultsBox.style.display = 'none';
    return;
  }

  fetch('search.php?q=' + encodeURIComponent(q))
    .then(r => r.text())
    .then(html => {
      resultsBox.innerHTML = html;
      resultsBox.style.display = 'block';
    });
});

// === MOSTRAR POPUP DO USUÁRIO ===
document.addEventListener('click', async (e) => {
  const userItem = e.target.closest('.user-item');
  if (userItem) {
    const userId = userItem.dataset.user;
    const res = await fetch('get_user_info.php?id=' + userId);
    popup.innerHTML = await res.text();
    popup.style.display = 'block';
    overlay.style.display = 'block';
  }

  // Fechar popup ao clicar fora
  if (e.target === overlay) {
    popup.style.display = 'none';
    overlay.style.display = 'none';
  }
});

// === SEGUIR / DEIXAR DE SEGUIR NO POPUP ===
document.addEventListener('click', async (e) => {
  if (e.target.id === 'btnFollow') {
    const btn = e.target;
    const userId = btn.dataset.id;
    const formData = new FormData();
    formData.append('id', userId);

    try {
      const res = await fetch('toggle_follow.php', { method: 'POST', body: formData });
      const result = await res.json();

      if (result.success) {
        // Atualiza botão
        btn.textContent = result.following ? 'Deixar de seguir' : 'Seguir';
        btn.style.background = result.following ? '#ff7fae' : '#fa8fbd';
        btn.dataset.following = result.following;

        // Atualiza contagem de seguidores
        const followersP = document.getElementById('followersCount');
        if(followersP && result.followers !== undefined){
          followersP.textContent = `👥 Seguidores: ${result.followers}`;
        }
      } else {
        alert('Erro ao tentar seguir.');
      }
    } catch(err){
      console.error(err);
      alert('Erro ao tentar seguir.');
    }
  }
});


// === FECHAR RESULTADOS AO CLICAR FORA ===
document.addEventListener('click', e => {
  if (!e.target.closest('#searchResults') && e.target.id !== 'searchInput') {
    resultsBox.style.display = 'none';
  }
});
 function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('hidden');
  document.getElementById('topBar').classList.toggle('sidebar-hidden');
  document.getElementById('mainContent').classList.toggle('sidebar-hidden');
}

// === JS de Curtidas e Comentários ===
const userId = <?= json_encode($currentUser['id']) ?>;
function escapeHtml(text){const div=document.createElement('div');div.textContent=text;return div.innerHTML;}

document.getElementById('newPostForm').addEventListener('submit', function(e){
  e.preventDefault();
  const formData = new FormData();
  formData.append('action','create_post');
  formData.append('conteudo', document.getElementById('postContent').value.trim());
  if(document.getElementById('postImage').files[0]) formData.append('imagem', document.getElementById('postImage').files[0]);
  fetch('actions.php',{method:'POST',body:formData})
    .then(res=>res.json())
    .then(data=>{if(data.success){location.reload();} else {alert('Erro ao postar: '+(data.error||'Tente novamente'));}});
});

document.addEventListener('click', function(e){
  if(e.target.classList.contains('like-btn')){
    const btn = e.target;
    const postId = btn.dataset.postId;
    fetch('actions.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`action=like_post&post_id=${postId}`
    })
    .then(res=>res.json())
    .then(data=>{if(data.success){btn.classList.toggle('liked',data.liked); btn.textContent = data.liked ? ` Curtido (${data.total_likes})` : ` Curtir (${data.total_likes})`; }});
  }
});

document.addEventListener('submit', function(e){
  if(e.target.classList.contains('comment-form')){
    e.preventDefault();
    const form = e.target;
    const postId = form.dataset.postId;
    const input = form.querySelector('input');
    const conteudo = input.value.trim();
    if(!conteudo) return;
    fetch('actions.php',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:`action=add_comment&post_id=${postId}&conteudo=${encodeURIComponent(conteudo)}`
    })
    .then(res=>res.json())
    .then(data=>{
      if(data.success){ input.value=''; loadComments(postId); const countSpan=document.querySelector(`.post[data-post-id="${postId}"] .comment-count`); if(countSpan) countSpan.textContent=`${data.total_comentarios} Comentários`; }
    });
  }
});

function loadComments(postId){
  fetch(`actions.php?action=get_comments&post_id=${postId}`)
    .then(res=>res.json())
    .then(comments=>{
      const container=document.getElementById(`comments-${postId}`);
      if(!container) return;
      container.innerHTML='';
      if(comments.length>0){
        comments.forEach(c=>{
          const div=document.createElement('div');
          div.className='comment';
          div.innerHTML=`<strong>${escapeHtml(c.nome)}:</strong> ${escapeHtml(c.conteudo)} <span class="comment-date">${new Date(c.created_at).toLocaleString('pt-BR')}</span>`;
          container.appendChild(div);
        });
      }else{
        container.innerHTML='<p style="color: #666; font-size: 14px; text-align: center;">Nenhum comentário ainda.</p>';
      }
    });
}

document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.post').forEach(post=>{
    const postId = post.dataset.postId;
    if(postId) loadComments(postId);
  });
});
function atualizarNotificacoes() {
    fetch('notifications_actions.php')
    .then(res => res.json())
    .then(data => {
        // === NOTIFICAÇÕES ===
        const notifBadge = document.querySelector('.menu a[href="notifications.php"] .badge');
        if (data.notificacoes > 0) {
            if (notifBadge) {
                notifBadge.textContent = data.notificacoes;
            } else {
                const link = document.querySelector('.menu a[href="notifications.php"]');
                const span = document.createElement('span');
                span.className = 'badge';
                span.textContent = data.notificacoes;
                link.appendChild(span);
            }
        } else {
            if (notifBadge) notifBadge.remove();
        }

        // === MENSAGENS ===
        const msgBadge = document.querySelector('.menu a[href="chat.php"] .badge');
        if (data.mensagens > 0) {
            if (msgBadge) {
                msgBadge.textContent = data.mensagens;
            } else {
                const link = document.querySelector('.menu a[href="chat.php"]');
                const span = document.createElement('span');
                span.className = 'badge';
                span.textContent = data.mensagens;
                link.appendChild(span);
            }
        } else {
            if (msgBadge) msgBadge.remove();
        }
    });
}
// Atualiza ao carregar a página
atualizarNotificacoes();

// Atualiza a cada 5 segundos
setInterval(atualizarNotificacoes, 5000);


// === Script da busca em tempo real ===
document.getElementById('searchInput').addEventListener('input', async function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('searchResults');

    if (query.length === 0) {
        resultsDiv.style.display = 'none';
        resultsDiv.innerHTML = '';
        return;
    }

    try {
        const response = await fetch('search.php?q=' + encodeURIComponent(query));
        const data = await response.text();
        resultsDiv.innerHTML = data;
        resultsDiv.style.display = 'block';
    } catch (error) {
        console.error('Erro na pesquisa:', error);
    }
});

// Fecha resultados ao clicar fora
document.addEventListener('click', function(e) {
    const input = document.getElementById('searchInput');
    const results = document.getElementById('searchResults');
    if (!input.contains(e.target) && !results.contains(e.target)) {
        results.style.display = 'none';
    }
});

</script>

</body>
</html>