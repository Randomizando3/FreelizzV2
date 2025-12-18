<h2>Job #<?= (int)$job['id'] ?> • <?= htmlspecialchars($job['title']) ?></h2>

<div class="card">
  <div class="muted">Categoria: <?= htmlspecialchars($job['category_name']) ?> • Status: <?= htmlspecialchars($job['status']) ?></div>
  <div style="margin-top:10px"><?= $job['description_html'] ?></div>
</div>

<div style="height:12px"></div>

<div class="card">
  <h3>Propostas (ordenadas por plano)</h3>
  <table class="table">
    <thead><tr><th>Plano</th><th>Freelancer</th><th>Valor</th><th>Prazo</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($proposals as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['plan_code_snapshot']) ?></td>
          <td><b><?= htmlspecialchars($p['freelancer_name']) ?></b><div class="muted"><?= htmlspecialchars($p['freelancer_email']) ?></div></td>
          <td><?= number_format(((int)$p['price_cents'])/100, 2, ',', '.') ?></td>
          <td class="muted"><?= (int)$p['eta_days'] ?> dias</td>
          <td style="white-space:nowrap">
            <?php if ($job['status']==='published' && $p['status']==='sent'): ?>
              <form method="post" action="/client/jobs/<?= (int)$job['id'] ?>/accept" style="display:inline">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="proposal_id" value="<?= (int)$p['id'] ?>">
                <button class="btn" type="submit">Aceitar</button>
              </form>
            <?php else: ?>
              <span class="muted"><?= htmlspecialchars($p['status']) ?></span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td colspan="5" class="muted"><?= $p['cover_letter_html'] ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$proposals): ?><tr><td colspan="5" class="muted">Nenhuma proposta ainda.</td></tr><?php endif; ?>
    </tbody>
  </table>

  <div style="margin-top:10px">
    <a class="btn secondary" href="/client/jobs">Voltar</a>
  </div>
</div>
