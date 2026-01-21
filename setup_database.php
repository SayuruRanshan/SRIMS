<?php
session_start();
require_once 'config/db.php';

$database = new Database();
$db = $database->getConnection();

echo "<!DOCTYPE html>
<html>
<head>
    <title>SRIMS Quick Setup</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class='container'>
        <h2>🔧 SRIMS Auto Database Setup</h2>";

try {
    echo "<h4>Step 1: Creating database...</h4>";
    $db->exec("CREATE DATABASE IF NOT EXISTS srims");
    $db->exec("USE srims");
    echo "<p class='success'>✅ Database 'srims' ready</p>";
    
    echo "<h4>Step 2: Creating tables...</h4>";
    
    // Users table
    $db->exec("DROP TABLE IF EXISTS users");
    $db->exec("CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(100) NOT NULL,
      pin_code VARCHAR(255) NOT NULL,
      department_id INT,
      role ENUM('admin','user') NOT NULL DEFAULT 'user',
      status TINYINT DEFAULT 1,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='success'>✅ Users table created</p>";
    
    // Departments table
    $db->exec("DROP TABLE IF EXISTS departments");
    $db->exec("CREATE TABLE departments (
      id INT AUTO_INCREMENT PRIMARY KEY,
      department_name VARCHAR(100) NOT NULL UNIQUE,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='success'>✅ Departments table created</p>";
    
    // Product groups table
    $db->exec("DROP TABLE IF EXISTS product_groups");
    $db->exec("CREATE TABLE product_groups (
      id INT AUTO_INCREMENT PRIMARY KEY,
      group_name VARCHAR(100) NOT NULL UNIQUE,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='success'>✅ Product groups table created</p>";
    
    // Products table
    $db->exec("DROP TABLE IF EXISTS products");
    $db->exec("CREATE TABLE products (
      id INT AUTO_INCREMENT PRIMARY KEY,
      product_name VARCHAR(100) NOT NULL UNIQUE,
      group_id INT,
      current_stock INT DEFAULT 0,
      min_stock INT DEFAULT 10,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (group_id) REFERENCES product_groups(id) ON DELETE SET NULL
    )");
    echo "<p class='success'>✅ Products table created</p>";
    
    // Stock logs table
    $db->exec("DROP TABLE IF EXISTS stock_logs");
    $db->exec("CREATE TABLE stock_logs (
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
    )");
    echo "<p class='success'>✅ Stock logs table created</p>";
    
    // Settings table
    $db->exec("DROP TABLE IF EXISTS settings");
    $db->exec("CREATE TABLE settings (
      id INT AUTO_INCREMENT PRIMARY KEY,
      company_name VARCHAR(150) DEFAULT 'SRIMS',
      company_logo VARCHAR(255),
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "<p class='success'>✅ Settings table created</p>";
    
    echo "<h4>Step 3: Inserting default data...</h4>";
    
    // Insert default admin user
    $admin_exists = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'")->fetch()['count'];
    if ($admin_exists == 0) {
        $hashed_pin = password_hash('1234', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO users (username, pin_code, role, department_id) VALUES 
                   ('Admin', '$hashed_pin', 'admin', 1)");
        echo "<p class='success'>✅ Default admin user created (PIN: 1234)</p>";
    } else {
        echo "<p class='success'>✅ Admin user already exists</p>";
    }
    
    // Insert default department
    $dept_exists = $db->query("SELECT COUNT(*) as count FROM departments")->fetch()['count'];
    if ($dept_exists == 0) {
        $db->exec("INSERT INTO departments (department_name) VALUES ('General')");
        echo "<p class='success'>✅ Default department created</p>";
    } else {
        echo "<p class='success'>✅ Departments already exist</p>";
    }
    
    // Insert default product groups
    $groups_exist = $db->query("SELECT COUNT(*) as count FROM product_groups")->fetch()['count'];
    if ($groups_exist == 0) {
        $db->exec("INSERT INTO product_groups (group_name) VALUES 
                   ('Beverages'), ('Food'), ('Cleaning Supplies'), ('Office Supplies')");
        echo "<p class='success'>✅ Default product groups created</p>";
    } else {
        echo "<p class='success'>✅ Product groups already exist</p>";
    }
    
    // Insert default settings
    $settings_exist = $db->query("SELECT COUNT(*) as count FROM settings")->fetch()['count'];
    if ($settings_exist == 0) {
        $db->exec("INSERT INTO settings (company_name) VALUES 
                   ('SRIMS - Stock & Room Inventory Management System')");
        echo "<p class='success'>✅ Default settings created</p>";
    } else {
        echo "<p class='success'>✅ Settings already exist</p>";
    }
    
    // Add indexes for performance
    $db->exec("CREATE INDEX IF NOT EXISTS idx_stock_logs_product ON stock_logs(product_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_stock_logs_date ON stock_logs(created_at)");
    
    echo "<div class='alert alert-success'>";
    echo "<h3>🎉 SETUP COMPLETE!</h3>";
    echo "<p>All database tables created successfully with proper structure.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h3>❌ SETUP FAILED</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<div class='mt-4 text-center'>
        <a href='../auth/login.php' class='btn btn-success btn-lg me-2'>
            <i class='fas fa-sign-in-alt me-2'></i>Go to Login
        </a>
        <a href='../debug_database.php' class='btn btn-info btn-lg'>
            <i class='fas fa-bug me-2'></i>Debug Database
        </a>
     </div>
</div>
</body>
</html>";
?>