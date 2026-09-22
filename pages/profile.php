c:\Users\Harshani\Downloads\profile.php<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get current user details
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Update password
if(isset($_POST['update_password'])) {
    $current_password = md5($_POST['current_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Check current password
    if($current_password != $user['password']) {
        $error = "Current password is incorrect!";
    } elseif($new_password != $confirm_password) {
        $error = "New passwords do not match!";
    } elseif(strlen($new_password) < 4) {
        $error = "Password must be at least 4 characters!";
    } else {
        $new_password_hashed = md5($new_password);
        $update = "UPDATE users SET password = '$new_password_hashed' WHERE id = $user_id";
        if(mysqli_query($conn, $update)) {
            $success = "Password updated successfully! Please login again.";
            session_destroy();
            header("refresh:3; url=login.php");
        } else {
            $error = "Update failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile - Fruit Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
        }
        .navbar {
            background: #2E7D32;
            padding: 15px;
            color: white;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
        }
        .container {
            padding: 20px;
            max-width: 500px;
            margin: 50px auto;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #2E7D32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #1B5E20;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="dashboard.php">🍎 Fruit Store</a>
        <a href="products.php">Products</a>
        <a href="categories.php">Categories</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php" style="float:right;">Logout</a>
    </div>
    
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            
            <div class="info">
                <strong>Username:</strong> <?php echo $user['username']; ?><br>
                <strong>Email:</strong> <?php echo $user['email']; ?><br>
                <strong>Role:</strong> <?php echo $user['role']; ?>
            </div>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter current password" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
                <button type="submit" name="update_password" class="btn">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>