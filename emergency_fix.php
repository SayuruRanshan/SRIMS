<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>SRIMS Emergency Fix</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .status { padding: 15px; margin: 15px 0; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; border-left: 4px solid #ffc107; }
        .btn { padding: 12px 24px; border: none; border-radius: 5px; margin: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
        .step { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚨 SRIMS Emergency Database Fix</h1>
        <p>This tool will fix the database structure issues completely.</p>";

try {
    // Connect to MySQL (without selecting database first)
    $pdo = new PDO('mysql:host=localhost;dbname=mysql', 'root', '');
    require_once 'config/db.php';
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='status success'>✅ Connected to MySQL server</div>";
    
    // Drop and recreate database completely
    $pdo->exec("DROP DATABASE IF EXISTS srims");
    $pdo->exec("CREATE DATABASE srims");
    $pdo->exec("USE srims");
    
    echo "<div class='status success'>✅ Database 'srims' recreated</div>";
    
    // Create tables one by one with exact correct structure
    $tables = [
        "CREATE TABLE users (
          id INT AUTO_INCREMENT PRIMARY KEY,
          username VARCHAR(100) NOT NULL,
          pin_code VARCHAR(255) NOT NULL,
          department_id INT,
          role ENUM('admin','user') NOT NULL DEFAULT 'user',
          status TINYINT DEFAULT 1,
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE departments (
          id INT AUTO_INCREMENT PRIMARY KEY,
          department_name VARCHAR(100) NOT NULL UNIQUE,
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE product_groups (
          id INT AUTO_INCREMENT PRIMARY KEY,
          group_name VARCHAR(100) NOT NULL UNIQUE,
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE products (
          id INT AUTO_INCREMENT PRIMARY KEY,
          product_name VARCHAR(100) NOT NULL UNIQUE,
          group_id INT,
          current_stock INT DEFAULT 0,
          min_stock INT DEFAULT 10,
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (group_id) REFERENCES product_groups(id) ON DELETE SET NULL
        )",
        
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
        )",
        
        "CREATE TABLE settings (
          id INT AUTO_INCREMENT PRIMARY KEY,
          company_name VARCHAR(150) DEFAULT 'SRIMS',
          company_logo VARCHAR(255),
          created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
        $table_name = substr($sql, strpos($sql, 'CREATE TABLE') + 13, strpos($sql, '(') - 13);
        echo "<div class='status success'>✅ Created table: $table_name</div>";
    }
    
    // Insert default data
    $inserts = [
        "INSERT INTO departments (department_name) VALUES ('General')",
        "INSERT INTO product_groups (group_name) VALUES ('Beverages'), ('Food'), ('Cleaning Supplies'), ('Office Supplies')",
        "INSERT INTO users (username, pin_code, role, department_id) VALUES ('Admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1)",
        "INSERT INTO settings (company_name) VALUES ('SRIMS - Stock & Room Inventory Management System')"
    ];
    
    foreach ($inserts as $sql) {
        $pdo->exec($sql);
    }
    
    echo "<div class='status success'>✅ Default data inserted</div>";
    
    echo "<div class='status success'>✅ Admin user created with PIN: 1234</div>";
    
    echo "<div class='step'>";
    echo "<h3>🎉 DATABASE SETUP COMPLETE!</h3>";
    echo "<p>All tables created successfully with correct structure.</p>";
    echo "<a href='auth/login.php' class='btn btn-success'>🔑 Go to Login</a>";
    echo "<a href='test_system.php' class='btn btn-primary'>🧪 Test System</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='status error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div class='warning'>";
    echo "<h4>🔧 Manual Fix:</h4>";
    echo "<div class='code'>-- Run this in MySQL terminal or phpMyAdmin:

-- 1. Create database
CREATE DATABASE IF NOT EXISTS srims;
USE srims;

-- 2. Create users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  pin_code VARCHAR(255) NOT NULL,
  department_id INT,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status TINYINT DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Create departments table  
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  department_name VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Create admin user
INSERT INTO users (username, pin_code, role, department_id) 
VALUES ('Admin', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);</div>";
    echo "</div>";
}

echo "</div>
</body>
</html>";
?>