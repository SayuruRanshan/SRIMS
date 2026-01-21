# SRIMS Database Setup Instructions

## 🚨 DATABASE SETUP REQUIRED

The error indicates your database tables are not created. Follow these steps:

### Method 1: Using phpMyAdmin (Recommended)
1. Open phpMyAdmin in your XAMPP/WAMP/MAMP
2. Create new database named `srims`
3. Click on the `srims` database
4. Click "Import" tab
5. Choose `database_setup.sql` file
6. Click "Go" button

### Method 2: Using MySQL Command Line
1. Open MySQL command line or terminal
2. Run: `mysql -u root -p < database_setup.sql`
3. Enter your MySQL password when prompted

### Method 3: Quick Fix Script
Visit: `http://localhost/SRIMS04/check_database.php`
This will show you exactly what's missing and provide the SQL to run.

## ✅ Verification

After setup, visit: `http://localhost/SRIMS04/auth/login.php`
Default Admin PIN: `1234`

## 🔍 Troubleshooting

If you still get errors:
1. Make sure database name is exactly `srims`
2. Ensure all 6 tables are created: users, departments, product_groups, products, stock_logs, settings
3. Check that your MySQL user has proper permissions

## 📋 Tables Created

- `users` - User accounts and authentication
- `departments` - Department management  
- `product_groups` - Product categories
- `products` - Product inventory
- `stock_logs` - Stock movement history
- `settings` - System configuration