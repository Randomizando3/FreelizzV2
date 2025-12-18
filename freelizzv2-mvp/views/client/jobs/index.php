<h2>Meus Jobs</h2>

<div class="grid">
  <div class="card col-4">
    <h3>Publicar Job</h3>
    <form method="post" action="/client/jobs/create">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label class="muted">Categoria</label>
      <select class="input" name="category_id" required>
        <option value="">Selecione</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <div style="height:8px"></div>
      <label class="muted">Título</label>
      <input class="input" name="title" required>

      <div style="height:8px"></div>
      <label class="muted">Tipo</label>
      <select class="input" name="budget_type">
        <option value="fixed">Fixo</option>
        <option value="hourly">Por hora</option>
      </select>

      <div style="height:8px"></div>
      <label class="muted">Budget mínimo (centavos)</label>
      <input class="input" name="budget_min_cents" value="10000">

      <div style="height:8px"></div>
      <label class="muted">Budget máximo (centavos)</label>
      <input class="input" name="budget_max_cents" value="30000">

      <div style="height:8px"></div>
      <label class="muted">Hora (centavos)</label>
      <input class="input" name="hourly_rate_cents" value="0">

      <div style="height:8px"></div>
      <label class="muted">Descrição (HTML)</label>
      <textarea class="input" name="description_html" rows="7"><p>Descreva o escopo...</p></textarea>

      <div style="height:12px"></div>
      <button class="btn" type="submit">Publicar</button>
    </form>
  </div>

  <div class="card col-8">
    <h3>Lista</h3>
    <table class="table">
      <thead><tr><th>ID</th><th>Título</th><th>Status</th><th>Propostas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($jobs as $j): ?>
          <tr>
            <td><?= (int)$j['id'] ?></td>
            <td><?= htmlspecialchars($j['title']) ?><div class="muted"><?= htmlspecialchars($j['category_name']) ?></div></td>
            <td><?= htmlspecialchars($j['status']) ?></td>
            <td class="muted">—</td>
            <td><a class="btn secondary" href="/client/jobs/<?= (int)$j['id'] ?>">Abrir</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$jobs): ?><tr><td colspan="5" class="muted">Nenhum job ainda.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
