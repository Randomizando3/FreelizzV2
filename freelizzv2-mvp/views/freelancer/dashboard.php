<div class="card">
  <h2>Freelancer • Dashboard</h2>
  <div class="muted">Plano atual: <b><?= htmlspecialchars((string)$plan['code']) ?></b> • Propostas hoje: <?= (int)$sentToday ?></div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px">
    <a class="btn" href="/freelancer/jobs">Buscar Jobs</a>
    <a class="btn secondary" href="/freelancer/proposals">Minhas Propostas</a>
    <a class="btn secondary" href="/freelancer/projects">Meus Projetos</a>
  </div>
</div>

<div style="height:12px"></div>

<div class="grid">
  <div class="card col-6"><b>Propostas</b><div class="muted"><?= (int)$proposalsTotal ?> total</div></div>
  <div class="card col-6"><b>Projetos ativos</b><div class="muted"><?= (int)$activeProjects ?></div></div>
</div>
