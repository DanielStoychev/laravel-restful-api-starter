# 🚀 Laravel API Kit - Deployment Guide

This guide covers deploying Laravel API Kit to various environments, from development to production.

## 📋 Table of Contents

- [Environment Overview](#-environment-overview)
- [Development Deployment](#-development-deployment)
- [Staging Deployment](#-staging-deployment)
- [Production Deployment](#-production-deployment)
- [Cloud Platforms](#-cloud-platforms)
- [Monitoring & Maintenance](#-monitoring--maintenance)

## 🌍 Environment Overview

### Environment Types

| Environment | Purpose | Features | Access |
|-------------|---------|----------|---------|
| **Development** | Local development | Hot reload, debug tools, test data | Localhost only |
| **Staging** | Pre-production testing | Production-like config, CI/CD testing | Internal team |
| **Production** | Live application | Optimized performance, monitoring | Public users |

### Configuration Matrix

| Setting | Development | Staging | Production |
|---------|-------------|---------|------------|
| `APP_ENV` | `local` | `staging` | `production` |
| `APP_DEBUG` | `true` | `false` | `false` |
| `LOG_LEVEL` | `debug` | `info` | `warning` |
| SSL/TLS | Optional | Required | Required |
| Caching | File-based | Redis | Redis |
| Queue Driver | `sync` | `redis` | `redis` |

## 🔧 Development Deployment

### Local Docker Setup

1. **Clone and Setup**
   ```bash
   git clone <repository-url> laravel-api-kit
   cd laravel-api-kit
   make setup
   ```

2. **Environment Configuration**
   ```bash
   # Copy environment file
   cp project/.env.example project/.env
   
   # Generate application key
   make key-generate
   
   # Run migrations and seeders
   make fresh-db
   ```

3. **Start Services**
   ```bash
   # Start all services
   make up
   
   # View logs
   make logs
   
   # Access application
   # API: http://localhost:8080
   # Swagger: http://localhost:8080/api/documentation
   # phpMyAdmin: http://localhost:8081
   ```

### Development URLs

- **API Base**: `http://localhost:8080/api`
- **Swagger UI**: `http://localhost:8080/api/documentation`
- **phpMyAdmin**: `http://localhost:8081`
- **Mailhog**: `http://localhost:8025`

## 🧪 Staging Deployment

### Prerequisites

- **Server Requirements**:
  - Ubuntu 20.04+ or CentOS 8+
  - Docker Engine 20.10+
  - Docker Compose 2.0+
  - 2GB+ RAM, 20GB+ Storage
  - SSL Certificate

### Staging Setup

1. **Server Preparation**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install Docker
   curl -fsSL https://get.docker.com -o get-docker.sh
   sh get-docker.sh
   sudo usermod -aG docker $USER
   
   # Install Docker Compose
   sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
   sudo chmod +x /usr/local/bin/docker-compose
   ```

2. **Application Deployment**
   ```bash
   # Clone repository
   git clone <repository-url> /var/www/laravel-api-kit
   cd /var/www/laravel-api-kit
   
   # Configure environment
   cp project/.env.example project/.env.staging
   nano project/.env.staging
   ```

3. **Environment Configuration (`.env.staging`)**
   ```bash
   APP_NAME="Laravel API Kit (Staging)"
   APP_ENV=staging
   APP_DEBUG=false
   APP_URL=https://staging.yourapp.com
   
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=laravel_staging
   DB_USERNAME=laravel
   DB_PASSWORD=secure_staging_password
   
   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   SESSION_DRIVER=redis
   
   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=staging@yourapp.com
   MAIL_PASSWORD=smtp_password
   MAIL_ENCRYPTION=tls
   ```

4. **SSL Configuration**
   ```bash
   # Create SSL directory
   sudo mkdir -p /etc/ssl/certs/yourapp
   
   # Copy SSL certificates
   sudo cp your-ssl-cert.pem /etc/ssl/certs/yourapp/
   sudo cp your-ssl-key.pem /etc/ssl/certs/yourapp/
   ```

5. **Deploy Application**
   ```bash
   # Build and start services
   docker-compose -f docker-compose.staging.yml up -d --build
   
   # Run initial setup
   docker-compose -f docker-compose.staging.yml exec app php artisan key:generate
   docker-compose -f docker-compose.staging.yml exec app php artisan migrate --seed
   docker-compose -f docker-compose.staging.yml exec app php artisan storage:link
   docker-compose -f docker-compose.staging.yml exec app php artisan config:cache
   docker-compose -f docker-compose.staging.yml exec app php artisan route:cache
   docker-compose -f docker-compose.staging.yml exec app php artisan view:cache
   ```

## 🏭 Production Deployment

### Infrastructure Requirements

- **Minimum Server Specs**:
  - 4 vCPUs, 8GB RAM
  - 100GB SSD storage
  - Load balancer (if scaling)
  - Database server (separate)
  - Redis cluster

### Production Architecture

```
Internet → Load Balancer → Web Servers → Application Servers
                              ↓
                         Database Cluster
                              ↓
                         Redis Cluster
```

### Step-by-Step Production Setup

#### 1. Database Server Setup

```bash
# Install MySQL 8.0
sudo apt install mysql-server-8.0
sudo mysql_secure_installation

# Create production database
sudo mysql -u root -p
CREATE DATABASE laravel_production;
CREATE USER 'laravel'@'%' IDENTIFIED BY 'ultra_secure_production_password';
GRANT ALL PRIVILEGES ON laravel_production.* TO 'laravel'@'%';
FLUSH PRIVILEGES;
```

#### 2. Redis Server Setup

```bash
# Install Redis
sudo apt install redis-server
sudo systemctl enable redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
# Set: requirepass your_redis_password
# Set: maxmemory 2gb
# Set: maxmemory-policy allkeys-lru

sudo systemctl restart redis-server
```

#### 3. Application Server Setup

```bash
# Clone and configure
git clone <repository-url> /var/www/laravel-api-kit
cd /var/www/laravel-api-kit

# Production environment
cp project/.env.example project/.env.production
```

#### 4. Production Environment (`.env.production`)

```bash
APP_NAME="Laravel API Kit"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourapp.com

DB_CONNECTION=mysql
DB_HOST=your-db-server-ip
DB_PORT=3306
DB_DATABASE=laravel_production
DB_USERNAME=laravel
DB_PASSWORD=ultra_secure_production_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=your-redis-server-ip
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379

# Email configuration
MAIL_MAILER=smtp
MAIL_HOST=your-production-smtp
MAIL_PORT=587
MAIL_USERNAME=noreply@yourapp.com
MAIL_PASSWORD=production_email_password
MAIL_ENCRYPTION=tls

# Security
SANCTUM_STATEFUL_DOMAINS=yourapp.com,api.yourapp.com
SESSION_DOMAIN=.yourapp.com

# Monitoring
LOG_CHANNEL=daily
LOG_LEVEL=warning
```

#### 5. Deploy Production

```bash
# Build production containers
docker-compose -f docker-compose.prod.yml up -d --build

# Initial deployment
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec app php artisan storage:link

# Optimize for production
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache
docker-compose -f docker-compose.prod.yml exec app php artisan event:cache

# Start queue workers
docker-compose -f docker-compose.prod.yml exec app php artisan queue:restart
```

## ☁️ Cloud Platforms

### AWS Deployment

#### Using ECS (Elastic Container Service)

1. **Build and Push Images**
   ```bash
   # Build production image
   docker build -f docker/Dockerfile.prod -t laravel-api-kit:latest .
   
   # Tag for ECR
   docker tag laravel-api-kit:latest 123456789.dkr.ecr.us-east-1.amazonaws.com/laravel-api-kit:latest
   
   # Push to ECR
   docker push 123456789.dkr.ecr.us-east-1.amazonaws.com/laravel-api-kit:latest
   ```

2. **ECS Task Definition**
   ```json
   {
     "family": "laravel-api-kit",
     "networkMode": "awsvpc",
     "requiresCompatibilities": ["FARGATE"],
     "cpu": "1024",
     "memory": "2048",
     "executionRoleArn": "arn:aws:iam::123456789:role/ecsTaskExecutionRole",
     "containerDefinitions": [
       {
         "name": "laravel-app",
         "image": "123456789.dkr.ecr.us-east-1.amazonaws.com/laravel-api-kit:latest",
         "portMappings": [
           {
             "containerPort": 80,
             "protocol": "tcp"
           }
         ],
         "environment": [
           {"name": "APP_ENV", "value": "production"}
         ],
         "logConfiguration": {
           "logDriver": "awslogs",
           "options": {
             "awslogs-group": "/ecs/laravel-api-kit",
             "awslogs-region": "us-east-1",
             "awslogs-stream-prefix": "ecs"
           }
         }
       }
     ]
   }
   ```

3. **Infrastructure as Code (Terraform)**
   ```hcl
   resource "aws_ecs_cluster" "laravel_cluster" {
     name = "laravel-api-kit"
     
     setting {
       name  = "containerInsights"
       value = "enabled"
     }
   }
   
   resource "aws_rds_instance" "laravel_db" {
     identifier     = "laravel-api-kit-db"
     engine         = "mysql"
     engine_version = "8.0"
     instance_class = "db.t3.micro"
     allocated_storage = 20
     
     db_name  = "laravel_production"
     username = "laravel"
     password = var.db_password
     
     vpc_security_group_ids = [aws_security_group.rds.id]
     db_subnet_group_name   = aws_db_subnet_group.laravel.name
     
     skip_final_snapshot = false
     final_snapshot_identifier = "laravel-api-kit-final-snapshot"
   }
   ```

### Digital Ocean App Platform

```yaml
# .do/app.yaml
name: laravel-api-kit
services:
- name: web
  source_dir: /
  dockerfile_path: docker/Dockerfile.prod
  github:
    repo: your-username/laravel-api-kit
    branch: main
  run_command: apache2-foreground
  environment_slug: php
  instance_count: 2
  instance_size_slug: basic-s
  http_port: 80
  routes:
  - path: /
  envs:
  - key: APP_ENV
    value: production
  - key: APP_KEY
    type: SECRET
    value: base64:your-generated-app-key
databases:
- name: laravel-db
  engine: MYSQL
  version: "8"
  size_slug: db-s-1vcpu-1gb
```

### Google Cloud Run

```yaml
# cloudbuild.yaml
steps:
  - name: 'gcr.io/cloud-builders/docker'
    args: ['build', '-f', 'docker/Dockerfile.prod', '-t', 'gcr.io/$PROJECT_ID/laravel-api-kit', '.']
  - name: 'gcr.io/cloud-builders/docker'
    args: ['push', 'gcr.io/$PROJECT_ID/laravel-api-kit']
  - name: 'gcr.io/cloud-builders/gcloud'
    args: ['run', 'deploy', 'laravel-api-kit', 
           '--image', 'gcr.io/$PROJECT_ID/laravel-api-kit',
           '--platform', 'managed',
           '--region', 'us-central1',
           '--allow-unauthenticated']
```

## 📊 Monitoring & Maintenance

### Health Checks

1. **Application Health Endpoint**
   ```bash
   curl -f http://your-app.com/api/health || exit 1
   ```

2. **Database Health Check**
   ```bash
   docker-compose exec app php artisan health:check
   ```

### Monitoring Stack

#### 1. Application Monitoring
- **Laravel Telescope** (Development)
- **Laravel Horizon** (Queue monitoring)
- **New Relic** or **DataDog** (APM)

#### 2. Infrastructure Monitoring
- **Prometheus + Grafana**
- **AWS CloudWatch** (if on AWS)
- **Uptime monitoring** (Pingdom, StatusCake)

#### 3. Log Management
- **ELK Stack** (Elasticsearch, Logstash, Kibana)
- **Fluentd** for log collection
- **AWS CloudWatch Logs**

### Backup Strategy

1. **Database Backups**
   ```bash
   # Daily database backup
   docker-compose exec db mysqldump -u laravel -p laravel_production > backup_$(date +%Y%m%d).sql
   
   # Upload to S3
   aws s3 cp backup_$(date +%Y%m%d).sql s3://your-backup-bucket/database/
   ```

2. **File System Backups**
   ```bash
   # Backup storage directory
   tar -czf storage_backup_$(date +%Y%m%d).tar.gz project/storage/app/
   aws s3 cp storage_backup_$(date +%Y%m%d).tar.gz s3://your-backup-bucket/files/
   ```

### Performance Optimization

1. **Application Level**
   ```bash
   # Enable OPcache
   echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini
   
   # Configure Redis caching
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   ```

2. **Database Optimization**
   ```sql
   -- Add database indexes
   CREATE INDEX idx_users_email ON users(email);
   CREATE INDEX idx_projects_user_id ON projects(user_id);
   CREATE INDEX idx_tasks_project_id ON tasks(project_id);
   ```

3. **Web Server Optimization**
   ```apache
   # Enable compression
   LoadModule deflate_module modules/mod_deflate.so
   
   <Location />
       SetOutputFilter DEFLATE
       SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png)$ no-gzip dont-vary
   </Location>
   
   # Enable caching
   <IfModule mod_expires.c>
       ExpiresActive on
       ExpiresByType text/css "access plus 1 year"
       ExpiresByType application/javascript "access plus 1 year"
       ExpiresByType image/png "access plus 1 year"
   </IfModule>
   ```

### Security Hardening

1. **SSL/TLS Configuration**
   ```apache
   <VirtualHost *:443>
       SSLEngine on
       SSLCertificateFile /etc/ssl/certs/yourapp/cert.pem
       SSLCertificateKeyFile /etc/ssl/certs/yourapp/key.pem
       
       # Modern SSL configuration
       SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
       SSLCipherSuite ECDHE+AESGCM:ECDHE+AES256:ECDHE+AES128:!aNULL:!MD5:!DSS
       SSLHonorCipherOrder on
   </VirtualHost>
   ```

2. **Firewall Configuration**
   ```bash
   # UFW firewall setup
   sudo ufw allow 22/tcp
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw --force enable
   ```

3. **Security Headers**
   ```apache
   # Security headers
   Header always set X-Content-Type-Options nosniff
   Header always set X-Frame-Options DENY
   Header always set X-XSS-Protection "1; mode=block"
   Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
   ```

### Scaling Strategy

#### Horizontal Scaling
1. **Load Balancer Configuration**
2. **Database Read Replicas**
3. **Redis Clustering**
4. **CDN Integration**

#### Vertical Scaling
1. **Resource Monitoring**
2. **Performance Profiling**
3. **Bottleneck Identification**
4. **Resource Allocation**

### Maintenance Procedures

#### Weekly Tasks
- Review application logs
- Check disk space usage
- Monitor response times
- Review security logs

#### Monthly Tasks
- Update dependencies
- Review and rotate secrets
- Backup verification
- Performance review

#### Quarterly Tasks
- Security audit
- Infrastructure review
- Cost optimization
- Disaster recovery testing

---

## 📞 Support & Troubleshooting

### Common Issues

1. **Service Won't Start**
   ```bash
   # Check logs
   docker-compose logs app
   
   # Restart services
   docker-compose restart
   ```

2. **Database Connection Issues**
   ```bash
   # Test database connection
   docker-compose exec app php artisan tinker
   >>> DB::connection()->getPdo();
   ```

3. **Queue Jobs Not Processing**
   ```bash
   # Check queue status
   docker-compose exec app php artisan queue:work --timeout=60
   
   # Restart queue workers
   docker-compose exec app php artisan queue:restart
   ```

### Emergency Procedures

1. **Application Downtime**
   - Enable maintenance mode
   - Investigate issue
   - Apply fix
   - Disable maintenance mode

2. **Database Issues**
   - Switch to read replica
   - Investigate primary database
   - Restore from backup if needed

3. **Security Incident**
   - Isolate affected systems
   - Change all credentials
   - Review access logs
   - Apply security patches

---

This deployment guide provides comprehensive coverage for deploying Laravel API Kit across different environments and platforms, ensuring reliable, secure, and scalable deployments.
