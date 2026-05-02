<?php
session_start();

// ── Auth guard ────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login and signup/login.php');
  exit;
}

// ── Database ──────────────────────────────────────────────────────
// db_connect.php
$host     = "localhost";
$dbname   = "htss";   // ← CHANGE THIS
$user     = "root";
$pass     = "";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$userId    = (int)$_SESSION['user_id'];
$userEmail = $_SESSION['user_email'] ?? '';

// ── Fetch user (column is 'fullname' not 'name') ──────────────────
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?? [];
mysqli_stmt_close($stmt);

// ── Fetch orders ──────────────────────────────────────────────────
// Orders link to 'customers' table by email (since customers are
// created fresh per order, not linked to users.id directly).
// We match by the logged-in user's email address.
$orders = [];

if ($userEmail) {
  $stmt2 = mysqli_prepare($conn, "
    SELECT
      o.id,
      o.order_date,
      o.status_ENUM      AS status,
      o.total_amount,
      o.payment_method,
      c.address,
      c.city,
      GROUP_CONCAT(oi.product_name ORDER BY oi.id SEPARATOR '||') AS item_names,
      GROUP_CONCAT(oi.quantity     ORDER BY oi.id SEPARATOR '||') AS item_qtys,
      GROUP_CONCAT(oi.price        ORDER BY oi.id SEPARATOR '||') AS item_prices
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    LEFT JOIN customers   c  ON o.customer_id = c.id
    WHERE c.email = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
  ");
  mysqli_stmt_bind_param($stmt2, 's', $userEmail);
  mysqli_stmt_execute($stmt2);
  $res2 = mysqli_stmt_get_result($stmt2);
  while ($row = mysqli_fetch_assoc($res2)) {
    $names  = array_filter(explode('||', $row['item_names']  ?? ''));
    $qtys   = explode('||', $row['item_qtys']   ?? '');
    $prices = explode('||', $row['item_prices'] ?? '');
    $row['items'] = [];
    foreach ($names as $i => $n) {
      $row['items'][] = [
        'name'  => $n,
        'qty'   => $qtys[$i]   ?? 1,
        'price' => $prices[$i] ?? 0,
      ];
    }
    $orders[] = $row;
  }
  mysqli_stmt_close($stmt2);
}

mysqli_close($conn);

// ── Computed helpers ──────────────────────────────────────────────
$name       = htmlspecialchars($user['fullname'] ?? $_SESSION['user_name']  ?? 'User');
$email      = htmlspecialchars($user['email']   ?? $_SESSION['user_email'] ?? '');
$phone      = htmlspecialchars($user['phone']   ?? $_SESSION['user_phone'] ?? '');
$address    = htmlspecialchars($user['address'] ?? $_SESSION['shipping_address'] ?? '');
$city       = htmlspecialchars($user['city']    ?? $_SESSION['shipping_city']    ?? '');
$joined     = isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : '—';
$orderCount = count($orders);
$totalSpent = array_sum(array_column($orders, 'total_amount'));
$initials   = strtoupper(substr($name, 0, 1));

// Status helpers
function stClass(string $s): string
{
  return match (strtolower(trim($s))) {
    'delivered', 'completed'          => 'st-delivered',
    'processing', 'confirmed', 'paid'  => 'st-processing',
    'shipped', 'dispatched'           => 'st-shipped',
    'cancelled', 'canceled', 'refunded' => 'st-cancelled',
    default                          => 'st-pending',
  };
}
function stIcon(string $s): string
{
  return match (strtolower(trim($s))) {
    'delivered', 'completed'          => 'bi-patch-check-fill',
    'processing', 'confirmed', 'paid'  => 'bi-arrow-repeat',
    'shipped', 'dispatched'           => 'bi-truck',
    'cancelled', 'canceled', 'refunded' => 'bi-x-circle-fill',
    default                          => 'bi-clock-fill',
  };
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Profile — Hassan Traders</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- Vendor -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Site -->
  <link rel="stylesheet" href="../NavBar/navbar.css" />

  <!-- Page styles -->
  <link rel="stylesheet" href="profile.css" />
</head>

<body>

  <?php include '../NavBar/navbar.php'; ?>

  <!-- ════════════════════════════════════════
       HERO BANNER
  ════════════════════════════════════════ -->
  <section class="profile-hero">
    <div class="hero-mesh"></div>
    <div class="container hero-content">

      <!-- Top row: avatar + name + logout -->
      <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-0">

        <div class="d-flex align-items-center gap-4">
          <div class="avatar-wrap">
            <div class="avatar-ring"><?= $initials ?></div>
            <span class="avatar-online"></span>
          </div>
          <div>
            <h1 class="hero-name"><?= $name ?></h1>
            <?php if ($email): ?>
              <p class="hero-email"><i class="bi bi-envelope-fill" style="font-size:11px;opacity:.6;"></i><?= $email ?></p>
            <?php endif; ?>
            <p class="hero-joined">
              <i class="bi bi-calendar3" style="font-size:10px;"></i>
              Member since <?= $joined ?>
            </p>
          </div>
        </div>

        <a href="../login and signup/logout.php" class="btn-logout">
          <i class="bi bi-box-arrow-right"></i> Sign Out
        </a>
      </div>

      <!-- Stat chips -->
      <div class="stat-chips">
        <div class="stat-chip">
          <div class="sc-val"><?= $orderCount ?></div>
          <div class="sc-lbl">Total Orders</div>
        </div>
        <div class="stat-chip">
          <div class="sc-val">Rs&nbsp;<?= number_format($totalSpent) ?></div>
          <div class="sc-lbl">Total Spent</div>
        </div>
        <div class="stat-chip">
          <div class="sc-val"><?= $joined ?></div>
          <div class="sc-lbl">Member Since</div>
        </div>
        <?php if ($orderCount > 0):
          $delivered = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'delivered'));
        ?>
          <div class="stat-chip">
            <div class="sc-val"><?= $delivered ?></div>
            <div class="sc-lbl">Delivered</div>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </section>

  <!-- ════════════════════════════════════════
       MAIN CONTENT
  ════════════════════════════════════════ -->
  <div class="container cards-offset">
    <div class="row g-4">

      <!-- ── LEFT: Account Details ── -->
      <div class="col-lg-4">
        <div class="card-panel h-100 d-flex flex-column">

          <div class="card-panel-head">
            <div class="head-icon"><i class="bi bi-person-vcard-fill"></i></div>
            <h2 class="head-title">Account Details</h2>
          </div>

          <div class="card-panel-body flex-grow-1">

            <div class="info-line">
              <div class="info-ico"><i class="bi bi-person-fill"></i></div>
              <div>
                <div class="info-lbl">Full Name</div>
                <div class="info-val"><?= $name ?: '—' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="info-ico"><i class="bi bi-envelope-fill"></i></div>
              <div>
                <div class="info-lbl">Email Address</div>
                <div class="info-val <?= $email ? '' : 'empty' ?>"><?= $email ?: 'Not provided' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="info-ico"><i class="bi bi-telephone-fill"></i></div>
              <div>
                <div class="info-lbl">Phone Number</div>
                <div class="info-val <?= $phone ? '' : 'empty' ?>"><?= $phone ?: 'Not provided' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="info-ico"><i class="bi bi-geo-alt-fill"></i></div>
              <div>
                <div class="info-lbl">City</div>
                <div class="info-val <?= $city ? '' : 'empty' ?>"><?= $city ?: 'Not provided' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="info-ico"><i class="bi bi-house-door-fill"></i></div>
              <div>
                <div class="info-lbl">Saved Address</div>
                <div class="info-val <?= $address ? '' : 'empty' ?>" style="font-size:13px;line-height:1.5;"><?= $address ?: 'Not provided' ?></div>
              </div>
            </div>

          </div><!-- /body -->

          <!-- Action buttons -->
          <div class="card-panel-body" style="border-top:1px solid var(--border-light);padding-top:18px;">
            <a href="../Add to Cart and CheckOut/checkout.php" class="btn-cta mb-2">
              <i class="bi bi-bag-plus-fill"></i> Place New Order
            </a>
            <a href="../Products/Products.php" class="btn-secondary-cta">
              <i class="bi bi-grid-fill"></i> Browse Products
            </a>
            <a href="#orders" class="btn-secondary-cta mt-2">
              <i class="bi bi-clock-history"></i> My Orders
            </a>
          </div>

        </div>
      </div><!-- /col-lg-4 -->

      <!-- ── RIGHT: Order History ── -->
      <div class="col-lg-8" id="orders">
        <div class="card-panel">

          <div class="card-panel-head">
            <div class="head-icon"><i class="bi bi-bag-heart-fill"></i></div>
            <h2 class="head-title">Order History</h2>
            <?php if ($orderCount): ?>
              <span class="head-badge" id="visibleCount"><?= $orderCount ?> order<?= $orderCount > 1 ? 's' : '' ?></span>
            <?php endif; ?>
          </div>

          <?php if (!empty($orders)): ?>

            <!-- Search + Filter bar -->
            <div class="order-filter-bar">
              <div class="order-search">
                <i class="bi bi-search"></i>
                <input type="search" id="orderSearch" placeholder="Search orders, products…" autocomplete="off" />
              </div>
              <select id="statusFilter" class="filter-select">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

          <?php endif; ?>

          <div class="card-panel-body">

            <?php if (empty($orders)): ?>
              <!-- Empty state -->
              <div class="empty-state">
                <i class="bi bi-bag-x empty-state-ico"></i>
                <h5>No orders yet</h5>
                <p>Your order history will appear here once you place your first order.</p>
                <a href="../Products/Products.php" class="btn-cta mt-3" style="width:auto;padding:12px 28px;">
                  <i class="bi bi-shop"></i> Start Shopping
                </a>
              </div>

            <?php else: ?>

              <!-- No filter results -->
              <div id="filterNoResults" style="display:none;text-align:center;padding:36px;color:var(--muted);">
                <i class="bi bi-search" style="font-size:2rem;display:block;margin-bottom:12px;opacity:.3;"></i>
                <p style="font-weight:600;">No orders match your search.</p>
              </div>

              <!-- Orders list -->
              <div class="orders-list">
                <?php foreach ($orders as $o): ?>
                  <div class="order-block">

                    <!-- Head -->
                    <div class="order-head">
                      <div class="order-meta">
                        <div class="order-ref" title="Click to copy">#<?= htmlspecialchars($o['id']) ?></div>
                        <div class="order-date">
                          <i class="bi bi-calendar3"></i>
                          <?= date('d M Y, g:i A', strtotime($o['order_date'] ?? $o['created_at'] ?? 'now')) ?>
                        </div>
                      </div>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="st-badge <?= stClass($o['status']) ?>">
                          <i class="bi <?= stIcon($o['status']) ?>"></i>
                          <?= ucfirst(htmlspecialchars($o['status'])) ?>
                        </span>
                        <?php if (!empty($o['items'])): ?>
                          <button class="btn-toggle-items">
                            <span class="toggle-label">Show Items</span>
                            <i class="bi bi-chevron-down toggle-icon" style="transition:transform .28s;"></i>
                          </button>
                        <?php endif; ?>
                      </div>
                    </div>

                    <!-- Delivery address (if available) -->
                    <?php if ($o['city'] || $o['address']): ?>
                      <div class="order-addr">
                        <i class="bi bi-geo-alt"></i>
                        <?= htmlspecialchars(array_filter([$o['city'], $o['address']]) ? implode(', ', array_filter([$o['city'], $o['address']])) : '') ?>
                      </div>
                    <?php endif; ?>

                    <!-- Items (collapsible) -->
                    <?php if (!empty($o['items'])): ?>
                      <div class="order-items-body">
                        <div class="order-items">
                          <?php foreach ($o['items'] as $item): ?>
                            <div class="oi-row">
                              <div class="oi-icon"><i class="bi bi-box-seam-fill"></i></div>
                              <div style="flex:1;min-width:0;">
                                <div class="oi-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="oi-qty">Qty: <?= (int)$item['qty'] ?></div>
                              </div>
                              <div class="oi-price">Rs <?= number_format($item['price']) ?></div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    <?php endif; ?>

                    <!-- Foot -->
                    <div class="order-foot">
                      <span class="order-pay">
                        <i class="bi bi-credit-card-fill"></i>
                        <?= htmlspecialchars(ucwords($o['payment_method'] ?? 'COD')) ?>
                      </span>
                      <span class="order-tot">Rs <?= number_format($o['total_amount']) ?></span>
                    </div>

                  </div>
                <?php endforeach; ?>
              </div>

            <?php endif; ?>
          </div><!-- /card-panel-body -->

          <!-- Summary footer -->
          <?php if (!empty($orders)): ?>
            <div class="orders-summary-bar">
              <span class="sum-label">Total spent across all orders</span>
              <span class="sum-val">Rs <?= number_format($totalSpent) ?></span>
            </div>
          <?php endif; ?>

        </div><!-- /card-panel -->
      </div><!-- /col-lg-8 -->

    </div><!-- /row -->
  </div><!-- /container -->

  <!-- ════ Scripts ════ -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../NavBar/navbar.js"></script>
  <script src="profile.js"></script>

</body>

</html>