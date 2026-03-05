<?php
// api.php
// JSON API for chat operations

require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$current_user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_contacts':
        // Get all users except current user
        // Also fetch the last message for each contact to display in sidebar
        $stmt = $db->prepare("
            SELECT u.id, u.username, u.phone, u.avatar, 
            (SELECT message FROM messages 
             WHERE (sender_id = u.id AND receiver_id = :me) 
                OR (sender_id = :me AND receiver_id = u.id) 
             ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT created_at FROM messages 
             WHERE (sender_id = u.id AND receiver_id = :me) 
                OR (sender_id = :me AND receiver_id = u.id) 
             ORDER BY created_at DESC LIMIT 1) as last_message_time
            FROM users u 
            WHERE u.id != :me
            ORDER BY last_message_time DESC
        ");
        $stmt->execute([':me' => $current_user_id]);
        $users = $stmt->fetchAll();
        echo json_encode($users);
        break;

    case 'get_messages':
        $contact_id = $_GET['contact_id'] ?? 0;
        
        $stmt = $db->prepare("
            SELECT * FROM messages 
            WHERE (sender_id = :me AND receiver_id = :other) 
               OR (sender_id = :other AND receiver_id = :me) 
            ORDER BY created_at ASC
        ");
        $stmt->execute([
            ':me' => $current_user_id,
            ':other' => $contact_id
        ]);
        $messages = $stmt->fetchAll();
        echo json_encode($messages);
        break;

    case 'send_message':
        // Read JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        $receiver_id = $input['receiver_id'] ?? 0;
        $message = trim($input['message'] ?? '');
        $message_type = $input['message_type'] ?? 'text';
        $media_url = $input['media_url'] ?? null;
        $media_name = $input['media_name'] ?? null;
        $media_size = $input['media_size'] ?? null;

        if ($receiver_id) {
            $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type, media_url, media_name, media_size) VALUES (:me, :other, :msg, :type, :url, :name, :size)");
            $stmt->execute([
                ':me' => $current_user_id,
                ':other' => $receiver_id,
                ':msg' => $message,
                ':type' => $message_type,
                ':url' => $media_url,
                ':name' => $media_name,
                ':size' => $media_size
            ]);
            
            $message_id = $db->lastInsertId();
            echo json_encode(['status' => 'success', 'message_id' => $message_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid receiver']);
        }
        break;
        
    case 'get_user_info':
        // Helper to get specific user info (for header)
        $user_id = $_GET['user_id'] ?? 0;
        $stmt = $db->prepare("SELECT id, username, phone, avatar FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();
        echo json_encode($user);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
