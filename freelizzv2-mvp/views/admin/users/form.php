<?php
$editing = ($mode ?? '') === 'edit';
$id = $editing ? (int)($user['id'] ?? 0) : 0;
$role = (string)($user['role'] ?? 'client');
$plan = (string)($account['plan_code'] ?? 'free');
?>
<h2><?= $editing ? 'Editar usuário' : 'Criar usuário' ?></h2>

<div class="card">
  <form method="post" action="<?= $editing ? '/admin/users/'.$id.'/edit' : '/admin/users/create' ?>">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

    <label class="muted">Role</label>
    <select class="input" name="role">
      <?php foreach (['admin','client','freelancer'] as $r): ?>
        <option value="<?= $r ?>" <?= $role===$r?'selected':'' ?>><?= $r ?></option>
      <?php endforeach; ?>
    </select>

    <div style="height:8px"></div>
    <label class="muted">Nome</label>
    <input class="input" name="name" value="<?= htmlspecialchars((string)($user['name'] ?? '')) ?>">

    <div style="height:8px"></div>
    <label class="muted">Email</label>
    <input class="input" name="email" type="email" value="<?= htmlspecialchars((string)($user['email'] ?? '')) ?>">

    <div style="height:8px"></div>
    <label class="muted">Status</label>
    <select class="input" name="status">
      <?php foreach (['active','blocked'] as $s): ?>
        <option value="<?= $s ?>" <?= ((string)($user['status'] ?? 'active'))===$s?'selected':'' ?>><?= $s ?></option>
      <?php endforeach; ?>
    </select>

    <div style="height:8px"></div>
    <label class="muted">Plano (apenas freelancer)</label>
    <select class="input" name="plan_code">
      <?php foreach (['free','plus','premium'] as $p): ?>
        <option value="<?= $p ?>" <?= $plan===$p?'selected':'' ?>><?= $p ?></option>
      <?php endforeach; ?>
    </select>

    <div style="height:8px"></div>
    <?php if (!$editing): ?>
      <label class="muted">Senha</label>
      <input class="input" name="password" type="password" placeholder="mínimo 6 caracteres">
    <?php else: ?>
      <label class="muted">Nova senha (opcional)</label>
      <input class="input" name="new_password" type="password" placeholder="deixe em branco para não alterar">
    <?php endif; ?>

    <div style="height:12px"></div>
    <button class="btn" type="submit">Salvar</button>
    <a class="btn secondary" href="/admin/users">Voltar</a>
  </form>
</div>
