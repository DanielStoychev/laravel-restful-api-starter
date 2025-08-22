# API Documentation - Laravel API Kit

## Base URL
```
Local Development: http://localhost:8000/api
Staging: https://staging-api.laravel-api-kit.com/api
Production: https://api.laravel-api-kit.com/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your-jwt-token}
```

### Authentication Endpoints

#### Register User
```http
POST /auth/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
    },
    "message": "User registered successfully"
}
```

#### Login
```http
POST /auth/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "user"
        },
        "token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
        "expires_at": "2024-01-02T00:00:00.000000Z"
    },
    "message": "Login successful"
}
```

#### Logout
```http
POST /auth/logout
```
*Requires authentication*

**Response (200):**
```json
{
    "success": true,
    "message": "Successfully logged out"
}
```

#### Refresh Token
```http
POST /auth/refresh
```
*Requires authentication*

**Response (200):**
```json
{
    "success": true,
    "data": {
        "token": "2|eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
        "expires_at": "2024-01-02T00:00:00.000000Z"
    },
    "message": "Token refreshed successfully"
}
```

#### Get Authenticated User
```http
GET /user
```
*Requires authentication*

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "user",
        "email_verified_at": "2024-01-01T00:00:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

#### Password Reset Request
```http
POST /auth/forgot-password
```

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Password reset link sent to your email"
}
```

#### Reset Password
```http
POST /auth/reset-password
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "token": "reset-token-from-email",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

## Projects API

### List Projects
```http
GET /projects
```
*Requires authentication*

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 15, max: 100)
- `search` (string): Search in name and description
- `status` (string): Filter by status (active, completed, archived)
- `sort` (string): Sort field (name, created_at, updated_at)
- `order` (string): Sort order (asc, desc)

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "My First Project",
            "description": "This is a sample project",
            "status": "active",
            "user_id": 1,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z",
            "tasks_count": 5,
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "from": 1,
            "last_page": 1,
            "per_page": 15,
            "to": 1,
            "total": 1
        }
    }
}
```

### Create Project
```http
POST /projects
```
*Requires authentication*

**Request Body:**
```json
{
    "name": "New Project",
    "description": "Project description",
    "status": "active"
}
```

**Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "name": "New Project",
        "description": "Project description",
        "status": "active",
        "user_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "message": "Project created successfully"
}
```

### Get Project
```http
GET /projects/{id}
```
*Requires authentication*

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "My First Project",
        "description": "This is a sample project",
        "status": "active",
        "user_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "tasks": [
            {
                "id": 1,
                "title": "Task 1",
                "description": "Task description",
                "status": "pending",
                "priority": "medium",
                "due_date": "2024-01-15T00:00:00.000000Z",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

### Update Project
```http
PUT /projects/{id}
```
*Requires authentication and ownership/admin role*

**Request Body:**
```json
{
    "name": "Updated Project Name",
    "description": "Updated description",
    "status": "completed"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Updated Project Name",
        "description": "Updated description",
        "status": "completed",
        "user_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T01:00:00.000000Z"
    },
    "message": "Project updated successfully"
}
```

### Delete Project
```http
DELETE /projects/{id}
```
*Requires authentication and ownership/admin role*

**Response (200):**
```json
{
    "success": true,
    "message": "Project deleted successfully"
}
```

## Tasks API

### List Project Tasks
```http
GET /projects/{project_id}/tasks
```
*Requires authentication*

**Query Parameters:**
- `page`, `per_page`, `search`, `sort`, `order` (same as projects)
- `status` (string): Filter by status (pending, in_progress, completed)
- `priority` (string): Filter by priority (low, medium, high, urgent)
- `assigned_to` (integer): Filter by assigned user ID

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Task Title",
            "description": "Task description",
            "status": "pending",
            "priority": "medium",
            "project_id": 1,
            "assigned_to": 1,
            "due_date": "2024-01-15T00:00:00.000000Z",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z",
            "assignee": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "total": 1
        }
    }
}
```

### Create Task
```http
POST /projects/{project_id}/tasks
```
*Requires authentication*

**Request Body:**
```json
{
    "title": "New Task",
    "description": "Task description",
    "priority": "high",
    "due_date": "2024-01-15T00:00:00.000000Z",
    "assigned_to": 1
}
```

**Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "title": "New Task",
        "description": "Task description",
        "status": "pending",
        "priority": "high",
        "project_id": 1,
        "assigned_to": 1,
        "due_date": "2024-01-15T00:00:00.000000Z",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    },
    "message": "Task created successfully"
}
```

### Get Task
```http
GET /tasks/{id}
```
*Requires authentication*

### Update Task
```http
PUT /tasks/{id}
```
*Requires authentication and permission*

### Delete Task
```http
DELETE /tasks/{id}
```
*Requires authentication and permission*

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    },
    "code": 422
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Unauthenticated.",
    "code": 401
}
```

### Forbidden (403)
```json
{
    "success": false,
    "message": "This action is unauthorized.",
    "code": 403
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Resource not found.",
    "code": 404
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Internal server error.",
    "code": 500
}
```

## Rate Limiting

API endpoints are rate limited:
- **Authentication endpoints**: 5 attempts per minute per IP
- **General API endpoints**: 60 requests per minute per user
- **Admin endpoints**: 100 requests per minute per user

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1609459200
```

## Filtering and Searching

### Global Search
Use the `search` parameter to search across multiple fields:
```
GET /api/projects?search=laravel
```

### Advanced Filtering
Use field-specific filters:
```
GET /api/projects?status=active&user_id=1
```

### Sorting
Use `sort` and `order` parameters:
```
GET /api/projects?sort=created_at&order=desc
```

### Pagination
Use `page` and `per_page` parameters:
```
GET /api/projects?page=2&per_page=20
```

## Status Codes

- `200` - OK (Successful GET, PUT, DELETE)
- `201` - Created (Successful POST)
- `204` - No Content (Successful DELETE with no response body)
- `400` - Bad Request (Invalid request format)
- `401` - Unauthorized (Authentication required)
- `403` - Forbidden (Insufficient permissions)
- `404` - Not Found (Resource doesn't exist)
- `409` - Conflict (Resource already exists)
- `422` - Unprocessable Entity (Validation failed)
- `429` - Too Many Requests (Rate limit exceeded)
- `500` - Internal Server Error (Server error)

## Development Tools

### Interactive API Documentation
Visit `http://localhost:8000/docs` for Swagger UI documentation with interactive testing capabilities.

### Postman Collection
Import the collection from `/docs/postman/` directory for easy API testing.

### Health Check
```http
GET /health
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "status": "healthy",
        "database": "connected",
        "cache": "connected",
        "queue": "running"
    },
    "timestamp": "2024-01-01T00:00:00.000000Z"
}
```
