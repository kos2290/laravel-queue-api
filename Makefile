build-up:
	docker compose -f 'docker-compose.yml' up -d --build

up:
	docker compose up -d

down:
	docker compose down

cli:
	docker compose exec php-cli bash

npm-build:
	docker compose exec node npm run build

npm-dev:
	docker compose exec node npm run dev
