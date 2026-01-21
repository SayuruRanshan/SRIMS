<?php
require_once 'config/db.php';

$database = new Database();
$pdo = $database->getConnection();

try {
    echo "<h3>Fixing Settings Table...</h3><br>";
    
    // Drop and recreate table to ensure correct structure
    $pdo->exec("DROP TABLE IF EXISTS settings");
    echo "✅ Old settings table dropped<br>";
    
    // Create table with all required columns
    $createTable = "
        CREATE TABLE settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_name VARCHAR(255) NOT NULL,
            system_name VARCHAR(255) NOT NULL DEFAULT 'SRIMS',
            company_logo VARCHAR(255) DEFAULT NULL
        )
    ";
    $pdo->exec($createTable);
    echo "✅ Settings table created with correct structure<br>";
    
    // Insert default settings
    $insert = $pdo->prepare("INSERT INTO settings (company_name, system_name) VALUES (?, ?)");
    $insert->execute(['Your Company', 'SRIMS']);
    echo "✅ Default settings inserted<br>";
    
    // Verify table structure
    echo "<br><strong>Verification - Table Structure:</strong><br>";
    $columns = $pdo->query("DESCRIBE settings")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})<br>";
    }
    
    // Verify data
    echo "<br><strong>Verification - Current Settings:</strong><br>";
    $current = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if ($current) {
        echo "- Company Name: " . htmlspecialchars($current['company_name']) . "<br>";
        echo "- System Name: " . htmlspecialchars($current['system_name']) . "<br>";
        echo "- Company Logo: " . ($current['company_logo'] ?: 'None') . "<br>";
    }
    
    echo "<br><div class='alert alert-success'>";
    echo "✅ Settings table is now properly configured!";
    echo "</div>";
    
    echo "<br><a href='admin/settings.php' class='btn btn-primary'>Go to Settings Page</a>";
    echo " | ";
    echo "<a href='admin/dashboard.php' class='btn btn-success'>Go to Dashboard</a>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.alert { padding: 10px; margin: 10px 0; border-radius: 5px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.btn { padding: 10px 20px; margin: 5px; text-decoration: none; border-radius: 5px; color: white; }
.btn-primary { background: #007bff; }
.btn-success { background: #28a745; }
</style>