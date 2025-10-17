<?php
session_start();
require 'conexao.php';

// Verifica se logado
if (!isset($_SESSION['logado']) || !$_SESSION['logado'] || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$currentUserId = $_SESSION['user_id'];

// ================= MARCAR NOTIFICAÇÕES COMO LIDAS =================
$markRead = $conn->prepare("UPDATE notificacoes SET lida = 1 WHERE usuario_id = ? AND lida = 0");
$markRead->bind_param("i", $currentUserId);
$markRead->execute();

// ================= BUSCAR DADOS DO USUÁRIO =================
$stmt = $conn->prepare("SELECT id, nome, email, foto FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();
$currentUser['foto'] = !empty($currentUser['foto']) && file_exists($currentUser['foto']) ? $currentUser['foto'] : 'user.jpg';

// ================= CONTAR NOTIFICAÇÕES NÃO LIDAS =================
$count_stmt = $conn->prepare("SELECT COUNT(*) as nao_lidas FROM notificacoes WHERE usuario_id = ? AND lida = 0");
$count_stmt->bind_param("i", $currentUserId);
$count_stmt->execute();
$nao_lidas = $count_stmt->get_result()->fetch_assoc()['nao_lidas'] ?? 0;

// ================= ATUALIZAR LAST_LOGIN E PRESENCE =================
$update_stmt = $conn->prepare("UPDATE usuarios SET last_login = NOW() WHERE id = ?");
$update_stmt->bind_param("i", $currentUserId);
$update_stmt->execute();

$presence_stmt = $conn->prepare("INSERT INTO presence (usuario_id, last_seen) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE last_seen = NOW()");
$presence_stmt->bind_param("i", $currentUserId);
$presence_stmt->execute();

// ================= BUSCAR LISTA DE USUÁRIOS ONLINE =================
$online_stmt = $conn->prepare("
    SELECT DISTINCT u.id, u.nome, u.foto 
    FROM presence p 
    JOIN usuarios u ON p.usuario_id = u.id 
    WHERE p.usuario_id != ? AND p.last_seen >= (NOW() - INTERVAL 60 SECOND)
    ORDER BY u.nome
");
$online_stmt->bind_param("i", $currentUserId);
$online_stmt->execute();
$result = $online_stmt->get_result();
$onlineUsersList = [];
while ($row = $result->fetch_assoc()) {
    $onlineUsersList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mensagens - XoXo</title>
<style>
/* ===== RESET E BASE ===== */
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
body {
  display: flex;
  min-height: 100vh;
  position: relative;
  z-index: 0; /* Garante que o conteúdo fique acima do fundo */
}

body::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('fundo.png') center/cover no-repeat;
  opacity: 0.2;
  z-index: -1; /* Coloca o fundo atrás do conteúdo */
}

/* ===== SIDEBAR ===== */
.sidebar { width:280px; background:linear-gradient(135deg,#fa8fbdff,#f84a95ff); color:#fff; display:flex; flex-direction:column; padding:40px 20px; position:fixed; top:0; bottom:0; left:0; transition:all 0.3s ease; }
.sidebar.hidden { transform:translateX(-100%); }
.user-profile-sidebar { margin-top:auto; padding:20px; background:rgba(255,255,255,0.15); border-radius:12px; display:flex; align-items:center; gap:15px; }
.user-profile-sidebar .user-info { display:flex; align-items:center; gap:10px; }
.user-avatar { width:50px; height:50px; border-radius:50%; overflow:hidden; flex-shrink:0; }
.user-avatar img { width:100%; height:100%; object-fit:cover; }
.user-details { display:flex; flex-direction:column; }
.user-name { font-weight:600; color:#fff; font-size:16px; }
.user-email { font-size:14px; color:#eee; }
.logo-container { display:flex; align-items:center; gap:8px; margin-bottom:30px; }
.logo-container img { height:40px; width:auto; }
.menu { display:flex; flex-direction:column; gap:15px; }
.menu a { color:#fff; text-decoration:none; font-weight:500; font-size:16px; padding:10px 15px; border-radius:8px; display:flex; align-items:center; gap:10px; transition:background 0.2s ease, transform 0.2s ease; }
.menu a:hover { background:rgba(255,255,255,0.15); transform:translateX(5px); }
.menu a.active { background:rgba(255,255,255,0.25); font-weight:700; }
.badge { background: #ff4757; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; margin-left: 5px; font-weight: bold; }
/* ===== TOP BAR ===== */
.top-bar { position:fixed; left:280px; right:0; top:0; height:60px; background:rgba(255,255,255,0.9); display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#333; z-index:2; transition:left 0.3s ease; }
.top-bar.sidebar-hidden { left:0; }
.menu-toggle { background:none; border:none; cursor:pointer; color:#333; }
.menu-toggle svg { width:24px; height:24px; }
.user-greeting { font-size:18px; font-weight:500; color:#333; }
/* ===== MAIN CONTENT ===== */
.main-content { margin-left:280px; padding:80px 40px 40px 40px; flex:1; transition:margin-left 0.3s ease; }
.main-content.sidebar-hidden { margin-left:0; }
/* ===== CHAT ===== */
.chat-container { display:flex; gap:20px; height:70vh; }
.users-list { width:250px; background:#f9f9f9; border-radius:12px; padding:15px; box-shadow:0 4px 12px rgba(0,0,0,0.1); overflow-y:auto; }
.users-list h3 { margin-bottom:10px; color:#333; }
.users-list .user { padding:10px; border-radius:8px; cursor:pointer; margin-bottom:5px; background:#fff; transition:0.2s; display:flex; align-items:center; gap:10px; }
.users-list .user:hover { background:#f5c2e7; transform:translateX(5px); }
.users-list .user.active { background:#e94089; color:#fff; }
.users-list .user img { width:30px; height:30px; border-radius:50%; object-fit:cover; }
.users-list .user .fallback { width:30px; height:30px; background:#c11760; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:bold; }
.chat-box { flex:1; display:flex; flex-direction:column; background:#fff; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); overflow:hidden; }
.messages { flex:1; padding:15px; overflow-y:auto; background: #fff; }
.message { margin-bottom:10px; padding:10px 15px; border-radius:12px; max-width:70%; word-wrap:break-word; }
.message.sent { background:#e94089; color:#fff; align-self:flex-end; margin-left:auto; }
.message.received { background:#fff; color:#000; align-self:flex-start; border:1px solid #fa8fbdff; }
.message-time { font-size:10px; margin-top:2px; opacity:0.7; text-align:right; }
.message-form { display:flex; padding:15px; border-top:1px solid #ddd; background:#fff; }
.message-form input { flex:1; padding:12px; border-radius:25px; border:1px solid #ccc; outline:none; margin-right:10px; font-size:14px; }
.message-form button { padding:12px 20px; border:none; border-radius:25px; background:linear-gradient(135deg,#e94089,#c11760); color:#fff; cursor:pointer; font-weight:600; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.message-form button:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(233,64,137,0.4); }
.no-chat { text-align:center; color:#666; padding:20px; font-size:16px; }
/* ===== RESPONSIVO ===== */
@media (max-width:900px){ body{flex-direction:column;} .sidebar{width:100%; position:relative; padding:30px;} .menu a{display:inline-block; margin:5px 0;} .main-content{margin-left:0; padding:20px;} .chat-container{flex-direction:column;} .users-list{width:100%; max-height:200px;} }
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
    <a href="index.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg> Feed
    </a>
    <a href="perfil.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8V22h19.2v-2.8c0-3.2-6.4-4.8-9.6-4.8z"/></svg> Perfil
    </a>
    <a href="chat.php" class="active">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M4 4h16v12H5.17L4 17.17V4z"/></svg> Mensagens
    </a>
    <a href="notifications.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24">
        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5S10 3.17 10 4v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
      </svg> Notificações <?php if ($nao_lidas>0): ?> 
        <span class="badge"><?= $nao_lidas ?></span><?php endif; ?>
    </a>
    <a href="logout.php">
      <svg width="20" height="20" fill="#fff" viewBox="0 0 24 24"><path d="M16 13v-2H7V8l-5 4 5 4v-3z"/></svg> Sair
    </a>
  </div>
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
  <div class="user-greeting">Olá, <?= htmlspecialchars($currentUser['nome']) ?>!</div>
</div>

<div class="main-content" id="mainContent">
    <h2>Mensagens</h2><br>
    <div class="chat-container">
        <div class="users-list" id="usersList">
            <h3>Usuários Online</h3>
            <?php if (empty($onlineUsersList)): ?>
                <p style="color: #666; font-style: italic;">Nenhum usuário online no momento.</p>
            <?php else: ?>
                <?php foreach ($onlineUsersList as $user): ?>
                    <div class="user" data-id="<?= $user['id'] ?>">
                        <?php
                        $foto = (!empty($user['foto']) && file_exists($user['foto'])) ? $user['foto'] : 'user.jpg';
                        ?>
                        <img src="<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($user['nome']) ?>">

                        <span><?= htmlspecialchars($user['nome']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="chat-box">
          <div class="chat-header" id="chatHeader" style="display:none; padding:15px; background:#f2f2f2; border-bottom:1px solid #ddd; display:flex; justify-content:space-between; align-items:center;">
            <div style="font-weight:600;" id="chatUserName">Usuário</div>
            <button onclick="openUserInfo()" style="background:none; border:none; cursor:pointer;">
              <img src="info.png" alt="info" style="width:22px; height:22px;"></button>
          </div>
          <div class="messages" id="messages">
            <div class="no-chat">Selecione um usuário para começar a conversar.</div>
          </div>
          <form class="message-form" id="messageForm">
            <input type="text" id="messageInput" placeholder="Digite uma mensagem..." disabled>
            <button type="submit" disabled>Enviar</button>
          </form>
        </div>
    </div>
</div>
<!-- MODAL DE INFORMAÇÕES DO USUÁRIO -->
<div id="userInfoModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999;">
    <div style="background:#fff; padding:20px; border-radius:12px; max-width:400px; width:90%; text-align:center; position:relative;">
        <!-- X fechar -->
        <button onclick="closeUserInfo()" style="position:absolute; top:10px; right:15px; background:none; border:none; cursor:pointer;">
            <img src="xis.png" alt="Fechar" style="width:22px; height:22px;">
        </button>

        <div id="userInfoContent">
            <img id="infoFoto" src="user.jpg" alt="Foto" style="width:80px; height:80px; border-radius:50%; object-fit:cover; margin-bottom:10px;"><br>
            <h3 id="infoNome">Nome do Usuário</h3><br>
            <p id="infoEmail">email@exemplo.com</p><br>

            <button 
            id="seguirBtn" data-id=""  
            onclick="seguirUsuario(this)" 
            style="margin-top:10px; background:#e94089; color:#fff; padding:10px 20px; border:none; border-radius:20px; font-weight:bold; cursor:pointer;">
            <img src="mais.png" alt="Seguir" style="width:18px; height:18px; vertical-align:middle; margin-right:5px;">
            Seguir
          </button>

        </div>
    </div>
</div>


<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('hidden');
  document.getElementById('topBar').classList.toggle('sidebar-hidden');
  document.getElementById('mainContent').classList.toggle('sidebar-hidden');
}

let selectedUserId = null;
const currentUserId = <?= $currentUserId ?>;

// ================= CHAT =================
function loadMessages() {
    if(!selectedUserId) return;
    fetch(`chat_actions.php?action=load&to=${selectedUserId}`)
    .then(res=>res.json())
    .then(data=>{
        const messagesDiv = document.getElementById('messages');
        messagesDiv.innerHTML='';
        if(data.length>0){
            data.forEach(m=>{
                const div=document.createElement('div');
                div.className=`message ${m.from_id==currentUserId?'sent':'received'}`;
                const time=new Date(m.created_at).toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
                div.innerHTML=`<div>${m.conteudo}</div><div class="message-time">${time}</div>`;
                messagesDiv.appendChild(div);
            });
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        } else {
            messagesDiv.innerHTML='<div class="no-chat">Nenhuma mensagem ainda. Comece a conversa!</div>';
        }
    });
}
function openUserInfo() {
    if (!selectedUserId) {
        alert('Selecione um usuário primeiro!');
        return;
    }

    fetch('info.php?id=' + selectedUserId)
    .then(res => res.json())
    .then(data => {
        document.getElementById('infoNome').textContent = data.nome || 'Usuário';
        document.getElementById('infoEmail').textContent = data.email || 'E-mail não disponível';
        document.getElementById('infoFoto').src = (data.foto && data.foto.trim() !== '') ? data.foto : 'user.jpg';

        const btn = document.getElementById('seguirBtn');
        btn.dataset.id = data.id; // <-- garante que o data-id está correto
        btn.textContent = '';      // limpa texto para re-adicionar
        const img = document.createElement('img');
        img.src = 'mais.png';
        img.style.width = '18px';
        img.style.height = '18px';
        img.style.verticalAlign = 'middle';
        img.style.marginRight = '5px';
        btn.appendChild(img);
        btn.append('Seguir');      // adiciona texto novamente
        btn.disabled = false;      // habilita caso estivesse desabilitado antes

        document.getElementById('userInfoModal').style.display = 'flex';
    })
    .catch(err => {
        console.error('Erro ao carregar info:', err);
        alert('Erro ao carregar informações do usuário.');
    });
}



function closeUserInfo() {
    document.getElementById('userInfoModal').style.display = 'none';
}

function seguirUsuario(btn) {
    const idSeguido = btn.dataset.id;
    fetch('seguir.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(idSeguido)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            // Atualiza o botão para "Seguindo"
            btn.textContent = 'Seguindo';
            btn.style.background = '#aaa'; // muda a cor para indicar inativo
            btn.disabled = true;

            // Opcional: mantém ícone
            const img = document.createElement('img');
            img.src = 'mais.png';
            img.style.width = '18px';
            img.style.height = '18px';
            img.style.verticalAlign = 'middle';
            img.style.marginRight = '5px';
            btn.prepend(img);

            // Atualiza badge de notificações
            updateNotificationsBadge();
        } else {
            alert(data.error || 'Erro ao seguir.');
        }
    })
    .catch(err => {
        console.error('Erro ao seguir usuário:', err);
        alert('Erro de conexão.');
    });
}



function refreshUsers() {
    fetch('refresh_users.php')
    .then(res => res.json())
    .then(data => {
        const usersListDiv = document.getElementById('usersList');
        let html = '<h3>Usuários Online</h3>';
        if (data.length > 0) {
            data.forEach(u => {
                // Verifica se o usuário tem foto; se não, usa user.jpg
                let foto = 'user.jpg';
                if (u.foto && u.foto.trim() !== '') {
                    foto = u.foto;
                }

                html += `<div class="user" data-id="${u.id}">
                            <img src="${foto}" alt="${u.nome}">
                            <span>${u.nome}</span>
                         </div>`;
            });
        } else {
            html += '<p style="color: #666; font-style: italic;">Nenhum usuário online no momento.</p>';
        }
        usersListDiv.innerHTML = html;

        // === ADICIONAR EVENTO DE CLIQUE ===
        document.querySelectorAll('.user').forEach(u => {
            u.addEventListener('click', function(){
                document.querySelectorAll('.user').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                selectedUserId = this.dataset.id;

                // Exibir nome do contato no topo
                const nomeContato = this.querySelector('span').textContent;
                document.getElementById('chatUserName').textContent = nomeContato;
                document.getElementById('chatHeader').style.display = 'flex';

                document.getElementById('messageInput').disabled = false;
                document.querySelector('#messageForm button').disabled = false;
                loadMessages();
            });
        });
    });
}


document.getElementById('messageForm').addEventListener('submit', function(e){
    e.preventDefault();
    if(!selectedUserId) return alert('Selecione um usuário primeiro!');
    const msgInput=document.getElementById('messageInput');
    const msg=msgInput.value.trim();
    if(!msg) return;
    fetch('chat_actions.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`action=send&to=${selectedUserId}&message=${encodeURIComponent(msg)}`
    })
    .then(res=>res.json())
    .then(data=>{ if(data.success){ msgInput.value=''; loadMessages(); } else alert('Erro ao enviar: '+(data.error||'Tente novamente')); })
    .catch(err=>{ console.error('Erro no envio:',err); alert('Erro de conexão.'); });
});

setInterval(()=>{ if(selectedUserId) loadMessages(); refreshUsers(); },3000);

// ================= NOTIFICAÇÕES =================
function updateNotificationsBadge(){
  fetch('notificacoes_actions.php?action=count&_=' + new Date().getTime())
  .then(res=>res.json())
  .then(data=>{
    const link=document.querySelector('a[href="notifications.php"]');
    let badge=link.querySelector('.badge');
    if(data.nao_lidas>0){
      if(!badge){
        badge=document.createElement('span');
        badge.className='badge';
        link.appendChild(badge);
      }
      badge.textContent=data.nao_lidas;
      badge.style.display='inline';
    } else if(badge){
      badge.style.display='none';
    }
  })
  .catch(err=>console.error('Erro ao atualizar badge:',err));
}
updateNotificationsBadge();
setInterval(updateNotificationsBadge,5000);
</script>
</body>
</html>
