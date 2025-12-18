<h2>Job #<?= (int)$job['id'] ?> • <?= htmlspecialchars($job['title']) ?></h2>

<div class="card">
  <div class="muted">Cliente: <?= htmlspecialchars($job['client_name']) ?> • Categoria: <?= htmlspecialchars($job['category_name']) ?></div>
  <div style="margin-top:10px"><?= $job['description_html'] ?></div>

  <div style="margin-top:10px" class="muted">
    Tipo: <?= htmlspecialchars($job['budget_type']) ?> •
    Budget: <?= number_format(((int)$job['budget_min_cents'])/100, 2, ',', '.') ?> a
    <?= number_format(((int)$job['budget_max_cents'])/100, 2, ',', '.') ?>
  </div>

  <?php if ($avg !== null): ?>
    <div class="muted">Média das propostas (visível pelo seu plano): <?= number_format($avg/100, 2, ',', '.') ?></div>
  <?php endif; ?>
</div>

<div style="height:12px"></div>

<div class="card">
  <h3>Enviar proposta</h3>

  <?php if ($myProposal): ?>
    <div class="muted">Você já enviou proposta. Status: <b><?= htmlspecialchars($myProposal['status']) ?></b></div>
  <?php else: ?>
    <form method="post" action="/freelancer/jobs/<?= (int)$job['id'] ?>/propose">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label class="muted">Proposta (HTML)</label>
      <textarea class="input" name="cover_letter_html" rows="6"><p>Olá! Eu posso fazer...</p></textarea>

      <div style="height:8px"></div>
      <label class="muted">Valor (centavos)</label>
      <input class="input" name="price_cents" value="15000">

      <div style="height:8px"></div>
      <label class="muted">Prazo (dias)</label>
      <input class="input" name="eta_days" value="7">

      <div style="height:12px"></div>
      <button class="btn" type="submit">Enviar</button>
      <a class="btn secondary" href="/freelancer/jobs">Voltar</a>
    </form>
  <?php endif; ?>
</div>
