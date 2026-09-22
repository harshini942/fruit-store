<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Add Supplier
if(isset($_POST['add_supplier'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $query = "INSERT INTO suppliers (name, phone, email, address) VALUES ('$name', '$phone', '$email', '$address')";
    mysqli_query($conn, $query);
    header("Location: suppliers.php");
    exit();
}

// Edit Supplier
if(isset($_POST['edit_supplier'])) {
    $id = intval($_POST['supplier_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $query = "UPDATE suppliers SET name='$name', phone='$phone', email='$email', address='$address' WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: suppliers.php");
    exit();
}

// Delete Supplier
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM suppliers WHERE id=$id");
    header("Location: suppliers.php");
    exit();
}

// Get suppliers
$suppliers = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Suppliers - Fruit Store</title>
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
            margin: 8% auto; 
            padding: 25px; 
            width: 450px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px var(--shadow);
        }
        
        .modal-content h3 {
            color: var(--pine-tree);
            margin-bottom: 15px;
        }
        
        .modal-content input, .modal-content textarea { 
            width: 100%; 
            padding: 10px; 
            margin: 8px 0; 
            border: 1px solid #ddd; 
            border-radius: 8px;
        }
        
        .modal-content input:focus, .modal-content textarea:focus {
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
            .card { padding: 15px; overflow-x: auto; }
            th, td { padding: 8px 6px; font-size: 11px; }
            .btn { padding: 4px 8px; font-size: 10px; }
            .modal-content { width: 90%; margin: 20% auto; }
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
    </div>
    
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-truck"></i> Suppliers Management</h2>
            <br>
            <button class="btn btn-add" onclick="showAddModal()"><i class="fas fa-plus"></i> Add New Supplier</button>
            <br><br>
            
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        while($row = mysqli_fetch_assoc($suppliers)): 
                        ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <button class="btn btn-edit" onclick="showEditModal(
                                    <?php echo $row['id']; ?>,
                                    '<?php echo addslashes($row['name']); ?>',
                                    '<?php echo addslashes($row['phone']); ?>',
                                    '<?php echo addslashes($row['email']); ?>',
                                    '<?php echo addslashes($row['address']); ?>'
                                )"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)"><i class="fas fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                        <?php 
                        $count++;
                        endwhile; 
                        ?>
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
            <h3><i class="fas fa-user-plus"></i> Add New Supplier</h3>
            <form method="POST">
                <input type="text" name="name" placeholder="Supplier Name" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="email" name="email" placeholder="Email">
                <textarea name="address" rows="3" placeholder="Address"></textarea>
                <div class="modal-buttons">
                    <button type="submit" name="add_supplier" class="btn btn-add"><i class="fas fa-save"></i> Save</button>
                    <button type="button" class="btn btn-delete" onclick="closeAddModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3><i class="fas fa-edit"></i> Edit Supplier</h3>
            <form method="POST">
                <input type="hidden" name="supplier_id" id="edit_id">
                <input type="text" name="name" id="edit_name" placeholder="Supplier Name" required>
                <input type="text" name="phone" id="edit_phone" placeholder="Phone Number" required>
                <input type="email" name="email" id="edit_email" placeholder="Email">
                <textarea name="address" id="edit_address" rows="3" placeholder="Address"></textarea>
                <div class="modal-buttons">
                    <button type="submit" name="edit_supplier" class="btn btn-add"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-delete" onclick="closeEditModal()"><i class="fas fa-times"></i> Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showAddModal() { document.getElementById('addModal').style.display = 'block'; }
        function closeAddModal() { document.getElementById('addModal').style.display = 'none'; }
        
        function showEditModal(id, name, phone, email, address) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_phone').value = phone;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_address').value = address;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
        
        function confirmDelete(id) {
            if(confirm('Are you sure you want to delete this supplier?')) {
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