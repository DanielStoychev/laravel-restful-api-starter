# Laravel Restful API Starter

> **Production-ready Laravel 11 REST API template with 100% test coverage, comprehensive authentication, role-based authorization, and enterprise-level architecture.**

[![Tests](https://img.shields.io/badge/tests-95%20passing-brightgreen)](https://github.com/DanielStoychev/laravel-restful-api-starter)
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/DanielStoychev/laravel-restful-api-starter)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)

## 🎯 **Key Features**

### **🏆 Production-Ready Architecture**
- ✅ **100% Test Coverage** (95/95 tests passing)
- ✅ **Enterprise Security** with Sanctum authentication
- ✅ **Policy-Based Authorization** with granular permissions  
- ✅ **Form Request Validation** with custom error messages
- ✅ **Rate Limiting** protection against abuse
- ✅ **Comprehensive Caching** for optimal performance

### **🔐 Authentication & Security**
- JWT token authentication with Laravel Sanctum
- Password reset functionality with email notifications
- Role-based user management (Admin, Manager, User)
- CSRF protection and input validation
- Rate limiting on all API endpoints

### **📊 Project & Task Management**
- Complete CRUD operations for Projects and Tasks
- User ownership isolation and security
- Advanced filtering and pagination
- Real-time task status tracking
- Due date management with overdue detection

### **🧪 Testing Excellence**
- **Unit Tests**: Models, relationships, validation
- **Feature Tests**: API endpoints, authentication flows
- **Integration Tests**: Complete user workflows
- **Database Testing**: Factories, seeders, migrations

## 🛠️ **Technical Stack**

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Backend** | Laravel 11.x | PHP framework |
| **Authentication** | Laravel Sanctum | API token management |
| **Database** | MySQL 8.0 | Primary database |
| **Caching** | Redis/Database | Performance optimization |
| **Testing** | PHPUnit | Automated testing |
| **Documentation** | OpenAPI/Swagger | API documentation |
| **Containerization** | Docker | Development environment |
| **CI/CD** | GitHub Actions | Automated testing & deployment |

## 🚀 **Quick Start**

### **Prerequisites**
- Docker & Docker Compose
- Git

### **Installation**

```bash
# Clone the repository
git clone https://github.com/DanielStoychev/laravel-restful-api-starter.git
cd laravel-restful-api-starter

# Start Docker environment
docker-compose up -d

# Install dependencies
docker exec laravel_api_kit_app composer install

# Set up environment
cp project/.env.example project/.env
docker exec laravel_api_kit_app php artisan key:generate

# Run migrations and seeders
docker exec laravel_api_kit_app php artisan migrate:fresh --seed

# Run tests to verify installation
docker exec laravel_api_kit_app php artisan test
```

### **API Access**
- **Base URL**: `http://localhost:8000/api`
- **Documentation**: `http://localhost:8000/api/documentation`
- **Health Check**: `GET /api/health`

## 📚 **API Endpoints**

### **Authentication**
```http
POST   /api/auth/register           # User registration
POST   /api/auth/login              # User login
POST   /api/auth/logout             # User logout
POST   /api/auth/forgot-password    # Password reset request
POST   /api/auth/reset-password     # Password reset confirmation
GET    /api/user                    # Get authenticated user
```

### **Projects** (Authenticated)
```http
GET    /api/projects                # List user projects
POST   /api/projects                # Create project
GET    /api/projects/{id}           # Get project details
PUT    /api/projects/{id}           # Update project
DELETE /api/projects/{id}           # Delete project
```

### **Tasks** (Authenticated)
```http
GET    /api/tasks                   # List user tasks
POST   /api/tasks                   # Create task
GET    /api/tasks/{id}              # Get task details
PUT    /api/tasks/{id}              # Update task
DELETE /api/tasks/{id}              # Delete task
GET    /api/projects/{id}/tasks     # Get project tasks
```

## 🧪 **Testing**

### **Run All Tests**
```bash
docker exec laravel_api_kit_app php artisan test
```

### **Test Coverage**
- **Unit Tests**: 49 tests covering models and business logic
- **Feature Tests**: 46 tests covering API endpoints and user workflows
- **Total**: 95 tests with 495 assertions - **100% passing**

### **Test Categories**
- **Model Tests**: Relationships, validation, factories
- **API Tests**: CRUD operations, authentication flows
- **Security Tests**: Authorization, rate limiting, validation

## 🏗️ **Architecture Highlights**

### **Design Patterns**
- **Repository Pattern**: Clean data access abstraction
- **Policy Pattern**: Authorization logic separation
- **Request/Response Pattern**: Structured API communication
- **Factory Pattern**: Consistent test data generation

### **Security Features**
- **Input Validation**: Form Request classes with custom rules
- **Authorization Policies**: Granular permission control
- **Rate Limiting**: Protection against API abuse
- **Data Sanitization**: XSS and injection prevention

### **Performance Optimization**
- **Database Caching**: Query result optimization
- **Eager Loading**: N+1 query prevention
- **Response Caching**: API response optimization
- **Database Indexing**: Optimized query performance

## 📖 **Code Quality**

### **Standards Followed**
- **PSR-12**: PHP coding standard compliance
- **SOLID Principles**: Clean architecture design
- **DRY Principle**: Code reusability and maintainability
- **Laravel Best Practices**: Framework-specific optimizations

### **Development Tools**
- **PHP CS Fixer**: Code style consistency
- **PHPStan**: Static analysis for bug prevention  
- **Laravel Pint**: Code formatting automation
- **Swagger/OpenAPI**: Comprehensive API documentation

## 🚢 **Deployment**

### **Production Environment**
```bash
# Build production Docker image
docker build -f docker/Dockerfile.prod -t laravel-api-prod .

# Deploy with environment variables
docker run -d --name api-production \
  -e APP_ENV=production \
  -e DB_HOST=your-db-host \
  -e DB_PASSWORD=your-secure-password \
  laravel-api-prod
```

### **Environment Configuration**
- **Development**: Docker Compose with hot reload
- **Testing**: SQLite in-memory database
- **Production**: Optimized container with production settings

## 🤝 **Contributing**

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 **License**

This project is open-sourced under the [MIT license](LICENSE).

## 👨‍💻 **Developer**

**Daniel Stoychev**
- **GitHub**: [@DanielStoychev](https://github.com/DanielStoychev)
- **LinkedIn**: [Daniel Stoychev](https://linkedin.com/in/daniel-stoychev)

---

## 🌟 **Why This Template?**

This Laravel API template demonstrates **senior-level PHP/Laravel expertise** through:

- **Enterprise Architecture**: Scalable, maintainable code structure
- **Security-First Approach**: Comprehensive protection against common vulnerabilities  
- **Test-Driven Development**: Bulletproof reliability with extensive test coverage
- **Production-Ready**: Optimized for real-world deployment scenarios
- **Documentation Excellence**: Clear, comprehensive project documentation
- **Industry Standards**: Following Laravel and PHP best practices

Perfect for **job interviews**, **portfolio projects**, or as a **starting point** for production applications.

---

*Built with ❤️ using Laravel 11, showcasing modern PHP development practices and enterprise-level application architecture.*
