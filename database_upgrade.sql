-- SRIMS Database Upgrade - Advanced Features
-- Run this after basic setup to add enterprise features

-- Add min_stock column to products (if not exists)
ALTER TABLE products ADD COLUMN min_stock INT DEFAULT 5 AFTER current_stock;

-- Add user_id to stock_logs (if not exists) - for tracking who made changes
ALTER TABLE stock_logs ADD COLUMN user_id INT AFTER department_id;

-- Create backup table for critical operations (optional)
CREATE TABLE IF NOT EXISTS stock_logs_backup (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_id INT,
    product_id INT,
    department_id INT,
    quantity INT,
    type ENUM('IN','OUT'),
    user_id INT,
    created_at DATETIME,
    backup_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Update existing products with default min_stock if null
UPDATE products SET min_stock = 5 WHERE min_stock IS NULL;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_stock_logs_product ON stock_logs(product_id);
CREATE INDEX IF NOT EXISTS idx_stock_logs_date ON stock_logs(created_at);
CREATE INDEX IF NOT EXISTS idx_stock_logs_type ON stock_logs(type);
CREATE INDEX IF NOT EXISTS idx_products_stock ON products(current_stock);
CREATE INDEX IF NOT EXISTS idx_products_min_stock ON products(min_stock);