<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(isset($_GET['remove'])) {
    $id = $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

if(isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

if(isset($_POST['update'])) {
    $id = $_POST['product_id'];
    $qty = floatval($_POST['quantity']);
    if($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        if(isset($_SESSION['cart'][$id]) && is_array($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] = $qty;
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    header("Location: cart.php");
    exit();
}

$cart_items = [];
$total = 0;
$total_kg = 0;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', $ids);
    
    if(!empty($ids_string)) {
        $query = "SELECT * FROM products WHERE id IN ($ids_string)";
        $result = mysqli_query($conn, $query);
        
        while($row = mysqli_fetch_assoc($result)) {
            $product_id = $row['id'];
            $cart_item = $_SESSION['cart'][$product_id];
            
            if(is_array($cart_item)) {
                $qty = $cart_item['qty'] ?? 1;
                $price = $cart_item['price'] ?? $row['price'];
            } else {
                $qty = floatval($cart_item);
                $price = $row['price'];
            }
            
            $row['quantity'] = $qty;
            $row['subtotal'] = $price * $qty;
            $row['display_price'] = $price;
            
            $total += $row['subtotal'];
            $total_kg += $qty;
            $cart_items[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart - Fruit Store</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f5f5; 
            min-height: 100vh; 
        }
        
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
        
        .navbar { 
            background: rgba(19, 47, 6, 0.95); 
            backdrop-filter: blur(10px); 
            padding: 15px 30px; 
            color: white; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            flex-wrap: wrap; 
            border-bottom: 2px solid var(--asparagus);
            box-shadow: 0 2px 15px var(--shadow);
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
            padding: 8px 15px; 
            border-radius: 8px; 
            transition: all 0.3s;
        }
        
        .navbar a:hover { 
            background: rgba(116, 142, 72, 0.3); 
            color: var(--celery);
        }
        
        .navbar .logout {
            background: var(--pine-tree);
        }
        
        .navbar .logout:hover {
            background: #1a3d0a;
        }
        
        .container { padding: 20px; max-width: 1200px; margin: 0 auto; }
        
        .card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            border-radius: 20px; 
            padding: 25px; 
            box-shadow: 0 5px 20px var(--shadow); 
            border: 1px solid rgba(166, 194, 97, 0.2); 
        }
        
        .card h2 { 
            color: var(--pine-tree); 
            margin-bottom: 15px; 
        }
        
        table { width: 100%; border-collapse: collapse; }
        
        th, td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #e0e0e0; 
        }
        
        th { 
            background: var(--clover); 
            color: white; 
        }
        
        tr:hover {
            background: rgba(166, 194, 97, 0.1);
        }
        
        .btn { 
            padding: 8px 20px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500; 
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-danger { 
            background: #c62828; 
            color: white; 
        }
        
        .btn-danger:hover {
            background: #b71c1c;
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
        }
        
        .btn-success { 
            background: var(--asparagus); 
            color: white; 
        }
        
        .btn-success:hover {
            background: var(--dilley);
            box-shadow: 0 4px 15px rgba(116, 142, 72, 0.3);
        }
        
        .btn-primary { 
            background: var(--clover); 
            color: white; 
        }
        
        .btn-primary:hover {
            background: var(--pine-tree);
            box-shadow: 0 4px 15px rgba(19, 47, 6, 0.3);
        }
        
        .total { 
            font-size: 24px; 
            font-weight: bold; 
            text-align: right; 
            margin-top: 20px; 
            color: var(--pine-tree); 
        }
        
        .total small { 
            font-size: 14px; 
            font-weight: normal; 
            color: #666; 
        }
        
        .actions { 
            display: flex; 
            gap: 10px; 
            margin-top: 20px; 
            flex-wrap: wrap; 
        }
        
        .empty-cart { 
            text-align: center; 
            padding: 50px; 
        }
        
        .empty-cart i { 
            font-size: 64px; 
            color: #ccc; 
        }
        
        .empty-cart h3 { 
            color: #666; 
            margin-top: 20px; 
        }
        
        .qty-input { 
            width: 70px; 
            padding: 5px; 
            text-align: center; 
            border: 2px solid #ddd; 
            border-radius: 5px; 
        }
        
        .qty-input:focus {
            outline: none;
            border-color: var(--asparagus);
        }
        
        .kg-label { 
            font-size: 12px; 
            color: #999; 
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
            .navbar { flex-direction: column; gap: 10px; text-align: center; }
            table { font-size: 12px; }
            .btn { padding: 5px 10px; font-size: 11px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fas fa-apple-alt"></i> Fruit Store</div>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="store.php">Store</a>
            <a href="cart.php">Cart</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-shopping-cart"></i> Your Cart</h2>
            <hr style="margin:15px 0; border-color: rgba(166,194,97,0.3);">
            
            <?php if(!empty($cart_items)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price / kg</th>
                            <th>Quantity (kg)</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>Rs. <?php echo number_format($item['display_price'], 2); ?></td>
                            <td>
                                <form method="POST" style="display:flex;gap:5px;align-items:center;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" class="qty-input" value="<?php echo $item['quantity']; ?>" min="0.5" step="0.5">
                                    <span class="kg-label">kg</span>
                                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                                </form>
                            </td>
                            <td>Rs. <?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <a href="?remove=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Remove this item?')">Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="total">
                    Total: Rs. <?php echo number_format($total, 2); ?> 
                    <small>(<?php echo number_format($total_kg, 1); ?> kg)</small>
                </div>
                
                <div class="actions">
                    <a href="?clear=1" class="btn btn-danger" onclick="return confirm('Clear entire cart?')">Clear Cart</a>
                    <a href="checkout.php" class="btn btn-success" style="margin-left:auto;">Proceed to Checkout</a>
                </div>
                
            <?php else: ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty!</h3>
                    <p style="color:#999;">Browse our store to add items.</p>
                    <br>
                    <a href="store.php" class="btn btn-success">Continue Shopping</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <i class="fas fa-leaf"></i> <?php echo date('Y'); ?> Fruit Store Management System
        </div>
    </div>
</body>
</html>