#!/bin/bash

# Laravel API Kit Setup Script
# This script sets up the Laravel API Kit project for development

set -e  # Exit on any error

echo "🚀 Laravel API Kit - Project Setup"
echo "=================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "\n${BLUE}=== $1 ===${NC}"
}

# Check if we're in the project directory
if [[ ! -f "composer.json" ]]; then
    print_error "Please run this script from the project directory (where composer.json is located)"
    exit 1
fi

# Check dependencies
print_header "Checking System Dependencies"

command -v php >/dev/null 2>&1 || { 
    print_error "PHP is not installed or not in PATH. Please install PHP 8.2+ first."
    exit 1
}

php_version=$(php -r "echo PHP_VERSION;")
print_status "PHP version: $php_version"

if ! php -m | grep -q "pdo_mysql"; then
    print_warning "PDO MySQL extension not found. Database connections may fail."
fi

command -v composer >/dev/null 2>&1 || { 
    print_error "Composer is not installed or not in PATH. Please install Composer first."
    exit 1
}

composer_version=$(composer --version | head -n1)
print_status "Composer version: $composer_version"

command -v docker >/dev/null 2>&1 || { 
    print_error "Docker is not installed or not in PATH. Please install Docker first."
    exit 1
}

docker_version=$(docker --version)
print_status "Docker version: $docker_version"

command -v docker-compose >/dev/null 2>&1 || { 
    print_error "Docker Compose is not installed or not in PATH. Please install Docker Compose first."
    exit 1
}

# Install PHP dependencies
print_header "Installing PHP Dependencies"
print_status "Installing Composer packages..."
composer install --no-interaction --prefer-dist --optimize-autoloader

if [[ $? -ne 0 ]]; then
    print_error "Composer install failed. Please check your network connection and try again."
    exit 1
fi

# Setup environment file
print_header "Environment Configuration"

if [[ ! -f ".env" ]]; then
    print_status "Creating .env file from .env.example..."
    cp .env.example .env
else
    print_warning ".env file already exists. Skipping copy."
fi

# Generate application key
print_status "Generating application key..."
php artisan key:generate --no-interaction

# Create required directories
print_header "Creating Required Directories"

directories=(
    "storage/app/public"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "storage/logs"
    "bootstrap/cache"
    "database/seeders"
    "database/factories" 
    "tests/Feature/Auth"
    "tests/Feature/Project"
    "tests/Feature/Task"
    "tests/Unit/Models"
    "tests/Unit/Services"
)

for dir in "${directories[@]}"; do
    if [[ ! -d "$dir" ]]; then
        print_status "Creating directory: $dir"
        mkdir -p "$dir"
    fi
done

# Set proper permissions
print_header "Setting Permissions"
if [[ "$OSTYPE" != "msys" ]] && [[ "$OSTYPE" != "win32" ]]; then
    print_status "Setting storage and cache permissions..."
    chmod -R 775 storage bootstrap/cache
    
    if command -v chown >/dev/null 2>&1; then
        chown -R $USER:www-data storage bootstrap/cache
    fi
fi

# Start Docker services
print_header "Starting Docker Services"
print_status "Starting Docker containers..."

# Check if Docker daemon is running
if ! docker info >/dev/null 2>&1; then
    print_error "Docker daemon is not running. Please start Docker and try again."
    exit 1
fi

# Build and start containers
docker-compose up -d --build

if [[ $? -ne 0 ]]; then
    print_error "Failed to start Docker containers. Please check Docker configuration."
    exit 1
fi

# Wait for database to be ready
print_status "Waiting for database to be ready..."
sleep 15

# Check database connection
print_status "Testing database connection..."
max_attempts=30
attempt=0

while [[ $attempt -lt $max_attempts ]]; do
    if docker-compose exec -T app php artisan db:show --database=mysql >/dev/null 2>&1; then
        print_status "Database connection successful!"
        break
    else
        attempt=$((attempt + 1))
        if [[ $attempt -eq $max_attempts ]]; then
            print_error "Could not connect to database after $max_attempts attempts."
            print_error "Please check your database configuration."
            exit 1
        fi
        print_status "Waiting for database... (attempt $attempt/$max_attempts)"
        sleep 2
    fi
done

# Run database migrations
print_header "Database Setup"
print_status "Running database migrations..."
docker-compose exec -T app php artisan migrate --force

if [[ $? -ne 0 ]]; then
    print_error "Database migration failed. Please check your database configuration."
    exit 1
fi

# Seed database with demo data
print_status "Seeding database with demo data..."
docker-compose exec -T app php artisan db:seed --force

# Create storage link
print_status "Creating storage symlink..."
docker-compose exec -T app php artisan storage:link

# Clear caches
print_header "Clearing Application Caches"
docker-compose exec -T app php artisan config:clear
docker-compose exec -T app php artisan cache:clear
docker-compose exec -T app php artisan route:clear
docker-compose exec -T app php artisan view:clear

# Generate API documentation
print_header "Generating API Documentation"
print_status "Generating Swagger documentation..."
docker-compose exec -T app php artisan l5-swagger:generate

# Run tests
print_header "Running Test Suite"
print_status "Running application tests..."
docker-compose exec -T app php artisan test

if [[ $? -ne 0 ]]; then
    print_warning "Some tests failed. This is normal for initial setup."
    print_warning "Please review the test output and fix any issues."
fi

# Code quality checks
print_header "Code Quality Checks"
print_status "Running Laravel Pint (code formatting)..."
docker-compose exec -T app ./vendor/bin/pint --test

print_status "Running PHPStan (static analysis)..."
docker-compose exec -T app ./vendor/bin/phpstan analyse --memory-limit=2G

# Final checks
print_header "Final Setup Validation"

# Check if services are running
services_status=$(docker-compose ps --format "table {{.Service}}\t{{.State}}")
print_status "Docker services status:"
echo "$services_status"

# Display access URLs
print_header "🎉 Setup Complete!"

echo -e "\n${GREEN}Your Laravel API Kit is ready!${NC}"
echo -e "\n${BLUE}Access URLs:${NC}"
echo -e "  • API:              ${GREEN}http://localhost:8000${NC}"
echo -e "  • Documentation:    ${GREEN}http://localhost:8000/docs${NC}"
echo -e "  • Mailhog:         ${GREEN}http://localhost:8025${NC}"
echo -e "  • phpMyAdmin:      ${GREEN}http://localhost:8080${NC}"

echo -e "\n${BLUE}Demo Credentials:${NC}"
echo -e "  • Admin: ${GREEN}admin@laravel-api-kit.com${NC} / ${GREEN}password${NC}"
echo -e "  • User:  ${GREEN}user@laravel-api-kit.com${NC} / ${GREEN}password${NC}"

echo -e "\n${BLUE}Useful Commands:${NC}"
echo -e "  • make up           - Start containers"
echo -e "  • make down         - Stop containers"  
echo -e "  • make test         - Run tests"
echo -e "  • make shell        - Access app container"
echo -e "  • make logs         - View app logs"

echo -e "\n${BLUE}Next Steps:${NC}"
echo -e "  1. Visit ${GREEN}http://localhost:8000/docs${NC} for API documentation"
echo -e "  2. Import Postman collection from ${GREEN}docs/postman/${NC}"
echo -e "  3. Review the ${GREEN}README.md${NC} for detailed usage instructions"
echo -e "  4. Check ${GREEN}docs/ARCHITECTURE.md${NC} for project structure"

echo -e "\n${GREEN}Happy coding! 🚀${NC}"
