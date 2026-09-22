<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Create reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Save token in database
        $update = "UPDATE users SET reset_token='$token', reset_expires='$expires' WHERE id=" . $user['id'];
        
        if(mysqli_query($conn, $update)) {
            $reset_link = "http://localhost/fruit-store/pages/reset-password.php?token=" . $token;
            $success = "Password reset link: <a href='$reset_link' target='_blank'>Click here to reset password</a>";
        } else {
            $error = "Database error. Please try again.";
        }
    } else {
        $error = "Email address not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Fruit Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { width: 100%; max-width: 450px; padding: 20px; }
        .card { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
        .card h2 { text-align: center; color: #333; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-weight: 500; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; }
        .form-group input:focus { outline: none; border-color: #4CAF50; }
        .btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
        .btn:hover { transform: translateY(-2px); }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; word-break: break-all; }
        .text-center { text-align: center; margin-top: 20px; }
        .text-center a { color: #7eea66; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-key"></i> Forgot Password</h2>
            <div class="subtitle">Enter your email to reset password</div>
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" placeholder="Enter your registered email" required>
                </div>
                <button type="submit" class="btn">Send Reset Link</button>
            </form>
            <div class="text-center">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>