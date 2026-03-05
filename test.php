<?php
// test.php - Database Test Page
require 'db.php';

echo "<h2>WhatsApp Clone - Database Test</h2>";
echo "<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;width:100%;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#25d366;color:white;}</style>";

// Test 1: Check Users Table
echo "<h3>✅ Test 1: Users in Database</h3>";
try {
    $stmt = $db->query("SELECT id, username, phone, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<p><strong>Total Users: " . count($users) . "</strong></p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Phone</th><th>Created At</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['phone']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>❌ No users found! Sample users should have been created automatically.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 2: Check Messages Table
echo "<h3>✅ Test 2: Messages in Database</h3>";
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM messages");
    $result = $stmt->fetch();
    echo "<p>Total Messages: <strong>" . $result['count'] . "</strong></p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 3: API Test
echo "<h3>✅ Test 3: Quick Links</h3>";
echo "<p><a href='signup.php' style='background:#25d366;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>📝 Signup Page</a></p>";
echo "<p><a href='login.php' style='background:#128c7e;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>🔐 Login Page</a></p>";
echo "<p><a href='index.php' style='background:#075e54;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin:5px;'>💬 Chat Page</a></p>";

echo "<hr>";
echo "<h3>📋 Sample Login Credentials</h3>";
echo "<ul>";
echo "<li><strong>Username:</strong> ali@gmail.com | <strong>Password:</strong> 123456</li>";
echo "<li><strong>Username:</strong> sara@gmail.com | <strong>Password:</strong> 123456</li>";
echo "<li><strong>Username:</strong> ahmed@gmail.com | <strong>Password:</strong> 123456</li>";
echo "</ul>";

echo "<hr>";
echo "<p style='color:green;'><strong>✅ Database connection is working!</strong></p>";
?>
