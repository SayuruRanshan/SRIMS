<?php
echo "<h2>🔍 SRIMS Database Structure Check</h2>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=srims', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>📋 Departments Table Structure:</h3>";
    $result = $pdo->query("DESCRIBE departments");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Departments table not found!</p>";
    }
    
    echo "<h3>📋 Users Table Structure:</h3>";
    $result = $pdo->query("DESCRIBE users");
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Users table not found!</p>";
    }
    
    echo "<h3>🧪 Test Login Query:</h3>";
    try {
        $query = "SELECT u.*, d.department_name FROM users u LEFT JOIN departments d ON u.department_id = d.id WHERE u.status = 1 LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        echo "<p style='color: green;'>✅ Query executed successfully</p>";
        if ($test_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<p>✅ Sample user found: " . $test_user['username'] . " | Department: " . ($test_user['department_name'] ?? 'NULL') . "</p>";
        } else {
            echo "<p>⚠️ No users found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Query failed: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h3>🔧 EXACT FIXES NEEDED:</h3>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>If department_name column doesn't exist:</h4>";
echo "<code style='background: #f8f9fa; padding: 10px; display: block;'>";
echo "-- Option 1: Add missing column
ALTER TABLE departments ADD department_name VARCHAR(100);

-- Option 2: Use existing column
SELECT u.*, d.name AS department_name
FROM users u 
LEFT JOIN departments d ON u.department_id = d.id
WHERE u.pin_code = ?";
echo "</code>";

echo "<h4>If table name is different:</h4>";
echo "<code style='background: #f8f9fa; padding: 10px; display: block;'>";
echo "-- Check what your actual table is called
SHOW TABLES LIKE '%department%';

-- Use correct table name
SELECT u.*, d.department_name
FROM users u 
LEFT JOIN actual_table_name d ON u.department_id = d.id
WHERE u.pin_code = ?";
echo "</code>";
echo "</div>";

echo "<h3>🎯 PERFECT LOGIN QUERY (after fix):</h3>";
echo "<code style='background: #d4edda; padding: 15px; display: block;'>";
echo "\$query = \"SELECT 
    u.id,
    u.username, 
    u.pin_code,
    u.department_id,
    u.role,
    u.status,
    d.department_name
FROM users u 
LEFT JOIN departments d ON u.department_id = d.id 
WHERE u.pin_code = ? AND u.status = 1\";

\$stmt = \$pdo->prepare(\$query);
\$stmt->execute([\$pin]);
\$user = \$stmt->fetch(PDO::FETCH_ASSOC);";
echo "</code>";

echo "<h3>🚀 NEXT STEPS:</h3>";
echo "<ol>";
echo "<li>Look at the table structure output above</li>";
echo "<li>Find your actual column names</li>";
echo "<li>Use the corrected query</li>";
echo "<li>Test the query in MySQL first</li>";
echo "<li>Then update your PHP file</li>";
echo "</ol>";
?>