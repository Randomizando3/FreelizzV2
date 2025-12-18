<div class="card">
  <h1>Criar conta</h1>
  <p class="muted">Escolha Cliente ou Freelancer.</p>

  <form method="post" action="/register">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

    <label class="muted">Tipo</label>
    <select class="input" name="role">
      <option value="client">Cliente</option>
      <option value="freelancer">Freelancer</option>
    </select>

    <div style="height:8px"></div>
    <label class="muted">Nome</label>
    <input class="input" name="name" required>

    <div style="height:8px"></div>
    <label class="muted">E-mail</label>
    <input class="input" type="email" name="email" required>

    <div style="height:8px"></div>
    <label class="muted">Senha</label>
    <input class="input" type="password" name="password" required>

    <div style="height:12px"></div>
    <button class="btn" type="submit">Criar</button>
    <a class="btn secondary" href="/login">Já tenho conta</a>
  </form>
</div>
