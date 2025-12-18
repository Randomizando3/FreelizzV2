<h2>Buscar Jobs</h2>

<div class="card">
  <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
    <select class="input" name="cat" style="max-width:220px">
      <option value="0">Categoria</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= $cat==(int)$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <select class="input" name="type" style="max-width:220px">
      <option value="">Tipo</option>
      <option value="fixed" <?= $type==='fixed'?'selected':'' ?>>Fixo</option>
      <option value="hourly" <?= $type==='hourly'?'selected':'' ?>>Hora</option>
    </select>

    <button class="btn" type="submit">Filtrar</button>
  </form>
</div>

<div style="height:12px"></div>

<div class="card">
  <table class="table">
    <thead><tr><th>ID</th><th>Título</th><th>Categoria</th><th>Tipo</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($jobs as $j): ?>
        <tr>
          <td><?= (int)$j['id'] ?></td>
          <td><?= htmlspecialchars($j['title']) ?></td>
          <td><?= htmlspecialchars($j['category_name']) ?></td>
          <td class="muted"><?= htmlspecialchars($j['budget_type']) ?></td>
          <td><a class="btn secondary" href="/freelancer/jobs/<?= (int)$j['id'] ?>">Abrir</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$jobs): ?><tr><td colspan="5" class="muted">Nenhum job.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
