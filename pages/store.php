<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ===== HANDLE ADD PRODUCT =====
if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = !empty($_POST['category']) ? intval($_POST['category']) : 'NULL';
    $price = floatval($_POST['price']);
    $stock = floatval($_POST['stock']);
    
    $query = "INSERT INTO products (name, category_id, price, stock) VALUES ('$name', $category, $price, $stock)";
    if(mysqli_query($conn, $query)) {
        header("Location: store.php?msg=added_product");
        exit();
    }
}

// ===== HANDLE DELETE PRODUCT =====
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: store.php?msg=deleted");
    exit();
}

// ===== HANDLE EDIT PRODUCT =====
if(isset($_POST['edit_product'])) {
    $id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = !empty($_POST['category']) ? intval($_POST['category']) : 'NULL';
    $price = floatval($_POST['price']);
    $stock = floatval($_POST['stock']);
    
    $query = "UPDATE products SET name='$name', category_id=$category, price=$price, stock=$stock WHERE id=$id";
    if(mysqli_query($conn, $query)) {
        header("Location: store.php?msg=updated");
        exit();
    }
}

// Get all products
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          ORDER BY p.id DESC";
$products = mysqli_query($conn, $query);

// Get categories for dropdown
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

// Handle Add to Cart
if(isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = floatval($_POST['quantity']);
    
    $check = mysqli_query($conn, "SELECT stock, name, price FROM products WHERE id = $product_id");
    $row = mysqli_fetch_assoc($check);
    
    if($row['stock'] <= 0) {
        $error = " Sorry, " . $row['name'] . " is currently out of stock!";
    } elseif($quantity > $row['stock']) {
        $error = "Not enough stock! Available: " . $row['stock'] . " kg";
    } else {
        $price_per_kg = $row['price'];
        
        if(!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if(isset($_SESSION['cart'][$product_id])) {
            if(is_array($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['qty'] += $quantity;
            } else {
                $old_qty = floatval($_SESSION['cart'][$product_id]);
                $_SESSION['cart'][$product_id] = [
                    'qty' => $old_qty + $quantity,
                    'price' => $price_per_kg,
                    'name' => $row['name'],
                    'original_price' => $price_per_kg
                ];
            }
        } else {
            $_SESSION['cart'][$product_id] = [
                'qty' => $quantity,
                'price' => $price_per_kg,
                'name' => $row['name'],
                'original_price' => $price_per_kg
            ];
        }
        
        header("Location: store.php?msg=added");
        exit();
    }
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$error = isset($error) ? $error : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store - Fruit Store</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--asparagus);
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
            border-radius: 5px; 
            transition: all 0.3s; 
        }
        
        .navbar a:hover { 
            background: rgba(116, 142, 72, 0.3); 
            color: var(--celery);
        }
        
        .nav-links { display: flex; flex-wrap: wrap; gap: 5px; align-items: center; }
        
        .cart-badge {
            background: #ff9800;
            color: white;
            padding: 2px 8px;
            border-radius: 50%;
            font-size: 12px;
            margin-left: 5px;
        }
        
        .logout {
            background: var(--pine-tree);
            border-radius: 8px;
        }
        
        .logout:hover {
            background: #1a3d0a !important;
        }
        
        .container { padding: 20px; max-width: 1400px; margin: 0 auto; }

        /* ===== HEADER CARD ===== */
        .header-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 5px 20px var(--shadow);
            border: 1px solid rgba(166, 194, 97, 0.2);
        }
        
        .header-card h1 { 
            font-size: 32px; 
            color: var(--pine-tree); 
            margin-bottom: 10px; 
        }
        
        .header-card h1 i {
            color: var(--asparagus);
        }
        
        .header-card p { color: #666; }

        /* ===== ALERTS ===== */
        .alert {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: rgba(232, 245, 233, 0.95);
            color: var(--clover);
            border: 1px solid #a5d6a7;
        }
        
        .alert-error {
            background: rgba(255, 235, 238, 0.95);
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        /* ===== TABLE STYLES ===== */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px var(--shadow);
            border: 1px solid rgba(166, 194, 97, 0.2);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background: var(--clover);
            color: white;
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        tr:hover {
            background: rgba(166, 194, 97, 0.1);
        }

        .product-name {
            font-weight: bold;
            color: var(--pine-tree);
        }

        .price-cell {
            color: var(--asparagus);
            font-weight: bold;
            font-size: 18px;
        }
        
        .price-cell .kg-label {
            font-size: 12px;
            font-weight: normal;
            color: #999;
        }

        .stock-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            font-weight: bold;
        }

        .stock-badge.in-stock {
            background: #e8f5e9;
            color: var(--clover);
        }

        .stock-badge.low-stock {
            background: #fff3e0;
            color: #e65100;
            animation: blink 1s infinite;
        }

        .stock-badge.out-stock {
            background: #ffebee;
            color: #c62828;
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .qty-input {
            width: 70px;
            padding: 6px 8px;
            text-align: center;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .qty-input:focus {
            outline: none;
            border-color: var(--asparagus);
        }

        .qty-input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .btn-add-to-cart {
            padding: 8px 18px;
            background: linear-gradient(135deg, var(--clover), var(--asparagus));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(116, 142, 72, 0.4);
            background: linear-gradient(135deg, var(--pine-tree), var(--clover));
        }

        .btn-add-to-cart:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .out-of-stock-msg {
            color: #c62828;
            font-weight: bold;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .out-of-stock-msg i {
            font-size: 16px;
        }

        .bulk-info {
            font-size: 12px;
            color: #e65100;
            background: #fff3e0;
            padding: 3px 12px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 500;
        }

        /* ===== TABLE FOOTER ===== */
        .table-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid rgba(166, 194, 97, 0.3);
            margin-top: 20px;
        }

        .table-footer .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .table-footer .btn-add {
            background: var(--asparagus);
            color: white;
            box-shadow: 0 4px 15px rgba(116, 142, 72, 0.3);
        }

        .table-footer .btn-add:hover {
            background: var(--dilley);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(116, 142, 72, 0.4);
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin-top: 30px;
            border-top: 1px solid rgba(166, 194, 97, 0.2);
        }
        
        .footer i {
            color: var(--celery);
        }

        /* ===== MODAL STYLES ===== */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(19, 47, 6, 0.6);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 450px;
            max-width: 95%;
            box-shadow: 0 10px 40px var(--shadow);
            border: 1px solid rgba(166, 194, 97, 0.2);
        }
        
        .modal-content h3 {
            color: var(--pine-tree);
            margin-bottom: 20px;
        }
        
        .modal-content h3 i {
            color: var(--asparagus);
        }
        
        .modal-content label {
            display: block;
            margin: 10px 0 5px;
            font-weight: 600;
            color: #333;
        }
        
        .modal-content input, .modal-content select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .modal-content input:focus, .modal-content select:focus {
            outline: none;
            border-color: var(--asparagus);
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .modal-buttons .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .modal-buttons .btn-save {
            background: var(--asparagus);
            color: white;
        }
        
        .modal-buttons .btn-save:hover {
            background: var(--dilley);
        }
        
        .modal-buttons .btn-cancel {
            background: #c62828;
            color: white;
        }
        
        .modal-buttons .btn-cancel:hover {
            background: #b71c1c;
        }

        /* Edit & Delete Buttons in Table */
        .btn-edit {
            padding: 6px 12px;
            background: var(--green-smoke);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .btn-edit:hover {
            background: var(--dilley);
        }
        
        .btn-delete {
            padding: 6px 12px;
            background: #c62828;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .btn-delete:hover {
            background: #b71c1c;
        }

        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 10px; }
            table { font-size: 12px; }
            th, td { padding: 8px 6px; }
            .price-cell { font-size: 14px; }
            .qty-input { width: 50px; }
            .btn-add-to-cart { padding: 5px 10px; font-size: 11px; }
            .modal-content { width: 95%; padding: 20px; }
            .table-footer { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo"><i class="fas fa-apple-alt"></i> Fruit Store</div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-boxes"></i> Products</a>
            <a href="store.php"><i class="fas fa-store"></i> Store</a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart
                <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): 
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
    </div>
    
    <div class="container">
        <div class="header-card">
            <h1><i class="fas fa-store"></i> Fresh Fruits Store</h1>
            
        </div>
        
        <?php if($msg == 'added'): ?>
            <div class="alert"><i class="fas fa-check-circle"></i> ✅ Product added to cart!</div>
        <?php elseif($msg == 'added_product'): ?>
            <div class="alert"><i class="fas fa-check-circle"></i> ✅ Product added successfully!</div>
        <?php elseif($msg == 'updated'): ?>
            <div class="alert"><i class="fas fa-check-circle"></i> ✅ Product updated successfully!</div>
        <?php elseif($msg == 'deleted'): ?>
            <div class="alert"><i class="fas fa-check-circle"></i> ✅ Product deleted successfully!</div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price (per KG)</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Bulk Discount</th>
                        <th>Qty (kg)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($products) > 0): ?>
                        <?php 
                        $count = 1;
                        while($row = mysqli_fetch_assoc($products)): 
                            $price = $row['price'];
                            $discount_percent = 0;
                            
                            if($row['stock'] >= 10) {
                                $discount_percent = 15;
                            } elseif($row['stock'] >= 5) {
                                $discount_percent = 10;
                            }
                            
                            if($row['stock'] <= 0) {
                                $stock_class = 'out-stock';
                                $stock_text = ' Out of Stock';
                                $status_text = 'Out of Stock';
                                $status_color = '#c62828';
                                $disabled = 'disabled';
                            } elseif($row['stock'] <= 5) {
                                $stock_class = 'low-stock';
                                $stock_text = 'Low Stock (' . $row['stock'] . ' kg)';
                                $status_text = ' Low Stock';
                                $status_color = '#e65100';
                                $disabled = '';
                            } else {
                                $stock_class = 'in-stock';
                                $stock_text = ' In Stock (' . $row['stock'] . ' kg)';
                                $status_text = ' In Stock';
                                $status_color = 'var(--clover)';
                                $disabled = '';
                            }
                        ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td class="product-name"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                            <td class="price-cell">
                                Rs. <?php echo number_format($price, 2); ?> 
                                <span class="kg-label">/ kg</span>
                            </td>
                            <td>
                                <span class="stock-badge <?php echo $stock_class; ?>">
                                    <?php echo $stock_text; ?>
                                </span>
                            </td>
                            <td style="color: <?php echo $status_color; ?>; font-weight: bold;">
                                <?php echo $status_text; ?>
                            </td>
                            <td>
                                <?php if($discount_percent > 0 && $row['stock'] > 0): ?>
                                    <span class="bulk-info"> <?php echo $discount_percent; ?>% off</span>
                                <?php else: ?>
                                    <span style="color:#999; font-size:12px;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['stock'] > 0): ?>
                                    <form method="POST" action="" style="display:flex; align-items:center; gap:5px;">
                                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                        <input type="number" name="quantity" class="qty-input" value="1" min="0.5" max="<?php echo $row['stock']; ?>" step="0.5">
                                    </form>
                                <?php else: ?>
                                    <span style="color:#999; font-size:12px;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:5px; flex-wrap:wrap;">
                                    <?php if($row['stock'] > 0): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="add_to_cart" class="btn-add-to-cart">
                                                <i class="fas fa-shopping-cart"></i> Add
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="out-of-stock-msg">
                                            <i class="fas fa-times-circle"></i> Not Available
                                        </span>
                                    <?php endif; ?>
                                    <button class="btn-edit" onclick='editProduct(<?php echo $row['id']; ?>, "<?php echo addslashes($row['name']); ?>", <?php echo $row['category_id'] ?? 'null'; ?>, <?php echo $row['price']; ?>, <?php echo $row['stock']; ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" onclick="deleteProduct(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding:40px;">
                                <i class="fas fa-box-open" style="font-size:48px; color:#ccc; display:block; margin-bottom:15px;"></i>
                                <h3 style="color:#666;">No Products Available</h3>
                                <p style="color:#999;">Click "Add Product" button below to add fresh fruits!</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- ✅ Table Footer - Add Button පහළින් -->
            <div class="table-footer">
                <button class="btn btn-add" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>
        </div>
        
        <div class="footer">
            <i class="fas fa-leaf"></i> <?php echo date('Y'); ?> Fruit Store Management System - Fresh Fruits Only
        </div>
    </div>

    <!-- ===== ADD MODAL ===== -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-plus-circle"></i> Add New Product</h3>
            <form method="POST">
                <label>Product Name</label>
                <input type="text" name="name" placeholder="Enter product name" required>
                <label>Category</label>
                <select name="category">
                    <option value="">Select Category</option>
                    <?php 
                    mysqli_data_seek($categories, 0);
                    while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <label>Price (Rs. per kg)</label>
                <input type="number" step="0.01" name="price" placeholder="Enter price" required>
                <label>Stock (kg)</label>
                <input type="number" step="0.5" name="stock" placeholder="Enter stock" required>
                <div class="modal-buttons">
                    <button type="submit" name="add_product" class="btn btn-save"><i class="fas fa-save"></i> Save</button>
                    <button type="button" class="btn btn-cancel" onclick="closeAddModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== EDIT MODAL ===== -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Edit Product</h3>
            <form method="POST">
                <input type="hidden" name="product_id" id="edit_id">
                <label>Product Name</label>
                <input type="text" name="name" id="edit_name" required>
                <label>Category</label>
                <select name="category" id="edit_category">
                    <option value="">Select Category</option>
                    <?php 
                    mysqli_data_seek($categories, 0);
                    while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <label>Price (Rs. per kg)</label>
                <input type="number" step="0.01" name="price" id="edit_price" required>
                <label>Stock (kg)</label>
                <input type="number" step="0.5" name="stock" id="edit_stock" required>
                <div class="modal-buttons">
                    <button type="submit" name="edit_product" class="btn btn-save"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-cancel" onclick="closeEditModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ✅ Add Modal
        function showAddModal() {
            document.getElementById('addModal').classList.add('active');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }

        // ✅ Edit Modal
        function editProduct(id, name, category, price, stock) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category').value = category || '';
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('editModal').classList.add('active');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // ✅ Delete Product
        function deleteProduct(id, name) {
            if(confirm('Are you sure you want to delete "' + name + '"?')) {
                window.location.href = '?delete=' + id;
            }
        }

        // ✅ Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>