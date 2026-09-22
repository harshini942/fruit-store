<?php
require_once __DIR__ . '/../config/database.php';

// Start session only if not already started
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
    $password = md5($_POST['password']);
    
    $query = "SELECT * FROM users WHERE (username='$username' OR email='$username') AND password='$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'ඔබගේ පරිශීලක නම හෝ මුරපදය වැරදියි!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fruit Store Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== COLOR PALETTE ===== */
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
            overflow: hidden;
        }
        
        /* ===== SLIDING BACKGROUND ANIMATION - MEDIUM SPEED ===== */
        .slider-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            animation: slideAnimation 30s infinite;
        }

        /* Slide 1 - Fresh Fruits */
        .slide1 {
            background-image: url('../image/png1.jpeg');
            animation-delay: 0s;
        }

        /* Slide 2 - Mangoes */
        .slide2 {
            background-image: url('../image/png2.jpeg');
            animation-delay: 7.5s;
        }

        /* Slide 3 - Fruit Market */
        .slide3 {
            background-image: url('../image/png3.jpeg');
            animation-delay: 15s;
        }

        /* Slide 4 - Strawberries */
        .slide4 {
            background-image: url('../image/png4.jpeg');
            animation-delay: 22.5s;
        }

        @keyframes slideAnimation {
            0% { opacity: 0; transform: scale(1); }
            6% { opacity: 1; transform: scale(1.01); }
            22% { opacity: 1; transform: scale(1.02); }
            35% { opacity: 0; transform: scale(1.02); }
            100% { opacity: 0; transform: scale(1); }
        }
        
        /* Dark Overlay for better text visibility */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.55);
            z-index: -1;
        }
        
        /* Optional: Add a subtle pattern overlay */
        .pattern-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" opacity="0.05"><path fill="none" d="M10,10 L90,10 M10,20 L90,20 M10,30 L90,30 M10,40 L90,40 M10,50 L90,50 M10,60 L90,60 M10,70 L90,70 M10,80 L90,80 M10,90 L90,90 M10,10 L10,90 M20,10 L20,90 M30,10 L30,90 M40,10 L40,90 M50,10 L50,90 M60,10 L60,90 M70,10 L70,90 M80,10 L80,90 M90,10 L90,90" stroke="white" stroke-width="0.5"/></svg>');
            background-repeat: repeat;
            z-index: -1;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
            z-index: 1;
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
        
        /* ===== LOGIN CARD ===== */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
        }
        
        /* ===== TITLE ===== */
        .login-card h2 {
            text-align: center;
            color: var(--pine-tree);
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .login-card h2 i {
            color: var(--asparagus);
            margin-right: 10px;
        }
        
        .login-subtitle {
            text-align: center;
            color: #555;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        /* ===== FORM GROUP ===== */
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
        
        /* ===== CHECKBOX ===== */
        .checkbox-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .checkbox-group label {
            margin-bottom: 0;
            font-weight: normal;
            color: #555;
        }
        
        .checkbox-group a {
            color: var(--asparagus);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .checkbox-group a:hover {
            text-decoration: underline;
        }
        
        /* ===== LOGIN BUTTON - COLOR PALETTE ===== */
        .btn-login {
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
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(116, 142, 72, 0.4);
            background: linear-gradient(135deg, var(--pine-tree), var(--clover));
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* ===== ALERT ===== */
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
        
        /* ===== TEXT CENTER ===== */
        .text-center {
            text-align: center;
            margin-top: 25px;
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
        
        /* Fruit decoration elements */
        .fruit-decoration {
            position: absolute;
            opacity: 0.1;
            pointer-events: none;
        }
        
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .login-card h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Sliding Background Images -->
    <div class="slider-container">
        <div class="slide slide1"></div>
        <div class="slide slide2"></div>
        <div class="slide slide3"></div>
        <div class="slide slide4"></div>
    </div>
    <div class="overlay"></div>
    <div class="pattern-overlay"></div>
    
    <div class="login-container">
        <div class="login-card">
            <h2><i class="fas fa-apple-alt"></i> Fruit Store Management</h2>
            <div class="login-subtitle">Please login to your account</div>
            
            <?php if($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username or Email</label>
                    <input type="text" name="username" placeholder="Enter your username or email" required autofocus>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot-password.php">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> LOGIN</button>
            </form>
            
            <div class="text-center">
                <a href="register.php"><i class="fas fa-user-plus"></i> Don't have an account? Register</a>
            </div>
        </div>
    </div>
</body>
</html>