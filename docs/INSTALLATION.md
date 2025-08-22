# 🚀 Laravel API Kit - Installation Guide

This guide will walk you through setting up Laravel API Kit on your local development environment.

## Prerequisites

Before you begin, ensure you have the following installed:

### Required Software

| Software | Minimum Version | Download Link | Notes |
|----------|----------------|---------------|-------|
| **PHP** | 8.2 | [Download](https://www.php.net/downloads.php) | Required for Laravel 11 |
| **Composer** | 2.0 | [Download](https://getcomposer.org/download/) | PHP dependency manager |
| **Docker** | 20.10 | [Download](https://www.docker.com/get-started) | Container runtime |
| **Docker Compose** | 2.0 | [Download](https://docs.docker.com/compose/install/) | Multi-container Docker apps |
| **Git** | 2.30 | [Download](https://git-scm.com/downloads) | Version control |

### PHP Extensions

Ensure the following PHP extensions are installed:

```bash
# Check installed extensions
php -m

# Required extensions:
- pdo_mysql
- mbstring
- xml
- ctype
- json
- tokenizer
- openssl
- zip
- fileinfo
- bcmath
```

### System Requirements

- **Memory**: Minimum 4GB RAM (8GB recommended)
- **Storage**: 2GB free space
- **OS**: Linux, macOS, or Windows with WSL2

## Installation Methods

Choose your preferred installation method:

### Method 1: Automated Setup (Recommended)

The fastest way to get up and running:

```bash
# 1. Clone the repository
git clone https://github.com/your-username/laravel-api-kit.git
cd laravel-api-kit

# 2. Make setup script executable (Linux/macOS)
chmod +x scripts/setup.sh

# 3. Run the automated setup
./scripts/setup.sh
```

**Windows Users**: Use Git Bash or WSL2 to run the setup script.

### Method 2: Manual Setup

For more control over the installation process:

#### Step 1: Clone Repository
```bash
git clone https://github.com/your-username/laravel-api-kit.git
cd laravel-api-kit
```

#### Step 2: Navigate to Project Directory
```bash
cd project
```

#### Step 3: Install PHP Dependencies
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

#### Step 4: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Step 5: Start Docker Services
```bash
# From the root directory (laravel-api-kit/)
cd ..
docker-compose up -d --build
```

#### Step 6: Database Setup
```bash
# Wait for database to start (about 30 seconds)
sleep 30

# Run migrations
docker-compose exec app php artisan migrate --force

# Seed demo data
docker-compose exec app php artisan db:seed --force
```

#### Step 7: Storage Setup
```bash
docker-compose exec app php artisan storage:link
```

#### Step 8: Generate Documentation
```bash
docker-compose exec app php artisan l5-swagger:generate
```

## Verification

After installation, verify everything is working:

### 1. Check Service Status
```bash
docker-compose ps
```

All services should show "Up" status:
- `laravel_api_kit_app`
- `laravel_api_kit_db`
- `laravel_api_kit_redis`
- `laravel_api_kit_mailhog`
- `laravel_api_kit_phpmyadmin`

### 2. Test API Endpoints

#### Health Check
```bash
curl http://localhost:8000/api/health
```

Expected response:
```json
{
    "success": true,
    "data": {
        "status": "healthy",
        "database": "connected",
        "cache": "connected"
    }
}
```

#### Test Authentication
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@laravel-api-kit.com",
    "password": "password"
  }'
```

### 3. Access Web Interfaces

| Service | URL | Credentials |
|---------|-----|-------------|
| **API Documentation** | http://localhost:8000/docs | N/A |
| **Mailhog** | http://localhost:8025 | N/A |
| **phpMyAdmin** | http://localhost:8080 | root / password |

### 4. Run Tests
```bash
# From project directory
docker-compose exec app php artisan test
```

All tests should pass ✅

## Demo Data

The installation creates demo users for testing:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@laravel-api-kit.com | password |
| **User** | user@laravel-api-kit.com | password |

## Troubleshooting

### Common Issues and Solutions

#### Port Conflicts
If ports 8000, 3306, 6379, 8025, or 8080 are in use:

1. **Check what's using the port**:
   ```bash
   # Linux/macOS
   lsof -i :8000
   
   # Windows
   netstat -ano | findstr :8000
   ```

2. **Stop conflicting services** or **modify port mapping** in `docker-compose.yml`:
   ```yaml
   ports:
     - "8001:80"  # Change from 8000:80
   ```

#### Database Connection Failed

1. **Check database container logs**:
   ```bash
   docker-compose logs db
   ```

2. **Verify database is ready**:
   ```bash
   docker-compose exec db mysql -u root -ppassword -e "SHOW DATABASES;"
   ```

3. **Reset database**:
   ```bash
   docker-compose down -v
   docker-compose up -d
   # Wait 30 seconds, then run migrations again
   ```

#### Permission Issues (Linux/macOS)

```bash
# Fix storage permissions
sudo chown -R $USER:www-data project/storage project/bootstrap/cache
chmod -R 775 project/storage project/bootstrap/cache
```

#### Composer Memory Issues

```bash
# Increase PHP memory limit
php -d memory_limit=2G /usr/local/bin/composer install
```

#### Docker Issues

1. **Docker daemon not running**:
   ```bash
   # Start Docker service
   sudo systemctl start docker  # Linux
   # Or start Docker Desktop application
   ```

2. **Insufficient disk space**:
   ```bash
   # Clean up Docker
   docker system prune -a
   ```

3. **Network conflicts**:
   ```bash
   # Reset Docker networks
   docker network prune
   ```

## Environment Variables

Key environment variables you may need to modify:

```bash
# Application
APP_NAME="Laravel API Kit"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_api_kit
DB_USERNAME=root
DB_PASSWORD=password

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
REDIS_PASSWORD=redispassword

# Mail Testing
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

## Development Tools

### Useful Make Commands

The project includes a Makefile with shortcuts:

```bash
make up          # Start containers
make down        # Stop containers
make shell       # Access app container
make test        # Run tests
make logs        # View logs
make fresh       # Reset database
make quality     # Code quality checks
```

### IDE Configuration

#### VS Code
Recommended extensions:
- PHP Intelephense
- Laravel Extension Pack
- Docker
- GitLens

#### PHPStorm
- Enable Laravel plugin
- Configure PHP interpreter to use Docker
- Set up database connection to localhost:3306

## Next Steps

1. 📚 **Read Documentation**: Check out [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)
2. 🚀 **Start Developing**: See [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md)
3. 🧪 **API Testing**: Import Postman collection from `docs/postman/`
4. 📖 **API Documentation**: Visit http://localhost:8000/docs

## Getting Help

If you encounter issues:

1. 📖 Check this installation guide
2. 🐛 Search existing GitHub issues
3. 💬 Create a new GitHub issue with:
   - Your OS and versions
   - Complete error messages
   - Steps to reproduce

## Performance Optimization

For development performance improvements:

### Docker Performance (macOS/Windows)

1. **Allocate more resources** to Docker Desktop:
   - Memory: 4GB minimum, 8GB recommended
   - CPUs: 4 cores recommended

2. **Use cached volumes** in `docker-compose.yml`:
   ```yaml
   volumes:
     - ./project:/var/www/html:cached
   ```

### PHP Performance

1. **Enable OPcache** in development:
   ```ini
   # In docker/php/local.ini
   opcache.enable=1
   opcache.revalidate_freq=1
   ```

Congratulations! 🎉 Your Laravel API Kit is now ready for development.
