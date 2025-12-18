<?php
use App\Helpers\Auth;
use App\Helpers\Flash;

$u = Auth::user();
$f = Flash::get();

ob_start();
require $viewFile;
$page = ob_get_clean();
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin • <?= htmlspecialchars((string)(getenv('APP_NAME') ?: 'FreelizzV2')) ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<main class="container">
  <div class="admin-top">
    <div class="brand"><a href="/admin">Admin</a> <span class="muted">• <?= htmlspecialchars((string)($u['email'] ?? '')) ?></span></div>
    <div>
      <a class="btn secondary" href="/">Ir para o site</a>
    </div>
  </div>

  <?php if ($f): ?>
    <div class="card" style="border-left:6px solid #2563eb">
      <b><?= htmlspecialchars($f['type']) ?></b>
      <div class="muted"><?= htmlspecialchars($f['msg']) ?></div>
    </div>
    <div style="height:10px"></div>
  <?php endif; ?>

  <div class="admin-wrap">
    <aside class="admin-side">
      <a href="/admin">Dashboard</a>
      <a href="/admin/users">Usuários</a>
      <a href="/admin/categories">Categorias</a>
      <a href="/admin/jobs">Jobs</a>
      <a href="/admin/verifications">Verificações</a>
      <a href="/admin/settings">Configurações</a>

      <div style="height:10px"></div>
      <form action="/logout" method="post">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <button class="btn secondary" type="submit" style="width:100%">Sair</button>
      </form>
    </aside>

    <section class="admin-main">
      <?= $page ?>
    </section>
  </div>
</main>
</body>
</html>
