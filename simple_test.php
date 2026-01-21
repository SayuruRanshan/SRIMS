<?php
// Simple test to verify database structure
try {
    $pdo = new PDO('mysql:host=localhost;dbname=srims', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 Testing database structure...\n\n";
    
    // Test departments table
    $stmt = $pdo->query("SHOW COLUMNS FROM departments");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "📋 Departments table columns:\n";
    foreach ($columns as $col) {
        echo "  ✅ " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    // Test users table
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n📋 Users table columns:\n";
    foreach ($columns as $col) {
        echo "  ✅ " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    // Test the exact login query
    $query = "SELECT u.*, COALESCE(d.department_name, 'Unassigned') as department_name
              FROM users u 
              LEFT JOIN departments d ON u.department_id = d.id 
              WHERE u.status = 1";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n🧪 Testing login query...\n";
    echo "  ✅ Query executed successfully\n";
    echo "  ✅ Found " . count($users) . " users\n";
    
    if (!empty($users)) {
        echo "  ✅ Sample user: " . $users[0]['username'] . " | Department: " . ($users[0]['department_name'] ?? 'None') . "\n";
        echo "  ✅ Login query structure: WORKING\n";
    }
    
    echo "\n🎉 DATABASE IS CORRECTLY STRUCTURED!\n";
    echo "🔑 Test with: http://localhost/SRIMS04/auth/login.php\n";
    echo "🔑 Default PIN: 1234\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\n🔧 Run: http://localhost/SRIMS04/emergency_fix.php\n";
}
?>