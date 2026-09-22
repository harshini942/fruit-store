<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Store Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f5f5;
            position: relative;
            min-height: 100vh;
        }
        
        /* Background Image */
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
        
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(19, 47, 6, 0.7);
            z-index: -1;
        }
        
        /* ===== NAVBAR ===== */
        .navbar { 
            background: rgba(19, 47, 6, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px; 
            color: white; 
            box-shadow: 0 2px 15px var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border-bottom: 3px solid var(--asparagus);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
            color: var(--celery);
        }
        
        .navbar .logo i {
            margin-right: 10px;
            color: var(--green-smoke);
        }
        
        .navbar a { 
            color: white; 
            text-decoration: none; 
            padding: 10px 15px; 
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .navbar a:hover { 
            background: rgba(116, 142, 72, 0.3); 
            color: var(--celery);
        }
        
        .navbar .logout { 
            background: var(--pine-tree); 
            border-radius: 8px; 
        }
        
        .navbar .logout:hover {
            background: #1a3d0a !important;
        }
        
        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }
        
        .cart-badge {
            background: #ff9800;
            color: white;
            padding: 2px 8px;
            border-radius: 50%;
            font-size: 11px;
            margin-left: 5px;
        }
        
        .container { 
            padding: 20px; 
            max-width: 1400px; 
            margin: 0 auto; 
        }
        
        .card { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 25px; 
            border-radius: 20px; 
            margin-bottom: 20px; 
            box-shadow: 0 5px 20px var(--shadow);
            border: 1px solid rgba(166, 194, 97, 0.2);
        }
        
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
            .navbar { flex-direction: column; gap: 15px; text-align: center; }
            .container { padding: 10px; }
        }
    </style>
</head>
<body>
    <!-- ===== NAVBAR ===== -->
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
                    <span class="cart-badge"><?php echo number_format($total_kg, 1); ?>kg</span>
                <?php endif; ?>
            </a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>
    
    <div class="container">