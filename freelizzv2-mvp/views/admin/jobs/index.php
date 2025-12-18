<h2>Jobs</h2>

<div class="card">
  <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
    <select class="input" name="status" style="max-width:220px">
      <option value="">Status (todos)</option>
      <?php foreach (['draft','published','in_progress','completed','canceled'] as $s): ?>
        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filtrar</button>
  </form>
</div>

<div style="height:12px"></div>

<div class="grid">
  <div class="card col-4">
    <h3>Novo Job (teste)</h3>
    <form method="post" action="/admin/jobs/create">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label class="muted">Client ID</label>
      <input class="input" name="client_id" placeholder="ex: 1">

      <div style="height:8px"></div>
      <label class="muted">Categoria</label>
      <select class="input" name="category_id">
        <option value="">Selecione</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <div style="height:8px"></div>
      <label class="muted">Título</label>
      <input class="input" name="title">

      <div style="height:8px"></div>
      <label class="muted">Tipo</label>
      <select class="input" name="budget_type">
        <option value="fixed">fixed</option>
        <option value="hourly">hourly</option>
      </select>

      <div style="height:8px"></div>
      <label class="muted">Status</label>
      <select class="input" name="status">
        <option value="draft">draft</option>
        <option value="published">published</option>
      </select>

      <div style="height:8px"></div>
      <label class="muted">Descrição (HTML)</label>
      <textarea class="input" name="description_html" rows="6"><p>Descrição do job...</p></textarea>

      <div style="height:10px"></div>
      <button class="btn" type="submit">Criar</button>
    </form>
  </div>

  <div class="card col-8">
    <h3>Lista</h3>
    <table class="table">
      <thead>
        <tr><th>ID</th><th>Título</th><th>Categoria</th><th>Cliente</th><th>Status</th><th>Ações</th></tr>
      </thead>
      <tbody>
        <?php foreach ($jobs as $j): ?>
          <tr>
            <td><?= (int)$j['id'] ?></td>
            <td><?= htmlspecialchars($j['title']) ?></td>
            <td><?= htmlspecialchars($j['category_name']) ?></td>
            <td class="muted"><?= htmlspecialchars($j['client_email']) ?></td>
            <td><?= htmlspecialchars($j['status']) ?></td>
            <td><a class="btn secondary" href="/admin/jobs/<?= (int)$j['id'] ?>">Abrir</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$jobs): ?><tr><td colspan="6" class="muted">Nenhum job.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
