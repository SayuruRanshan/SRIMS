<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SRIMS - Database Structure Check</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>🔍 SRIMS Database Structure Verification</h2>
        
        <?php
        require_once 'config/db.php';
        $database = new Database();
        $db = $database->getConnection();
        
        echo "<h3>✅ Database Connection: <span class='success'>WORKING</span></h3>";
        
        // Check tables exist
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $required_tables = ['users', 'departments', 'product_groups', 'products', 'stock_logs', 'settings'];
        $missing_tables = array_diff($required_tables, $tables);
        
        if (!empty($missing_tables)) {
            echo "<div class='alert alert-danger'>";
            echo "<h4>❌ Missing Tables:</h4>";
            echo "<ul>";
            foreach ($missing_tables as $table) {
                echo "<li class='error'>$table</li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<h4>✅ All Required Tables: <span class='success'>PRESENT</span></h4>";
            
            // Check departments table structure
            echo "<h5>📋 Departments Table Structure:</h5>";
            $dept_columns = $db->query("SHOW COLUMNS FROM departments")->fetchAll(PDO::FETCH_ASSOC);
            echo "<table class='table table-bordered'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($dept_columns as $col) {
                echo "<tr>";
                echo "<td class='success'>" . $col['Field'] . "</td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Check users table structure
            echo "<h5>📋 Users Table Structure:</h5>";
            $user_columns = $db->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_ASSOC);
            echo "<table class='table table-bordered'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($user_columns as $col) {
                echo "<tr>";
                echo "<td class='success'>" . $col['Field'] . "</td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Test the actual login query
            echo "<h5>🧪 Testing Login Query:</h5>";
            try {
                $test_query = "SELECT 
                                u.id,
                                u.username,
                                u.pin_code,
                                u.department_id,
                                u.role,
                                u.status,
                                COALESCE(d.department_name, 'Unassigned') as department_name
                              FROM users u 
                              LEFT JOIN departments d ON u.department_id = d.id 
                              WHERE u.status = 1";
                
                $stmt = $db->prepare($test_query);
                $stmt->execute();
                $test_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<div class='alert alert-success'>";
                echo "<strong>✅ Query SUCCESS!</strong><br>";
                echo "Found " . count($test_users) . " users<br>";
                echo "Sample user: <code>" . ($test_users[0]['username'] ?? 'None') . "</code><br>";
                echo "Department field: <code>" . ($test_users[0]['department_name'] ?? 'None') . "</code>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>";
                echo "<strong>❌ Query FAILED!</strong><br>";
                echo "Error: " . $e->getMessage();
                echo "</div>";
            }
            
            // Show sample data
            echo "<h5>📊 Sample Data:</h5>";
            $sample_users = $db->query("SELECT u.username, u.role, d.department_name FROM users u LEFT JOIN departments d ON u.department_id = d.id LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($sample_users)) {
                echo "<table class='table table-bordered'>";
                echo "<tr><th>Username</th><th>Role</th><th>Department</th></tr>";
                foreach ($sample_users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['department_name'] ?? 'Unassigned') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        
        echo "<hr>";
        echo "<div class='code'>";
        echo "<strong>✅ FIXED LOGIN QUERY (Copy-Paste Ready):</strong><br>";
        echo '$query = "SELECT 
  u.id,
  u.username, 
  u.pin_code,
  u.department_id,
  u.role,
  u.status,
  COALESCE(d.department_name, \"Unassigned\") as department_name
FROM users u 
LEFT JOIN departments d ON u.department_id = d.id 
WHERE u.status = 1";';
        echo "</div>";
        ?>
        
        <div class="mt-4">
            <a href="auth/login.php" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i>Go to Login
            </a>
        </div>
    </div>
</body>
</html>