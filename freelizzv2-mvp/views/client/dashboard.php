<div class="card">
  <h2>Cliente • Dashboard</h2>
  <p class="muted">Acesse seus jobs e projetos.</p>
  <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px">
    <a class="btn" href="/client/jobs">Meus Jobs</a>
    <a class="btn secondary" href="/client/projects">Meus Projetos</a>
  </div>
</div>

<div style="height:12px"></div>

<div class="grid">
  <div class="card col-6"><b>Jobs</b><div class="muted"><?= (int)$jobs ?> total</div></div>
  <div class="card col-6"><b>Projetos ativos</b><div class="muted"><?= (int)$activeProjects ?></div></div>
</div>
