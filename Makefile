# -----------------------------------------------
# Makefile — Docker shortcuts for rhyme_app
# Usage: make <command>
# -----------------------------------------------

COMPOSE=docker compose
EXEC=$(COMPOSE) exec laravel.test

.PHONY: help up down restart bash migrate fresh seed logs build npm-build

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

up: ## Start all containers in detached mode
	$(COMPOSE) up -d

down: ## Stop and remove containers
	$(COMPOSE) down

restart: ## Restart all containers
	$(COMPOSE) restart

bash: ## Open a shell inside the app container
	$(EXEC) bash

migrate: ## Run database migrations
	$(EXEC) php artisan migrate

fresh: ## Drop all tables and re-run migrations + seeds
	$(EXEC) php artisan migrate:fresh --seed

seed: ## Run database seeders
	$(EXEC) php artisan db:seed

logs: ## Tail app container logs
	$(COMPOSE) logs -f laravel.test

build: ## Build the app Docker image
	$(COMPOSE) build laravel.test

npm-build: ## Install npm deps and build assets inside container
	$(EXEC) bash -c "npm install && npm run build"

composer-install: ## Run composer install inside container
	$(EXEC) composer install

cache-clear: ## Clear all application caches
	$(EXEC) php artisan optimize:clear

queue-work: ## Start the queue worker
	$(EXEC) php artisan queue:work

tinker: ## Open Laravel Tinker REPL
	$(EXEC) php artisan tinker
