<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Handle Add Product
if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = !empty($_POST['category']) ? intval($_POST['category']) : 'NULL';
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    
    $query = "INSERT INTO products (name, category_id, price, stock) VALUES ('$name', $category, $price, $stock)";
    if(mysqli_query($conn, $query)) {
        header("Location: products.php?msg=added");
        exit();
    }
}

// Handle Delete Product
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: products.php?msg=deleted");
    exit();
}

// Handle Update Stock
if(isset($_POST['update_stock'])) {
    $id = intval($_POST['product_id']);
    $stock = intval($_POST['stock']);
    mysqli_query($conn, "UPDATE products SET stock=$stock WHERE id=$id");
    header("Location: products.php?msg=stock_updated");
    exit();
}

// Handle Edit Product
if(isset($_POST['edit_product'])) {
    $id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    mysqli_query($conn, "UPDATE products SET name='$name', price=$price, stock=$stock WHERE id=$id");
    header("Location: products.php?msg=updated");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
if($search) {
    $products = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE '%$search%' ORDER BY p.id ASC");
} else {
    $products = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC");
}
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - Fruit Store</title>
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
            background: #f0f2f5;
            position: relative;
            min-height: 100vh;
        }
        
        /* ===== BACKGROUND IMAGE ===== */
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
        
        .logout { 
            background: var(--pine-tree); 
            border-radius: 8px; 
        }
        
        .logout:hover {
            background: #1a3d0a !important;
        }
        
        .container { padding: 20px; max-width: 1400px; margin: 0 auto; }
        
        /* ===== CARD ===== */
        .card { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            padding: 25px; 
            border-radius: 20px; 
            margin-bottom: 20px; 
            box-shadow: 0 5px 20px var(--shadow);
            border: 1px solid rgba(166, 194, 97, 0.2);
        }
        
        .card h2 {
            color: var(--pine-tree);
            margin-bottom: 15px;
        }
        
        /* ===== ALERT ===== */
        .alert {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #e8f5e9;
            color: var(--clover);
            border: 1px solid #a5d6a7;
        }
        
        /* ===== TABLE ===== */
        .table-wrapper {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 15px;
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
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        
        tr:hover {
            background: rgba(166, 194, 97, 0.1);
        }
        
        .price-cell {
            color: var(--asparagus);
            font-weight: bold;
        }
        
        /* ===== BUTTONS ===== */
        .btn { 
            padding: 6px 12px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            margin: 2px; 
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }
        
        .btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 4px 10px var(--shadow);
        }
        
        .btn-add { 
            background: var(--asparagus); 
            color: white; 
            padding: 10px 20px; 
        }
        
        .btn-add:hover {
            background: var(--dilley);
        }
        
        .btn-edit { 
            background: var(--green-smoke); 
            color: white; 
        }
        
        .btn-edit:hover {
            background: var(--dilley);
        }
        
        .btn-delete { 
            background: #c62828; 
            color: white; 
        }
        
        .btn-delete:hover {
            background: #b71c1c;
        }
        
        .btn-search { 
            background: var(--clover); 
            color: white; 
        }
        
        .btn-search:hover {
            background: var(--pine-tree);
        }
        
        /* ===== SEARCH ===== */
        .search-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .search-box { 
            padding: 10px 15px; 
            width: 280px; 
            border: 2px solid #ddd; 
            border-radius: 10px; 
            font-size: 14px;
        }
        
        .search-box:focus { 
            outline: none; 
            border-color: var(--asparagus); 
        }
        
        /* ===== MODAL ===== */
        .modal { 
            display: none; 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(19, 47, 6, 0.6); 
            z-index: 1000; 
        }
        
        .modal-content { 
            background: white; 
            margin: 8% auto; 
            padding: 25px; 
            width: 400px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px var(--shadow);
        }
        
        .modal-content h3 {
            color: var(--pine-tree);
            margin-bottom: 15px;
        }
        
        .modal-content input, .modal-content select { 
            width: 100%; 
            padding: 10px; 
            margin: 8px 0; 
            border: 1px solid #ddd; 
            border-radius: 8px;
        }
        
        .modal-content input:focus, .modal-content select:focus {
            outline: none;
            border-color: var(--asparagus);
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .modal-buttons .btn {
            flex: 1;
            justify-content: center;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        /* ===== FOOTER ===== */
        .footer {
            text-align: center;
            padding: 20px;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
        }
        
        .footer i {
            color: var(--celery);
        }
        
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 15px; text-align: center; }
            .container { padding: 10px; }
            .card { padding: 15px; }
            th, td { padding: 8px 6px; font-size: 11px; }
            .btn { padding: 4px 8px; font-size: 10px; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <i class="fas fa-apple-alt"></i> Fruit Store Management
        </div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="products.php"><i class="fas fa-boxes"></i> Products</a>
            <a href="categories.php"><i class="fas fa-tags"></i> Categories</a>
            <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
            <a href="billing.php"><i class="fas fa-receipt"></i> Billing</a>
            <a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="store.php"><i class="fas fa-store"></i> Store</a>
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i> Cart
                <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <span style="background:#ff9800; color:white; padding:2px 8px; border-radius:50%; font-size:11px; margin-left:5px;">
                        <?php echo array_sum($_SESSION['cart']); ?>kg
                    </span>
                <?php endif; ?>
            </a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-boxes"></i> Products Management</h2>
            
            <?php if($msg == 'added'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> ✅ Product added successfully!</div>
            <?php elseif($msg == 'updated'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> ✅ Product updated successfully!</div>
            <?php elseif($msg == 'deleted'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> ✅ Product deleted successfully!</div>
            <?php elseif($msg == 'stock_updated'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> ✅ Stock updated successfully!</div>
            <?php endif; ?>
            
            <div class="search-section">
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="text" name="search" class="search-box" placeholder="🔍 Search products..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-search"><i class="fas fa-search"></i> Search</button>
                    <?php if($search): ?>
                        <a href="products.php" class="btn btn-delete"><i class="fas fa-times"></i> Clear</a>
                    <?php endif; ?>
                </form>
                <button class="btn btn-add" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price (Rs.)</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($products) > 0): ?>
                            <?php 
                            $count = 1;
                            while($row = mysqli_fetch_assoc($products)): 
                            ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td>
                                    <?php if($row['category_name']): ?>
                                        <span style="background:#e8f5e9; padding:4px 8px; border-radius:12px; font-size:11px;">
                                            <?php echo htmlspecialchars($row['category_name']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:#999;">Uncategorized</span>
                                    <?php endif; ?>
                                </td>
                                <td class="price-cell">Rs. <?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <?php echo $row['stock']; ?>
                                    <?php if($row['stock'] <= 5 && $row['stock'] > 0): ?> 
                                        <i class="fas fa-exclamation-triangle" style="color:#ff9800;"></i>
                                    <?php elseif($row['stock'] <= 0): ?>
                                        <i class="fas fa-times-circle" style="color:#c62828;"></i>
                                    <?php else: ?>
                                        <i class="fas fa-box" style="color:var(--asparagus);"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['stock'] <= 0): ?>
                                        <span style="background:#ffebee; color:#c62828; padding:4px 10px; border-radius:20px; font-size:11px;">
                                            <i class="fas fa-times-circle"></i> Out of Stock
                                        </span>
                                    <?php elseif($row['stock'] <= 5): ?>
                                        <span style="background:#fff3e0; color:#e65100; padding:4px 10px; border-radius:20px; font-size:11px;">
                                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                                        </span>
                                    <?php else: ?>
                                        <span style="background:#e8f5e9; color:var(--clover); padding:4px 10px; border-radius:20px; font-size:11px;">
                                            <i class="fas fa-check-circle"></i> In Stock
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-edit" onclick='editProduct(<?php echo $row['id']; ?>, "<?php echo addslashes($row['name']); ?>", <?php echo $row['price']; ?>, <?php echo $row['stock']; ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-delete" onclick='deleteProduct(<?php echo $row['id']; ?>)'>
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <?php 
                            $count++;
                            endwhile; 
                            ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 50px;">
                                    <i class="fas fa-box-open" style="font-size: 48px; color: #ccc;"></i><br>
                                    No products found. Click "Add New Product" to get started.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="footer">
            <i class="fas fa-leaf"></i> <?php echo date('Y'); ?> Fruit Store Management System - Fresh Fruits Only
        </div>
    </div>
    
    <!-- Add Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-plus-circle"></i> Add New Product</h3>
            <form method="POST">
                <input type="text" name="name" placeholder="Product Name" required>
                <select name="category">
                    <option value="">Select Category</option>
                    <?php 
                    mysqli_data_seek($categories, 0);
                    while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="number" step="0.01" name="price" placeholder="Price (Rs.)" required>
                <input type="number" name="stock" placeholder="Stock Quantity" required>
                <div class="modal-buttons">
                    <button type="submit" name="add_product" class="btn btn-add"><i class="fas fa-save"></i> Save</button>
                    <button type="button" class="btn btn-delete" onclick="closeAddModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Edit Product</h3>
            <form method="POST">
                <input type="hidden" name="product_id" id="edit_id">
                <input type="text" name="name" id="edit_name" required>
                <input type="number" step="0.01" name="price" id="edit_price" required>
                <input type="number" name="stock" id="edit_stock" required>
                <div class="modal-buttons">
                    <button type="submit" name="edit_product" class="btn btn-add"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-delete" onclick="closeEditModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showAddModal() { document.getElementById('addModal').style.display = 'block'; }
        function closeAddModal() { document.getElementById('addModal').style.display = 'none'; }
        
        function editProduct(id, name, price, stock) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
        
        function deleteProduct(id) {
            if(confirm('Are you sure you want to delete this product?')) {
                window.location.href = '?delete=' + id;
            }
        }
        
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>