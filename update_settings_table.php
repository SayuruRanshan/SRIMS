<?php
require_once 'config/db.php';

$database = new Database();
$pdo = $database->getConnection();

try {
    // Check if table exists
    $checkTable = $pdo->query("SHOW TABLES LIKE 'settings'")->rowCount();
    if ($checkTable == 0) {
        // Create table with all columns
        $pdo->exec("
            CREATE TABLE settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                company_name VARCHAR(255) NOT NULL,
                system_name VARCHAR(255) NOT NULL DEFAULT 'SRIMS',
                company_logo VARCHAR(255) DEFAULT NULL
            )
        ");
        echo "✅ Settings table created successfully!<br>";
        
        // Insert default settings
        $insert = $pdo->prepare("INSERT INTO settings (company_name, system_name) VALUES (?, ?)");
        $insert->execute(['Your Company', 'SRIMS']);
        echo "✅ Default settings inserted!<br>";
    } else {
        // Check if system_name column exists
        $checkColumn = $pdo->query("SHOW COLUMNS FROM settings LIKE 'system_name'")->rowCount();
        if ($checkColumn == 0) {
            // Add missing columns
            $pdo->exec("ALTER TABLE settings ADD COLUMN system_name VARCHAR(255) NOT NULL DEFAULT 'SRIMS'");
            echo "✅ system_name column added!<br>";
        }
        
        $checkLogoColumn = $pdo->query("SHOW COLUMNS FROM settings LIKE 'company_logo'")->rowCount();
        if ($checkLogoColumn == 0) {
            $pdo->exec("ALTER TABLE settings ADD COLUMN company_logo VARCHAR(255) DEFAULT NULL");
            echo "✅ company_logo column added!<br>";
        }
        
        echo "✅ Table structure updated successfully!<br>";
    }
    
    // Show current table structure
    echo "<br><strong>Current table structure:</strong><br>";
    $columns = $pdo->query("DESCRIBE settings")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='admin/settings.php'>Go to Settings Page</a>";
?>