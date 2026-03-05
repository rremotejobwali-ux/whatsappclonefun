<?php
// db.php
// Database connection using PDO for MySQL

$host = 'localhost';
$dbname = 'rsk0_07';
$username = 'rsk0_07';
$password = '123456';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Check if users table exists
    $tableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    
    if ($tableExists) {
        // Check if phone column exists in users table
        $checkColumn = $db->query("SHOW COLUMNS FROM users LIKE 'phone'");
        
        if ($checkColumn->rowCount() == 0) {
            // Phone column doesn't exist - need to recreate tables
            $db->exec("DROP TABLE IF EXISTS messages");
            $db->exec("DROP TABLE IF EXISTS users");
        }
    }

    // Ensure users table exists with phone field
    $db->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) UNIQUE NOT NULL,
        `phone` VARCHAR(20) UNIQUE NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `avatar` VARCHAR(255) DEFAULT 'default.png',
        `status` VARCHAR(255) DEFAULT 'Hey there! I am using WhatsApp.',
        `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Ensure messages table exists
    $db->exec("CREATE TABLE IF NOT EXISTS `messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `sender_id` INT NOT NULL,
        `receiver_id` INT NOT NULL,
        `message` TEXT NOT NULL,
        `message_type` ENUM('text', 'image', 'video', 'voice', 'file') DEFAULT 'text',
        `media_url` VARCHAR(500) DEFAULT NULL,
        `media_name` VARCHAR(255) DEFAULT NULL,
        `media_size` INT DEFAULT NULL,
        `status` TINYINT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Add sample users if table is empty
    $userCount = $db->query("SELECT COUNT(*) as count FROM users")->fetch();
    if ($userCount['count'] == 0) {
        // Password for all: 123456
        $hashedPass = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        
        $db->exec("INSERT INTO users (username, phone, password, avatar) VALUES
            ('ali@gmail.com', '+92 301 9876543', '$hashedPass', 'https://ui-avatars.com/api/?name=Ali&background=random'),
            ('sara@gmail.com', '+92 333 5551234', '$hashedPass', 'https://ui-avatars.com/api/?name=Sara&background=random'),
            ('ahmed@gmail.com', '+92 321 1112233', '$hashedPass', 'https://ui-avatars.com/api/?name=Ahmed&background=random')
        ");
    }
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
