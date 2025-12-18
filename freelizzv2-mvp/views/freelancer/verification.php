<h2>Verificação</h2>

<div class="card">
  <div class="muted">Preço: <?= number_format($price/100, 2, ',', '.') ?></div>

  <?php if ($ver): ?>
    <div style="margin-top:8px">
      <b>Status:</b> <?= htmlspecialchars($ver['status']) ?> • <b>Pago:</b> <?= htmlspecialchars($ver['paid_status']) ?>
      <div class="muted">Selfie: <?= htmlspecialchars($ver['selfie_path']) ?></div>
      <div class="muted">Documento: <?= htmlspecialchars($ver['document_path']) ?></div>
      <?php if (!empty($ver['reason'])): ?><div class="muted">Motivo: <?= htmlspecialchars($ver['reason']) ?></div><?php endif; ?>
    </div>
  <?php else: ?>
    <div class="muted">Nenhuma solicitação ainda.</div>
  <?php endif; ?>
</div>

<div style="height:12px"></div>

<div class="card">
  <h3>Enviar verificação</h3>
  <form method="post" action="/freelancer/verification" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
    <label class="muted">Selfie</label>
    <input class="input" type="file" name="selfie" required>
    <div style="height:8px"></div>
    <label class="muted">Documento (legível)</label>
    <input class="input" type="file" name="document" required>
    <div style="height:12px"></div>
    <button class="btn" type="submit">Enviar</button>
  </form>
</div>
