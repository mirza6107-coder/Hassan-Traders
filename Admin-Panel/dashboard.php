<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db_connect.php');

if (!isset($conn) || !$conn) {
  die("❌ Database connection failed. Please check db_connect.php");
}

if (!isset($_SESSION['user_name'])) {
  header("Location: ../login and signup/login.php");
  exit();
}

// ====================== AUTO DETECT COLUMN NAMES ======================
$cols = [];
$r = mysqli_query($conn, "SHOW COLUMNS FROM products");
while ($c = mysqli_fetch_assoc($r)) {
  $cols[] = $c['Field'];
}

$idCol   = in_array('ID', $cols) ? 'ID' : 'id';
$nameCol = in_array('P_name', $cols) ? 'P_name' : (in_array('name', $cols) ? 'name' : 'P_name');
$qtyCol  = in_array('Quantity', $cols) ? 'Quantity' : (in_array('stock', $cols) ? 'stock' : 'Quantity');

// ====================== 1. Total Products ======================
$total_products = 0;
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
if ($result) {
  $row = mysqli_fetch_assoc($result);
  $total_products = (int)($row['total'] ?? 0);
}

// ====================== 2. Low Stock Items ======================
$low_stock_count = 0;
$low_stock_items = [];

$stock_query = "SELECT `$idCol` as id, `$nameCol` as name, `$qtyCol` as Quantity 
                FROM products 
                WHERE `$qtyCol` < 10 
                ORDER BY `$qtyCol` ASC LIMIT 5";

$stock_result = mysqli_query($conn, $stock_query);
if ($stock_result) {
  $low_stock_count = mysqli_num_rows($stock_result);
  while ($item = mysqli_fetch_assoc($stock_result)) {
    $low_stock_items[] = $item;
  }
}

// ====================== 3. Total Revenue ======================
$total_revenue = 0;
$rev_query = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue 
              FROM orders WHERE status_ENUM = 'Delivered'";

$rev_result = mysqli_query($conn, $rev_query);
if ($rev_result) {
  $row = mysqli_fetch_assoc($rev_result);
  $total_revenue = (int)($row['total_revenue'] ?? 0);
}

// ====================== 4. Total Orders & Pending ======================
$total_orders = 0;
$pending_orders = 0;

$q1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
if ($q1) {
  $row = mysqli_fetch_assoc($q1);
  $total_orders = (int)($row['total'] ?? 0);
}

$q2 = mysqli_query($conn, "SELECT COUNT(*) as pending FROM orders WHERE status_ENUM = 'Pending'");
if ($q2) {
  $row = mysqli_fetch_assoc($q2);
  $pending_orders = (int)($row['pending'] ?? 0);
}

