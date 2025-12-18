<h2>Minhas Propostas</h2>

<div class="card">
  <table class="table">
    <thead><tr><th>ID</th><th>Job</th><th>Status</th><th>Valor</th><th>Data</th></tr></thead>
    <tbody>
      <?php foreach ($proposals as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= htmlspecialchars($p['title']) ?></td>
          <td><?= htmlspecialchars($p['status']) ?></td>
          <td><?= number_format(((int)$p['price_cents'])/100, 2, ',', '.') ?></td>
          <td class="muted"><?= htmlspecialchars($p['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$proposals): ?><tr><td colspan="5" class="muted">Nenhuma proposta.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
