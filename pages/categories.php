<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Add Category
if(isset($_POST['add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    $query = "INSERT INTO categories (name) VALUES ('$name')";
    if(mysqli_query($conn, $query)) {
        header("Location: categories.php?msg=added");
        exit();
    }
}

// Edit Category - Only Name
if(isset($_POST['edit_category'])) {
    $id = intval($_POST['category_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    $query = "UPDATE categories SET name='$name' WHERE id=$id";
    if(mysqli_query($conn, $query)) {
        header("Location: categories.php?msg=updated");
        exit();
    }
}

// Delete Category
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM categories WHERE id=$id";
    if(mysqli_query($conn, $query)) {
        header("Location: categories.php?msg=deleted");
        exit();
    }
}

// Get categories with product counts
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
if($search) {
    $categories = mysqli_query($conn, "SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id WHERE c.name LIKE '%$search%' GROUP BY c.id ORDER BY c.id ASC");
} else {
    $categories = mysqli_query($conn, "SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.id ASC");
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories - Fruit Store</title>
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
            position: relative;
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
        
        /* Navbar */
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
            border-bottom: 2px solid var(--asparagus);
        }
        
        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
            color: var(--celery);
        }
        
        .navbar .logo i {
            color: var(--green-smoke);
            margin-right: 10px;
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
        
        /* Card */
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
        
        /* Alert */
        .alert {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            background: #e8f5e9;
            color: var(--clover);
            border: 1px solid #a5d6a7;
        }
        
        /* Table Styles */
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
        
        /* Buttons */
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
        
        .btn:hover { transform: translateY(-2px); }
        
        .btn-add { 
            background: var(--asparagus); 
            color: white; 
            padding: 10px 20px; 
        }
        
        .btn-add:hover {
            background: var(--dilley);
            box-shadow: 0 4px 15px rgba(116, 142, 72, 0.3);
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
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
        }
        
        .btn-view { 
            background: var(--clover); 
            color: white; 
        }
        
        .btn-view:hover {
            background: var(--pine-tree);
        }
        
        .btn-search { 
            background: var(--clover); 
            color: white; 
        }
        
        .btn-search:hover {
            background: var(--pine-tree);
        }
        
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
        
        /* Modal */
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
            margin: 10% auto; 
            padding: 25px; 
            width: 400px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px var(--shadow);
        }
        
        .modal-content h3 {
            color: var(--pine-tree);
            margin-bottom: 15px;
        }
        
        .modal-content input { 
            width: 100%; 
            padding: 10px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 8px;
        }
        
        .modal-content input:focus {
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
        
        .product-count-badge {
            background: #e8f5e9;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            color: var(--clover);
        }
        
        .status-active {
            background: #e8f5e9;
            color: var(--clover);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
        }
        
        .status-empty {
            background: #fff3e0;
            color: #e65100;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
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
            .card { padding: 15px; }
            th, td { padding: 8px 6px; font-size: 11px; }
            .btn { padding: 4px 8px; font-size: 10px; }
            .modal-content { width: 90%; margin: 30% auto; }
        }
    </style>
</head>
<body>
    <!-- Navbar with Color Palette -->
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
    </div>
    
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-tags"></i> Categories Management</h2>
            
            <?php if($msg == 'added'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> ✅ Category added successfully!</div>
            <?php elseif($msg == 'updated'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> ✅ Category updated successfully!</div>
            <?php elseif($msg == 'deleted'): ?>
                <div class="alert"><i class="fas fa-check-circle"></i> ✅ Category deleted successfully!</div>
            <?php endif; ?>
            
            <div class="search-section">
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="text" name="search" class="search-box" placeholder="🔍 Search categories..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-search"><i class="fas fa-search"></i> Search</button>
                    <?php if($search): ?>
                        <a href="categories.php" class="btn btn-delete"><i class="fas fa-times"></i> Clear</a>
                    <?php endif; ?>
                </form>
                <button class="btn btn-add" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Add New Category
                </button>
            </div>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($categories) > 0): ?>
                            <?php 
                            $count = 1;
                            while($row = mysqli_fetch_assoc($categories)): 
                            ?>
                            <tr>
                                <td><?php echo $count; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td style="text-align: center;">
                                    <span class="product-count-badge">
                                        <?php echo $row['product_count']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['product_count'] > 0): ?>
                                        <span class="status-active">
                                            <i class="fas fa-check-circle"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="status-empty">
                                            <i class="fas fa-plus-circle"></i> Empty
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-edit" onclick='showEditModal(<?php echo $row['id']; ?>, "<?php echo addslashes($row['name']); ?>")'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-view" onclick='viewProducts(<?php echo $row['id']; ?>, "<?php echo addslashes($row['name']); ?>")'>
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-delete" onclick='confirmDelete(<?php echo $row['id']; ?>, "<?php echo addslashes($row['name']); ?>")'>
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
                                <td colspan="5" style="text-align: center; padding: 50px;">
                                    <i class="fas fa-tags" style="font-size: 48px; color: #ccc;"></i><br>
                                    No categories found. Click "Add New Category" to get started.
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
    
    <!-- Add Category Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-plus-circle"></i> Add New Category</h3>
            <form method="POST" action="">
                <label>Category Name:</label>
                <input type="text" name="name" placeholder="Enter category name" required>
                <div class="modal-buttons">
                    <button type="submit" name="add_category" class="btn btn-add"><i class="fas fa-save"></i> Save Category</button>
                    <button type="button" class="btn btn-delete" onclick="closeAddModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Category Modal - Only Name -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Edit Category</h3>
            <form method="POST" action="">
                <input type="hidden" name="category_id" id="edit_id">
                <label>Category Name:</label>
                <input type="text" name="name" id="edit_name" placeholder="Enter category name" required>
                <div class="modal-buttons">
                    <button type="submit" name="edit_category" class="btn btn-add"><i class="fas fa-save"></i> Update Category</button>
                    <button type="button" class="btn btn-delete" onclick="closeEditModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Add Modal Functions
        function showAddModal() { 
            document.getElementById('addModal').style.display = 'block'; 
        }
        function closeAddModal() { 
            document.getElementById('addModal').style.display = 'none'; 
        }
        
        // Edit Modal Functions
        function showEditModal(id, name) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeEditModal() { 
            document.getElementById('editModal').style.display = 'none'; 
        }
        
        // View Products Function
        function viewProducts(catId, catName) {
            window.location.href = 'products.php?category=' + catId;
        }
        
        // Delete Function
        function confirmDelete(id, name) {
            if(confirm('Are you sure you want to delete category "' + name + '"? This may affect related products.')) {
                window.location.href = '?delete=' + id;
            }
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>