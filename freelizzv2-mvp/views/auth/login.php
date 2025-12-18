<h1>Login</h1>
<?php if (!empty($_GET['err'])): ?><p class='muted'>Credenciais inválidas.</p><?php endif; ?>
<div class='card'>
  <form method='post'>
    <input type='hidden' name='_csrf' value='<?= htmlspecialchars($csrf) ?>'>
    <label class='muted'>E-mail</label>
    <input class='input' name='email' type='email'>
    <label class='muted'>Senha</label>
    <input class='input' name='password' type='password'>
    <p style='margin-top:10px'><button class='btn' type='submit'>Entrar</button></p>
  </form>
</div>
