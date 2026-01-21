<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRIMS - Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding: 50px; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .code-block { background-color: #f8f9fa; border-radius: 8px; padding: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="fas fa-database me-2"></i>SRIMS Database Setup</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        require_once 'config/db.php';
                        $database = new Database();
                        $db = $database->getConnection();
                        
                        echo '<h5><i class="fas fa-check-circle text-success"></i> Database Connection: OK</h5>';
                        
                        // Check tables
                        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                        echo '<h6>Tables found:</h6>';
                        echo '<ul>';
                        foreach ($tables as $table) {
                            echo '<li class="text-success"><i class="fas fa-table me-2"></i>' . $table . '</li>';
                        }
                        echo '</ul>';
                        
                        $required_tables = ['users', 'departments', 'product_groups', 'products', 'stock_logs', 'settings'];
                        $missing_tables = array_diff($required_tables, $tables);
                        
                        if (!empty($missing_tables)) {
                            echo '<div class="alert alert-danger">';
                            echo '<h6><i class="fas fa-exclamation-triangle"></i> Missing Tables:</h6>';
                            echo '<ul>';
                            foreach ($missing_tables as $table) {
                                echo '<li>' . $table . '</li>';
                            }
                            echo '</ul>';
                            echo '</div>';
                            
                            echo '<h6><i class="fas fa-tools"></i> To fix this, run the following SQL in your MySQL client:</h6>';
                            echo '<div class="code-block">';
                            echo htmlspecialchars(file_get_contents('database_setup.sql'));
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-success">';
                            echo '<h6><i class="fas fa-check-circle"></i> All Required Tables Present!</h6>';
                            echo '<p>Your database is properly set up. You can now <a href="auth/login.php" class="btn btn-primary">Go to Login</a></p>';
                            echo '</div>';
                            
                            // Check admin user
                            $admin = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch();
                            if ($admin['count'] == 0) {
                                echo '<div class="alert alert-warning">';
                                echo '<h6><i class="fas fa-user-plus"></i> Create Admin User:</h6>';
                                echo '<p>No admin user found. You need to create one manually:</p>';
                                echo '<div class="code-block">';
                                echo "INSERT INTO users (username, pin_code, role) VALUES ('Admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');";
                                echo '</div>';
                                echo '<small class="text-muted">Default PIN: 1234</small>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>