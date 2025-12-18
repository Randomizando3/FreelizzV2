<?php
$u = $_SESSION['user'] ?? null;
ob_start();
require $viewFile;
$page = ob_get_clean();
?>
<!doctype html>
<html lang='pt-BR'>
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <title><?= htmlspecialchars((string)(getenv('APP_NAME') ?: 'FreelizzV2')) ?></title>
  <link rel='stylesheet' href='/assets/css/app.css'>
</head>
<body>
<header class='nav'>
  <div class='row'>
    <div class='brand'><a href='/'><?= htmlspecialchars((string)(getenv('APP_NAME') ?: 'FreelizzV2')) ?></a></div>
    <div>
      <a href='/jobs'>Jobs</a>
      <span style='margin:0 8px;color:#e5e7eb'>|</span>
      <?php if (!$u): ?>
        <a href='/login'>Login</a>
      <?php else: ?>
        <form action='/logout' method='post' style='display:inline'>
          <input type='hidden' name='_csrf' value='<?= htmlspecialchars($csrf) ?>'>
          <button class='btn secondary' type='submit'>Sair</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class='container'><?= $page ?></main>
<script src='/assets/js/app.js'></script>
</body>
</html>
