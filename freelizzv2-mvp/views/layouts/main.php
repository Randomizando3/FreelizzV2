<?php
use App\Helpers\Auth;
use App\Helpers\Flash;

$u = Auth::user();
$f = Flash::get();

ob_start();
require $viewFile;
$page = ob_get_clean();

$appName = (string)(getenv('APP_NAME') ?: 'FreelizzV2');
$role = (string)($u['role'] ?? '');
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($appName) ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="nav">
  <div class="row">
    <div class="brand"><a href="/"><?= htmlspecialchars($appName) ?></a></div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
      <a href="/jobs">Jobs</a>

      <?php if (!$u): ?>
        <span style="margin:0 8px;color:#e5e7eb">|</span>
        <a href="/login">Login</a>
        <a href="/register">Cadastro</a>

      <?php else: ?>

        <?php if ($role === 'client'): ?>
          <span style="margin:0 8px;color:#e5e7eb">|</span>
          <a href="/client">Cliente</a>
          <a href="/client/jobs">Meus Jobs</a>
          <a href="/client/projects">Projetos</a>

        <?php elseif ($role === 'freelancer'): ?>
          <span style="margin:0 8px;color:#e5e7eb">|</span>
          <a href="/freelancer">Freelancer</a>
          <a href="/freelancer/jobs">Buscar Jobs</a>
          <a href="/freelancer/proposals">Propostas</a>
          <a href="/freelancer/projects">Projetos</a>
          <a href="/freelancer/profile">Perfil</a>
          <a href="/freelancer/verification">Verificação</a>
          <a href="/freelancer/subscription">Assinatura</a>

        <?php elseif ($role === 'admin'): ?>
          <span style="margin:0 8px;color:#e5e7eb">|</span>
          <a href="/admin">Admin</a>
          <a href="/admin/users">Usuários</a>
          <a href="/admin/jobs">Jobs</a>
          <a href="/admin/verifications">Verificações</a>
          <a href="/admin/settings">Config</a>
        <?php endif; ?>

        <span style="margin:0 8px;color:#e5e7eb">|</span>
        <form action="/logout" method="post" style="display:inline">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
          <button class="btn secondary" type="submit">Sair</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="container">
  <?php if ($f): ?>
    <div class="card" style="border-left:6px solid #2563eb">
      <b><?= htmlspecialchars($f['type']) ?></b>
      <div class="muted"><?= htmlspecialchars($f['msg']) ?></div>
    </div>
    <div style="height:10px"></div>
  <?php endif; ?>

  <?php if (Auth::isImpersonating()): ?>
    <div class="card" style="border-left:6px solid #f59e0b">
      <b>Impersonation ativa</b>
      <div class="muted">Você está navegando como outro usuário.</div>
      <form action="/admin/impersonate/stop" method="post" style="margin-top:8px">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <button class="btn secondary" type="submit">Voltar para Admin</button>
      </form>
    </div>
    <div style="height:10px"></div>
  <?php endif; ?>

  <?= $page ?>
</main>

<script src="/assets/js/app.js"></script>
</body>
</html>
