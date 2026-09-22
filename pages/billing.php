<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
include '../includes/header.php';

// Generate invoice number
$invoice_no = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY name");
$products = mysqli_query($conn, "SELECT p.*, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.stock > 0 ORDER BY p.name");
?>

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

    body {
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

    .billing-container {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 25px;
        margin-top: 20px;
    }
    
    .product-selector, .cart-items {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 5px 20px var(--shadow);
        border: 1px solid rgba(166, 194, 97, 0.2);
    }
    
    .product-selector h4, .cart-items h4 {
        color: var(--pine-tree);
        margin-bottom: 20px;
    }
    
    .product-selector h4 i, .cart-items h4 i {
        color: var(--asparagus);
    }
    
    .product-selector label, .cart-items label {
        font-weight: bold;
        color: #333;
        margin-top: 10px;
        display: block;
    }
    
    .product-selector label i, .cart-items label i {
        color: var(--asparagus);
        margin-right: 5px;
    }
    
    .product-selector input, .product-selector select {
        width: 100%;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 14px;
        margin-top: 5px;
        transition: all 0.3s;
    }
    
    .product-selector input:focus, .product-selector select:focus {
        outline: none;
        border-color: var(--asparagus);
        box-shadow: 0 0 0 3px rgba(116, 142, 72, 0.2);
    }
    
    .product-selector .btn, .cart-items .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 15px;
    }
    
    .btn-primary {
        background: var(--clover);
        color: white;
        width: 100%;
    }
    
    .btn-primary:hover {
        background: var(--pine-tree);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(19, 47, 6, 0.3);
    }
    
    .btn-success {
        background: var(--asparagus);
        color: white;
    }
    
    .btn-success:hover {
        background: var(--dilley);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(116, 142, 72, 0.3);
    }
    
    .btn-danger {
        background: #c62828;
        color: white;
    }
    
    .btn-danger:hover {
        background: #b71c1c;
        transform: translateY(-2px);
    }
    
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    
    .cart-table th {
        background: var(--clover);
        color: white;
        padding: 10px;
        text-align: left;
    }
    
    .cart-table td {
        padding: 10px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .cart-table tr:hover {
        background: rgba(166, 194, 97, 0.1);
    }
    
    .cart-total {
        font-size: 22px;
        font-weight: bold;
        color: var(--pine-tree);
        text-align: right;
        padding-top: 15px;
    }

    /* ✅ Discount Badge */
    .discount-badge {
        background: #ff9800;
        color: white;
        font-size: 11px;
        padding: 2px 10px;
        border-radius: 20px;
        margin-left: 8px;
        display: inline-block;
    }

    .original-price {
        text-decoration: line-through;
        color: #999;
        font-size: 14px;
        margin-right: 5px;
    }

    .final-price {
        color: #2E7D32;
        font-weight: bold;
        font-size: 16px;
    }

    /* ✅ Stock Alert Styles */
    .stock-alert {
        padding: 8px 15px;
        border-radius: 8px;
        margin-top: 10px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        display: none;
    }

    .stock-alert.warning {
        background: #fff3e0;
        color: #e65100;
        border-left: 4px solid #ff9800;
        display: flex;
    }

    .stock-alert.danger {
        background: #ffebee;
        color: #c62828;
        border-left: 4px solid #f44336;
        display: flex;
    }

    .stock-alert.success {
        background: #e8f5e9;
        color: #2e7d32;
        border-left: 4px solid #4CAF50;
        display: flex;
    }

    .stock-alert i {
        font-size: 18px;
    }
    
    .mt-3 {
        margin-top: 15px;
    }
    
    .mb-3 {
        margin-bottom: 15px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 14px;
        background: white;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--asparagus);
        box-shadow: 0 0 0 3px rgba(116, 142, 72, 0.2);
    }
    
    .form-control[readonly] {
        background: #f5f5f5;
    }
    
    hr {
        margin: 20px 0;
        border: none;
        border-top: 1px solid rgba(166, 194, 97, 0.3);
    }
    
    .actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .actions .btn {
        flex: 1;
        text-align: center;
    }
    
    .remove-btn {
        background: #c62828;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .remove-btn:hover {
        background: #b71c1c;
        transform: scale(1.05);
    }
    
    .kg-label {
        font-size: 12px;
        color: #999;
    }
    
    .price-display {
        font-weight: bold;
        color: var(--asparagus);
    }

    .product-selector small a {
        color: var(--asparagus) !important;
    }
    
    .product-selector small a:hover {
        color: var(--clover) !important;
        text-decoration: underline;
    }

    /* ✅ Blink Animation for Low Stock */
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }

    .blink-warning {
        animation: blink 1s infinite;
    }
    
    @media (max-width: 992px) {
        .billing-container {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .billing-container {
            grid-template-columns: 1fr;
        }
        .cart-table { font-size: 11px; }
        .cart-table th, .cart-table td { padding: 6px; }
    }
</style>

<div class="billing-container">
    <!-- Left Panel - Product Selector -->
    <div class="product-selector">
        <h4><i class="fas fa-shopping-cart"></i> New Invoice</h4>
        
        <div class="mb-3">
            <label><i class="fas fa-receipt"></i> Invoice No:</label>
            <input type="text" class="form-control" value="<?php echo $invoice_no; ?>" readonly>
        </div>

        <div class="mb-3">
            <label><i class="fas fa-warehouse"></i> Store:</label>
            <select name="store_id" id="store_id" class="form-control" required>
                <?php
                $store_check = mysqli_query($conn, "SHOW TABLES LIKE 'stores'");
                if(mysqli_num_rows($store_check) > 0) {
                    $res = mysqli_query($conn, "SELECT * FROM stores");
                    while($row = mysqli_fetch_assoc($res)) {
                        echo "<option value='".$row['id']."'>".$row['store_name']."</option>";
                    }
                } else {
                    echo "<option value='1'>Main Store</option>";
                }
                ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label><i class="fas fa-user"></i> Select Customer:</label>
            <select id="customer_id" class="form-control" required>
                <option value="">Select Customer</option>
                <?php while($customer = mysqli_fetch_assoc($customers)): ?>
                <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?> - <?php echo htmlspecialchars($customer['phone']); ?></option>
                <?php endwhile; ?>
            </select>
            <small><a href="customers.php">+ Add New Customer</a></small>
        </div>
        
        <div class="mb-3">
            <label><i class="fas fa-calendar-alt"></i> Invoice Date:</label>
            <input type="date" id="invoice_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <hr>
        <h5><i class="fas fa-plus-circle"></i> Add Items</h5>
        
        <div class="mb-3">
            <label><i class="fas fa-box"></i> Product:</label>
            <select id="product_id" class="form-control" onchange="updatePriceDisplay(); checkStockStatus();">
                <option value="">Select Product</option>
                <?php while($product = mysqli_fetch_assoc($products)): 
                    $stock = $product['stock'];
                    $stockStatus = '';
                    if($stock <= 2) {
                        $stockStatus = '⚠️ LOW STOCK!';
                    } elseif($stock <= 5) {
                        $stockStatus = '⚠️ Low Stock';
                    }
                ?>
                <option value="<?php echo $product['id']; ?>" 
                        data-price="<?php echo $product['price']; ?>" 
                        data-stock="<?php echo $stock; ?>"
                        data-name="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php echo htmlspecialchars($product['name']); ?> - Rs. <?php echo number_format($product['price'], 2); ?> / kg 
                    (Stock: <?php echo $stock; ?> kg <?php echo $stockStatus; ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- ✅ Stock Alert Display -->
        <div id="stockAlert" class="stock-alert">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="stockAlertMessage"></span>
        </div>
        
        <div class="mb-3">
            <label><i class="fas fa-weight-hanging"></i> Quantity (kg):</label>
            <input type="number" id="quantity" class="form-control" min="0.1" step="0.1" value="1.0" oninput="updatePriceDisplay(); checkStockStatus();">
        </div>

        <!-- ✅ Price Display with Discount -->
        <div id="priceDisplay" class="mb-3" style="display:none; background:#f5f5f5; padding:15px; border-radius:10px; border-left:4px solid var(--asparagus);">
            <div style="font-size:14px; color:#666;">
                <span id="priceInfo"></span>
            </div>
            <div style="font-size:20px; font-weight:bold; color:var(--pine-tree); margin-top:5px;">
                <span id="finalPriceDisplay">Rs. 0.00</span>
            </div>
            <div id="discountInfo" style="font-size:13px; color:#ff9800; margin-top:3px;"></div>
        </div>
        
        <button class="btn btn-primary" onclick="addToCart()">
            <i class="fas fa-plus"></i> Add to Cart
        </button>
    </div>
    
    <!-- Right Panel - Cart Items -->
    <div class="cart-items">
        <h4><i class="fas fa-receipt"></i> Cart Items</h4>
        <div id="cart-items">
            <p style="color:#999; text-align:center; padding:30px 0;">
                <i class="fas fa-shopping-cart" style="font-size:48px; display:block; color:#ccc;"></i>
                No items in cart
            </p>
        </div>
        
        <div class="mt-3">
            <div class="mb-3">
                <label><i class="fas fa-money-bill-wave"></i> Total Amount:</label>
                <input type="text" id="total_amount" class="form-control" value="Rs. 0.00" readonly>
            </div>
            
            <div class="actions">
                <button class="btn btn-danger" onclick="clearCart()">
                    <i class="fas fa-trash"></i> Clear
                </button>
                <button class="btn btn-success" onclick="submitInvoice()">
                    <i class="fas fa-save"></i> Save Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Cart array
let cart = JSON.parse(localStorage.getItem('billing_cart')) || [];

// ✅ Function to check stock status
function checkStockStatus() {
    const productSelect = document.getElementById('product_id');
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const stock = parseFloat(productSelect.options[productSelect.selectedIndex]?.dataset?.stock) || 0;
    const productName = productSelect.options[productSelect.selectedIndex]?.dataset?.name || '';
    const alertDiv = document.getElementById('stockAlert');
    const alertMsg = document.getElementById('stockAlertMessage');
    
    if (!productSelect.value) {
        alertDiv.style.display = 'none';
        return;
    }
    
    if (stock <= 0) {
        alertDiv.className = 'stock-alert danger';
        alertMsg.innerHTML = `<strong>${productName}</strong> is <strong>OUT OF STOCK!</strong>`;
        alertDiv.style.display = 'flex';
        return;
    }
    
    if (stock <= 2) {
        alertDiv.className = 'stock-alert danger blink-warning';
        alertMsg.innerHTML = `⚠️ <strong>${productName}</strong> has <strong>VERY LOW STOCK</strong> (${stock} kg remaining)!`;
        alertDiv.style.display = 'flex';
        return;
    }
    
    if (stock <= 5) {
        alertDiv.className = 'stock-alert warning';
        alertMsg.innerHTML = `⚠️ <strong>${productName}</strong> has <strong>LOW STOCK</strong> (${stock} kg remaining)`;
        alertDiv.style.display = 'flex';
        return;
    }
    
    if (quantity > stock) {
        alertDiv.className = 'stock-alert danger';
        alertMsg.innerHTML = `❌ <strong>Not enough stock!</strong> Available: ${stock} kg`;
        alertDiv.style.display = 'flex';
        return;
    }
    
    alertDiv.className = 'stock-alert success';
    alertMsg.innerHTML = `✅ <strong>${productName}</strong> is available (${stock} kg in stock)`;
    alertDiv.style.display = 'flex';
}

// ✅ Function to calculate discount based on quantity
function calculateDiscount(price, qty) {
    let discountPercent = 0;
    if (qty >= 10) {
        discountPercent = 15;
    } else if (qty >= 5) {
        discountPercent = 10;
    } else if (qty >= 2) {
        discountPercent = 5;
    }
    
    const discountAmount = price * (discountPercent / 100);
    const finalPrice = price - discountAmount;
    return { discountPercent, discountAmount, finalPrice };
}

// ✅ Update price display when product or quantity changes
function updatePriceDisplay() {
    const productSelect = document.getElementById('product_id');
    const productId = productSelect.value;
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const price = parseFloat(productSelect.options[productSelect.selectedIndex]?.dataset?.price) || 0;
    const productName = productSelect.options[productSelect.selectedIndex]?.dataset?.name || '';
    const stock = parseFloat(productSelect.options[productSelect.selectedIndex]?.dataset?.stock) || 0;
    
    const priceDisplay = document.getElementById('priceDisplay');
    
    if (!productId || quantity <= 0 || quantity > stock) {
        priceDisplay.style.display = 'none';
        return;
    }
    
    const { discountPercent, discountAmount, finalPrice } = calculateDiscount(price, quantity);
    const totalOriginal = price * quantity;
    const totalFinal = finalPrice * quantity;
    const totalDiscount = totalOriginal - totalFinal;
    
    document.getElementById('priceInfo').innerHTML = `
        <strong>${productName}</strong> × ${quantity.toFixed(1)} kg
        ${discountPercent > 0 ? `<span class="discount-badge">-${discountPercent}%</span>` : ''}
    `;
    
    let discountText = '';
    if (discountPercent > 0) {
        discountText = `🎉 ${discountPercent}% off! You save Rs. ${totalDiscount.toFixed(2)}`;
    } else {
        discountText = `💡 Buy ${quantity >= 5 ? 'more' : '5+ kg'} for discount!`;
    }
    document.getElementById('discountInfo').innerHTML = discountText;
    
    let priceHtml = '';
    if (discountPercent > 0) {
        priceHtml = `
            <span class="original-price">Rs. ${totalOriginal.toFixed(2)}</span>
            <span class="final-price">Rs. ${totalFinal.toFixed(2)}</span>
        `;
    } else {
        priceHtml = `Rs. ${totalOriginal.toFixed(2)}`;
    }
    document.getElementById('finalPriceDisplay').innerHTML = priceHtml;
    
    priceDisplay.style.display = 'block';
}

// Add to cart function
function addToCart() {
    const productSelect = document.getElementById('product_id');
    const productId = productSelect.value;
    const productName = productSelect.options[productSelect.selectedIndex]?.dataset?.name || '';
    const price = parseFloat(productSelect.options[productSelect.selectedIndex]?.dataset?.price) || 0;
    const stock = parseFloat(productSelect.options[productSelect.selectedIndex]?.dataset?.stock) || 0;
    const quantity = parseFloat(document.getElementById('quantity').value) || 1;
    
    if (!productId) {
        alert('Please select a product!');
        return;
    }
    
    if (quantity <= 0) {
        alert('Please enter a valid quantity!');
        return;
    }
    
    if (quantity > stock) {
        alert('Not enough stock! Available: ' + stock + ' kg');
        return;
    }
    
    // ✅ Calculate final price with discount
    const { discountPercent, finalPrice } = calculateDiscount(price, quantity);
    const totalPrice = finalPrice * quantity;
    
    // Check if product already in cart
    const existingIndex = cart.findIndex(item => item.id == productId);
    if (existingIndex >= 0) {
        const newQty = cart[existingIndex].qty + quantity;
        if (newQty > stock) {
            alert('Not enough stock! Available: ' + stock + ' kg');
            return;
        }
        cart[existingIndex].qty = newQty;
        cart[existingIndex].price = price;
        cart[existingIndex].finalPrice = calculateDiscount(price, newQty).finalPrice;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: price,
            finalPrice: finalPrice,
            qty: quantity,
            stock: stock,
            discountPercent: discountPercent
        });
    }
    
    localStorage.setItem('billing_cart', JSON.stringify(cart));
    updateCartDisplay();
    document.getElementById('quantity').value = 1.0;
    document.getElementById('priceDisplay').style.display = 'none';
    checkStockStatus();
}

