<?php
session_start();
// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: pages/dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Store Management System - Welcome</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1619566636858-adf3ef46400b?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }

        /* Dark Overlay */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        /* Main Container */
        .cover-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            padding: 20px;
        }

        /* Hero Card */
        .hero-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 50px;
            max-width: 700px;
            width: 100%;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Section */
        .logo {
            margin-bottom: 20px;
        }

        .logo-icon {
            font-size: 80px;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Title */
        .hero-card h1 {
            font-size: 42px;
            color: #2d5016;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .hero-card h1 i {
            color: #4caf50;
        }

        .tagline {
            font-size: 18px;
            color: #4caf50;
            margin-bottom: 25px;
            letter-spacing: 2px;
            font-weight: 500;
        }

        /* Description */
        .description {
            margin-bottom: 40px;
        }

        .description p {
            color: #555;
            line-height: 1.8;
            font-size: 16px;
        }

        /* Buttons */
        .buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 14px 35px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            color: white;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
        }

        .btn-secondary {
            background: transparent;
            color: #4caf50;
            border: 2px solid #4caf50;
        }

        .btn-secondary:hover {
            background: #4caf50;
            color: white;
            transform: translateY(-3px);
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-card {
                padding: 30px 20px;
            }
            
            .hero-card h1 {
                font-size: 28px;
            }
            
            .tagline {
                font-size: 14px;
            }
            
            .buttons {
                gap: 15px;
            }
            
            .btn {
                padding: 10px 25px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="cover-container">
        <div class="hero-card">
            <div class="logo">
                <i class="fas fa-apple-alt logo-icon"></i>
            </div>
            
            <h1>
                <i class="fas fa-store"></i> Fruit Store<br>
                Management System
            </h1>
            
            <div class="tagline">
                <i class="fas fa-chart-line"></i> Smart Management for Fresh Fruits
            </div>
            
            <div class="description">
                <p>Welcome to the most comprehensive fruit store management solution. 
                Manage your inventory, track sales, handle billing, and grow your business 
                with our easy-to-use system.</p>
            </div>
            
            <div class="buttons">
                <a href="pages/login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login to Account
                </a>
                <a href="pages/register.php" class="btn btn-secondary">
                    <i class="fas fa-user-plus"></i> Create Account
                </a>
            </div>
            
            <div class="footer">
                <p><i class="fas fa-leaf"></i> Fresh Fruits • Quality Service • Smart Management</p>
            </div>
        </div>
    </div>
</body>
</html>