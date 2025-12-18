<h2>Verificações</h2>

<div class="card">
  <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
    <select class="input" name="status" style="max-width:220px">
      <?php foreach (['pending','approved','rejected'] as $s): ?>
        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filtrar</button>
  </form>
</div>

<div style="height:12px"></div>

<div class="card">
  <table class="table">
    <thead><tr><th>ID</th><th>Freelancer</th><th>Status</th><th>Data</th><th>Ações</th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td>
            <b><?= htmlspecialchars($r['name']) ?></b>
            <div class="muted"><?= htmlspecialchars($r['email']) ?></div>
          </td>
          <td><?= htmlspecialchars($r['status']) ?></td>
          <td class="muted"><?= htmlspecialchars($r['created_at']) ?></td>
          <td><a class="btn secondary" href="/admin/verifications/<?= (int)$r['id'] ?>">Abrir</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="5" class="muted">Sem registros.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
