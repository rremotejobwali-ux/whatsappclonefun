<?php
// auth.php
// Handles login and signup logic

require_once 'db.php';
session_start();

// Helper to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper to get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Handle Signup
if (isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    
    // Auto-generate a simple avatar based on first letter
    $avatar = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=random";

    if (!empty($username) && !empty($phone) && !empty($password)) {
        try {
            $stmt = $db->prepare("INSERT INTO users (username, phone, password, avatar) VALUES (:username, :phone, :password, :avatar)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([
                ':username' => $username,
                ':phone' => $phone,
                ':password' => $hashed_password,
                ':avatar' => $avatar
            ]);
            
            // Auto login after signup
            $_SESSION['user_id'] = $db->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['phone'] = $phone;
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                // Username or phone exists, try to login if password matches
                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username OR phone = :phone");
                $stmt->execute([':username' => $username, ':phone' => $phone]);
                $existingUser = $stmt->fetch();

                if ($existingUser && password_verify($password, $existingUser['password'])) {
                    $_SESSION['user_id'] = $existingUser['id'];
                    $_SESSION['username'] = $existingUser['username'];
                    $_SESSION['phone'] = $existingUser['phone'];
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Username or phone number already exists! Please try different credentials.";
                }
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    } else {
        $error = "All fields are required!";
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username OR phone = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['phone'] = $user['phone'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid username/phone or password!";
        }
    } else {
        $error = "All fields are required!";
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
