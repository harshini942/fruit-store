<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
include '../includes/header.php';

// Get filter parameters
$report_type = $_GET['type'] ?? 'daily';
$date = $_GET['date'] ?? date('Y-m-d');
$month = $_GET['month'] ?? date('Y-m');

// Use 'total' column (from your invoices table)
$total_column = 'total';

// Get sales data based on report type
if ($report_type == 'daily') {
    $sales_query = "SELECT i.*, c.name as customer_name 
                    FROM invoices i 
                    LEFT JOIN customers c ON i.customer_id = c.id 
                    WHERE DATE(i.invoice_date) = '$date' 
                    ORDER BY i.id DESC";
    // ✅ Date format: 8/7/2026
    $title = "Daily Sales Report - " . date('j/n/Y', strtotime($date));
    $total_result = mysqli_query($conn, "SELECT SUM($total_column) as total FROM invoices WHERE DATE(invoice_date) = '$date'");
    $total_sales = ($total_result && mysqli_num_rows($total_result) > 0) ? mysqli_fetch_assoc($total_result)['total'] : 0;
} elseif ($report_type == 'weekly') {
    $sales_query = "SELECT i.*, c.name as customer_name 
                    FROM invoices i 
                    LEFT JOIN customers c ON i.customer_id = c.id 
                    WHERE YEARWEEK(i.invoice_date) = YEARWEEK('$date') 
                    ORDER BY i.id DESC";
    $title = "Weekly Sales Report - Week " . date('W', strtotime($date));
    $total_result = mysqli_query($conn, "SELECT SUM($total_column) as total FROM invoices WHERE YEARWEEK(invoice_date) = YEARWEEK('$date')");
    $total_sales = ($total_result && mysqli_num_rows($total_result) > 0) ? mysqli_fetch_assoc($total_result)['total'] : 0;
} else {
    $sales_query = "SELECT i.*, c.name as customer_name 
                    FROM invoices i 
                    LEFT JOIN customers c ON i.customer_id = c.id 
                    WHERE DATE_FORMAT(i.invoice_date, '%Y-%m') = '$month' 
                    ORDER BY i.id DESC";
    $title = "Monthly Sales Report - " . date('F Y', strtotime($month . '-01'));
    $total_result = mysqli_query($conn, "SELECT SUM($total_column) as total FROM invoices WHERE DATE_FORMAT(invoice_date, '%Y-%m') = '$month'");
    $total_sales = ($total_result && mysqli_num_rows($total_result) > 0) ? mysqli_fetch_assoc($total_result)['total'] : 0;
}

// Execute main query with error checking
$sales = mysqli_query($conn, $sales_query);
if (!$sales) {
    $error = "Query failed: " . mysqli_error($conn);
    $sales = [];
}

// ✅ Get category sales data
$category_sales_query = "SELECT 
                            COALESCE(c.name, 'Uncategorized') AS category_name,
                            SUM(ii.total) AS total_sales
                         FROM invoice_items ii 
                         JOIN products p ON ii.product_id = p.id 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         GROUP BY p.category_id 
                         ORDER BY total_sales DESC";

$category_sales = mysqli_query($conn, $category_sales_query);
if (!$category_sales) {
    $error = "Category query failed: " . mysqli_error($conn);
    $category_sales = [];
}

// Store chart data in PHP arrays
$chart_labels = [];
$chart_data = [];
if (isset($category_sales) && is_object($category_sales) && mysqli_num_rows($category_sales) > 0) {
    while ($cat = mysqli_fetch_assoc($category_sales)) {
        $chart_labels[] = $cat['category_name'] ?? 'Uncategorized';
        $chart_data[] = floatval($cat['total_sales'] ?? 0);
    }
}

// If no data, add placeholder
if (empty($chart_labels)) {
    $chart_labels[] = 'No Data';
    $chart_data[] = 0;
}
?>

