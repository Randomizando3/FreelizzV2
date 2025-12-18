<div class="card">
  <h2>Dashboard</h2>
  <p class="muted">Visão rápida do sistema.</p>
</div>

<div style="height:12px"></div>

<div class="grid">
  <div class="card col-4">
    <b>Usuários</b>
    <div class="muted"><?= (int)$usersTotal ?> total</div>
    <div class="muted"><?= (int)$clients ?> clientes • <?= (int)$freelas ?> freelancers</div>
  </div>

  <div class="card col-4">
    <b>Jobs</b>
    <div class="muted"><?= (int)$jobsAll ?> total</div>
    <div class="muted"><?= (int)$jobsPub ?> publicados</div>
  </div>

  <div class="card col-4">
    <b>Verificações</b>
    <div class="muted"><?= (int)$pendingVer ?> pendentes</div>
    <a class="btn secondary" href="/admin/verifications" style="margin-top:10px">Abrir fila</a>
  </div>
</div>
