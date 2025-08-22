<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel API Kit</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .container {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 800px;
            width: 90%;
        }

        .logo {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .version {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .endpoints {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .endpoint-card {
            background: rgba(255, 255, 255, 0.15);
            padding: 1.5rem;
            border-radius: 15px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .endpoint-card:hover {
            transform: translateY(-5px);
        }

        .endpoint-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #fff;
        }

        .endpoint-url {
            background: rgba(0, 0, 0, 0.3);
            padding: 0.7rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            margin: 0.5rem 0;
            word-break: break-all;
            color: #e0e0e0;
        }

        .method-get { border-left: 4px solid #28a745; }
        .method-post { border-left: 4px solid #ffc107; }
        .method-put { border-left: 4px solid #17a2b8; }
        .method-delete { border-left: 4px solid #dc3545; }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .stat {
            text-align: center;
            margin: 0.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .doc-button {
            display: inline-block; 
            background: linear-gradient(45deg, #ff6b6b, #ee5a24); 
            color: white; 
            padding: 1rem 2rem; 
            border-radius: 50px; 
            text-decoration: none; 
            font-weight: bold; 
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.4); 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .doc-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 90, 36, 0.6);
        }

        @media (max-width: 600px) {
            .logo {
                font-size: 2.5rem;
            }

            .endpoints {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">&#x1F5A5;&#xFE0F; Laravel API Kit</div>
        <div class="subtitle">Professional REST API with Authentication</div>
        <div class="version">Laravel {{ $laravel_version ?? app()->version() }}</div>

        <div class="stats">
            <div class="stat">
                <span class="stat-number">{{ $users ?? 0 }}</span>
                <span class="stat-label">Users</span>
            </div>
            <div class="stat">
                <span class="stat-number">{{ $projects ?? 0 }}</span>
                <span class="stat-label">Projects</span>
            </div>
            <div class="stat">
                <span class="stat-number">{{ $tasks ?? 0 }}</span>
                <span class="stat-label">Tasks</span>
            </div>
            <div class="stat">
                <span class="stat-number">100%</span>
                <span class="stat-label">Functional</span>
            </div>
        </div>

        <div class="endpoints">
            <div class="endpoint-card">
                <div class="endpoint-title">&#x1F511; Authentication</div>
                <div class="endpoint-url method-post">POST /api/auth/register</div>
                <div class="endpoint-url method-post">POST /api/auth/login</div>
                <div class="endpoint-url method-post">POST /api/auth/logout</div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-title">&#x1F4C1; Projects CRUD</div>
                <div class="endpoint-url method-get">GET /api/projects</div>
                <div class="endpoint-url method-post">POST /api/projects</div>
                <div class="endpoint-url method-get">GET /api/projects/{id}</div>
                <div class="endpoint-url method-put">PUT /api/projects/{id}</div>
                <div class="endpoint-url method-delete">DELETE /api/projects/{id}</div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-title">&#x2705; Tasks CRUD</div>
                <div class="endpoint-url method-get">GET /api/tasks</div>
                <div class="endpoint-url method-post">POST /api/tasks</div>
                <div class="endpoint-url method-get">GET /api/tasks/{id}</div>
                <div class="endpoint-url method-put">PUT /api/tasks/{id}</div>
                <div class="endpoint-url method-delete">DELETE /api/tasks/{id}</div>
            </div>

            <div class="endpoint-card">
                <div class="endpoint-title">&#x2699;&#xFE0F; Additional Services</div>
                <div class="endpoint-url method-get">GET /api/user (Protected)</div>
                <div class="endpoint-url method-get">phpMyAdmin: :8080</div>
                <div class="endpoint-url method-get">MySQL: :3306</div>
                <div class="endpoint-url method-get">Redis: :6379</div>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <a href="/api/documentation" class="doc-button">
                &#x1F4DA; View Interactive API Documentation
            </a>
        </div>

        <p style="margin-top: 2rem; opacity: 0.8; font-size: 0.9rem;">
            &#x1F389; Your Laravel API Kit is ready! Use tools like Postman, curl, or any HTTP client to test the endpoints.
            <br><br>
            <strong>Quick Test:</strong> Try registering a user at <code>/api/auth/register</code>
        </p>
    </div>
</body>
</html>
