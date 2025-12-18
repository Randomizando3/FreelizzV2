<h2>Categorias</h2>

<div class="grid">
  <div class="card col-4">
    <h3>Nova categoria</h3>
    <form method="post" action="/admin/categories/create">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <label class="muted">Nome</label>
      <input class="input" name="name">
      <div style="height:8px"></div>
      <label class="muted">Slug</label>
      <input class="input" name="slug" placeholder="ex: design">
      <div style="height:10px"></div>
      <button class="btn" type="submit">Criar</button>
    </form>
  </div>

  <div class="card col-8">
    <h3>Lista</h3>
    <table class="table">
      <thead><tr><th>ID</th><th>Nome</th><th>Slug</th><th>Ações</th></tr></thead>
      <tbody>
        <?php foreach ($cats as $c): ?>
          <tr>
            <td><?= (int)$c['id'] ?></td>
            <td>
              <form method="post" action="/admin/categories/<?= (int)$c['id'] ?>/edit" style="display:flex;gap:8px;align-items:center">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                <input class="input" name="name" value="<?= htmlspecialchars($c['name']) ?>">
                <input class="input" name="slug" value="<?= htmlspecialchars($c['slug']) ?>">
                <button class="btn secondary" type="submit">Salvar</button>
              </form>
            </td>
            <td><?= htmlspecialchars($c['slug']) ?></td>
            <td>
              <form method="post" action="/admin/categories/<?= (int)$c['id'] ?>/delete" onsubmit="return confirm('Remover categoria?')">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                <button class="btn secondary" type="submit">Remover</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$cats): ?><tr><td colspan="4" class="muted">Nenhuma categoria.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