// Update cart display with KG price and discount
function updateCartDisplay() {
    const container = document.getElementById('cart-items');
    const totalInput = document.getElementById('total_amount');
    let total = 0;
    
    if (cart.length === 0) {
        container.innerHTML = `
            <p style="color:#999; text-align:center; padding:30px 0;">
                <i class="fas fa-shopping-cart" style="font-size:48px; display:block; color:#ccc;"></i>
                No items in cart
            </p>
        `;
        totalInput.value = 'Rs. 0.00';
        return;
    }
    
    let html = `
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty (kg)</th>
                    <th>Price / kg</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    `;
    
    cart.forEach((item, index) => {
        const itemPrice = item.finalPrice || item.price;
        const subtotal = itemPrice * item.qty;
        total += subtotal;
        const discountBadge = item.discountPercent > 0 ? `<span class="discount-badge">-${item.discountPercent}%</span>` : '';
        html += `
            <tr>
                <td>${item.name} ${discountBadge}</td>
                <td>${item.qty.toFixed(1)}</td>
                <td>Rs. ${item.price.toFixed(2)} / kg</td>
                <td>Rs. ${subtotal.toFixed(2)}</td>
                <td>
                    <button class="remove-btn" onclick="removeItem(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
        <div class="cart-total">Total: Rs. ${total.toFixed(2)}</div>
    `;
    
    container.innerHTML = html;
    totalInput.value = 'Rs. ' + total.toFixed(2);
}

// Remove item from cart
function removeItem(index) {
    cart.splice(index, 1);
    localStorage.setItem('billing_cart', JSON.stringify(cart));
    updateCartDisplay();
}

// Clear cart
function clearCart() {
    if (!confirm('Are you sure you want to clear the cart?')) return;
    cart = [];
    localStorage.setItem('billing_cart', JSON.stringify(cart));
    updateCartDisplay();
}

// Submit invoice
function submitInvoice() {
    if (cart.length === 0) {
        alert('Cart is empty! Please add items.');
        return;
    }
    
    const customerId = document.getElementById('customer_id').value;
    const invoiceDate = document.getElementById('invoice_date').value;
    const storeId = document.getElementById('store_id')?.value || 1;
    const total = cart.reduce((sum, item) => sum + ((item.finalPrice || item.price) * item.qty), 0);
    
    if (!customerId) {
        alert('Please select a customer!');
        return;
    }
    
    if (!confirm('Save this invoice? Total: Rs. ' + total.toFixed(2))) return;
    
    const data = {
        customer_id: customerId,
        store_id: storeId,
        invoice_date: invoiceDate,
        items: cart,
        total: total
    };
    
    fetch('api/save_invoice.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✅ Invoice saved successfully!');
            cart = [];
            localStorage.setItem('billing_cart', JSON.stringify(cart));
            updateCartDisplay();
            window.location.reload();
        } else {
            alert('❌ Error: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('❌ Error saving invoice. Please try again.');
        console.error('Error:', error);
    });
}

// Update cart on page load
updateCartDisplay();

// ✅ Check stock status on page load
setTimeout(checkStockStatus, 500);
</script>

<?php include '../includes/footer.php'; ?>