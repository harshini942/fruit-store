<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($invoice_id == 0) {
    header("Location: dashboard.php");
    exit();
}

// Get invoice details
$query = "SELECT * FROM invoices WHERE id = $invoice_id";
$result = mysqli_query($conn, $query);
$invoice = mysqli_fetch_assoc($result);
if(!$invoice) {
    header("Location: dashboard.php");
    exit();
}

// Get invoice items
$items = mysqli_query($conn, "SELECT i.*, p.name FROM invoice_items i LEFT JOIN products p ON i.product_id = p.id WHERE i.invoice_id = $invoice_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice - Fruit Store</title>
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
        
        .container { 
            padding: 20px; 
            max-width: 800px; 
            margin: 0 auto; 
        }
        
        .invoice-box { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 5px 20px var(--shadow); 
            border: 1px solid rgba(166, 194, 97, 0.2);
        }
        
        .invoice-header { 
            text-align: center; 
            border-bottom: 2px solid var(--asparagus); 
            padding-bottom: 20px; 
            margin-bottom: 20px; 
        }
        
        .invoice-header h1 { 
            color: var(--pine-tree); 
        }
        
        .invoice-header h1 i {
            color: var(--asparagus);
        }
        
        .invoice-header p { 
            color: #666; 
        }
        
        .invoice-details { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 20px; 
        }
        
        .invoice-details div {
            color: #333;
        }
        
        .invoice-details strong {
            color: var(--pine-tree);
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
        
        .total { 
            font-size: 22px; 
            font-weight: bold; 
            text-align: right; 
            margin-top: 15px; 
            color: var(--pine-tree); 
        }
        
        .btn { 
            padding: 10px 25px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-print { 
            background: var(--asparagus); 
            color: white; 
        }
        
        .btn-print:hover {
            background: var(--dilley);
            box-shadow: 0 4px 15px rgba(116, 142, 72, 0.3);
        }
        
        .btn-back { 
            background: var(--clover); 
            color: white; 
        }
        
        .btn-back:hover {
            background: var(--pine-tree);
            box-shadow: 0 4px 15px rgba(19, 47, 6, 0.3);
        }
        
        .footer { 
            text-align: center; 
            margin-top: 30px; 
            color: rgba(255,255,255,0.7); 
            font-size: 12px; 
            border-top: 1px solid rgba(166, 194, 97, 0.2);
            padding-top: 20px;
        }
        
        .footer i {
            color: var(--celery);
        }
        
        .actions { 
            display: flex; 
            gap: 10px; 
            justify-content: center; 
            margin-top: 20px; 
        }
        
        @media print {
            .navbar, .actions, .footer { display: none; }
            body::before, body::after { display: none; }
            .invoice-box { box-shadow: none; border: 1px solid #ddd; }
        }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 10px; text-align: center; }
            .invoice-details { flex-direction: column; gap: 10px; }
            .actions { flex-direction: column; }
            .btn { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fas fa-apple-alt"></i> Fruit Store</div>
        <div>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="store.php"><i class="fas fa-store"></i> Store</a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="invoice-box">
            <div class="invoice-header">
                <h1><i class="fas fa-apple-alt"></i> Fruit Store</h1>
                <p>Fresh Fruits - Peradeniya, Kandy</p>
                <p><strong>Invoice #<?php echo str_pad($invoice['id'], 5, '0', STR_PAD_LEFT); ?></strong></p>
                <p>Date: <?php echo date('d/m/Y h:i A', strtotime($invoice['created_at'])); ?></p>
            </div>
            
            <div class="invoice-details">
                <div><strong>Customer:</strong> <?php echo htmlspecialchars($invoice['customer_name']); ?></div>
                <div><strong>Phone:</strong> <?php echo htmlspecialchars($invoice['customer_phone']); ?></div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty (kg)</th>
                        <th>Price / kg</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    while($item = mysqli_fetch_assoc($items)): 
                        $subtotal = $item['quantity'] * $item['price'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo number_format($item['quantity'], 1); ?></td>
                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div class="total">Total: Rs. <?php echo number_format($total, 2); ?></div>
            
            <div class="actions">
                <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Print Invoice</button>
                <a href="store.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back to Store</a>
            </div>
        </div>
        <div class="footer">
            <i class="fas fa-leaf"></i> <?php echo date('Y'); ?> Fruit Store Management System
        </div>
    </div>
</body>
</html>