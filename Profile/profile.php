<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login and signup/login.php');
  exit;
}

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

// ── Fetch user info ───────────────────────────────
$userId = (int)$_SESSION['user_id'];
$user   = [];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $userId);
$stmt->execute();
$res  = $stmt->get_result();
$user = $res->fetch_assoc() ?? [];
$stmt->close();

// ── Fetch orders ──────────────────────────────────
$orders = [];
$stmt2 = $conn->prepare("
    SELECT 
        o.id, 
        o.order_date, 
        o.status_ENUM AS status, 
        o.total_amount,
        o.payment_method, 
        c.address, 
        c.city,
        GROUP_CONCAT(oi.product_name SEPARATOR '||') AS item_names,
        GROUP_CONCAT(oi.quantity     SEPARATOR '||') AS item_qtys,
        GROUP_CONCAT(oi.price        SEPARATOR '||') AS item_prices
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    LEFT JOIN customers   c  ON o.customer_id = c.id
    WHERE o.customer_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
/*
  ── If your table columns differ, adjust the query above.
     Common column name variants:
       total_amount  → total  | grand_total
       payment_method→ payment
     Also adjust order_items columns as needed.
*/
$stmt2->bind_param('i', $userId);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) {
  $names  = array_filter(explode('||', $row['item_names']  ?? ''));
  $qtys   = explode('||', $row['item_qtys']   ?? '');
  $prices = explode('||', $row['item_prices']  ?? '');
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
$stmt2->close();
$conn->close();

// ── Helpers ───────────────────────────────────────
$name        = htmlspecialchars($user['name']     ?? $_SESSION['user_name'] ?? 'User');
$email       = htmlspecialchars($user['email']    ?? $_SESSION['user_email'] ?? '');
$phone       = htmlspecialchars($user['phone']    ?? $_SESSION['user_phone'] ?? '');
$address     = htmlspecialchars($user['address']  ?? $_SESSION['shipping_address'] ?? '');
$city        = htmlspecialchars($user['city']     ?? $_SESSION['shipping_city'] ?? '');
$joined      = isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : '—';
$orderCount  = count($orders);
$totalSpent  = array_sum(array_column($orders, 'total_amount'));
$initials    = strtoupper(substr($name, 0, 1));

function statusClass(string $s): string
{
  return match (strtolower(trim($s))) {
    'delivered', 'completed' => 'st-delivered',
    'processing', 'confirmed' => 'st-processing',
    'shipped'                => 'st-shipped',
    'cancelled', 'canceled'  => 'st-cancelled',
    default                  => 'st-pending',
  };
}
function statusIcon(string $s): string
{
  return match (strtolower(trim($s))) {
    'delivered', 'completed' => 'bi-patch-check-fill',
    'processing', 'confirmed' => 'bi-arrow-repeat',
    'shipped'                => 'bi-truck',
    'cancelled', 'canceled'  => 'bi-x-circle-fill',
    default                  => 'bi-clock-fill',
  };
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Profile – Hassan Traders</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../NavBar/navbar.css" />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    :root {
      --red: #dc3545;
      --red-dark: #b71c1c;
      --red-glow: rgba(220, 53, 69, 0.18);
      --red-soft: rgba(220, 53, 69, 0.07);
      --bg: #f4f4f6;
      --surface: #ffffff;
      --text: #141420;
      --muted: #71717a;
      --border: #e4e4e7;
      --radius: 18px;
      --shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    h1,
    h2,
    h3,
    .fw-display {
      font-family: 'Syne', sans-serif;
    }

    /* ── HERO ─────────────────────────────────────────── */
    .profile-hero {
      background: linear-gradient(140deg, #141420 0%, #7f0000 55%, #dc3545 100%);
      padding: 52px 0 100px;
      position: relative;
      overflow: hidden;
    }

    .profile-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 80% 20%, rgba(220, 53, 69, 0.25) 0%, transparent 60%),
        radial-gradient(circle at 10% 80%, rgba(183, 28, 28, 0.2) 0%, transparent 50%);
    }

    .profile-hero::after {
      content: '';
      position: absolute;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.025'%3E%3Ccircle cx='40' cy='40' r='30'/%3E%3C/g%3E%3C/svg%3E");
      background-size: 80px 80px;
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .avatar-ring {
      width: 88px;
      height: 88px;
      border-radius: 50%;
      background: linear-gradient(135deg, #dc3545, #b71c1c);
      border: 3px solid rgba(255, 255, 255, 0.25);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Syne', sans-serif;
      font-size: 2rem;
      font-weight: 800;
      color: white;
      flex-shrink: 0;
      box-shadow: 0 8px 30px rgba(220, 53, 69, 0.5);
    }

    .hero-name {
      font-family: 'Syne', sans-serif;
      font-size: 1.75rem;
      font-weight: 800;
      color: white;
      line-height: 1.1;
      margin: 0;
    }

    .hero-email {
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.88rem;
      margin: 2px 0 0;
    }

    .stat-chip {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      background: rgba(255, 255, 255, 0.10);
      border: 1px solid rgba(255, 255, 255, 0.18);
      border-radius: 14px;
      padding: 12px 22px;
      backdrop-filter: blur(10px);
      color: white;
    }

    .stat-chip .val {
      font-family: 'Syne', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      line-height: 1;
    }

    .stat-chip .lbl {
      font-size: 0.72rem;
      color: rgba(255, 255, 255, 0.6);
      margin-top: 3px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }

    .btn-logout-hero {
      border: 1.5px solid rgba(255, 255, 255, 0.3);
      color: rgba(255, 255, 255, 0.85);
      background: transparent;
      border-radius: 50px;
      padding: 8px 22px;
      font-size: 0.83rem;
      font-weight: 600;
      transition: all 0.25s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 7px;
    }

    .btn-logout-hero:hover {
      background: rgba(255, 255, 255, 0.12);
      color: white;
    }

    /* ── CARD OFFSET ──────────────────────────────────── */
    .cards-offset {
      margin-top: -62px;
    }

    /* ── CARD BASE ────────────────────────────────────── */
    .card-panel {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .card-panel-head {
      padding: 18px 24px 14px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-panel-head .head-icon {
      width: 36px;
      height: 36px;
      background: var(--red-soft);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--red);
      font-size: 1rem;
      flex-shrink: 0;
    }

    .card-panel-head .head-title {
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: 1rem;
      margin: 0;
      flex: 1;
    }

    .card-panel-body {
      padding: 20px 24px;
    }

    /* ── INFO ROWS ────────────────────────────────────── */
    .info-line {
      display: flex;
      align-items: flex-start;
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
      gap: 12px;
    }

    .info-line:last-child {
      border-bottom: none;
    }

    .info-line .ico {
      width: 32px;
      height: 32px;
      border-radius: 9px;
      background: var(--red-soft);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--red);
      font-size: 0.85rem;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .info-line .lbl {
      font-size: 0.72rem;
      color: var(--muted);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .info-line .val {
      font-size: 0.93rem;
      font-weight: 600;
      color: var(--text);
      margin-top: 1px;
    }

    .info-line .val.empty {
      color: var(--muted);
      font-style: italic;
      font-weight: 400;
    }

    /* ── ORDER CARD ───────────────────────────────────── */
    .order-block {
      border: 1px solid var(--border);
      border-radius: 14px;
      overflow: hidden;
      transition: box-shadow 0.2s, transform 0.2s;
    }

    .order-block:hover {
      box-shadow: 0 8px 28px rgba(0, 0, 0, 0.09);
      transform: translateY(-2px);
    }

    .order-head {
      background: var(--bg);
      padding: 13px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 8px;
      border-bottom: 1px solid var(--border);
    }

    .order-ref {
      font-size: 0.72rem;
      font-weight: 700;
      color: var(--muted);
      letter-spacing: 0.07em;
      text-transform: uppercase;
    }

    .order-date {
      font-size: 0.8rem;
      color: var(--muted);
    }

    /* Status badges */
    .st-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 12px;
      border-radius: 50px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.03em;
    }

    .st-delivered {
      background: #d1fae5;
      color: #065f46;
    }

    .st-processing {
      background: #fef9c3;
      color: #854d0e;
    }

    .st-shipped {
      background: #dbeafe;
      color: #1e40af;
    }

    .st-cancelled {
      background: #fee2e2;
      color: #991b1b;
    }

    .st-pending {
      background: #ede9fe;
      color: #5b21b6;
    }

    .order-items-wrap {
      padding: 14px 18px;
    }

    .oi-row {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 9px 0;
      border-bottom: 1px dashed var(--border);
    }

    .oi-row:last-child {
      border-bottom: none;
    }

    .oi-dot {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: var(--red-soft);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--red);
      font-size: 1rem;
      flex-shrink: 0;
    }

    .oi-name {
      font-weight: 600;
      font-size: 0.88rem;
    }

    .oi-qty {
      font-size: 0.78rem;
      color: var(--muted);
    }

    .oi-price {
      font-weight: 700;
      color: var(--red);
      margin-left: auto;
      white-space: nowrap;
    }

    .order-foot {
      background: var(--bg);
      padding: 11px 18px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-top: 1px solid var(--border);
    }

    .order-pay {
      font-size: 0.78rem;
      color: var(--muted);
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .order-tot {
      font-family: 'Syne', sans-serif;
      font-weight: 800;
      font-size: 1rem;
      color: var(--text);
    }

    /* ── EMPTY STATE ──────────────────────────────────── */
    .empty-orders {
      padding: 56px 20px;
      text-align: center;
    }

    .empty-orders .empty-ico {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: var(--red-soft);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      font-size: 1.9rem;
      color: var(--red);
    }

    .empty-orders h5 {
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      margin-bottom: 6px;
    }

    .empty-orders p {
      color: var(--muted);
      font-size: 0.88rem;
    }

    /* ── CTA BUTTON ───────────────────────────────────── */
    .btn-cta {
      background: linear-gradient(135deg, #dc3545, #b71c1c);
      color: white !important;
      border: none;
      border-radius: 50px;
      padding: 12px 28px;
      font-weight: 700;
      font-size: 0.9rem;
      letter-spacing: 0.3px;
      box-shadow: 0 6px 20px rgba(220, 53, 69, 0.35);
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-cta:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 28px rgba(220, 53, 69, 0.5);
    }

    /* ── RESPONSIVE ───────────────────────────────────── */
    @media (max-width: 576px) {
      .hero-name {
        font-size: 1.4rem;
      }

      .stat-chip {
        padding: 10px 14px;
      }

      .card-panel-body {
        padding: 16px;
      }
    }
  </style>
</head>

<body>

  <?php include '../NavBar/navbar.php'; ?>

  <!-- ═══════════════ HERO ═══════════════ -->
  <section class="profile-hero">
    <div class="container hero-content">

      <!-- Top row -->
      <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
          <div class="avatar-ring"><?= $initials ?></div>
          <div>
            <p class="hero-name"><?= $name ?></p>
            <p class="hero-email"><?= $email ?: 'No email on file' ?></p>
          </div>
        </div>
        <a href="../login and signup/logout.php" class="btn-logout-hero">
          <i class="bi bi-box-arrow-right"></i> Logout
        </a>
      </div>

      <!-- Stats row -->
      <div class="d-flex flex-wrap gap-3">
        <div class="stat-chip">
          <div class="val"><?= $orderCount ?></div>
          <div class="lbl">Total Orders</div>
        </div>
        <div class="stat-chip">
          <div class="val">Rs&nbsp;<?= number_format($totalSpent) ?></div>
          <div class="lbl">Total Spent</div>
        </div>
        <div class="stat-chip">
          <div class="val"><?= $joined ?></div>
          <div class="lbl">Member Since</div>
        </div>
      </div>

    </div>
  </section>

  <!-- ═══════════════ MAIN CONTENT ═══════════════ -->
  <div class="container pb-5 cards-offset">
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
              <div class="ico"><i class="bi bi-person-fill"></i></div>
              <div>
                <div class="lbl">Full Name</div>
                <div class="val"><?= $name ?: '—' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="ico"><i class="bi bi-envelope-fill"></i></div>
              <div>
                <div class="lbl">Email Address</div>
                <div class="val <?= $email ? '' : 'empty' ?>"><?= $email ?: 'Not provided' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="ico"><i class="bi bi-telephone-fill"></i></div>
              <div>
                <div class="lbl">Phone Number</div>
                <div class="val <?= $phone ? '' : 'empty' ?>"><?= $phone ?: 'Not provided' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="ico"><i class="bi bi-geo-alt-fill"></i></div>
              <div>
                <div class="lbl">City</div>
                <div class="val <?= $city ? '' : 'empty' ?>"><?= $city ?: 'Not provided' ?></div>
              </div>
            </div>

            <div class="info-line">
              <div class="ico"><i class="bi bi-house-door-fill"></i></div>
              <div>
                <div class="lbl">Saved Address</div>
                <div class="val <?= $address ? '' : 'empty' ?>"><?= $address ?: 'Not provided' ?></div>
              </div>
            </div>

          </div>

          <!-- Bottom CTA -->
          <div class="card-panel-body" style="border-top: 1px solid var(--border); padding-top: 16px;">
            <a href="../Add to Cart and CheckOut/checkout.php" class="btn-cta w-100 justify-content-center">
              <i class="bi bi-bag-plus-fill"></i> Place New Order
            </a>
            <a href="../Products/Products.php" class="btn-cta w-100 justify-content-center mt-2"
              style="background: var(--bg); color: var(--text) !important; box-shadow: none; border: 1px solid var(--border);">
              <i class="bi bi-grid-fill"></i> Browse Products
            </a>
          </div>
        </div>
      </div>

      <!-- ── RIGHT: Order History ── -->
      <div class="col-lg-8" id="orders">
        <div class="card-panel">
          <div class="card-panel-head">
            <div class="head-icon"><i class="bi bi-bag-heart-fill"></i></div>
            <h2 class="head-title">Order History</h2>
            <?php if ($orderCount): ?>
              <span class="badge rounded-pill ms-auto"
                style="background:var(--red-soft);color:var(--red);font-size:.75rem;font-weight:700;padding:6px 12px;">
                <?= $orderCount ?> order<?= $orderCount > 1 ? 's' : '' ?>
              </span>
            <?php endif; ?>
          </div>

          <div class="card-panel-body">
            <?php if (empty($orders)): ?>
              <div class="empty-orders">
                <div class="empty-ico"><i class="bi bi-bag-x"></i></div>
                <h5>No orders yet</h5>
                <p>Your orders will appear here once you place one.</p>
                <a href="../Products/Products.php" class="btn-cta mt-2">
                  <i class="bi bi-shop"></i> Start Shopping
                </a>
              </div>

            <?php else: ?>
              <div class="d-flex flex-column gap-3">
                <?php foreach ($orders as $o): ?>
                  <div class="order-block">

                    <!-- Head -->
                    <div class="order-head">
                      <div>
                        <div class="order-ref"># <?= htmlspecialchars($o['id']) ?></div>
                        <div class="order-date">
                          <i class="bi bi-calendar3 me-1"></i>
                          <?= date('d M Y, g:i A', strtotime($o['created_at'])) ?>
                        </div>
                      </div>
                      <span class="st-badge <?= statusClass($o['status']) ?>">
                        <i class="bi <?= statusIcon($o['status']) ?>"></i>
                        <?= htmlspecialchars($o['status']) ?>
                      </span>
                    </div>

                    <!-- Items -->
                    <div class="order-items-wrap">
                      <?php foreach ($o['items'] as $item): ?>
                        <div class="oi-row">
                          <div class="oi-dot"><i class="bi bi-box-seam-fill"></i></div>
                          <div>
                            <div class="oi-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="oi-qty">Qty: <?= (int)$item['qty'] ?></div>
                          </div>
                          <div class="oi-price">Rs <?= number_format($item['price']) ?></div>
                        </div>
                      <?php endforeach; ?>
                    </div>

                    <!-- Foot -->
                    <div class="order-foot">
                      <span class="order-pay">
                        <i class="bi bi-credit-card"></i>
                        <?= htmlspecialchars(ucfirst($o['payment_method'] ?? 'COD')) ?>
                      </span>
                      <span class="order-tot">Rs <?= number_format($o['total_amount']) ?></span>
                    </div>

                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

        </div><!-- /card-panel -->
      </div><!-- /col -->

    </div><!-- /row -->
  </div><!-- /container -->

  <script src="../NavBar/navbar.js"></script>
</body>

</html>