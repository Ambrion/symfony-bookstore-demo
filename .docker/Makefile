export PUID ?= $(shell id -u)
export PGID ?= $(shell id -g)

build:
	if [ ! -f .env ]; then cp .env.dist .env; fi
	docker compose build --build-arg PUID=$(PUID) --build-arg PGID=$(PGID)
install:
	docker compose exec --user dev php composer install
start:
	docker compose up -d
stop:
	docker compose down
enter:
	docker compose exec --user dev php sh
migrate:
	docker compose exec --user dev php bin/console doctrine:migrations:migrate --no-interaction
import:
	docker compose exec --user dev php bin/console app:import-book-from-url
worker-start:
	docker compose up -d php-worker-contact-form
worker-stop:
	docker compose stop php-worker-contact-form