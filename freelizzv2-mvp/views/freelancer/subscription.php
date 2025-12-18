<h2>Assinatura</h2>

<div class="card">
  <div class="muted">Plano atual: <b><?= htmlspecialchars((string)$current['code']) ?></b></div>
</div>

<div style="height:12px"></div>

<div class="grid">
  <?php foreach ($plans as $p): ?>
    <div class="card col-4">
      <h3><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['code']) ?>)</h3>
      <div class="muted">Propostas/dia: <?= (int)$p['proposals_per_day'] ?> (0=ilimitado)</div>
      <div class="muted">Ordenação: <?= (int)$p['proposal_sort_weight'] ?></div>
      <div class="muted">Taxa %: <?= htmlspecialchars((string)$p['take_rate_pct']) ?></div>
      <div class="muted">Pode ver média: <?= (int)$p['can_view_avg'] ? 'Sim':'Não' ?></div>

      <form method="post" action="/freelancer/subscription/set" style="margin-top:10px">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
        <input type="hidden" name="plan_code" value="<?= htmlspecialchars($p['code']) ?>">
        <button class="btn secondary" type="submit">Selecionar (teste)</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
