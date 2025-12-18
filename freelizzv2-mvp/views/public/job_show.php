<h1><?= htmlspecialchars($job['title']) ?></h1>
<p class='muted'><?= htmlspecialchars($job['category_name']) ?> · <?= htmlspecialchars($job['budget_type']) ?></p>
<div class='card'><?= $job['description_html'] ?></div>
