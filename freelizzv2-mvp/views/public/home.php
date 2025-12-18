<div class='grid'>
  <section class='card col-8'>
    <h1>FreelizzV2 (MVP)</h1>
    <p class='muted'>Base funcional: Docker + Apache/PHP 8 + MySQL, rotas públicas e login.</p>
    <p><a class='btn' href='/jobs'>Ver Jobs</a></p>
  </section>
  <aside class='card col-4'>
    <h3>Setup</h3>
    <ol class='muted'>
      <li>docker compose up -d --build</li>
      <li>docker exec -it freelizz_web php /var/www/html/scripts/migrate.php</li>
      <li>http://localhost:8787</li>
    </ol>
  </aside>
</div>
