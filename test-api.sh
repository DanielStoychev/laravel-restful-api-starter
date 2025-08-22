#!/bin/bash

# Comprehensive Laravel API Kit Testing Script

echo "🧪 COMPREHENSIVE API TESTING STARTED"
echo "=====================================\n"

BASE_URL="http://127.0.0.1:8081"
TOKEN=""
PROJECT_ID=""
TASK_ID=""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test function
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local expected_status=$4
    local description=$5
    local headers=${6:-"Content-Type: application/json"}
    
    echo -e "${BLUE}Testing:${NC} $description"
    echo "Method: $method | Endpoint: $endpoint"
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -H "$headers" "$BASE_URL$endpoint")
    else
        if [ -n "$data" ]; then
            response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X "$method" -H "$headers" -d "$data" "$BASE_URL$endpoint")
        else
            response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X "$method" -H "$headers" "$BASE_URL$endpoint")
        fi
    fi
    
    # Extract HTTP status
    status=$(echo "$response" | grep "HTTP_STATUS:" | cut -d: -f2)
    body=$(echo "$response" | sed '/HTTP_STATUS:/d')
    
    if [ "$status" = "$expected_status" ]; then
        echo -e "${GREEN}✓ PASSED${NC} (Status: $status)"
    else
        echo -e "${RED}✗ FAILED${NC} (Expected: $expected_status, Got: $status)"
        echo "Response: $body"
    fi
    
    echo "----------------------------------------\n"
    return $status
}

# 1. TEST HEALTH ENDPOINT
echo -e "${BLUE}=== HEALTH CHECK TEST ===${NC}"
test_endpoint "GET" "/api/health" "" "200" "Health check endpoint"

# 2. TEST USER REGISTRATION  
echo -e "${BLUE}=== AUTHENTICATION TESTS ===${NC}"
REGISTER_DATA='{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X POST -H "Content-Type: application/json" -d "$REGISTER_DATA" "$BASE_URL/api/auth/register")
status=$(echo "$response" | grep "HTTP_STATUS:" | cut -d: -f2)
body=$(echo "$response" | sed '/HTTP_STATUS:/d')

echo -e "${BLUE}Testing:${NC} User Registration"
if [ "$status" = "201" ]; then
    echo -e "${GREEN}✓ PASSED${NC} (Status: $status)"
    # Extract token for further tests
    TOKEN=$(echo "$body" | grep -o '"token":"[^"]*' | cut -d'"' -f4)
    if [ -n "$TOKEN" ]; then
        echo "Token extracted successfully"
    fi
else
    echo -e "${RED}✗ FAILED${NC} (Expected: 201, Got: $status)"
    echo "Response: $body"
fi
echo "----------------------------------------\n"

# 3. TEST USER LOGIN
LOGIN_DATA='{"email":"test@example.com","password":"password123"}'
response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X POST -H "Content-Type: application/json" -d "$LOGIN_DATA" "$BASE_URL/api/auth/login")
status=$(echo "$response" | grep "HTTP_STATUS:" | cut -d: -f2)
body=$(echo "$response" | sed '/HTTP_STATUS:/d')

echo -e "${BLUE}Testing:${NC} User Login"
if [ "$status" = "200" ]; then
    echo -e "${GREEN}✓ PASSED${NC} (Status: $status)"
    # Update token from login
    NEW_TOKEN=$(echo "$body" | grep -o '"token":"[^"]*' | cut -d'"' -f4)
    if [ -n "$NEW_TOKEN" ]; then
        TOKEN="$NEW_TOKEN"
        echo "Login token updated"
    fi
else
    echo -e "${RED}✗ FAILED${NC} (Expected: 200, Got: $status)"
    echo "Response: $body"
fi
echo "----------------------------------------\n"

# 4. TEST GET AUTHENTICATED USER
echo -e "${BLUE}Testing:${NC} Get Authenticated User"
response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" "$BASE_URL/api/auth/user")
status=$(echo "$response" | grep "HTTP_STATUS:" | cut -d: -f2)

