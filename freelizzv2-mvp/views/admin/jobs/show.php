<h2>Job #<?= (int)$job['id'] ?></h2>

<div class="card">
  <div><b>Título:</b> <?= htmlspecialchars($job['title']) ?></div>
  <div class="muted">Categoria: <?= htmlspecialchars($job['category_name']) ?> • Cliente: <?= htmlspecialchars($job['client_email']) ?></div>
  <div class="muted">Tipo: <?= htmlspecialchars($job['budget_type']) ?> • Status: <?= htmlspecialchars($job['status']) ?></div>
</div>

<div style="height:12px"></div>

<div class="card">
  <h3>Descrição</h3>
  <div><?= $job['description_html'] ?></div>
</div>

<div style="height:12px"></div>

<div class="card">
  <h3>Atualizar status</h3>
  <form method="post" action="/admin/jobs/<?= (int)$job['id'] ?>/status" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
    <select class="input" name="status" style="max-width:220px">
      <?php foreach (['draft','published','in_progress','completed','canceled'] as $s): ?>
        <option value="<?= $s ?>" <?= $job['status']===$s?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Salvar</button>
    <a class="btn secondary" href="/admin/jobs">Voltar</a>
  </form>
</div>
