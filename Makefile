# Start container
up:
docker compose up --build

# Start utan rebuild
start:
docker compose up

# Stoppa containrar
stop:
docker compose down

# Shell i containern
shell:
docker compose exec slim-service sh

# Loggar
logs:
docker compose logs -f

# Kör tester
 test:
docker compose exec slim-service vendor/bin/phpunit

# Kör PHPStan
stan:
docker compose exec slim-service vendor/bin/phpstan analyse src --level=max

# Kör migreringar
migrate:
docker compose exec slim-service vendor/bin/phinx migrate

# Kör kodformattering
fix:
docker compose exec slim-service vendor/bin/php-cs-fixer fix
