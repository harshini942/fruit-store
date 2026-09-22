<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$error = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header("Location: forgot-password.php");
    exit();
}

$query = "SELECT * FROM users WHERE reset_token='$token' AND reset_expires > NOW()";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) != 1) {
    header("Location: forgot-password.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 4) {
        $error = "Password must be at least 4 characters!";
    } else {
        $hashed = md5($password);
        $update = "UPDATE users SET password='$hashed', reset_token=NULL, reset_expires=NULL WHERE id=" . $user['id'];
        mysqli_query($conn, $update);
        header("Location: password-reset-success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container { width: 100%; max-width: 400px; padding: 20px; }
        .card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .card h2 { text-align: center; margin-bottom: 20px; color: #333; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #555; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        .btn { width: 100%; padding: 12px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .btn:hover { background: #5a67d8; }
        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .text-center { text-align: center; margin-top: 15px; }
        .text-center a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>🔐 Reset Password</h2>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm password" required>
                </div>
                <button type="submit" class="btn">Reset Password</button>
            </form>
            <div class="text-center">
                <a href="login.php">← Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>