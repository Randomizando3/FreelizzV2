# FreelizzV2 MVP (base)
docker compose up -d --build
docker exec -it freelizz_web php /var/www/html/scripts/migrate.php
http://localhost:8787
