<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel API Kit - Professional Project & Task Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="gradient-bg text-white py-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-2">🚀 Laravel API Kit</h1>
                <p class="text-xl opacity-90">Professional Project & Task Management API</p>
                <div class="mt-4 space-x-4">
                    <span class="bg-green-500 px-3 py-1 rounded-full text-sm">✅ Laravel {{ $stats['laravel_version'] }}</span>
                    <span class="bg-blue-500 px-3 py-1 rounded-full text-sm">🐘 PHP {{ $stats['php_version'] }}</span>
                    <span class="bg-purple-500 px-3 py-1 rounded-full text-sm">🗄️ {{ ucfirst($stats['database']) }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Statistics Section -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['users'] }}</div>
                    <div class="text-gray-600">👤 Registered Users</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['projects'] }}</div>
                    <div class="text-gray-600">📁 Active Projects</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['tasks'] }}</div>
                    <div class="text-gray-600">📝 Tasks Created</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">🎯 Core Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Authentication -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <h3 class="text-xl font-semibold mb-3 text-blue-600">🔐 Authentication System</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li>✅ User Registration & Login</li>
                        <li>✅ Laravel Sanctum Token Auth</li>
                        <li>✅ Secure Password Hashing</li>
                        <li>✅ Email Validation</li>
                    </ul>
                </div>

                <!-- Project Management -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <h3 class="text-xl font-semibold mb-3 text-green-600">📊 Project Management</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li>✅ Create & Manage Projects</li>
                        <li>✅ Project Status Tracking</li>
                        <li>✅ Start/End Date Management</li>
                        <li>✅ User-Scoped Projects</li>
                    </ul>
                </div>

                <!-- Task Management -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
                    <h3 class="text-xl font-semibold mb-3 text-purple-600">📋 Task Management</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li>✅ Create & Assign Tasks</li>
                        <li>✅ Priority Levels</li>
                        <li>✅ Due Date Tracking</li>
                        <li>✅ Status Management</li>
                    </ul>
                </div>

                <!-- API Features -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-orange-500">
                    <h3 class="text-xl font-semibold mb-3 text-orange-600">🌐 RESTful API</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li>✅ 24+ API Endpoints</li>
                        <li>✅ Full CRUD Operations</li>
                        <li>✅ JSON Response Format</li>
                        <li>✅ Pagination Support</li>
                    </ul>
                </div>

                <!-- Security -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500">
                    <h3 class="text-xl font-semibold mb-3 text-red-600">🛡️ Security Features</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li>✅ CORS Protection</li>
                        <li>✅ Request Validation</li>
                        <li>✅ SQL Injection Prevention</li>
                        <li>✅ Bearer Token Auth</li>
                    </ul>
                </div>

                <!-- Technology -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-teal-500">
                    <h3 class="text-xl font-semibold mb-3 text-teal-600">⚡ Modern Stack</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li>✅ Laravel 11.45.2</li>
                        <li>✅ PHP 8.2+</li>
                        <li>✅ MySQL Database</li>
                        <li>✅ Docker Containerized</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- API Endpoints Section -->
    <section class="py-12 bg-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">🔧 API Endpoints</h2>
            
            <div x-data="{ activeTab: 'auth' }" class="max-w-6xl mx-auto">
                <!-- Tab Navigation -->
                <div class="flex flex-wrap justify-center mb-8 space-x-2">
                    <button @click="activeTab = 'auth'" :class="activeTab === 'auth' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700'" class="px-4 py-2 rounded-lg font-medium transition-colors">🔐 Authentication</button>
                    <button @click="activeTab = 'projects'" :class="activeTab === 'projects' ? 'bg-green-500 text-white' : 'bg-white text-gray-700'" class="px-4 py-2 rounded-lg font-medium transition-colors">📁 Projects</button>
                    <button @click="activeTab = 'tasks'" :class="activeTab === 'tasks' ? 'bg-purple-500 text-white' : 'bg-white text-gray-700'" class="px-4 py-2 rounded-lg font-medium transition-colors">📝 Tasks</button>
                </div>

                <!-- Authentication Endpoints -->
                <div x-show="activeTab === 'auth'" class="space-y-4">
                    <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-mono">POST</span>
                                <span class="ml-2 font-mono text-gray-800">/api/auth/register</span>
                            </div>
                            <span class="text-gray-600">User Registration</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-mono">POST</span>
                                <span class="ml-2 font-mono text-gray-800">/api/auth/login</span>
                            </div>
                            <span class="text-gray-600">User Authentication</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">GET</span>
                                <span class="ml-2 font-mono text-gray-800">/api/auth/user</span>
                            </div>
                            <span class="text-gray-600">Get Current User</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-mono">POST</span>
                                <span class="ml-2 font-mono text-gray-800">/api/auth/logout</span>
                            </div>
                            <span class="text-gray-600">User Logout</span>
                        </div>
                    </div>
                </div>

                <!-- Projects Endpoints -->
                <div x-show="activeTab === 'projects'" class="space-y-4">
                    <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">GET</span>
                                <span class="ml-2 font-mono text-gray-800">/api/projects</span>
                            </div>
                            <span class="text-gray-600">List All Projects</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-mono">POST</span>
                                <span class="ml-2 font-mono text-gray-800">/api/projects</span>
                            </div>
                            <span class="text-gray-600">Create New Project</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">GET</span>
                                <span class="ml-2 font-mono text-gray-800">/api/projects/{id}</span>
                            </div>
                            <span class="text-gray-600">Get Project Details</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-mono">PUT</span>
                                <span class="ml-2 font-mono text-gray-800">/api/projects/{id}</span>
                            </div>
                            <span class="text-gray-600">Update Project</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-mono">DELETE</span>
                                <span class="ml-2 font-mono text-gray-800">/api/projects/{id}</span>
                            </div>
                            <span class="text-gray-600">Delete Project</span>
                        </div>
                    </div>
                </div>

                <!-- Tasks Endpoints -->
                <div x-show="activeTab === 'tasks'" class="space-y-4">
                    <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">GET</span>
                                <span class="ml-2 font-mono text-gray-800">/api/tasks</span>
                            </div>
                            <span class="text-gray-600">List All Tasks</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-mono">POST</span>
                                <span class="ml-2 font-mono text-gray-800">/api/tasks</span>
                            </div>
                            <span class="text-gray-600">Create New Task</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">GET</span>
                                <span class="ml-2 font-mono text-gray-800">/api/tasks/{id}</span>
                            </div>
                            <span class="text-gray-600">Get Task Details</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-mono">PUT</span>
                                <span class="ml-2 font-mono text-gray-800">/api/tasks/{id}</span>
                            </div>
                            <span class="text-gray-600">Update Task</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-mono">DELETE</span>
                                <span class="ml-2 font-mono text-gray-800">/api/tasks/{id}</span>
                            </div>
                            <span class="text-gray-600">Delete Task</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-mono">GET</span>
                                <span class="ml-2 font-mono text-gray-800">/api/projects/{id}/tasks</span>
                            </div>
                            <span class="text-gray-600">Get Project Tasks</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-8 text-gray-800">🚀 Quick Links</h2>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="/api/docs" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                    📚 API Documentation
                </a>
                <a href="/api/health" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                    ❤️ Health Check
                </a>
                <button onclick="testAPI()" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold shadow-lg hover:shadow-xl transition-all duration-200">
                    🧪 Test API
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="gradient-bg text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="mb-2">Built with ❤️ using Laravel {{ $stats['laravel_version'] }} & Modern Web Technologies</p>
            <p class="text-sm opacity-75">Professional Project & Task Management API • Ready for Production</p>
        </div>
    </footer>

    <script>
        async function testAPI() {
            try {
                const response = await fetch('/api/health');
                const data = await response.json();
                
                if (response.ok) {
                    alert('✅ API Test Successful!\n\nStatus: ' + data.status + '\nMessage: ' + data.message);
                } else {
                    alert('❌ API Test Failed!\n\nError: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                alert('❌ API Test Failed!\n\nNetwork Error: ' + error.message);
            }
        }
    </script>
</body>
</html>
