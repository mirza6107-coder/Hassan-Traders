<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('db_connect.php');

if (!isset($conn) || !$conn) {
  die("Database connection failed.");
}

if (!isset($_SESSION['user_name'])) {
  header("Location: ../login and signup/login.php");
  exit();
}

// Get period from URL (default: this month)
$period = $_GET['period'] ?? 'month';
$days = ($period === '3months') ? 90 : (($period === '6months') ? 180 : 30);

// ==================== ADVANCED REPORT QUERIES ====================

// 1. Key Metrics
$metrics = [];

// Total Revenue (Delivered)
$query = "SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE status_ENUM = 'Delivered'";
$res = mysqli_query($conn, $query);
$metrics['total_revenue'] = (int)mysqli_fetch_assoc($res)['revenue'];

// Revenue in selected period
$query = "SELECT COALESCE(SUM(total_amount), 0) as revenue 
          FROM orders 
          WHERE status_ENUM = 'Delivered' 
          AND order_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)";
$res = mysqli_query($conn, $query);
$metrics['period_revenue'] = (int)mysqli_fetch_assoc($res)['revenue'];

// Orders Count
$query = "SELECT COUNT(*) as cnt FROM orders WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)";
$res = mysqli_query($conn, $query);
$metrics['orders_count'] = (int)mysqli_fetch_assoc($res)['cnt'];

// Average Order Value
$metrics['avg_order'] = $metrics['orders_count'] > 0 ? round($metrics['period_revenue'] / $metrics['orders_count']) : 0;

// Active Customers
$query = "SELECT COUNT(DISTINCT customer_id) as active 
          FROM orders 
          WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)";
$res = mysqli_query($conn, $query);
$metrics['active_customers'] = (int)mysqli_fetch_assoc($res)['active'];

// 2. Monthly Revenue Trend (Last 6 Months)
$monthly_trend = [];
$query = "
    SELECT 
        DATE_FORMAT(order_date, '%b %Y') as month_label,
        SUM(total_amount) as revenue,
        COUNT(*) as order_count
    FROM orders 
    WHERE status_ENUM = 'Delivered' 
    GROUP BY YEAR(order_date), MONTH(order_date)
    ORDER BY YEAR(order_date) DESC, MONTH(order_date) DESC 
    LIMIT 6";
$res = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($res)) {
  $monthly_trend[] = $row;
}
$monthly_trend = array_reverse($monthly_trend); // oldest to newest

// 3. Top 8 Selling Products
$top_products = [];
$query = "
    SELECT oi.product_name, SUM(oi.quantity) as total_qty, SUM(oi.quantity * oi.price) as total_revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status_ENUM = 'Delivered'
    GROUP BY oi.product_name
    ORDER BY total_revenue DESC LIMIT 8";
$res = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($res)) {
  $top_products[] = $row;
}

// 4. Sales by City
$city_sales = [];
$query = "
    SELECT 
        c.city,
        COUNT(*) as order_count,
        SUM(o.total_amount) as revenue
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
    WHERE o.status_ENUM = 'Delivered'
    GROUP BY c.city
    ORDER BY revenue DESC
    LIMIT 6";
$res = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($res)) {
  $city_sales[] = $row;
}