if [ "$status" = "200" ]; then
    echo -e "${GREEN}✓ PASSED${NC} (Status: $status)"
else
    echo -e "${RED}✗ FAILED${NC} (Expected: 200, Got: $status)"
fi
echo "----------------------------------------\n"

# 5. PROJECT CRUD TESTS
echo -e "${BLUE}=== PROJECT CRUD TESTS ===${NC}"

# Create Project
PROJECT_DATA='{"name":"Test Project","description":"A test project","status":"active"}'
response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d "$PROJECT_DATA" "$BASE_URL/api/projects")
status=$(echo "$response" | grep "HTTP_STATUS:" | cut -d: -f2)
body=$(echo "$response" | sed '/HTTP_STATUS:/d')

echo -e "${BLUE}Testing:${NC} Create Project"
if [ "$status" = "201" ]; then
    echo -e "${GREEN}✓ PASSED${NC} (Status: $status)"
    PROJECT_ID=$(echo "$body" | grep -o '"id":[^,}]*' | head -1 | cut -d: -f2)
    echo "Project ID: $PROJECT_ID"
else
    echo -e "${RED}✗ FAILED${NC} (Expected: 201, Got: $status)"
    echo "Response: $body"
fi
echo "----------------------------------------\n"

# Get Projects
test_endpoint "GET" "/api/projects" "" "200" "Get Projects List" "Authorization: Bearer $TOKEN"

# Get Specific Project
if [ -n "$PROJECT_ID" ]; then
    test_endpoint "GET" "/api/projects/$PROJECT_ID" "" "200" "Get Specific Project" "Authorization: Bearer $TOKEN"
fi

# 6. TASK CRUD TESTS  
echo -e "${BLUE}=== TASK CRUD TESTS ===${NC}"

# Create Task
if [ -n "$PROJECT_ID" ]; then
    TASK_DATA="{\"title\":\"Test Task\",\"description\":\"A test task\",\"project_id\":$PROJECT_ID,\"status\":\"todo\",\"priority\":\"medium\"}"
    response=$(curl -s -w "\nHTTP_STATUS:%{http_code}" -X POST -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" -d "$TASK_DATA" "$BASE_URL/api/tasks")
    status=$(echo "$response" | grep "HTTP_STATUS:" | cut -d: -f2)
    body=$(echo "$response" | sed '/HTTP_STATUS:/d')
    
    echo -e "${BLUE}Testing:${NC} Create Task"
    if [ "$status" = "201" ]; then
        echo -e "${GREEN}✓ PASSED${NC} (Status: $status)"
        TASK_ID=$(echo "$body" | grep -o '"id":[^,}]*' | head -1 | cut -d: -f2)
        echo "Task ID: $TASK_ID"
    else
        echo -e "${RED}✗ FAILED${NC} (Expected: 201, Got: $status)"
        echo "Response: $body"
    fi
    echo "----------------------------------------\n"
fi

# Get Tasks
test_endpoint "GET" "/api/tasks" "" "200" "Get Tasks List" "Authorization: Bearer $TOKEN"

# Get Tasks for Project
if [ -n "$PROJECT_ID" ]; then
    test_endpoint "GET" "/api/projects/$PROJECT_ID/tasks" "" "200" "Get Project Tasks" "Authorization: Bearer $TOKEN"
fi

# 7. TEST UNAUTHORIZED ACCESS
echo -e "${BLUE}=== SECURITY TESTS ===${NC}"
test_endpoint "GET" "/api/projects" "" "401" "Unauthorized Access to Projects" "Content-Type: application/json"

# 8. TEST INVALID AUTHENTICATION
test_endpoint "GET" "/api/projects" "" "401" "Invalid Token Access" "Authorization: Bearer invalid_token"

echo -e "${GREEN}🏁 COMPREHENSIVE TESTING COMPLETED${NC}"
echo "==========================================="
