<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Check if user exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = 'Username or email already exists!';
    } else {
        $query = "INSERT INTO users (username, email, password, full_name, phone, role) 
                  VALUES ('$username', '$email', '$password', '$full_name', '$phone', 'staff')";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Fruit Store Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ============================================================
           COLOR PALETTE
           #132F06 - PINE TREE
           #3D5316 - CLOVER
           #5B7341 - DILLEY
           #748E48 - ASPARAGUS
           #94AA64 - GREEN SMOKE
           #A6C261 - CELERY
           ============================================================ */
        :root {
            --pine-tree: #132F06;
            --clover: #3D5316;
            --dilley: #5B7341;
            --asparagus: #748E48;
            --green-smoke: #94AA64;
            --celery: #A6C261;
            --shadow: rgba(19, 47, 6, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }
        
        /* ===== BACKGROUND IMAGE ===== */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../image/png5.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }
        
        /* ===== DARK OVERLAY ===== */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(19, 47, 6, 0.6);
            z-index: -1;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px var(--shadow);
            border: 1px solid rgba(166, 194, 97, 0.2);
            transition: transform 0.3s ease;
        }
        
        .register-card:hover {
            transform: translateY(-5px);
        }
        
        .register-card h2 {
            text-align: center;
            color: var(--pine-tree);
            margin-bottom: 10px;
            font-size: 32px;
        }
        
        .register-card h2 i {
            color: var(--asparagus);
            margin-right: 10px;
        }
        
        .register-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        
        .form-group label i {
            color: var(--asparagus);
            margin-right: 8px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--asparagus);
            box-shadow: 0 0 0 3px rgba(116, 142, 72, 0.2);
        }
        
        /* ===== REGISTER BUTTON - COLOR PALETTE ===== */
        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--clover), var(--asparagus));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(116, 142, 72, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(116, 142, 72, 0.4);
            background: linear-gradient(135deg, var(--pine-tree), var(--clover));
        }
        
        .btn-register:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: var(--clover);
            border: 1px solid #a5d6a7;
        }
        
        .text-center {
            text-align: center;
            margin-top: 20px;
        }
        
        .text-center a {
            color: var(--asparagus);
            text-decoration: none;
            font-weight: 500;
        }
        
        .text-center a:hover {
            text-decoration: underline;
        }
        
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .register-card {
                padding: 30px 20px;
            }
            
            .register-card h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <h2>
                <i class="fas fa-apple-alt"></i> Create Account
            </h2>
            <div class="register-subtitle">Register to manage your fruit store</div>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user-circle"></i> Full Name</label>
                    <input type="text" name="full_name" placeholder="Enter your full name" required autofocus>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone</label>
                    <input type="text" name="phone" placeholder="Enter your phone number">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Choose a password" required>
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>
            
            <div class="text-center">
                <a href="login.php">✨ Already have an account? Login</a>
            </div>
        </div>
    </div>
</body>
</html>