<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Fruit Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
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
            position: relative;
        }

        /* ✅ Body Background Image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../image/png6.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
        }
        
        /* Navbar Styles */
        .navbar {
            background: rgba(19, 47, 6, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: 0 2px 15px var(--shadow);
            border-bottom: 2px solid var(--asparagus);
        }
        
        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            color: var(--celery);
        }
        
        .navbar .logo i {
            margin-right: 10px;
            color: var(--green-smoke);
        }
        
        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(116, 142, 72, 0.3);
            color: var(--celery);
        }
        
        .nav-links .logout {
            background: var(--pine-tree);
            border-radius: 8px;
        }
        
        .nav-links .logout:hover {
            background: #1a3d0a;
        }
        
        /* Container */
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* ===== WELCOME SECTION - Body Background Image එකම ===== */
        .welcome-section {
            position: relative;
            padding: 50px 40px;
            border-radius: 20px;
            margin-bottom: 30px;
            color: white;
            overflow: hidden;
            box-shadow: 0 5px 20px var(--shadow);
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: 1px solid rgba(166, 194, 97, 0.3);
        }
        
        /* ✅ Welcome Section Background - Body Image එකම */
        .welcome-section::before {
            content: '';
            position: absolute;
            top: -10%;
            left: -10%;
            width: 120%;
            height: 120%;
            background-image: url('../image/png6.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -2;
            border-radius: 20px;
        }
        
        /* ✅ Welcome Section Overlay */
        .welcome-section::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(19, 47, 6, 0.55);
            z-index: -1;
            border-radius: 20px;
        }
        
        .welcome-section h1 {
            font-size: 32px;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            color: #ffffff;
        }
        
        .welcome-section p {
            font-size: 16px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
            color: #ffffff;
        }
        
        .welcome-section i {
            margin-right: 10px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 10px var(--shadow);
            transition: transform 0.3s;
            border: 1px solid rgba(166, 194, 97, 0.15);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(19, 47, 6, 0.25);
        }
        
        .stat-card i {
            font-size: 48px;
            color: var(--asparagus);
            margin-bottom: 15px;
        }
        
        .stat-card h2 {
            font-size: 36px;
            color: var(--pine-tree);
            margin-bottom: 5px;
        }
        
        .stat-card p {
            color: #666;
            font-size: 14px;
        }
        
        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .menu-item {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px 20px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 10px var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            display: block;
            border: 1px solid rgba(166, 194, 97, 0.15);
        }
        
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(19, 47, 6, 0.25);
            border-color: var(--asparagus);
        }
        
        .menu-item i {
            font-size: 48px;
            color: var(--asparagus);
            margin-bottom: 15px;
        }
        
        .menu-item h3 {
            margin-bottom: 5px;
            color: var(--pine-tree);
        }
        
        .menu-item small {
            color: #666;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            border-top: 1px solid rgba(166, 194, 97, 0.2);
            margin-top: 20px;
        }
        
        .footer i {
            color: var(--celery);
        }
        
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .container {
                padding: 15px;
            }
            
            .welcome-section {
                padding: 35px 25px;
                min-height: 150px;
            }
            
            .welcome-section h1 {
                font-size: 22px;
            }
            
            .welcome-section p {
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .welcome-section {
                padding: 25px 20px;
                min-height: 130px;
            }
            
            .welcome-section h1 {
                font-size: 18px;
            }
            
            .welcome-section p {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-apple-alt"></i> Fruit Store Management
        </div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-boxes"></i> Products</a>
            <a href="categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="suppliers.php"><i class="fas fa-truck"></i> Suppliers</a>
            <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
            <a href="billing.php"><i class="fas fa-receipt"></i> Billing</a>
            <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="store.php"><i class="fas fa-store"></i> Store</a>
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i> Cart
                <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): 
                    $total_kg = 0;
                    foreach($_SESSION['cart'] as $item) {
                        if(is_array($item)) {
                            $total_kg += $item['qty'] ?? 0;
                        } else {
                            $total_kg += floatval($item);
                        }
                    }
                ?>
                    <span style="background:#ff9800; color:white; padding:2px 8px; border-radius:50%; font-size:11px; margin-left:5px;">
                        <?php echo number_format($total_kg, 1); ?>kg
                    </span>
                <?php endif; ?>
            </a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <!-- ✅ Welcome Section - Body Background Image එකම -->
        <div class="welcome-section">
            <h1>
                <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>! 
            </h1>
            <p>
                <i class="fas fa-calendar-alt"></i> Today is <?php echo date('l, F j, Y'); ?>
            </p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <h2>24</h2>
                <p>Total Products</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h2>45</h2>
                <p>Total Customers</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-truck"></i>
                <h2>8</h2>
                <p>Total Suppliers</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <h2>Rs. 12,500</h2>
                <p>Today's Sales</p>
            </div>
        </div>
        
        <div class="menu-grid">
            <a href="products.php" class="menu-item">
                <i class="fas fa-boxes"></i>
                <h3>Products</h3>
                <small>Manage your products</small>
            </a>
            <a href="categories.php" class="menu-item">
                <i class="fas fa-tags"></i>
                <h3>Categories</h3>
                <small>Manage categories</small>
            </a>
            <a href="suppliers.php" class="menu-item">
                <i class="fas fa-truck"></i>
                <h3>Suppliers</h3>
                <small>Manage suppliers</small>
            </a>
            <a href="customers.php" class="menu-item">
                <i class="fas fa-users"></i>
                <h3>Customers</h3>
                <small>Manage customers</small>
            </a>
            <a href="billing.php" class="menu-item">
                <i class="fas fa-receipt"></i>
                <h3>Billing</h3>
                <small>Create invoices</small>
            </a>
            <a href="reports.php" class="menu-item">
                <i class="fas fa-chart-bar"></i>
                <h3>Reports</h3>
                <small>View sales reports</small>
            </a>
            <a href="store.php" class="menu-item">
                <i class="fas fa-store"></i>
                <h3>Store</h3>
                <small>Shop fresh fruits</small>
            </a>
        </div>
        
        <div class="footer">
            <i class="fas fa-leaf"></i> <?php echo date('Y'); ?> Fruit Store Management System. All rights reserved.
        </div>
    </div>
</body>
</html>