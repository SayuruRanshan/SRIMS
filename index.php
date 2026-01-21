<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRIMS - Stock & Room Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .landing-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 50px;
            max-width: 600px;
            text-align: center;
        }
        .logo {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }
        .title {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        .subtitle {
            color: #6c757d;
            font-size: 1.2rem;
            margin-bottom: 40px;
        }
        .btn-setup {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-setup:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .features {
            text-align: left;
            margin: 30px 0;
            background: rgba(0, 0, 0, 0.05);
            padding: 20px;
            border-radius: 10px;
        }
        .feature-item {
            margin: 10px 0;
            padding-left: 25px;
            position: relative;
        }
        .feature-item:before {
            content: "✅";
            position: absolute;
            left: 0;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="logo">
            <i class="fas fa-warehouse"></i>
        </div>
        <h1 class="title">SRIMS</h1>
        <p class="subtitle">Stock & Room Inventory Management System</p>
        
        <div class="features">
            <h4>🚀 Enterprise Features:</h4>
            <div class="feature-item">Real-time stock management</div>
            <div class="feature-item">Multi-department support</div>
            <div class="feature-item">Advanced reporting & analytics</div>
            <div class="feature-item">Warehouse display mode</div>
            <div class="feature-item">Export to PDF/Excel</div>
            <div class="feature-item">Secure PIN authentication</div>
            <div class="feature-item">Role-based access control</div>
        </div>
        
        <div class="d-grid gap-3 col-md-2 mx-auto">
            <a href="setup_database.php" class="btn-setup">
                <i class="fas fa-database me-2"></i>Setup Database
            </a>
            <a href="auth/login.php" class="btn-setup btn-secondary">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </a>
        </div>
        
        <div class="mt-4">
            <small class="text-muted">
                <strong>Default PIN:</strong> 1234 | 
                <strong>Default Role:</strong> Admin
            </small>
        </div>
        
        <div class="mt-4">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>Quick Start:</h6>
                <ol class="text-start mb-0">
                    <li>Click "Setup Database" (creates all tables & default data)</li>
                    <li>Click "Login" to access the system</li>
                    <li>Use PIN: <strong>1234</strong></li>
                </ol>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>