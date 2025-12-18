<h2>Verificação #<?= (int)$ver['id'] ?></h2>

<div class="card">
  <div><b>Freelancer:</b> <?= htmlspecialchars($ver['name']) ?> (<?= htmlspecialchars($ver['email']) ?>)</div>
  <div class="muted">Status: <?= htmlspecialchars($ver['status']) ?> • Criado em: <?= htmlspecialchars($ver['created_at']) ?></div>
  <?php if (!empty($ver['reason'])): ?>
    <div class="muted">Motivo: <?= htmlspecialchars($ver['reason']) ?></div>
  <?php endif; ?>
</div>

<div style="height:12px"></div>

<div class="grid">
  <div class="card col-6">
    <h3>Selfie</h3>
    <div class="muted"><?= htmlspecialchars((string)$ver['selfie_path']) ?></div>
  </div>
  <div class="card col-6">
    <h3>Documento</h3>
    <div class="muted"><?= htmlspecialchars((string)$ver['document_path']) ?></div>
  </div>
</div>

<div style="height:12px"></div>

<div class="card">
  <?php if ($ver['status'] === 'pending'): ?>
    <form method="post" action="/admin/verifications/<?= (int)$ver['id'] ?>/approve" style="display:inline">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <button class="btn" type="submit">Aprovar</button>
    </form>

    <form method="post" action="/admin/verifications/<?= (int)$ver['id'] ?>/reject" style="display:inline;margin-left:8px">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input class="input" name="reason" placeholder="motivo" style="max-width:320px;display:inline-block">
      <button class="btn secondary" type="submit">Reprovar</button>
    </form>
  <?php else: ?>
    <span class="muted">Esta solicitação já foi finalizada.</span>
  <?php endif; ?>

  <a class="btn secondary" href="/admin/verifications" style="margin-left:8px">Voltar</a>
</div>
