// js/script.js

// Load products for billing
function loadProducts() {
    $.ajax({
        url: '/fruit-store/api/get_products.php',
        method: 'GET',
        success: function(response) {
            let products = JSON.parse(response);
            let html = '<option value="">Select Product</option>';
            products.forEach(product => {
                html += `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">${product.name} - Rs. ${product.price} (Stock: ${product.stock})</option>`;
            });
            $('#product_id').html(html);
        }
    });
}

// Add item to cart
function addToCart() {
    let productId = $('#product_id').val();
    let productName = $('#product_id option:selected').text();
    let quantity = $('#quantity').val();
    let price = $('#product_id option:selected').data('price');
    
    if (!productId || quantity <= 0) {
        alert('Please select product and enter quantity');
        return;
    }
    
    let cartItem = {
        id: productId,
        name: productName,
        quantity: parseInt(quantity),
        price: parseFloat(price),
        total: parseFloat(quantity) * parseFloat(price)
    };
    
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    cart.push(cartItem);
    localStorage.setItem('cart', JSON.stringify(cart));
    
    updateCartDisplay();
    $('#quantity').val('');
    $('#product_id').val('');
}

// Update cart display
function updateCartDisplay() {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    let html = '<table class="table"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr></thead><tbody>';
    let total = 0;
    
    cart.forEach((item, index) => {
        html += `<tr>
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>Rs. ${item.price}</td>
            <td>Rs. ${item.total}</td>
            <td><button onclick="removeFromCart(${index})" class="btn btn-danger btn-sm">Remove</button></td>
        </tr>`;
        total += item.total;
    });
    
    html += `</tbody><tfoot><tr><td colspan="3"><strong>Total</strong></td><td><strong>Rs. ${total}</strong></td><td></td></tr></tfoot></table>`;
    $('#cart-items').html(html);
    $('#total_amount').val(total);
}

// Remove from cart
function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

// Clear cart
function clearCart() {
    localStorage.removeItem('cart');
    updateCartDisplay();
}

// Submit invoice
// Submit invoice
function submitInvoice() {
    let store_id = $('#store_id').val();
    let customerId = $('#customer_id').val();
    let invoiceDate = $('#invoice_date').val();
    let cart = JSON.parse(localStorage.getItem('cart') || '[]');
    
    // මෙතනදී store_id එකත් පරීක්ෂා කරනවා (Select කරලද කියලා)
    if (!customerId || !invoiceDate || cart.length === 0 || !store_id) {
        alert('Please select customer, date, store and add items to cart');
        return;
    }
    
    $.ajax({
        url: '/fruit-store/api/save_invoice.php',
        method: 'POST',
        data: {
            customer_id: customerId,
            invoice_date: invoiceDate,
            cart: JSON.stringify(cart),
            store_id: store_id, // මේක අනිවාර්යයි
            total: $('#total_amount').val()
        },
        success: function(response) {
            let result = JSON.parse(response);
            if (result.success) {
                alert('Invoice saved successfully! Invoice No: ' + result.invoice_no);
                clearCart();
                window.print();
                location.reload(); // පේජ් එක අලුත් කරන්න
            } else {
                alert('Error: ' + result.message);
            }
        }
    });
}

// Load low stock alerts
function loadAlerts() {
    $.ajax({
        url: '/fruit-store/api/get_alerts.php',
        method: 'GET',
        success: function(response) {
            let alerts = JSON.parse(response);
            let html = '';
            alerts.forEach(alert => {
                html += `<div class="alert alert-warning">⚠️ ${alert.message}</div>`;
            });
            $('#alerts-container').html(html);
        }
    });
}

// Load charts for reports
function loadSalesChart() {
    $.ajax({
        url: '/fruit-store/api/get_sales_data.php',
        method: 'GET',
        success: function(response) {
            let data = JSON.parse(response);
            
            // Bar Chart
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Sales (Rs.)',
                        data: data.values,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Pie Chart
            const ctxPie = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: data.categoryLabels,
                    datasets: [{
                        data: data.categoryValues,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
                    }]
                }
            });
        }
    });
}

// Search products
function searchProducts() {
    let search = $('#search').val();
    $.ajax({
        url: '/fruit-store/api/search_products.php',
        method: 'GET',
        data: { search: search },
        success: function(response) {
            $('#products-table-body').html(response);
        }
    });
}

// Update stock
function updateStock(productId, newStock) {
    $.ajax({
        url: '/fruit-store/api/update_stock.php',
        method: 'POST',
        data: {
            product_id: productId,
            stock: newStock
        },
        success: function(response) {
            let result = JSON.parse(response);
            if (result.success) {
                alert('Stock updated successfully!');
                location.reload();
            } else {
                alert('Error updating stock');
            }
        }
    });
}

// Auto refresh alerts every 30 seconds
setInterval(loadAlerts, 30000);

$(document).ready(function() {
    if ($('#product_id').length) {
        loadProducts();
    }
    
    if ($('#alerts-container').length) {
        loadAlerts();
    }
    
    if ($('#salesChart').length) {
        loadSalesChart();
    }
});