// ====================== 5. Recent Orders ======================
$orders_result = mysqli_query($conn, "
    SELECT o.id, o.total_amount, o.status_ENUM as status, o.order_date, c.name as customer_name 
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    ORDER BY o.order_date DESC LIMIT 5
");

$statusMap = [
  'Pending'    => ['Pending', 'badge-pending'],
  'Processing' => ['Processing', 'badge-processing'],
  'Shipped'    => ['Shipped', 'badge-shipped'],
  'Delivered'  => ['Delivered', 'badge-delivered'],
  'Cancelled'  => ['Cancelled', 'badge-cancelled']
];
// Query to get daily revenue for the last 30 days
$chart_query = "SELECT DATE(order_date) as date, SUM(total_amount) as daily_revenue 
                FROM orders 
                WHERE status_ENUM = 'Delivered' 
                AND order_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(order_date)
                ORDER BY date ASC";

$chart_result = mysqli_query($conn, $chart_query);

$dates = [];
$revenues = [];

while ($row = mysqli_fetch_assoc($chart_result)) {
  // Format date for the chart (e.g., "Apr 29")
  $dates[] = date('M d', strtotime($row['date']));
  $revenues[] = (float)$row['daily_revenue'];
}

// Convert PHP arrays to JSON for JavaScript use
$json_dates = json_encode($dates);
$json_revenues = json_encode($revenues);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Hassan Traders Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="dashboard.css" />
</head>

<body>
  <?php include('sidebar.php'); ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-title">
        <h4>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>!</h4>
        <p id="topbarDate">Hassan Traders • Admin Dashboard</p>
      </div>
      <div class="topbar-actions">
        <span class="badge-live">● Live</span>
        <a href="addNEWproducts.php" class="btn-primary-custom">
          <i class="bi bi-plus-circle"></i> Add Product
        </a>
      </div>
    </div>

    <div class="content">

      <!-- Low Stock Alert -->
      <?php if ($low_stock_count > 0): ?>
        <div class="alert-low-stock">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span>
            <strong><?php echo $low_stock_count; ?> items</strong> are low in stock.
            <?php if (!empty($low_stock_items)): ?>
              Priority: <?php echo htmlspecialchars($low_stock_items[0]['name'] ?? 'Product'); ?>
              (<?php echo $low_stock_items[0]['Quantity'] ?? 0; ?> left)
            <?php endif; ?>
            <a href="inventory.php" style="color: var(--primary); font-weight: 600;">View Inventory →</a>
          </span>
        </div>
      <?php endif; ?>

      <!-- Stat Cards -->
      <div class="stat-cards">
        <div class="stat-card">
          <div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">Rs. <?php echo number_format($total_revenue); ?></div>
            <div class="stat-change">Delivered Orders</div>
          </div>
          <div class="stat-icon icon-red"><i class="bi bi-currency-rupee"></i></div>
        </div>

        <div class="stat-card">
          <div>
            <div class="stat-label">Total Products</div>
            <div class="stat-value"><?php echo number_format($total_products); ?></div>
            <div class="stat-change">In Catalog</div>
          </div>
          <div class="stat-icon icon-green"><i class="bi bi-box-seam"></i></div>
        </div>

        <div class="stat-card">
          <div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value"><?php echo number_format($total_orders); ?></div>
            <div class="stat-change"><?php echo $pending_orders; ?> Pending</div>
          </div>
          <div class="stat-icon icon-blue"><i class="bi bi-bag-check"></i></div>
        </div>

        <div class="stat-card">
          <div>
            <div class="stat-label">Low Stock Items</div>
            <div class="stat-value"><?php echo $low_stock_count; ?></div>
            <div class="stat-change down">Needs Attention</div>
          </div>
          <div class="stat-icon icon-orange"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
      </div>


      <!-- Charts + Recent Orders -->
      <div class="charts-row">
        <div class="card-panel">
          <div class="card-panel-header">
            <h5>Revenue Overview (Last 30 Days)</h5>
            <a href="reports.php" class="link-red">Full Report →</a>
          </div>
          <div class="card-panel-body">
            <canvas id="revenueChart" height="100"></canvas>
          </div>
        </div>

        <div class="card-panel">
          <div class="card-panel-header">
            <h5>Recent Orders</h5>
            <a href="orders.php" class="link-red">View All</a>
          </div>
          <div id="recentOrdersList">
            <?php if ($orders_result && mysqli_num_rows($orders_result) > 0): ?>
              <?php while ($order = mysqli_fetch_assoc($orders_result)):
                $dbStatus = $order['status'];
                $st = $statusMap[$dbStatus] ?? ['Pending', 'badge-pending'];
              ?>
                <div class="order-item">
                  <div>
                    <div class="order-id">#ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></div>
                    <div class="order-desc">
                      <?php echo htmlspecialchars($order['customer_name'] ?? 'Customer'); ?> •
                      <?php echo date('d M Y', strtotime($order['order_date'] ?? 'now')); ?>
                    </div>
                    <div class="order-amount">Rs. <?php echo number_format($order['total_amount'] ?? 0); ?></div>
                  </div>
                  <span class="badge-status <?php echo $st[1]; ?>">
                    <?php echo $st[0]; ?>
                  </span>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="text-muted p-3">No recent orders yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    document.getElementById("topbarDate").textContent =
      "Hassan Traders • " + new Date().toLocaleDateString("en-PK", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
      });

    const ctx = document.getElementById("revenueChart").getContext("2d");
    new Chart(ctx, {
      type: "line",
      data: {
        // These variables now come from your PHP query
        labels: <?php echo $json_dates; ?>,
        datasets: [{
          label: "Revenue (Rs.)",
          data: <?php echo $json_revenues; ?>,
          borderColor: "#c0392b",
          backgroundColor: "rgba(192,57,43,0.08)",
          tension: 0.4,
          borderWidth: 3,
          pointRadius: 4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            ticks: {
              callback: v => "Rs. " + (v / 1000) + "k"
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
  </script>
</body>

</html>