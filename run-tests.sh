#!/bin/bash

# Laravel API Kit - Comprehensive Test Suite
# This script runs all tests and generates coverage reports

echo "🚀 Laravel API Kit - Test Suite Execution"
echo "========================================="

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop first."
    exit 1
fi

# Navigate to the project directory
cd "$(dirname "$0")/project"

echo "📋 Running Tests..."

# Run PHPUnit tests with coverage
echo "⚡ Running Unit Tests..."
docker-compose exec app php artisan test tests/Unit --coverage-text

echo "⚡ Running Feature Tests..."
docker-compose exec app php artisan test tests/Feature --coverage-text

echo "⚡ Running Full Test Suite with Coverage..."
docker-compose exec app php artisan test --coverage-html coverage

# Check for test failures
if [ $? -eq 0 ]; then
    echo "✅ All tests passed successfully!"
else
    echo "❌ Some tests failed. Please check the output above."
    exit 1
fi

# Generate PHPStan analysis
echo "📊 Running PHPStan Analysis..."
docker-compose exec app ./vendor/bin/phpstan analyse

# Display coverage summary
echo "📈 Test Coverage Summary:"
echo "========================"
docker-compose exec app php artisan test --coverage-text

echo "🎯 Testing Complete!"
echo "View detailed HTML coverage report: project/coverage/index.html"
