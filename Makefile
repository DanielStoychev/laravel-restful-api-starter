# Laravel API Kit - Development Makefile
# Provides convenient shortcuts for common development tasks

.PHONY: help setup up down build shell test test-coverage seed migrate fresh logs cache-clear optimize quality lint analyze

# Default target
help: ## Show this help message
	@echo "Laravel API Kit - Available Commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Project Setup Commands
setup: ## Initial project setup (first time only)
	@echo "🚀 Setting up Laravel API Kit..."
	@if [ ! -f project/.env ]; then cp project/.env.example project/.env; fi
	docker-compose up -d
	@echo "⏳ Waiting for services to start..."
	@sleep 10
	docker-compose exec -T app composer install --no-interaction
	docker-compose exec -T app php artisan key:generate
	docker-compose exec -T app php artisan migrate --force
	docker-compose exec -T app php artisan db:seed --force
	docker-compose exec -T app php artisan storage:link
	@echo "✅ Setup complete! Visit http://localhost:8000"

up: ## Start development containers
	docker-compose up -d

down: ## Stop development containers
	docker-compose down

build: ## Build containers from scratch
	docker-compose build --no-cache

restart: ## Restart all containers
	docker-compose restart

# Development Commands
shell: ## Access the application container shell
	docker-compose exec app bash

shell-db: ## Access the database container shell
	docker-compose exec db bash

logs: ## View application logs
	docker-compose logs -f app

logs-all: ## View all container logs
	docker-compose logs -f

# Database Commands
migrate: ## Run database migrations
	docker-compose exec -T app php artisan migrate

migrate-fresh: ## Fresh migration (destroys data)
	docker-compose exec -T app php artisan migrate:fresh --force

seed: ## Seed database with demo data
	docker-compose exec -T app php artisan db:seed --force

fresh: ## Fresh migrate with seeding
	docker-compose exec -T app php artisan migrate:fresh --seed --force

# Testing Commands
test: ## Run the test suite
	docker-compose exec -T app php artisan test

test-coverage: ## Run tests with coverage report
	docker-compose exec -T app php artisan test --coverage --coverage-html=coverage

test-unit: ## Run unit tests only
	docker-compose exec -T app php artisan test --testsuite=Unit

test-feature: ## Run feature tests only
	docker-compose exec -T app php artisan test --testsuite=Feature

pest: ## Run Pest tests
	docker-compose exec -T app ./vendor/bin/pest

pest-coverage: ## Run Pest tests with coverage
	docker-compose exec -T app ./vendor/bin/pest --coverage

# Code Quality Commands
quality: lint analyze ## Run all code quality checks

lint: ## Format code with Laravel Pint
	docker-compose exec -T app ./vendor/bin/pint

lint-check: ## Check code formatting without fixing
	docker-compose exec -T app ./vendor/bin/pint --test

analyze: ## Run static analysis with PHPStan
	docker-compose exec -T app ./vendor/bin/phpstan analyse

# Cache Commands
cache-clear: ## Clear all caches
	docker-compose exec -T app php artisan cache:clear
	docker-compose exec -T app php artisan config:clear
	docker-compose exec -T app php artisan route:clear
	docker-compose exec -T app php artisan view:clear

optimize: ## Optimize for production
	docker-compose exec -T app php artisan config:cache
	docker-compose exec -T app php artisan route:cache
	docker-compose exec -T app php artisan view:cache
	docker-compose exec -T app php artisan event:cache

optimize-clear: ## Clear optimization caches
	docker-compose exec -T app php artisan optimize:clear

# Development Utilities
tinker: ## Access Laravel Tinker
	docker-compose exec app php artisan tinker

queue-work: ## Start queue worker
	docker-compose exec app php artisan queue:work

queue-listen: ## Start queue listener
	docker-compose exec app php artisan queue:listen

route-list: ## Show all routes
	docker-compose exec -T app php artisan route:list

# Documentation Commands
docs-generate: ## Generate API documentation
	docker-compose exec -T app php artisan l5-swagger:generate

# Monitoring Commands
status: ## Show container status
	docker-compose ps

stats: ## Show container resource usage
	docker stats

# Cleanup Commands
clean: ## Remove containers and volumes
	docker-compose down -v
	docker-compose rm -f

clean-all: ## Remove containers, volumes, and images
	docker-compose down -v --rmi all
	docker-compose rm -f

# Production Commands
prod-build: ## Build production Docker image
	docker build -f docker/Dockerfile.prod -t laravel-api-kit:latest project/

prod-deploy: ## Deploy to production (requires configuration)
	@echo "🚀 Deploying to production..."
	@echo "⚠️  Make sure to configure your production environment first!"

# Backup Commands
backup-db: ## Backup database
	@echo "📦 Creating database backup..."
	docker-compose exec -T db mysqldump -u root -ppassword laravel_api_kit > backups/db_backup_$$(date +%Y%m%d_%H%M%S).sql
	@echo "✅ Database backup created in backups/"

restore-db: ## Restore database from backup (requires backup file)
	@read -p "Enter backup filename (from backups/): " backup; \
	docker-compose exec -i db mysql -u root -ppassword laravel_api_kit < backups/$$backup

# Development Environment Info
info: ## Show development environment information
	@echo "🔍 Laravel API Kit - Environment Information"
	@echo "=============================================="
	@echo "Application URL: http://localhost:8000"
	@echo "API Documentation: http://localhost:8000/docs"
	@echo "Mailhog: http://localhost:8025"
	@echo "phpMyAdmin: http://localhost:8080"
	@echo ""
	@echo "Container Status:"
	@docker-compose ps
	@echo ""
	@echo "Laravel Version:"
	@docker-compose exec -T app php artisan --version 2>/dev/null || echo "App container not running"

# First-time developer setup
dev-setup: ## Complete development environment setup with demo data
	@echo "👨‍💻 Setting up development environment..."
	@make setup
	@echo "🎯 Installing additional development dependencies..."
	docker-compose exec -T app composer require --dev --no-interaction
	@echo "📚 Generating API documentation..."
	docker-compose exec -T app php artisan l5-swagger:generate
	@echo "🧪 Running initial test suite..."
	docker-compose exec -T app php artisan test
	@echo ""
	@echo "🎉 Development environment ready!"
	@make info
