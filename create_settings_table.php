<?php
require_once 'config/db.php';

$database = new Database();
$pdo = $database->getConnection();

// Create settings table if it doesn't exist
$sql = "
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    system_name VARCHAR(255) NOT NULL DEFAULT 'SRIMS',
    company_logo VARCHAR(255) DEFAULT NULL
)";

try {
    $pdo->exec($sql);
    echo "✅ Settings table created successfully!<br>";
    
    // Insert default settings if table is empty
    $count = $pdo->query("SELECT COUNT(*) as count FROM settings")->fetch()['count'];
    if ($count == 0) {
        $insert = $pdo->prepare("INSERT INTO settings (company_name, system_name) VALUES (?, ?)");
        $insert->execute(['Your Company', 'SRIMS']);
        echo "✅ Default settings inserted!<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Error creating settings table: " . $e->getMessage() . "<br>";
}

echo "<br><a href='admin/settings.php'>Go to Settings Page</a>";
?>