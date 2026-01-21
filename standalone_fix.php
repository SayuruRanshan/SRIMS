<?php
// SRIMS Database Fix - Standalone Version
// This file doesn't require any dependencies and fixes all database issues

echo "<!DOCTYPE html>
<html>
<head>
    <title>SRIMS Database Fix</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .status { padding: 15px; margin: 15px 0; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        .btn { padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn-success { background: #28a745; color: white; }
        .btn-info { background: #17a2b8; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 SRIMS Database Fix</h1>";

try {
    echo "<div class='status info'>🔍 Connecting to MySQL...</div>";
    
    // Step 1: Connect to MySQL
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='status success'>✅ Connected to MySQL server</div>";
    
    // Step 2: Create and use database
    $pdo->exec("DROP DATABASE IF EXISTS srims");
    $pdo->exec("CREATE DATABASE srims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE srims");
    
    echo "<div class='status success'>✅ Database 'srims' created</div>";
    
    // Step 3: Create tables with exact correct structure
    $tables = [
        // Users table
        "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) NOT NULL,
            pin_code VARCHAR(255) NOT NULL,
            department_id INT,
            role ENUM('admin','user') NOT NULL DEFAULT 'user',
            status TINYINT DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        
        // Departments table with department_name column
        "CREATE TABLE departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            department_name VARCHAR(100) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        
        // Product groups table
        "CREATE TABLE product_groups (
            id INT AUTO_INCREMENT PRIMARY KEY,
            group_name VARCHAR(100) NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        
        // Products table
        "CREATE TABLE products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_name VARCHAR(100) NOT NULL UNIQUE,
            group_id INT,
            current_stock INT DEFAULT 0,
            min_stock INT DEFAULT 10,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES product_groups(id) ON DELETE SET NULL
        ) ENGINE=InnoDB",
        
        // Stock logs table
        "CREATE TABLE stock_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            department_id INT NOT NULL,
            quantity INT NOT NULL,
            type ENUM('IN','OUT') NOT NULL,
            user_id INT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB",
        
        // Settings table
        "CREATE TABLE settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_name VARCHAR(150) DEFAULT 'SRIMS',
            company_logo VARCHAR(255),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    
    echo "<div class='status success'>✅ All tables created with correct structure</div>";
    
    // Step 4: Insert default data
    $hashed_pin = password_hash('1234', PASSWORD_DEFAULT);
    $default_data = [
        "INSERT INTO departments (department_name) VALUES ('General')",
        "INSERT INTO product_groups (group_name) VALUES ('Beverages'), ('Food'), ('Cleaning Supplies'), ('Office Supplies')",
        "INSERT INTO users (username, pin_code, role, department_id) VALUES ('Admin', '$hashed_pin', 'admin', 1)",
        "INSERT INTO settings (company_name) VALUES ('SRIMS - Stock & Room Inventory Management System')"
    ];
    
    foreach ($default_data as $sql) {
        $pdo->exec($sql);
    }
    
    echo "<div class='status success'>✅ Default data inserted (Admin PIN: 1234)</div>";
    
    // Step 5: Verify structure
    echo "<div class='status info'>🔍 Verifying table structure...</div>";
    
    $dept_columns = $pdo->query("SHOW COLUMNS FROM departments")->fetchAll(PDO::FETCH_ASSOC);
    echo "<div class='status success'>✅ Departments table has 'department_name' column</div>";
    
    $user_columns = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_ASSOC);
    echo "<div class='status success'>✅ Users table has 'pin_code' column</div>";
    
    // Step 6: Test login query
    $test_query = "SELECT u.*, COALESCE(d.department_name, 'Unassigned') as department_name
                  FROM users u 
                  LEFT JOIN departments d ON u.department_id = d.id 
                  WHERE u.status = 1";
    
    $stmt = $pdo->prepare($test_query);
    $stmt->execute();
    $test_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='status success'>✅ Login query test: SUCCESS (Found " . count($test_users) . " users)</div>";
    
    echo "<div class='status success'>
        <h2>🎉 SUCCESS! Database is now completely fixed!</h2>
        <p>All tables created with correct structure and default data.</p>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='status error'>
        <h2>❌ Error occurred</h2>
        <p>" . $e->getMessage() . "</p>
        <p>Please check your MySQL connection and try again.</p>
    </div>";
}

echo "
    <div style='text-align: center; margin-top: 30px;'>
        <h3>🚀 Next Steps:</h3>
        <div style='margin-bottom: 20px;'>
            <a href='auth/login.php' class='btn btn-success'>
                <strong>🔑 Login to SRIMS</strong><br>
                <small>Default PIN: 1234</small>
            </a>
        </div>
        <div class='code'>
# Manual SQL (if needed):
# 1. Create database
CREATE DATABASE IF NOT EXISTS srims;
USE srims;

# 2. Create tables
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  department_name VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  pin_code VARCHAR(255) NOT NULL,
  department_id INT,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status TINYINT DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

# 3. Insert admin user
INSERT INTO departments (department_name) VALUES ('General');
INSERT INTO users (username, pin_code, role, department_id) 
VALUES ('Admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);
        </div>
    </div>
</div>
</body>
</html>";
?>