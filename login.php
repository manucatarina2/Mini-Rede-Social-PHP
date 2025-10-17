<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>XoXo - Login</title>
  <style>
    /* === RESET === */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
    body { display: flex; height: 100vh; background-color: #fff; }

    .left {
  flex: 1;
  position: relative;
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 40px;
  overflow: hidden;
}

/* === Fundo com imagem e gradiente translúcido === */
.left::before {
  content: "";
  position: absolute;
  inset: 0;
  background:
    linear-gradient(rgba(236, 72, 153, 0.6), rgba(190, 24, 93, 0.6)),
    url("fundo.png") center/cover no-repeat;
  opacity: 0.7;
  z-index: 0;
}

/* Conteúdo acima da imagem */
.left img,
.left h1,
.left p {
  position: relative;
  z-index: 1;
}

.left h1 {
  font-size: 48px;
  font-weight: 700;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.left p {
  font-size: 16px;
  line-height: 1.6;
  max-width: 400px;
}
p { font-size: 16px; line-height: 1.6; max-width: 400px; }

    .right { flex: 1; display: flex; align-items: center; justify-content: center; background-color: #fff; }
    .login-box { width: 360px; }
    .login-box h2 { text-align: center; font-size: 28px; font-weight: 700; color: #222; margin-bottom: 30px; }
    .login-box label { font-size: 14px; font-weight: 500; color: #444; }
    .login-box input { width: 100%; padding: 12px; margin: 8px 0 18px; border: 1px solid #eee; border-radius: 8px; background-color: #f9f9f9; font-size: 14px; }
    .login-box input:focus { outline: none; border-color: #e94089; background-color: #fff; }
    .login-box button { width: 100%; background: linear-gradient(135deg, #fa8fbdff, #f84a95ff); border: none; color: white; font-size: 15px; font-weight: 600; padding: 12px; border-radius: 25px; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .login-box button:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(233, 64, 137, 0.4); }
    .login-box .register { text-align: center; margin-top: 20px; font-size: 14px; color: #aaa; }
    .login-box .register a { color: #e94089; font-weight: 500; text-decoration: none; }
    .login-box .register a:hover { text-decoration: underline; }
    .error { color: red; text-align: center; margin: 10px 0; padding: 10px; background: #ffe6e6; border-radius: 5px; display: block; } /* Mudança: display block para mostrar erro */

    @media (max-width: 900px) {
      body { flex-direction: column; }
      .left { height: 45vh; padding: 30px; }
      .left h1 { font-size: 40px; }
      .right { height: 55vh; }
      .login-box { width: 90%; }
    }
  </style>
</head>
<body>

  <!-- LADO ESQUERDO -->
  <div class="left">
      <img src="logo.png" alt="Logo XoXo"><br>
      <h1>Bem-vindo de volta<br>à XoXo!</h1>
      <p>Fofocas, risadas e segredos. Conecte-se com pessoas incríveis e compartilhe momentos especiais em uma comunidade charmosa e misteriosa.</p>
  </div>

  <!-- LADO DIREITO -->
  <div class="right">
    <form class="login-box" method="POST" action="processa_login.php">
      <h2>Entrar</h2>

      <?php if (isset($_GET['error'])): ?>
        <div class="error"><?= htmlspecialchars(urldecode($_GET['error'])) ?></div>
      <?php endif; ?>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="seuemail@gmail.com" required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" placeholder="••••••••" required autocomplete="current-password">

      <button type="submit">Entrar</button>

      <div class="register">
        Não tem uma conta? <a href="register.php">Cadastre-se</a>
      </div>
    </form>
  </div>
</body>
</html>
