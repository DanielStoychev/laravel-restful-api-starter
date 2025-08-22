# Laravel API Kit Development Guide

## Quick Start

### Prerequisites
- PHP 8.2+
- Composer 2.0+
- Docker & Docker Compose
- Git

### Initial Setup

1. **Clone and Setup**
   ```bash
   git clone <repository-url> laravel-api-kit
   cd laravel-api-kit
   chmod +x scripts/setup.sh
   ./scripts/setup.sh
   ```

2. **Access the Application**
   - API: http://localhost:8000
   - Docs: http://localhost:8000/docs
   - Mailhog: http://localhost:8025
   - phpMyAdmin: http://localhost:8080

## Development Workflow

### Daily Development Commands

```bash
# Start development environment
make up

# View logs
make logs

# Run tests
make test

# Access container shell
make shell

# Stop environment
make down
```

### Code Quality

```bash
# Format code
make lint

# Run static analysis
make analyze

# Run all quality checks
make quality
```

### Testing

```bash
# Run all tests
make test

# Run with coverage
make test-coverage

# Run specific test suite
./vendor/bin/pest tests/Feature/AuthTest.php
```

### Database Operations

```bash
# Fresh migration with seeding
make fresh

# Run migrations only
make migrate

# Seed database
make seed
```

## Project Structure

```
laravel-api-kit/
├── project/              # Main Laravel application
├── docs/                 # Documentation
├── scripts/              # Utility scripts
├── docker/               # Docker configuration
├── .github/workflows/    # CI/CD pipelines
└── Makefile             # Development shortcuts
```

## API Development

### Creating New Endpoints

1. **Create Model & Migration**
   ```bash
   php artisan make:model Product -m
   ```

2. **Create Controller**
   ```bash
   php artisan make:controller Api/ProductController --api
   ```

3. **Create Form Requests**
   ```bash
   php artisan make:request StoreProductRequest
   php artisan make:request UpdateProductRequest
   ```

4. **Create API Resource**
   ```bash
   php artisan make:resource ProductResource
   ```

5. **Add Routes**
   ```php
   // routes/api.php
   Route::apiResource('products', ProductController::class);
   ```

### Testing New Features

1. **Create Tests**
   ```bash
   php artisan make:test ProductTest
   ```

2. **Write Feature Tests**
   ```php
   it('can create a product', function () {
       $user = User::factory()->create();
       
       $response = $this->actingAs($user)
           ->postJson('/api/products', [
               'name' => 'Test Product',
               'description' => 'Test Description'
           ]);
           
       $response->assertStatus(201);
   });
   ```

### API Documentation

Update Swagger annotations in controllers:

```php
/**
 * @OA\Get(
 *     path="/api/products",
 *     operationId="getProducts",
 *     tags={"Products"},
 *     summary="Get list of products",
 *     @OA\Response(response=200, description="Success")
 * )
 */
```

Generate documentation:
```bash
php artisan l5-swagger:generate
```

## Deployment

### Production Deployment

1. **Build Production Image**
   ```bash
   docker build -f docker/Dockerfile.prod -t laravel-api-kit:latest project/
   ```

2. **Environment Setup**
   - Copy `.env.example` to `.env.production`
   - Configure production database, cache, and queue settings
   - Set `APP_ENV=production` and `APP_DEBUG=false`

3. **Deploy with Docker Compose**
   ```bash
   docker-compose -f docker-compose.prod.yml up -d
   ```

### CI/CD Pipeline

The GitHub Actions workflow automatically:
- Runs code quality checks
- Executes the full test suite
- Builds Docker images
- Deploys to staging/production (configure as needed)

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R $USER:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

2. **Database Connection Issues**
   ```bash
   # Check database container
   docker-compose logs db
   
   # Reset database
   make fresh
   ```

3. **Queue Not Processing**
   ```bash
   # Check queue worker
   docker-compose logs queue
   
   # Restart queue worker
   docker-compose restart queue
   ```

### Performance Tips

1. **Enable OPcache in production**
2. **Use Redis for caching and sessions**
3. **Optimize Composer autoload**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```
4. **Cache configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Update documentation
4. Use conventional commits
5. Ensure CI pipeline passes

## Architecture Notes

### Service Layer Pattern
- Controllers handle HTTP concerns only
- Services contain business logic
- Models represent data and relationships

### Event-Driven Architecture
- Events trigger side effects
- Listeners handle event processing
- Jobs process background tasks

### API Design Principles
- RESTful resource design
- Consistent response format
- Proper HTTP status codes
- Comprehensive error handling

For detailed architecture information, see [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).
