<h2>Projeto #<?= (int)$project['id'] ?> • <?= htmlspecialchars($project['title']) ?></h2>

<div class="card">
  <div class="muted">Freelancer: <?= htmlspecialchars($project['freelancer_name']) ?> • <?= htmlspecialchars($project['freelancer_email']) ?></div>
  <div class="muted">Status: <?= htmlspecialchars($project['status']) ?></div>
</div>

<div style="height:12px"></div>

<div class="grid">
  <div class="card col-7">
    <h3>Chat</h3>

    <div style="max-height:420px;overflow:auto;border:1px solid var(--bd);border-radius:10px;padding:10px">
      <?php foreach ($messages as $m): ?>
        <div style="margin-bottom:8px">
          <b><?= htmlspecialchars($m['sender_name']) ?>:</b>
          <?= nl2br(htmlspecialchars($m['body'])) ?>
          <div class="muted" style="font-size:12px"><?= htmlspecialchars($m['created_at']) ?></div>
        </div>
      <?php endforeach; ?>
      <?php if (!$messages): ?><div class="muted">Sem mensagens.</div><?php endif; ?>
    </div>

    <form method="post" action="/client/projects/<?= (int)$project['id'] ?>/message" style="margin-top:10px;display:flex;gap:8px">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input class="input" name="body" placeholder="Digite..." style="flex:1">
      <button class="btn" type="submit">Enviar</button>
    </form>
  </div>

  <div class="card col-5">
    <h3>Ações</h3>

    <div class="muted">Pagamento “garantia” via Mercado Pago entra no próximo incremento (escrow). No MVP você já consegue testar: aceitar, chat, concluir, avaliar e disputa.</div>

    <div style="height:10px"></div>
    <form method="post" action="/client/projects/<?= (int)$project['id'] ?>/complete" onsubmit="return confirm('Marcar como concluído?')">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <button class="btn" type="submit">Marcar concluído</button>
    </form>

    <div style="height:10px"></div>
    <h4>Avaliar freelancer</h4>
    <form method="post" action="/client/projects/<?= (int)$project['id'] ?>/review">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input class="input" name="rating" placeholder="Nota 1-5">
      <div style="height:8px"></div>
      <input class="input" name="comment" placeholder="Comentário (opcional)">
      <div style="height:8px"></div>
      <button class="btn secondary" type="submit">Enviar avaliação</button>
    </form>

    <div style="height:12px"></div>
    <h4>Denúncia / Mediação</h4>
    <form method="post" action="/client/projects/<?= (int)$project['id'] ?>/dispute">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <input class="input" name="reason" placeholder="Motivo (ex: Escopo, pagamento, conduta)">
      <div style="height:8px"></div>
      <textarea class="input" name="details" rows="4" placeholder="Detalhes"></textarea>
      <div style="height:8px"></div>
      <button class="btn secondary" type="submit">Abrir mediação</button>
    </form>

    <div style="height:10px"></div>
    <a class="btn secondary" href="/client/projects">Voltar</a>
  </div>
</div>
