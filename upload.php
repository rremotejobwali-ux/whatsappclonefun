<?php
// upload.php - Handle file uploads
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$uploadDir = 'uploads/';
$response = ['success' => false];

// Create uploads directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Create subdirectories
$subdirs = ['images', 'videos', 'voice', 'files'];
foreach ($subdirs as $subdir) {
    $path = $uploadDir . $subdir . '/';
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $fileType = $_POST['type'] ?? 'file';
    
    // Validate file
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid() . '_' . time() . '.' . $fileExt;
        
        // Determine subdirectory based on type
        $subdir = 'files';
        if ($fileType === 'image' || in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $subdir = 'images';
        } elseif ($fileType === 'video' || in_array($fileExt, ['mp4', 'mov', 'avi', 'webm'])) {
            $subdir = 'videos';
        } elseif ($fileType === 'voice' || in_array($fileExt, ['mp3', 'wav', 'ogg', 'webm', 'm4a'])) {
            $subdir = 'voice';
        }
        
        $uploadPath = $uploadDir . $subdir . '/' . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $response = [
                'success' => true,
                'url' => $uploadPath,
                'filename' => $file['name'],
                'size' => $file['size'],
                'type' => $subdir
            ];
        } else {
            $response['error'] = 'Failed to move uploaded file';
        }
    } else {
        $response['error'] = 'File upload error: ' . $file['error'];
    }
} else {
    $response['error'] = 'No file uploaded';
}

echo json_encode($response);
?>
