<h1>Jobs</h1>
<div class='card'>
  <?php if (!$jobs): ?>
    <p class='muted'>Nenhum job publicado.</p>
  <?php endif; ?>
  <?php foreach ($jobs as $j): ?>
    <p>
      <a href='/jobs/<?= (int)$j['id'] ?>'><b><?= htmlspecialchars($j['title']) ?></b></a>
      <span class='muted'> — <?= htmlspecialchars($j['category_name']) ?> · <?= htmlspecialchars($j['budget_type']) ?></span>
    </p>
  <?php endforeach; ?>
</div>
