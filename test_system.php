<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>SRIMS Quick Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>🔍 SRIMS System Test</h1>";

// Test 1: Database Connection
try {
    require_once '../config/db.php';
    $database = new Database();
    $db = $database->getConnection();
    echo "<div class='status success'>✅ Database Connection: SUCCESS</div>";
} catch (Exception $e) {
    echo "<div class='status error'>❌ Database Connection: FAILED - " . $e->getMessage() . "</div>";
    exit();
}

// Test 2: Tables Exist
$required_tables = ['users', 'departments', 'product_groups', 'products', 'stock_logs', 'settings'];
$existing_tables = [];
try {
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($required_tables as $table) {
        if (in_array($table, $tables)) {
            $existing_tables[] = $table;
        }
    }
    
    if (count($existing_tables) === count($required_tables)) {
        echo "<div class='status success'>✅ All Required Tables: EXIST</div>";
    } else {
        $missing = array_diff($required_tables, $existing_tables);
        echo "<div class='status error'>❌ Missing Tables: " . implode(', ', $missing) . "</div>";
        echo "<div class='info'>💡 Run: <a href='setup_database.php'>setup_database.php</a></div>";
    }
} catch (Exception $e) {
    echo "<div class='status error'>❌ Table Check: FAILED - " . $e->getMessage() . "</div>";
}

// Test 3: Login Query Test
try {
    $test_query = "SELECT u.*, COALESCE(d.department_name, 'Unassigned') as department_name
                  FROM users u 
                  LEFT JOIN departments d ON u.department_id = d.id 
                  WHERE u.status = 1";
    $stmt = $db->prepare($test_query);
    $stmt->execute();
    $test_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='status success'>✅ Login Query: SUCCESS (Found " . count($test_users) . " users)</div>";
    
    if (!empty($test_users)) {
        $sample = $test_users[0];
        echo "<div class='info'>📋 Sample User: " . htmlspecialchars($sample['username']) . " | Role: " . htmlspecialchars($sample['role']) . "</div>";
    }
} catch (Exception $e) {
    echo "<div class='status error'>❌ Login Query: FAILED - " . $e->getMessage() . "</div>";
}

// Test 4: Session Support
echo "<div class='status ";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "success'>✅ Session Support: ACTIVE</div>";
} else {
    echo "error'>❌ Session Support: INACTIVE</div>";
}

// Test 5: File Permissions
$required_files = [
    '../config/db.php',
    '../admin/dashboard.php',
    '../user/dashboard.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<div class='status success'>✅ File Exists: " . basename($file) . "</div>";
    } else {
        echo "<div class='status error'>❌ File Missing: " . basename($file) . "</div>";
    }
}

echo "<div class='status info'>
    <h3>🚀 NEXT STEPS:</h3>
    <ol>
        <li>If all tests are SUCCESS: <a href='login.php'>Go to Login</a></li>
        <li>If tests failed: <a href='../setup_database.php'>Setup Database</a></li>
        <li>Default PIN: <strong>1234</strong></li>
    </ol>
    <hr>
    <p><strong>Quick Access:</strong></p>
    <ul>
        <li><a href='login.php'>🔑 Login Page</a></li>
        <li><a href='../admin/dashboard.php'>👨‍💼 Admin Dashboard</a> (requires login)</li>
        <li><a href='../user/dashboard.php'>👤 User Dashboard</a> (requires login)</li>
        <li><a href='../warehouse_display.php'>📺 Warehouse Display</a></li>
        <li><a href='../setup_database.php'>🛠️ Setup Database</a></li>
    </ul>
</div>";

echo "</body>
</html>";
?>