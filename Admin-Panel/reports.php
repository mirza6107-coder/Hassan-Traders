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
$monthly_trend = array_reverse($monthly_trend);

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
$status_dist = [];
$query = "SELECT status_ENUM, COUNT(*) as count FROM orders GROUP BY status_ENUM";
$res = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($res)) {
  $status_dist[$row['status_ENUM']] = (int)$row['count'];
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Advanced Reports — Hassan Traders</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="reports.css" />
</head>

<body>
  <?php include('sidebar.php'); ?>

  <main class="main">

    <!-- ── TOPBAR ── -->
    <div class="topbar">
      <div class="topbar-title">
        <h4><span>Hassan Traders</span> — Reports</h4>
        <p>
          <span class="live-dot"></span>
          Live data &nbsp;·&nbsp; <?php echo date('d M Y, h:i A'); ?>
        </p>
      </div>

      <!-- Period Selector -->
      <div class="period-selector">
        <a href="?period=month"   class="<?php echo $period === 'month'   ? 'active' : ''; ?>">This Month</a>
        <a href="?period=3months" class="<?php echo $period === '3months' ? 'active' : ''; ?>">3 Months</a>
        <a href="?period=6months" class="<?php echo $period === '6months' ? 'active' : ''; ?>">6 Months</a>
      </div>

      <button class="btn-print" onclick="window.print()">
        <i class="bi bi-printer-fill"></i> Export
      </button>
    </div>

    <!-- ── CONTENT ── -->
    <div class="content">

      <!-- Key Metrics -->
      <div class="stat-row">

        <div class="stat-card">
          <div class="stat-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
          <div class="stat-label">Revenue (<?php echo $period === 'month' ? 'This Month' : ($period === '3months' ? 'Last 3 Mo.' : 'Last 6 Mo.'); ?>)</div>
          <div class="stat-val">
            <span class="currency">Rs.</span><?php echo number_format($metrics['period_revenue']); ?>
          </div>
          <div class="stat-change"><i class="bi bi-arrow-up-short"></i> Delivered orders only</div>
        </div>

        <div class="stat-card">
          <div class="stat-card-icon"><i class="bi bi-bag-check-fill"></i></div>
          <div class="stat-label">Total Orders</div>
          <div class="stat-val"><?php echo number_format($metrics['orders_count']); ?></div>
          <div class="stat-change neutral"><i class="bi bi-calendar3"></i> Selected period</div>
        </div>

        <div class="stat-card">
          <div class="stat-card-icon"><i class="bi bi-receipt-cutoff"></i></div>
          <div class="stat-label">Avg. Order Value</div>
          <div class="stat-val">
            <span class="currency">Rs.</span><?php echo number_format($metrics['avg_order']); ?>
          </div>
          <div class="stat-change neutral"><i class="bi bi-calculator"></i> Per transaction</div>
        </div>

        <div class="stat-card">
          <div class="stat-card-icon"><i class="bi bi-people-fill"></i></div>
          <div class="stat-label">Active Customers</div>
          <div class="stat-val"><?php echo number_format($metrics['active_customers']); ?></div>
          <div class="stat-change neutral"><i class="bi bi-person-check"></i> Unique buyers</div>
        </div>

      </div>

      <!-- Charts Row 1 -->
      <div class="charts-grid">

        <!-- Revenue Trend -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5><i class="bi bi-bar-chart-line-fill me-2" style="color:var(--primary)"></i>Revenue Trend</h5>
            <span class="card-panel-badge">Last 6 Months</span>
          </div>
          <div class="card-panel-body">
            <canvas id="revenueTrendChart" height="115"></canvas>
          </div>
        </div>

        <!-- Order Status Donut -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5><i class="bi bi-pie-chart-fill me-2" style="color:var(--primary)"></i>Order Status</h5>
          </div>
          <div class="card-panel-body" style="display:flex; flex-direction:column; align-items:center;">
            <canvas id="statusChart" height="200" style="max-width:240px;"></canvas>
          </div>
        </div>

      </div>

      <!-- Charts Row 2 -->
      <div class="charts-grid-2">

        <!-- Top Products -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5><i class="bi bi-trophy-fill me-2" style="color:var(--primary)"></i>Top Selling Products</h5>
            <span class="card-panel-badge">By Revenue</span>
          </div>
          <div class="products-scroll">
            <div class="products-list" style="padding: 4px 0;">
              <?php foreach ($top_products as $i => $p): ?>
                <div class="top-prod-item">
                  <div class="top-prod-rank <?php echo $i == 0 ? 'gold' : ($i == 1 ? 'silver' : ($i == 2 ? 'bronze' : '')); ?>">
                    <?php echo $i + 1; ?>
                  </div>
                  <div class="top-prod-info">
                    <div class="top-prod-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
                    <div class="top-prod-qty"><?php echo number_format($p['total_qty']); ?> units sold</div>
                  </div>
                  <div class="top-prod-rev">Rs.&nbsp;<?php echo number_format($p['total_revenue']); ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Sales by City -->
        <div class="card-panel">
          <div class="card-panel-header">
            <h5><i class="bi bi-geo-alt-fill me-2" style="color:var(--primary)"></i>Sales by City</h5>
            <span class="card-panel-badge">Top 6</span>
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

            <!-- Revenue total note -->
            <div style="margin-top:20px; padding-top:16px; border-top:1px solid var(--border-light);">
              <div style="font-size:12px; color:var(--text-muted); font-weight:600; margin-bottom:4px;">ALL-TIME DELIVERED REVENUE</div>
              <div style="font-size:22px; font-weight:800; color:var(--text); letter-spacing:-0.5px;">
                Rs.&nbsp;<span style="color:var(--primary);"><?php echo number_format($metrics['total_revenue']); ?></span>
              </div>
            </div>
          </div>
        </div>

      </div>

      <p class="footer-note">Hassan Traders &copy; <?php echo date('Y'); ?> &nbsp;·&nbsp; All figures reflect delivered orders unless noted</p>

    </div><!-- /content -->
  </main>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
    Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

    // ── Revenue Trend ──
    new Chart(document.getElementById("revenueTrendChart"), {
      type: "line",
      data: {
        labels: <?php echo json_encode(array_column($monthly_trend, 'month_label')); ?>,
        datasets: [{
          label: "Revenue (Rs.)",
          data: <?php echo json_encode(array_column($monthly_trend, 'revenue')); ?>,
          borderColor: "#c0392b",
          backgroundColor: (ctx) => {
            const gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, ctx.chart.height);
            gradient.addColorStop(0,   "rgba(192,57,43,0.18)");
            gradient.addColorStop(1,   "rgba(192,57,43,0)");
            return gradient;
          },
          tension: 0.42,
          borderWidth: 2.5,
          pointRadius: 5,
          pointBackgroundColor: "#fff",
          pointBorderColor: "#c0392b",
          pointBorderWidth: 2,
          pointHoverRadius: 7,
          fill: true
        }]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#1a1e27",
            titleColor: "#ffffff",
            bodyColor: "#a0aab8",
            padding: 12,
            cornerRadius: 10,
            callbacks: {
              label: v => "  Rs. " + Number(v.raw).toLocaleString()
            }
          }
        },
        scales: {
          y: {
            grid: { color: "rgba(0,0,0,0.04)" },
            border: { dash: [4, 4] },
            ticks: {
              color: "#7b8498",
              font: { size: 11, weight: '600' },
              callback: v => "Rs. " + (v >= 1000000 ? (v/1000000).toFixed(1)+"M" : (v/1000).toFixed(0)+"K")
            }
          },
          x: {
            grid: { display: false },
            ticks: {
              color: "#7b8498",
              font: { size: 11, weight: '600' }
            }
          }
        }
      }
    });

    // ── Order Status Donut ──
    new Chart(document.getElementById("statusChart"), {
      type: "doughnut",
      data: {
        labels: ["Delivered", "Pending", "Processing", "Shipped", "Cancelled"],
        datasets: [{
          data: [
            <?php echo $status_dist['Delivered']  ?? 0; ?>,
            <?php echo $status_dist['Pending']    ?? 0; ?>,
            <?php echo $status_dist['Processing'] ?? 0; ?>,
            <?php echo $status_dist['Shipped']    ?? 0; ?>,
            <?php echo $status_dist['Cancelled']  ?? 0; ?>
          ],
          backgroundColor: ["#16a34a", "#d97706", "#2563eb", "#0891b2", "#c0392b"],
          borderColor: "#ffffff",
          borderWidth: 3,
          hoverBorderWidth: 3,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        cutout: "64%",
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              padding: 14,
              boxWidth: 10,
              boxHeight: 10,
              borderRadius: 3,
              useBorderRadius: true,
              font: { size: 12, weight: '600' },
              color: "#1a1e27"
            }
          },
          tooltip: {
            backgroundColor: "#1a1e27",
            titleColor: "#ffffff",
            bodyColor: "#a0aab8",
            padding: 12,
            cornerRadius: 10,
            callbacks: {
              label: v => "  " + v.label + ": " + v.raw.toLocaleString()
            }
          }
        }
      }
    });
  </script>
</body>
</html>