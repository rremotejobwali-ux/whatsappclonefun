<?php require 'auth.php'; ?>
<?php if (isLoggedIn()) { header("Location: index.php"); exit; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - WhatsApp Clone</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>WhatsApp Clone</h2>
            <p>Create your account</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="+92 300 1234567" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="signup" class="btn-primary">Sign Up</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>
