<?php
echo "<h1>🔧 SRIMS - EXACT FIXES</h1>";

// Run this after checking structure with check_structure.php

echo "<h2>🎯 TEST ALL POSSIBLE COLUMN NAMES</h2>";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=srims', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check all possible department table/column combinations
    $tests = [
        ['table' => 'departments', 'column' => 'department_name'],
        ['table' => 'departments', 'column' => 'name'],
        ['table' => 'department', 'column' => 'department_name'],
        ['table' => 'department', 'column' => 'name']
    ];
    
    foreach ($tests as $test) {
        echo "<h3>Testing: {$test['table']}.{$test['column']}</h3>";
        try {
            $result = $pdo->query("SELECT {$test['column']} FROM {$test['table']} LIMIT 1");
            if ($result && $result->fetch()) {
                echo "<p style='color: green;'>✅ SUCCESS: {$test['table']}.{$test['column']} EXISTS</p>";
                
                // Test the login query with this combination
                $query = "SELECT u.*, d.{$test['column']} AS department_name 
                          FROM users u 
                          LEFT JOIN {$test['table']} d ON u.department_id = d.id 
                          WHERE u.status = 1 LIMIT 1";
                
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                
                if ($test_user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<p style='color: blue;'>✅ LOGIN QUERY WORKS with: {$test['table']}.{$test['column']}</p>";
                    
                    // Provide the exact PHP code
                    echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
                    echo "<h4>🎯 EXACT PHP CODE TO USE:</h4>";
                    echo "<code style='background: #f8f9fa; padding: 15px; display: block; white-space: pre;'>";
                    echo "// LOGIN QUERY THAT WORKS
\$query = \"SELECT u.*, d.{$test['column']} AS department_name 
               FROM users u 
               LEFT JOIN {$test['table']} d ON u.department_id = d.id 
               WHERE u.pin_code = ? AND u.status = 1\";

\$stmt = \$pdo->prepare(\$query);
\$stmt->execute([\$pin]);
\$user = \$stmt->fetch(PDO::FETCH_ASSOC);

if (\$user && password_verify(\$pin, \$user['pin_code'])) {
    // User authenticated
    \$_SESSION['user_id'] = \$user['id'];
    \$_SESSION['username'] = \$user['username'];
    \$_SESSION['role'] = \$user['role'];
    \$_SESSION['department_id'] = \$user['department_id'];
    \$_SESSION['department_name'] = \$user['department_name'];
    
    if (\$user['role'] == 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../user/dashboard.php');
    }
    exit();
}";
                    echo "</code></div>";
                } else {
                    echo "<p style='color: orange;'>⚠️ No users found with this structure</p>";
                }
            } else {
                echo "<p style='color: red;'>❌ FAILED: {$test['table']}.{$test['column']} doesn't exist</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
        }
        echo "<hr>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your MySQL connection and database name.</p>";
}

echo "<h2>📋 MANUAL SQL FIXES (if needed):</h2>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>If column is 'name' instead of 'department_name':</h4>";
echo "<code style='background: #f8f9fa; padding: 10px; display: block;'>";
echo "-- Fix 1: Rename column (recommended)
ALTER TABLE departments CHANGE name department_name VARCHAR(100);

-- Fix 2: Update query
SELECT u.*, d.name AS department_name
FROM users u 
LEFT JOIN departments d ON u.department_id = d.id
WHERE u.pin_code = ?;";
echo "</code>";
echo "</div>";

echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>If table is 'department' instead of 'departments':</h4>";
echo "<code style='background: #f8f9fa; padding: 10px; display: block;'>";
echo "-- Fix 1: Update query
SELECT u.*, d.department_name
FROM users u 
LEFT JOIN department d ON u.department_id = d.id
WHERE u.pin_code = ?;";
echo "</code>";
echo "</div>";

echo "<h3>🚀 RECOMMENDED STEPS:</h3>";
echo "<ol>";
echo "<li><strong>Run:</strong> <a href='check_structure.php'>check_structure.php</a> - Shows actual structure</li>";
echo "<li><strong>Run:</strong> <a href='exact_fixes.php'>exact_fixes.php</a> - Tests all combinations</li>";
echo "<li>Find which combination works (green SUCCESS)</li>";
echo "<li>Use the exact PHP code provided</li>";
echo "<li>Update your login.php file</li>";
echo "</ol>";

echo "<div style='text-align: center; margin-top: 30px;'>";
echo "<a href='auth/login.php' class='btn btn-success' style='padding: 15px 30px; font-size: 18px;'>🔑 Test Login</a>";
echo "</div>";
?>