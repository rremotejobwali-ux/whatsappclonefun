<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Clone - API Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 15px; 
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { 
            color: #25d366; 
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        h2 { 
            color: #128c7e; 
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #25d366;
        }
        .status { 
            padding: 15px; 
            border-radius: 8px; 
            margin: 15px 0;
            font-weight: bold;
        }
        .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
        }
        th { 
            background: #25d366; 
            color: white;
            font-weight: 600;
        }
        tr:hover { background: #f5f5f5; }
        .btn { 
            background: #25d366; 
            color: white; 
            padding: 12px 25px; 
            text-decoration: none; 
            border-radius: 25px; 
            display: inline-block; 
            margin: 10px 10px 10px 0;
            transition: all 0.3s;
            font-weight: 600;
        }
        .btn:hover { 
            background: #128c7e; 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4);
        }
        .btn-secondary { background: #128c7e; }
        .btn-secondary:hover { background: #075e54; }
        pre { 
            background: #f4f4f4; 
            padding: 15px; 
            border-radius: 8px; 
            overflow-x: auto;
            border-left: 4px solid #25d366;
        }
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin: 20px 0;
        }
        .card { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }
        .card h3 { color: #128c7e; margin-bottom: 10px; }
        .loading { 
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #25d366;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 WhatsApp Clone - System Test</h1>
        <p style="color: #666; margin-bottom: 30px;">Complete functionality verification dashboard</p>

        <?php
        session_start();
        require 'db.php';

        // Test 1: Database Connection
        echo "<h2>✅ Test 1: Database Connection</h2>";
        try {
            $db->query("SELECT 1");
            echo "<div class='status success'>✓ Database connection successful!</div>";
            echo "<p><strong>Database:</strong> rsk0_07</p>";
        } catch (Exception $e) {
            echo "<div class='status error'>✗ Database connection failed: " . $e->getMessage() . "</div>";
        }

        // Test 2: Tables Check
        echo "<h2>✅ Test 2: Database Tables</h2>";
        try {
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "<div class='status success'>✓ Found " . count($tables) . " tables</div>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li><strong>$table</strong></li>";
            }
            echo "</ul>";
        } catch (Exception $e) {
            echo "<div class='status error'>✗ Error: " . $e->getMessage() . "</div>";
        }

        // Test 3: Users Table Structure
        echo "<h2>✅ Test 3: Users Table Structure</h2>";
        try {
            $columns = $db->query("SHOW COLUMNS FROM users")->fetchAll();
            echo "<div class='status success'>✓ Users table has " . count($columns) . " columns</div>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td><strong>{$col['Field']}</strong></td>";
                echo "<td>{$col['Type']}</td>";
                echo "<td>{$col['Null']}</td>";
                echo "<td>{$col['Key']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } catch (Exception $e) {
            echo "<div class='status error'>✗ Error: " . $e->getMessage() . "</div>";
        }

        // Test 4: Users Data
        echo "<h2>✅ Test 4: Registered Users</h2>";
        try {
            $stmt = $db->query("SELECT id, username, phone, created_at FROM users ORDER BY id");
            $users = $stmt->fetchAll();
            
            if (count($users) > 0) {
                echo "<div class='status success'>✓ Found " . count($users) . " registered users</div>";
                echo "<table>";
                echo "<tr><th>ID</th><th>Username</th><th>Phone Number</th><th>Registered</th></tr>";
                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>{$user['id']}</td>";
                    echo "<td><strong>{$user['username']}</strong></td>";
                    echo "<td>{$user['phone']}</td>";
                    echo "<td>" . date('M d, Y H:i', strtotime($user['created_at'])) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='status error'>✗ No users found! Sample users should be created automatically.</div>";
            }
        } catch (Exception $e) {
            echo "<div class='status error'>✗ Error: " . $e->getMessage() . "</div>";
        }

        // Test 5: Messages
        echo "<h2>✅ Test 5: Messages</h2>";
        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM messages");
            $result = $stmt->fetch();
            echo "<div class='status info'>ℹ Total messages in database: <strong>" . $result['count'] . "</strong></div>";
            
            if ($result['count'] > 0) {
                $stmt = $db->query("SELECT m.*, u1.username as sender, u2.username as receiver 
                                    FROM messages m 
                                    JOIN users u1 ON m.sender_id = u1.id 
                                    JOIN users u2 ON m.receiver_id = u2.id 
                                    ORDER BY m.created_at DESC LIMIT 10");
                $messages = $stmt->fetchAll();
                
                echo "<table>";
                echo "<tr><th>From</th><th>To</th><th>Message</th><th>Time</th></tr>";
                foreach ($messages as $msg) {
                    echo "<tr>";
                    echo "<td><strong>{$msg['sender']}</strong></td>";
                    echo "<td><strong>{$msg['receiver']}</strong></td>";
                    echo "<td>" . htmlspecialchars($msg['message']) . "</td>";
                    echo "<td>" . date('M d, H:i', strtotime($msg['created_at'])) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } catch (Exception $e) {
            echo "<div class='status error'>✗ Error: " . $e->getMessage() . "</div>";
        }

        // Test 6: API Endpoints
        echo "<h2>✅ Test 6: API Endpoints Test</h2>";
        if (isset($_SESSION['user_id'])) {
            echo "<div class='status success'>✓ You are logged in as user ID: {$_SESSION['user_id']}</div>";
            echo "<div class='grid'>";
            echo "<div class='card'>";
            echo "<h3>📋 Get Contacts</h3>";
            echo "<p>Endpoint: <code>api.php?action=get_contacts</code></p>";
            echo "<button onclick='testAPI(\"get_contacts\")' class='btn'>Test Now</button>";
            echo "<pre id='contacts-result'>Click to test...</pre>";
            echo "</div>";
            
            echo "<div class='card'>";
            echo "<h3>💬 Get Messages</h3>";
            echo "<p>Endpoint: <code>api.php?action=get_messages&contact_id=X</code></p>";
            echo "<button onclick='testAPI(\"get_messages\")' class='btn'>Test Now</button>";
            echo "<pre id='messages-result'>Click to test...</pre>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='status info'>ℹ You need to be logged in to test API endpoints</div>";
        }
        ?>

        <h2>🔗 Quick Navigation</h2>
        <div style="margin: 20px 0;">
            <a href="signup.php" class="btn">📝 Signup Page</a>
            <a href="login.php" class="btn btn-secondary">🔐 Login Page</a>
            <a href="index.php" class="btn">💬 Chat Application</a>
            <a href="test.php" class="btn btn-secondary">🧪 Simple Test</a>
        </div>

        <h2>📋 Sample Login Credentials</h2>
        <div class="grid">
            <div class="card">
                <h3>User 1: Ali</h3>
                <p><strong>Username:</strong> ali@gmail.com</p>
                <p><strong>Phone:</strong> +92 301 9876543</p>
                <p><strong>Password:</strong> 123456</p>
            </div>
            <div class="card">
                <h3>User 2: Sara</h3>
                <p><strong>Username:</strong> sara@gmail.com</p>
                <p><strong>Phone:</strong> +92 333 5551234</p>
                <p><strong>Password:</strong> 123456</p>
            </div>
            <div class="card">
                <h3>User 3: Ahmed</h3>
                <p><strong>Username:</strong> ahmed@gmail.com</p>
                <p><strong>Phone:</strong> +92 321 1112233</p>
                <p><strong>Password:</strong> 123456</p>
            </div>
        </div>

        <div class="status info" style="margin-top: 30px;">
            <strong>💡 Testing Instructions:</strong><br>
            1. Open this page in Browser 1<br>
            2. Login with ali@gmail.com in Browser 1<br>
            3. Open incognito/private window (Browser 2)<br>
            4. Login with sara@gmail.com in Browser 2<br>
            5. Send messages between both browsers<br>
            6. Messages should appear in real-time!
        </div>
    </div>

    <script>
        function testAPI(action) {
            const resultId = action === 'get_contacts' ? 'contacts-result' : 'messages-result';
            const resultEl = document.getElementById(resultId);
            resultEl.innerHTML = '<div class="loading"></div> Loading...';
            
            let url = `api.php?action=${action}`;
            if (action === 'get_messages') {
                url += '&contact_id=1'; // Test with user ID 1
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    resultEl.textContent = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    resultEl.textContent = 'Error: ' + error.message;
                });
        }
    </script>
</body>
</html>