// 5. Order Status Distribution
// Order Status Distribution Query
$status_dist = [];
$query = "SELECT status_ENUM, COUNT(*) as count FROM orders GROUP BY status_ENUM";
$res = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($res)) {
  // This will store keys like 'Delivered', 'Pending', etc.
  $status_dist[$row['status_ENUM']] = (int)$row['count'];
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Advanced Reports - Hassan Traders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="reports.css">
</head>

<body>
  <?php include('sidebar.php'); ?>

  <main class="main">
    <div class="topbar">
      <div class="topbar-title">
        <h4>Advanced Business Reports</h4>
        <p>Real-time analytics • Updated <?php echo date('d M Y, h:i A'); ?></p>
      </div>

      <!-- Period Selector -->
      <div class="btn-group">
        <a href="?period=month" class="btn  <?php echo $period === 'month' ? 'btn-primary' : 'btn-outline-secondary'; ?>">This Month</a>
        <a href="?period=3months" class="btn  <?php echo $period === '3months' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Last 3 Months</a>
        <a href="?period=6months" class="btn  <?php echo $period === '6months' ? 'btn-primary' : 'btn-outline-secondary'; ?>">Last 6 Months</a>
      </div>

      <button class="btn-primary-custom" onclick="window.print()">
        <i class="bi bi-printer"></i> Print / Export
      </button>
    </div>

    <div class="content">
      <!-- Key Metrics -->
      <div class="stat-row">
        <div class="stat-card">
          <div class="stat-label">Revenue (<?php echo ucfirst($period); ?>)</div>
          <div class="stat-val">Rs. <?php echo number_format($metrics['period_revenue']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Orders</div>
          <div class="stat-val"><?php echo number_format($metrics['orders_count']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Avg. Order Value</div>
          <div class="stat-val">Rs. <?php echo number_format($metrics['avg_order']); ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Active Customers</div>
          <div class="stat-val"><?php echo number_format($metrics['active_customers']); ?></div>
        </div>
      </div>

      <div class="charts-grid">
        <!-- Monthly Revenue Trend -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5>Revenue Trend (Last 6 Months)</h5>
          </div>
          <div class="card-panel-body">
            <canvas id="revenueTrendChart" height="110"></canvas>
          </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5>Order Status Distribution</h5>
          </div>
          <div class="card-panel-body">
            <canvas id="statusChart" height="200"></canvas>
          </div>
        </div>
      </div>

      <div class="charts-grid-2">
        <!-- Top Products -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5>Top Selling Products</h5>
          </div>
          <div class="card-panel-body" style="padding: 16px 20px; max-height: 420px; overflow-y: auto;">
            <?php foreach ($top_products as $i => $p): ?>
              <div class="top-prod-item">
                <div class="top-prod-rank <?php echo $i == 0 ? 'gold' : ($i == 1 ? 'silver' : ($i == 2 ? 'bronze' : '')); ?>"><?php echo $i + 1; ?></div>
                <div class="top-prod-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
                <div class="top-prod-rev">Rs. <?php echo number_format($p['total_revenue']); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Sales by City -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5>Sales by City</h5>
          </div>
          <div class="card-panel-body">
            <?php
            $max_rev = !empty($city_sales) ? $city_sales[0]['revenue'] : 1;
            foreach ($city_sales as $city):
              $pct = round(($city['revenue'] / $max_rev) * 100);
            ?>
              <div class="city-row">
                <span class="city-name"><?php echo htmlspecialchars($city['city'] ?? 'Unknown'); ?></span>
                <div class="city-bar">
                  <div class="city-fill" style="width: <?php echo $pct; ?>%"></div>
                </div>
                <span class="city-pct"><?php echo $pct; ?>%</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    // Monthly Revenue Trend
    new Chart(document.getElementById("revenueTrendChart"), {
      type: "line",
      data: {
        labels: <?php echo json_encode(array_column($monthly_trend, 'month_label')); ?>,
        datasets: [{
          label: "Revenue (Rs.)",
          data: <?php echo json_encode(array_column($monthly_trend, 'revenue')); ?>,
          borderColor: "#c0392b",
          backgroundColor: "rgba(192,57,43,0.1)",
          tension: 0.4,
          borderWidth: 3,
          pointRadius: 5
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            ticks: {
              callback: v => "Rs. " + (v / 1000000).toFixed(1) + "M"
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

    // Order Status Distribution
    new Chart(document.getElementById("statusChart"), {
      type: "doughnut",
      data: {
        labels: ["Delivered", "Pending", "Processing", "Shipped", "Cancelled"],
        datasets: [{
          data: [
            <?php echo $status_dist['Delivered'] ?? 0; ?>,
            <?php echo $status_dist['Pending'] ?? 0; ?>,
            <?php echo $status_dist['Processing'] ?? 0; ?>,
            <?php echo $status_dist['Shipped'] ?? 0; ?>,
            <?php echo $status_dist['Cancelled'] ?? 0; ?>
          ],
          backgroundColor: ["#27ae60", "#f39c12", "#2980b9", "#3498db", "#e74c3c"]
        }]
      },
      options: {
        responsive: true,
        cutout: "60%",
        plugins: {
          legend: {
            position: "bottom"
          }
        }
      }
    });
  </script>
</body>

</html>