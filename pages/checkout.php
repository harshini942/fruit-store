<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: store.php");
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
    $invoice_date = date('Y-m-d H:i:s');
    $total_amount = $total;
    
    $query = "INSERT INTO invoices (customer_name, customer_phone, total_amount, created_at) 
              VALUES ('$customer_name', '$customer_phone', $total_amount, '$invoice_date')";
    mysqli_query($conn, $query);
    $invoice_id = mysqli_insert_id($conn);
    
    foreach($cart_items as $item) {
        $product_id = $item['id'];
        $quantity = $item['quantity'];
        $price = $item['display_price'];
        $subtotal = $item['subtotal'];
        
        $query = "INSERT INTO invoice_items (invoice_id, product_id, quantity, price, total) 
                  VALUES ($invoice_id, $product_id, $quantity, $price, $subtotal)";
        mysqli_query($conn, $query);
        
        mysqli_query($conn, "UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
    }
    
    unset($_SESSION['cart']);
    header("Location: invoice.php?id=$invoice_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Fruit Store</title>
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
        
        .container { padding: 20px; max-width: 800px; margin: 0 auto; }
        
        .card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            border-radius: 20px; 
            padding: 30px; 
            box-shadow: 0 5px 20px var(--shadow); 
            border: 1px solid rgba(166, 194, 97, 0.2); 
        }
        
        .card h2 { 
            color: var(--pine-tree); 
            margin-bottom: 15px; 
        }
        
        .form-group { margin-bottom: 15px; }
        
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: #333; 
        }
        
        .form-group input { 
            width: 100%; 
            padding: 12px; 
            border: 2px solid #e0e0e0; 
            border-radius: 8px; 
            font-size: 15px; 
            transition: all 0.3s;
        }
        
        .form-group input:focus { 
            outline: none; 
            border-color: var(--asparagus); 
            box-shadow: 0 0 0 3px rgba(116, 142, 72, 0.2);
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
        }
        
        th, td { 
            padding: 10px; 
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
            padding: 12px 30px; 
            border: none; 
            border-radius: 10px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-success { 
            background: var(--asparagus); 
            color: white; 
            width: 100%; 
            font-size: 18px; 
        }
        
        .btn-success:hover {
            background: var(--dilley);
            box-shadow: 0 4px 15px rgba(116, 142, 72, 0.3);
        }
        
        .total { 
            font-size: 24px; 
            font-weight: bold; 
            text-align: right; 
            color: var(--pine-tree); 
        }
        
        .total small { 
            font-size: 14px; 
            font-weight: normal; 
            color: #666; 
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
            <h2><i class="fas fa-credit-card"></i> Checkout</h2>
            <hr style="margin:15px 0; border-color: rgba(166,194,97,0.3);">
            <h3>Order Summary</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty (kg)</th>
                        <th>Price / kg</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['quantity'], 1); ?> kg</td>
                        <td>Rs. <?php echo number_format($item['display_price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">
                Total: Rs. <?php echo number_format($total, 2); ?> 
                <small>(<?php echo number_format($total_kg, 1); ?> kg)</small>
            </div>
            <hr style="margin:20px 0; border-color: rgba(166,194,97,0.3);">
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Customer Name</label>
                    <input type="text" name="customer_name" placeholder="Enter customer name" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" name="customer_phone" placeholder="Enter phone number" required>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Complete Purchase</button>
            </form>
        </div>
        <div class="footer">
            <i class="fas fa-leaf"></i> <?php echo date('Y'); ?> Fruit Store Management System
        </div>
    </div>
</body>
</html>