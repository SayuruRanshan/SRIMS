<?php
require_once 'config/db.php';

$database = new Database();
$pdo = $database->getConnection();

try {
    // Drop existing table and recreate with correct structure
    $pdo->exec("DROP TABLE IF EXISTS settings");
    
    // Create table with all required columns
    $pdo->exec("
        CREATE TABLE settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            company_name VARCHAR(255) NOT NULL,
            system_name VARCHAR(255) NOT NULL DEFAULT 'SRIMS',
            company_logo VARCHAR(255) DEFAULT NULL
        )
    ");
    
    // Insert default settings
    $insert = $pdo->prepare("INSERT INTO settings (company_name, system_name) VALUES (?, ?)");
    $insert->execute(['Your Company', 'SRIMS']);
    
    echo "✅ Settings table recreated with correct structure!<br>";
    
    // Show table structure
    echo "<br><strong>Table structure:</strong><br>";
    $columns = $pdo->query("DESCRIBE settings")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<br><a href='admin/settings.php'>Go to Settings Page</a>";
?>