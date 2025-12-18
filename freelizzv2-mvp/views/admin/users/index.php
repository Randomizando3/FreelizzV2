<h2>Usuários</h2>

<div class="card">
  <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
    <input class="input" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="buscar nome/email" style="max-width:260px">
    <select class="input" name="role" style="max-width:180px">
      <option value="">Role (todas)</option>
      <?php foreach (['admin','client','freelancer'] as $r): ?>
        <option value="<?= $r ?>" <?= $role===$r?'selected':'' ?>><?= $r ?></option>
      <?php endforeach; ?>
    </select>
    <select class="input" name="status" style="max-width:180px">
      <option value="">Status (todos)</option>
      <?php foreach (['active','blocked'] as $s): ?>
        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filtrar</button>
    <a class="btn secondary" href="/admin/users/create">Criar usuário</a>
  </form>
</div>

<div style="height:12px"></div>

<div class="card">
  <table class="table">
    <thead>
      <tr>
        <th>ID</th><th>Role</th><th>Nome</th><th>Email</th><th>Status</th><th>Plano</th><th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['role']) ?></td>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td>
            <?php if ($u['status']==='active'): ?>
              <span class="badge ok">active</span>
            <?php else: ?>
              <span class="badge err">blocked</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars((string)($plans[(int)$u['id']] ?? '—')) ?></td>
          <td style="white-space:nowrap">
            <a class="btn secondary" href="/admin/users/<?= (int)$u['id'] ?>/edit">Editar</a>

            <?php if ($u['status']==='active'): ?>
              <form action="/admin/users/<?= (int)$u['id'] ?>/block" method="post" style="display:inline">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                <button class="btn secondary" type="submit">Bloquear</button>
              </form>
            <?php else: ?>
              <form action="/admin/users/<?= (int)$u['id'] ?>/unblock" method="post" style="display:inline">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                <button class="btn secondary" type="submit">Desbloquear</button>
              </form>
            <?php endif; ?>

            <form action="/admin/users/<?= (int)$u['id'] ?>/impersonate" method="post" style="display:inline">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
              <button class="btn secondary" type="submit">Impersonar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$users): ?>
        <tr><td colspan="7" class="muted">Nenhum resultado.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
