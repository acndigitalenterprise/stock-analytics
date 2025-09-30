# TickerAI Docker Development Environment
.PHONY: help build up down logs shell composer artisan test clean

# Default target
help: ## Show this help message
	@echo "TickerAI Docker Development Environment"
	@echo "======================================"
	@echo ""
	@echo "Available commands:"
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_-]+:.*##/ { printf "  %-20s %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

build: ## Build Docker images
	docker-compose build --no-cache

up: ## Start all services
	docker-compose up -d

down: ## Stop all services
	docker-compose down

logs: ## Show application logs
	docker-compose logs -f app

logs-all: ## Show all services logs
	docker-compose logs -f

shell: ## Access application shell
	docker-compose exec app bash

mysql: ## Access MySQL shell
	docker-compose exec mysql mysql -u root -psecret tickerai

redis: ## Access Redis CLI
	docker-compose exec redis redis-cli

composer: ## Run composer install
	docker-compose exec app composer install

artisan: ## Run artisan command (usage: make artisan cmd="migrate")
	docker-compose exec app php artisan $(cmd)

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

fresh: ## Fresh migrate with seed
	docker-compose exec app php artisan migrate:fresh --seed

key: ## Generate application key
	docker-compose exec app php artisan key:generate

cache: ## Clear all caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

queue: ## Monitor queue
	docker-compose exec app php artisan queue:work --verbose

test: ## Run tests
	docker-compose exec app php artisan test

permissions: ## Fix storage permissions
	docker-compose exec app chmod -R 777 storage bootstrap/cache

status: ## Show container status
	docker-compose ps

restart: ## Restart all services
	docker-compose restart

clean: ## Clean up everything
	docker-compose down -v
	docker system prune -f
	docker volume prune -f

setup: ## Complete setup (build, up, install, migrate)
	make build
	make up
	sleep 10
	make composer
	make key
	make migrate
	make permissions
	@echo "Setup complete! Access: http://localhost:8080"

install: ## Fresh installation
	make clean
	make setup

# Development helpers
npm: ## Install npm dependencies
	docker-compose exec app npm install

npm-dev: ## Run npm dev
	docker-compose exec app npm run dev

npm-build: ## Build assets for production
	docker-compose exec app npm run build