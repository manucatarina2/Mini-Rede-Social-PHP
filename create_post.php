<?php
require 'conexao.php';
session_start();
if (!isset($_SESSION['usuario'])) header('Location: login.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conteudo = trim($_POST['conteudo']);
    // TODO: handle file upload for imagem
    if ($conteudo === '') {
        $error = 'Digite algo.';
    } else {
        $stmt = $conn->prepare('INSERT INTO posts (usuario_id, conteudo) VALUES (?, ?)');
        $stmt->bind_param('is', $_SESSION['usuario']['id'], $conteudo);
        if ($stmt->execute()) header('Location: index.php');
        else $error = 'Erro ao publicar.';
    }
}
include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar Postagem</title>

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      display: flex;
      background: #f5f6fa;
      min-height: 100vh;
    }

    /* === SIDEBAR === */
    .sidebar {
      width: 250px;
      background: #111827;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 20px;
      position: fixed;
      height: 100%;
      box-shadow: 2px 0 8px rgba(0,0,0,0.2);
    }

    .logo {
      text-align: center;
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 30px;
      color: #60a5fa;
      letter-spacing: 1px;
    }

    .menu {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .menu a {
      text-decoration: none;
      color: #e5e7eb;
      font-size: 1rem;
      padding: 10px 15px;
      border-radius: 10px;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .menu a:hover {
      background: #2563eb;
      color: #fff;
      transform: translateX(5px);
    }

    /* === CONTEÚDO === */
    .content {
      margin-left: 270px;
      padding: 40px;
      flex: 1;
    }

    h2 {
      color: #111827;
      font-size: 1.6rem;
    }

    .form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      max-width: 600px;
      margin-top: 20px;
    }

    textarea {
      resize: none;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      font-size: 1rem;
      font-family: inherit;
    }

    .btn {
      background: #2563eb;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 10px 15px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn:hover {
      background: #1d4ed8;
    }

    .alert-error {
      color: #b91c1c;
      background: #fee2e2;
      border: 1px solid #fecaca;
      padding: 10px;
      border-radius: 8px;
      max-width: 600px;
    }
  </style>
</head>

<body>
  <!-- === SIDEBAR === -->
  <aside class="sidebar">
    <h2 class="logo">REDE_SOCIAL</h2>
    <nav class="menu">
      <?php if (!isset($_SESSION['usuario'])): ?>
        <a href="login.php"> Login</a>
      <?php endif; ?>

      <a href="perfil.php"> Área de Perfil</a>
      <a href="index.php"> Feed de Postagens</a>
      <a href="notifications.php"> Notificações</a>
      <a href="chat.php"> Chat</a>

      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="logout.php"> Logout</a>
      <?php endif; ?>
    </nav>
  </aside>

  <!-- === CONTEÚDO PRINCIPAL === -->
  <main class="content">
    <h2>Criar Postagem</h2>

    <?php if ($error): ?>
      <p class="alert-error"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" class="form">
      <textarea name="conteudo" rows="5" placeholder="O que você está pensando?"></textarea>
      <button class="btn">Publicar</button>
    </form>
  </main>
</body>
</html>

<?php include 'footer.php'; ?>
