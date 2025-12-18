<h2>Meu Perfil</h2>

<div class="card">
  <form method="post" action="/freelancer/profile" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

    <label class="muted">Foto</label>
    <input class="input" type="file" name="avatar">

    <div style="height:8px"></div>
    <label class="muted">Área</label>
    <input class="input" name="area" value="<?= htmlspecialchars((string)($acc['area'] ?? '')) ?>">

    <div style="height:8px"></div>
    <label class="muted">Descrição (HTML)</label>
    <textarea class="input" name="bio_html" rows="6"><?= htmlspecialchars((string)($acc['bio_html'] ?? '<p>Sobre mim...</p>')) ?></textarea>

    <div style="height:8px"></div>
    <label class="muted">Portfólio URL</label>
    <input class="input" name="portfolio_url" value="<?= htmlspecialchars((string)($acc['portfolio_url'] ?? '')) ?>">

    <div style="height:8px"></div>
    <label class="muted">Portfólio HTML</label>
    <textarea class="input" name="portfolio_html" rows="6"><?= htmlspecialchars((string)($acc['portfolio_html'] ?? '')) ?></textarea>

    <div style="height:12px"></div>
    <button class="btn" type="submit">Salvar</button>
  </form>

  <?php if (!empty($acc['avatar_path'])): ?>
    <div style="margin-top:10px" class="muted">Avatar: <a href="<?= htmlspecialchars($acc['avatar_path']) ?>" target="_blank"><?= htmlspecialchars($acc['avatar_path']) ?></a></div>
  <?php endif; ?>
</div>
