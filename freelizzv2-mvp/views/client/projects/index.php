<h2>Meus Projetos</h2>

<div class="card">
  <table class="table">
    <thead><tr><th>ID</th><th>Job</th><th>Freelancer</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($projects as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= htmlspecialchars($p['title']) ?></td>
          <td><?= htmlspecialchars($p['freelancer_name']) ?></td>
          <td><?= htmlspecialchars($p['status']) ?></td>
          <td><a class="btn secondary" href="/client/projects/<?= (int)$p['id'] ?>">Abrir</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$projects): ?><tr><td colspan="5" class="muted">Nenhum projeto.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
