<h2>Configurações</h2>

<div class="grid">
  <div class="card col-6">
    <h3>Parâmetros do sistema</h3>
    <form method="post" action="/admin/settings/save">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

      <label class="muted">E-mail suporte</label>
      <input class="input" name="support_email" value="<?= htmlspecialchars((string)($settings['support_email'] ?? '')) ?>">

      <div style="height:8px"></div>
      <label class="muted">WhatsApp suporte</label>
      <input class="input" name="support_whatsapp" value="<?= htmlspecialchars((string)($settings['support_whatsapp'] ?? '')) ?>">

      <div style="height:8px"></div>
      <label class="muted">E-mail destino do formulário de contato</label>
      <input class="input" name="contact_to_email" value="<?= htmlspecialchars((string)($settings['contact_to_email'] ?? '')) ?>">

      <div style="height:8px"></div>
      <label class="muted">Preço verificação (centavos)</label>
      <input class="input" name="verification_price_cents" value="<?= htmlspecialchars((string)($settings['verification_price_cents'] ?? '0')) ?>">

      <div style="height:8px"></div>
      <label class="muted">Mercado Pago Access Token</label>
      <input class="input" name="mp_access_token" value="<?= htmlspecialchars((string)($settings['mp_access_token'] ?? '')) ?>">

      <div style="height:8px"></div>
      <label class="muted">Mercado Pago Webhook Secret</label>
      <input class="input" name="mp_webhook_secret" value="<?= htmlspecialchars((string)($settings['mp_webhook_secret'] ?? '')) ?>">

      <div style="height:12px"></div>
      <button class="btn" type="submit">Salvar</button>
    </form>
  </div>

  <div class="card col-6">
    <h3>Planos</h3>
    <p class="muted">Tudo parametrizável no Admin.</p>

    <?php foreach ($plans as $pl): ?>
      <div class="card" style="margin-top:10px">
        <b><?= htmlspecialchars($pl['code']) ?></b>

        <form method="post" action="/admin/settings/plan/<?= htmlspecialchars($pl['code']) ?>" style="margin-top:8px">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

          <label class="muted">Nome</label>
          <input class="input" name="name" value="<?= htmlspecialchars($pl['name']) ?>">

          <div style="height:8px"></div>
          <label class="muted">Propostas/dia (0=ilimitado)</label>
          <input class="input" name="proposals_per_day" value="<?= (int)$pl['proposals_per_day'] ?>">

          <div style="height:8px"></div>
          <label class="muted">Taxa %</label>
          <input class="input" name="take_rate_pct" value="<?= htmlspecialchars((string)$pl['take_rate_pct']) ?>">

          <div style="height:8px"></div>
          <label class="muted">Min saque (centavos)</label>
          <input class="input" name="withdraw_min_cents" value="<?= (int)$pl['withdraw_min_cents'] ?>">

          <div style="height:8px"></div>
          <label class="muted">Janela saque (texto)</label>
          <input class="input" name="withdraw_window" value="<?= htmlspecialchars((string)$pl['withdraw_window']) ?>">

          <div style="height:8px"></div>
          <label class="muted">Prazo saque (texto)</label>
          <input class="input" name="withdraw_speed" value="<?= htmlspecialchars((string)$pl['withdraw_speed']) ?>">

          <div style="height:8px"></div>
          <label class="muted">Pode ver média de propostas?</label>
          <select class="input" name="can_view_avg">
            <option value="0" <?= (int)$pl['can_view_avg']===0?'selected':'' ?>>Não</option>
            <option value="1" <?= (int)$pl['can_view_avg']===1?'selected':'' ?>>Sim</option>
          </select>

          <div style="height:8px"></div>
          <label class="muted">Destaque público (Premium)</label>
          <select class="input" name="featured_public">
            <option value="0" <?= (int)$pl['featured_public']===0?'selected':'' ?>>Não</option>
            <option value="1" <?= (int)$pl['featured_public']===1?'selected':'' ?>>Sim</option>
          </select>

          <div style="height:8px"></div>
          <label class="muted">Prioridade suporte (1..3)</label>
          <input class="input" name="support_priority" value="<?= (int)$pl['support_priority'] ?>">

          <div style="height:10px"></div>
          <button class="btn secondary" type="submit">Salvar plano</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>
