-- SRIMS Database Setup
-- Stock and Room Inventory Management System

CREATE DATABASE IF NOT EXISTS srims;
USE srims;

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  pin_code VARCHAR(255) NOT NULL,
  department_id INT,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status TINYINT DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Departments table
CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  department_name VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Product groups table
CREATE TABLE product_groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  group_name VARCHAR(100) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_name VARCHAR(100) NOT NULL UNIQUE,
  group_id INT,
  current_stock INT DEFAULT 0,
  min_stock INT DEFAULT 10,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (group_id) REFERENCES product_groups(id) ON DELETE SET NULL
);

-- Stock logs table
CREATE TABLE stock_logs (
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
);

-- Settings table
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  company_name VARCHAR(150) DEFAULT 'SRIMS',
  company_logo VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (PIN: 1234)
INSERT INTO users (username, pin_code, department_id, role) VALUES 
('Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin');

-- Insert default department
INSERT INTO departments (department_name) VALUES 
('General');

-- Insert default product groups
INSERT INTO product_groups (group_name) VALUES 
('Beverages'),
('Food'),
('Cleaning Supplies'),
('Office Supplies');

-- Insert default settings
INSERT INTO settings (company_name) VALUES 
('SRIMS - Stock & Room Inventory Management System');