<style>
    /* ============================================================
       COLOR PALETTE
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

    .card {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px) !important;
        border-radius: 20px !important;
        box-shadow: 0 5px 20px var(--shadow) !important;
        border: 1px solid rgba(166, 194, 97, 0.2) !important;
        transition: all 0.3s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(19, 47, 6, 0.25) !important;
    }

    .card-body h4, .card-body h5 {
        color: var(--pine-tree) !important;
    }

    .card-body .text-success {
        color: var(--asparagus) !important;
    }

    .form-control {
        border: 2px solid #e0e0e0 !important;
        border-radius: 10px !important;
        padding: 10px 15px !important;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none !important;
        border-color: var(--asparagus) !important;
        box-shadow: 0 0 0 3px rgba(116, 142, 72, 0.2) !important;
    }

    .table thead th {
        background: var(--clover) !important;
        color: white !important;
        padding: 14px 12px !important;
        border: none !important;
    }

    .table tbody td {
        padding: 12px !important;
        border-bottom: 1px solid #e0e0e0 !important;
        vertical-align: middle !important;
    }

    .table tbody tr:hover {
        background: rgba(166, 194, 97, 0.1) !important;
    }

    label {
        color: var(--pine-tree) !important;
        font-weight: 600 !important;
    }

    hr {
        border-color: rgba(166, 194, 97, 0.3) !important;
    }

    .text-center i {
        color: #ccc !important;
    }

    .text-center strong {
        color: var(--pine-tree) !important;
    }

    @media (max-width: 768px) {
        .card {
            padding: 15px !important;
        }
        .table {
            font-size: 12px !important;
        }
        th, td {
            padding: 8px 6px !important;
        }
    }
</style>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5><i class="fas fa-filter" style="color:var(--asparagus);"></i> Filter Reports</h5>
                <form method="GET" action="">
                    <div class="mb-3">
                        <label>Report Type</label>
                        <select name="type" class="form-control" onchange="this.form.submit()">
                            <option value="daily" <?php echo $report_type == 'daily' ? 'selected' : ''; ?>>Daily</option>
                            <option value="weekly" <?php echo $report_type == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                            <option value="monthly" <?php echo $report_type == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                        </select>
                    </div>
                    
                    <?php if($report_type == 'daily'): ?>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" onchange="this.form.submit()">
                    </div>
                    <?php elseif($report_type == 'weekly'): ?>
                    <div class="mb-3">
                        <label>Date (Start of Week)</label>
                        <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" onchange="this.form.submit()">
                    </div>
                    <?php elseif($report_type == 'monthly'): ?>
                    <div class="mb-3">
                        <label>Month</label>
                        <input type="month" name="month" class="form-control" value="<?php echo $month; ?>" onchange="this.form.submit()">
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <h4><i class="fas fa-chart-line" style="color:var(--asparagus);"></i> <?php echo $title; ?></h4>
                <h5 class="text-success">Total Sales: Rs. <?php echo number_format($total_sales ?? 0, 2); ?></h5>
                <hr>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($sales) && is_object($sales) && mysqli_num_rows($sales) > 0): ?>
                                <?php while($sale = mysqli_fetch_assoc($sales)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sale['invoice_no'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer'); ?></td>
                                    <td><?php echo date('j/n/Y', strtotime($sale['invoice_date'] ?? date('Y-m-d'))); ?></td>
                                    <td><strong style="color:var(--asparagus);">Rs. <?php echo number_format($sale['total'] ?? 0, 2); ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-receipt" style="font-size:40px; color:#ccc; display:block; margin-bottom:10px;"></i>
                                        <strong style="color:var(--pine-tree);">No sales found</strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 style="color:var(--pine-tree);"><i class="fas fa-chart-bar" style="color:var(--asparagus);"></i> Sales by Category</h5>
                <canvas id="salesChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 style="color:var(--pine-tree);"><i class="fas fa-chart-pie" style="color:var(--asparagus);"></i> Category Distribution</h5>
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart Data from PHP
const categoryNames = <?php echo json_encode($chart_labels); ?>;
const categoryTotals = <?php echo json_encode($chart_data); ?>;

// Bar Chart - Sales by Category
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: categoryNames,
        datasets: [{
            label: 'Sales (Rs.)',
            data: categoryTotals,
            backgroundColor: ['#132F06', '#3D5316', '#5B7341', '#748E48', '#94AA64', '#A6C261', '#C9CBCF'],
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rs. ' + context.raw.toFixed(2);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rs. ' + value;
                    }
                }
            }
        }
    }
});

// Pie Chart - Category Distribution
const ctxPie = document.getElementById('categoryChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: categoryNames,
        datasets: [{
            data: categoryTotals,
            backgroundColor: ['#132F06', '#3D5316', '#5B7341', '#748E48', '#94AA64', '#A6C261', '#C9CBCF']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                        return context.label + ': Rs. ' + context.raw.toFixed(2) + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>